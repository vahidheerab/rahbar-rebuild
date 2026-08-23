[CmdletBinding()]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$PSNativeCommandUseErrorActionPreference = $true

$repositoryRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$composeFile = Join-Path $repositoryRoot 'rebuild\compose.yaml'
$integrationTest = Join-Path $PSScriptRoot 'payment-bridge-integration.php'

& docker compose -f $composeFile config --quiet
if ($LASTEXITCODE -ne 0) { throw 'Rebuild Compose configuration is invalid.' }

$containerId = (& docker compose -f $composeFile ps -q wordpress).Trim()
if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($containerId)) {
    throw 'Rebuild WordPress container is not running.'
}

$containerScript = '/tmp/rahbar-payment-bridge-integration.php'
try {
    & docker cp $integrationTest "${containerId}:${containerScript}"
    if ($LASTEXITCODE -ne 0) { throw 'Could not copy Payment Bridge test into the WordPress container.' }
    & docker exec $containerId php $containerScript
    if ($LASTEXITCODE -ne 0) { throw 'Payment Bridge integration tests failed.' }
}
finally {
    & docker exec $containerId rm -f $containerScript 2>$null
}
