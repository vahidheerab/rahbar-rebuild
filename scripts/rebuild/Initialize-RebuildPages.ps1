[CmdletBinding(SupportsShouldProcess = $true)]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$PSNativeCommandUseErrorActionPreference = $true

$repositoryRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$composeFile = Join-Path $repositoryRoot 'rebuild\compose.yaml'
$initializer = Join-Path $PSScriptRoot 'initialize-pages.php'

& docker compose -f $composeFile config --quiet
if ($LASTEXITCODE -ne 0) { throw 'Rebuild Compose configuration is invalid.' }

if (-not $PSCmdlet.ShouldProcess('Rebuild WordPress database', 'Create/update base pages and configure permalink/locale/timezone')) {
	return
}

$containerId = (& docker compose -f $composeFile ps -q wordpress).Trim()
if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($containerId)) {
	throw 'Rebuild WordPress container is not running.'
}

$containerScript = '/tmp/rahbar-initialize-pages.php'
try {
	& docker cp $initializer "${containerId}:${containerScript}"
	if ($LASTEXITCODE -ne 0) { throw 'Could not copy initializer into the WordPress container.' }

	& docker exec $containerId php $containerScript
	if ($LASTEXITCODE -ne 0) { throw 'Rebuild page initialization failed.' }
}
finally {
	& docker exec $containerId rm -f $containerScript 2>$null
}
