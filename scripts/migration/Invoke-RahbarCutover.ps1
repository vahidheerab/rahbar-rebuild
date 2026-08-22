[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('Preflight', 'Baseline', 'Snapshot', 'Reconcile', 'Cutover')]
    [string] $Action,
    [string] $EvidenceDirectory,
    [string] $MigrationAdapter,
    [switch] $ProductionConfirmed,
    [switch] $FreezeConfirmed
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$PSNativeCommandUseErrorActionPreference = $true

$script:RepositoryRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$script:LegacyCompose = Join-Path $script:RepositoryRoot 'legacy\compose.yaml'
$script:RebuildCompose = Join-Path $script:RepositoryRoot 'rebuild\compose.yaml'
$script:RunId = Get-Date -Format 'yyyyMMdd-HHmmss'
if ([string]::IsNullOrWhiteSpace($EvidenceDirectory)) {
    $EvidenceDirectory = Join-Path ([System.IO.Path]::GetTempPath()) "rahbar-migration-$($script:RunId)"
}
$script:EvidenceDirectory = [System.IO.Path]::GetFullPath($EvidenceDirectory)

function Write-Step([string] $Message) {
    Write-Host "[$(Get-Date -Format 'HH:mm:ss')] $Message"
}

function Assert-LastExitCode([string] $Operation) {
    if ($LASTEXITCODE -ne 0) { throw "$Operation failed with exit code $LASTEXITCODE." }
}

function Invoke-Compose([string] $ComposeFile, [string[]] $Arguments) {
    & docker compose -f $ComposeFile @Arguments
    Assert-LastExitCode "docker compose: $($Arguments -join ' ')"
}

function Get-DatabaseEnvironment([string] $ComposeFile) {
    $values = @{}
    foreach ($name in @('MYSQL_USER', 'MYSQL_PASSWORD', 'MYSQL_DATABASE')) {
        $value = & docker compose -f $ComposeFile exec -T db printenv $name
        Assert-LastExitCode "read container environment key $name"
        $values[$name] = ([string]$value).Trim()
        if ([string]::IsNullOrWhiteSpace($values[$name])) { throw "Database environment key is empty: $name" }
    }
    return $values
}

function Invoke-DatabaseQuery([string] $ComposeFile, [string] $Sql) {
    $envValues = Get-DatabaseEnvironment $ComposeFile
    $arguments = @(
        'compose', '-f', $ComposeFile, 'exec', '-T',
        '-e', "MYSQL_PWD=$($envValues.MYSQL_PASSWORD)",
        'db', 'mysql', '--default-character-set=utf8mb4', '--batch', '--skip-column-names',
        "-u$($envValues.MYSQL_USER)", $envValues.MYSQL_DATABASE, '-e', $Sql
    )
    $output = & docker @arguments
    Assert-LastExitCode 'read-only database query'
    return ([string]::Join("`n", @($output))).TrimEnd("`r", "`n")
}

function Get-DatabaseScalar([string] $ComposeFile, [string] $Sql) {
    $result = @(Invoke-DatabaseQuery $ComposeFile $Sql)
    if ($result.Count -ne 1) { throw "Expected one scalar result; received $($result.Count)." }
    return [string] $result[0]
}

function Assert-Prerequisites {
    Write-Step 'Checking Docker, Compose configuration and database health.'
    & docker version --format '{{.Server.Version}}' | Out-Null
    Assert-LastExitCode 'docker version'
    foreach ($file in @($script:LegacyCompose, $script:RebuildCompose)) {
        if (-not (Test-Path -LiteralPath $file -PathType Leaf)) { throw "Compose file not found: $file" }
        Invoke-Compose $file @('config', '--quiet')
    }
    if ((Get-DatabaseScalar $script:LegacyCompose 'SELECT 1;') -ne '1') { throw 'Legacy DB health query failed.' }
    if ((Get-DatabaseScalar $script:RebuildCompose 'SELECT 1;') -ne '1') { throw 'Rebuild DB health query failed.' }
    Write-Step 'Preflight passed.'
}

function New-EvidenceDirectory {
    if (-not (Test-Path -LiteralPath $script:EvidenceDirectory)) {
        New-Item -ItemType Directory -Path $script:EvidenceDirectory | Out-Null
    }
}

function Get-BaselineMetrics([string] $ComposeFile) {
    $sql = @"
SELECT 'database', DATABASE()
UNION ALL SELECT 'users', COUNT(*) FROM wp_users
UNION ALL SELECT 'published_posts', COUNT(*) FROM wp_posts WHERE post_type=0x706f7374 AND post_status=0x7075626c697368
UNION ALL SELECT 'published_pages', COUNT(*) FROM wp_posts WHERE post_type=0x70616765 AND post_status=0x7075626c697368
UNION ALL SELECT 'published_products', COUNT(*) FROM wp_posts WHERE post_type=0x70726f64756374 AND post_status=0x7075626c697368
UNION ALL SELECT 'orders', COUNT(*) FROM wp_posts WHERE post_type=0x73686f705f6f72646572
UNION ALL SELECT 'order_total', COALESCE(ROUND(SUM(CAST(pm.meta_value AS DECIMAL(30,2))),2),0) FROM wp_posts p JOIN wp_postmeta pm ON pm.post_id=p.ID AND pm.meta_key=0x5f6f726465725f746f74616c WHERE p.post_type=0x73686f705f6f72646572
UNION ALL SELECT 'transaction_ids', COUNT(DISTINCT pm.meta_value) FROM wp_posts p JOIN wp_postmeta pm ON pm.post_id=p.ID AND pm.meta_key=0x5f7472616e73616374696f6e5f6964 WHERE p.post_type=0x73686f705f6f72646572 AND LENGTH(pm.meta_value)>0
UNION ALL SELECT 'questions', COUNT(*) FROM wp_posts WHERE post_type=0x7161 AND post_status=0x7075626c697368
UNION ALL SELECT 'courses', COUNT(*) FROM wp_posts WHERE post_type=0x636f7572736573 AND post_status=0x7075626c697368
UNION ALL SELECT 'lessons', COUNT(*) FROM wp_posts WHERE post_type=0x6c6573736f6e AND post_status=0x7075626c697368
UNION ALL SELECT 'attachments', COUNT(*) FROM wp_posts WHERE post_type=0x6174746163686d656e74
UNION ALL SELECT 'max_user_id', COALESCE(MAX(ID),0) FROM wp_users
UNION ALL SELECT 'max_post_id', COALESCE(MAX(ID),0) FROM wp_posts
UNION ALL SELECT 'max_order_id', COALESCE(MAX(ID),0) FROM wp_posts WHERE post_type=0x73686f705f6f72646572
UNION ALL SELECT 'max_modified_gmt', COALESCE(DATE_FORMAT(MAX(post_modified_gmt),'%Y-%m-%dT%H:%i:%sZ'),'') FROM wp_posts;
"@
    $m = [ordered]@{ generated_at = (Get-Date).ToString('o') }
    $lines = (Invoke-DatabaseQuery $ComposeFile $sql) -split "`r?`n"
    foreach ($line in $lines) {
        if ([string]::IsNullOrWhiteSpace($line)) { continue }
        $parts = $line -split "`t", 2
        if ($parts.Count -ne 2) { throw "Unexpected baseline row: $line" }
        $value = $parts[1]
        if ($parts[0] -in @('users','published_posts','published_pages','published_products','orders','transaction_ids','questions','courses','lessons','attachments','max_user_id','max_post_id','max_order_id')) {
            $value = [long]$value
        }
        $m[$parts[0]] = $value
    }
    return [pscustomobject]$m
}

function Write-Baseline {
    New-EvidenceDirectory
    Write-Step 'Collecting non-PII baseline metrics and high-water marks.'
    $payload = [ordered]@{
        run_id = $script:RunId
        warning = 'Re-run against a fresh production snapshot on Cutover day.'
        source = Get-BaselineMetrics $script:LegacyCompose
        target = Get-BaselineMetrics $script:RebuildCompose
    }
    $path = Join-Path $script:EvidenceDirectory 'baseline.json'
    $payload | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath $path -Encoding UTF8
    Write-Step "Baseline written to $path"
    return $payload
}

function Export-DatabaseSnapshot([string] $ComposeFile, [string] $Label) {
    New-EvidenceDirectory
    $dumpPath = Join-Path $script:EvidenceDirectory "$Label-$($script:RunId).sql"
    if (-not $PSCmdlet.ShouldProcess($dumpPath, "Create $Label database snapshot")) { return }
    Write-Step "Creating $Label snapshot with --single-transaction."
    $envValues = Get-DatabaseEnvironment $ComposeFile
    $errorPath = Join-Path $script:EvidenceDirectory "$Label-mysqldump.stderr.log"
    $arguments = @(
        'compose', '-f', $ComposeFile, 'exec', '-T',
        '-e', "MYSQL_PWD=$($envValues.MYSQL_PASSWORD)",
        'db', 'mysqldump', '--default-character-set=utf8mb4', '--single-transaction', '--quick',
        '--routines', '--triggers', '--hex-blob', "-u$($envValues.MYSQL_USER)", $envValues.MYSQL_DATABASE
    )
    $process = Start-Process -FilePath 'docker' -ArgumentList $arguments -RedirectStandardOutput $dumpPath -RedirectStandardError $errorPath -WindowStyle Hidden -Wait -PassThru
    if ($process.ExitCode -ne 0) {
        $errorText = Get-Content -LiteralPath $errorPath -Raw -ErrorAction SilentlyContinue
        throw "mysqldump failed: $errorText"
    }
    Remove-Item -LiteralPath $errorPath -Force -ErrorAction SilentlyContinue
    $hash = (Get-FileHash -Algorithm SHA256 -LiteralPath $dumpPath).Hash.ToLowerInvariant()
    "$hash  $([System.IO.Path]::GetFileName($dumpPath))" | Set-Content -LiteralPath "$dumpPath.sha256" -Encoding ascii
    Write-Step "Snapshot and checksum written to $script:EvidenceDirectory"
}

function Compare-Baselines {
    $payload = Write-Baseline
    $fields = @('users','published_posts','published_pages','published_products','orders','order_total','transaction_ids','questions','courses','lessons','attachments')
    $rows = foreach ($field in $fields) {
        $source = $payload.source.$field
        $target = $payload.target.$field
        [pscustomobject]@{ Metric=$field; Source=$source; Target=$target; Equal=([string]$source -eq [string]$target) }
    }
    $path = Join-Path $script:EvidenceDirectory 'reconciliation.csv'
    $rows | Export-Csv -LiteralPath $path -NoTypeInformation -Encoding UTF8
    $rows | Format-Table -AutoSize
    if ($rows.Where({ -not $_.Equal }).Count -gt 0) { throw 'Reconciliation has differences. Do not cut over.' }
    Write-Step "Reconciliation passed and was written to $path"
}

function Invoke-MigrationAdapter([ValidateSet('Full','Delta')][string] $Mode) {
    if ([string]::IsNullOrWhiteSpace($MigrationAdapter)) {
        throw 'MigrationAdapter is required. Cutover remains blocked until payment/LMS/SpotPlayer target contracts are finalized.'
    }
    $path = [System.IO.Path]::GetFullPath($MigrationAdapter)
    if (-not (Test-Path -LiteralPath $path -PathType Leaf) -or [System.IO.Path]::GetExtension($path) -ne '.ps1') {
        throw "Reviewed PowerShell migration adapter not found: $path"
    }
    if (-not $PSCmdlet.ShouldProcess($path, "Run migration adapter in $Mode mode")) { return }
    & $path -Mode $Mode -EvidenceDirectory $script:EvidenceDirectory
    if ($LASTEXITCODE -and $LASTEXITCODE -ne 0) { throw "Migration adapter ($Mode) failed." }
}

Assert-Prerequisites
switch ($Action) {
    'Preflight' { return }
    'Baseline' { [void](Write-Baseline) }
    'Snapshot' {
        Export-DatabaseSnapshot $script:LegacyCompose 'source'
        Export-DatabaseSnapshot $script:RebuildCompose 'target-before'
    }
    'Reconcile' { Compare-Baselines }
    'Cutover' {
        if (-not $ProductionConfirmed) { throw 'Use -ProductionConfirmed only after verifying a fresh production source.' }
        if (-not $FreezeConfirmed) { throw 'Use -FreezeConfirmed only after writes are stopped and the freeze timestamp is recorded.' }
        Export-DatabaseSnapshot $script:LegacyCompose 'source-final'
        Export-DatabaseSnapshot $script:RebuildCompose 'target-before-cutover'
        [void](Write-Baseline)
        Invoke-MigrationAdapter 'Full'
        Invoke-MigrationAdapter 'Delta'
        Compare-Baselines
        Write-Step 'Data checks passed. Business smoke tests and Go/No-Go approval are still required before DNS change.'
    }
}
