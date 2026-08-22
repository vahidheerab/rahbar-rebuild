[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [int[]] $ArticleIds = @(41372, 40743, 40535, 40275, 40030, 39937),
    [int[]] $ProductIds = @(39885, 38963, 38524, 37704, 37669, 37475)
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$PSNativeCommandUseErrorActionPreference = $true
$repositoryRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$legacyCompose = Join-Path $repositoryRoot 'legacy\compose.yaml'
$rebuildCompose = Join-Path $repositoryRoot 'rebuild\compose.yaml'
$exporter = Join-Path $PSScriptRoot 'export-public-samples.php'
$importer = Join-Path $PSScriptRoot 'import-public-samples.php'
$sampleIds = @($ArticleIds + $ProductIds | Select-Object -Unique)

if (-not $sampleIds.Count) { throw 'At least one source ID is required.' }
foreach ($compose in @($legacyCompose, $rebuildCompose)) { & docker compose -f $compose config --quiet }
if (-not $PSCmdlet.ShouldProcess('Rebuild WordPress database', "Import $($ArticleIds.Count) public articles and $($ProductIds.Count) public products from Legacy")) { return }

$legacyContainer = (& docker compose -f $legacyCompose ps -q wordpress).Trim()
$rebuildContainer = (& docker compose -f $rebuildCompose ps -q wordpress).Trim()
if ([string]::IsNullOrWhiteSpace($legacyContainer) -or [string]::IsNullOrWhiteSpace($rebuildContainer)) { throw 'Both WordPress containers must be running.' }

$runDirectory = Join-Path ([System.IO.Path]::GetTempPath()) "rahbar-public-samples-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
New-Item -ItemType Directory -Path $runDirectory | Out-Null
$payloadPath = Join-Path $runDirectory 'public-samples.json'
$containerExporter = '/tmp/rahbar-export-public-samples.php'
$containerImporter = '/tmp/rahbar-import-public-samples.php'
$containerPayload = '/tmp/rahbar-public-samples.json'

try {
    & docker cp $exporter "${legacyContainer}:${containerExporter}"
    $payload = (& docker exec $legacyContainer php $containerExporter ($sampleIds -join ',')) -join "`n"
    if ($LASTEXITCODE -ne 0) { throw 'Legacy public sample export failed.' }
    [System.IO.File]::WriteAllText($payloadPath, $payload, (New-Object System.Text.UTF8Encoding($false)))
    & docker cp $importer "${rebuildContainer}:${containerImporter}"
    & docker cp $payloadPath "${rebuildContainer}:${containerPayload}"
    & docker exec $rebuildContainer php $containerImporter $containerPayload
    if ($LASTEXITCODE -ne 0) { throw 'Rebuild public sample import failed.' }
    $checksum = (Get-FileHash -Algorithm SHA256 -LiteralPath $payloadPath).Hash
    Write-Output "EvidenceDirectory=$runDirectory"
    Write-Output "PayloadSHA256=$checksum"
}
finally {
    if (-not [string]::IsNullOrWhiteSpace($legacyContainer)) { & docker exec $legacyContainer rm -f $containerExporter 2>$null }
    if (-not [string]::IsNullOrWhiteSpace($rebuildContainer)) { & docker exec $rebuildContainer rm -f $containerImporter $containerPayload 2>$null }
}
