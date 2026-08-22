param(
    [string]$RebuildRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..\rebuild')).Path
)

$ErrorActionPreference = 'Stop'
$version = '11.0.1'
$locale = 'fa_IR'
$expectedSha256 = '5D6FE66C8501F1BD3659E1FB2C17A4DB45487F562EB4D125120375533A1A8912'
$url = "https://downloads.wordpress.org/translation/plugin/woocommerce/$version/$locale.zip"
$archive = Join-Path ([System.IO.Path]::GetTempPath()) "rahbar-woocommerce-$version-$locale.zip"
$destination = Join-Path $RebuildRoot 'site\wp-content\languages\plugins'

try {
    Invoke-WebRequest -Uri $url -OutFile $archive
    $actualSha256 = (Get-FileHash -LiteralPath $archive -Algorithm SHA256).Hash
    if ($actualSha256 -ne $expectedSha256) {
        throw "WooCommerce translation checksum mismatch. Expected $expectedSha256, got $actualSha256."
    }

    New-Item -ItemType Directory -Force -Path $destination | Out-Null
    Expand-Archive -LiteralPath $archive -DestinationPath $destination -Force
    Write-Output "Installed WooCommerce $version $locale translations (SHA256: $actualSha256)."
}
finally {
    if (Test-Path -LiteralPath $archive) {
        Remove-Item -LiteralPath $archive
    }
}
