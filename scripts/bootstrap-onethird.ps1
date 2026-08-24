#Requires -Version 5.1
<#
.SYNOPSIS
  Place OT Hello in the canonical OneThird tree and wire GitHub + shared FTP.
#>
$ErrorActionPreference = 'Stop'

$OneThird = 'C:\Users\micha\OneDrive\Dokumenty\AI-Projects\OneThird'
$Dest = Join-Path $OneThird 'ot-hello'
$Repo = 'https://github.com/hikikomorime/ot-hello.git'

function Ensure-Remote {
    param(
        [Parameter(Mandatory = $true)][string]$Name,
        [Parameter(Mandatory = $true)][string]$Url
    )
    $existing = git remote
    if ($existing -contains $Name) {
        git remote set-url $Name $Url
    } else {
        git remote add $Name $Url
    }
}

if (-not (Test-Path $OneThird)) {
    New-Item -ItemType Directory -Path $OneThird | Out-Null
}

$here = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$alreadyHere = (Test-Path (Join-Path $here 'ot-hello.php'))

if ($alreadyHere -and ($here -ieq $Dest)) {
    Set-Location $Dest
} elseif (Test-Path (Join-Path $Dest '.git')) {
    Set-Location $Dest
    git pull --ff-only origin main
} elseif (Test-Path $Dest) {
    throw "Folder exists but is not a git checkout: $Dest"
} elseif ($alreadyHere) {
    Write-Host "Repo is currently at $here"
    Write-Host "Copy or clone it to $Dest (canonical OneThird path)."
    $Dest = $here
    Set-Location $Dest
} else {
    git clone $Repo $Dest
    Set-Location $Dest
}

Ensure-Remote -Name 'origin' -Url $Repo
Ensure-Remote -Name 'github' -Url $Repo

$rewriterEnv = @(
    (Join-Path $OneThird 'ai-rewriter\scripts\.env'),
    (Join-Path $OneThird 'ot-rewriter\scripts\.env'),
    (Join-Path $OneThird 'ot-seo\scripts\.env')
) | Where-Object { Test-Path $_ } | Select-Object -First 1

$localEnv = Join-Path $Dest 'scripts\.env'
if ($rewriterEnv -and -not (Test-Path $localEnv)) {
    Copy-Item $rewriterEnv $localEnv
}

if (Test-Path $localEnv) {
    $text = Get-Content -Raw $localEnv
    if ($text -notmatch '(?m)^OT_PLUGINS_FTP_REMOTE_DIR=') {
        Add-Content -Path $localEnv -Value "`nOT_PLUGINS_FTP_REMOTE_DIR=ot-hello"
    } else {
        $updated = [regex]::Replace($text, '(?m)^OT_PLUGINS_FTP_REMOTE_DIR=.*$', 'OT_PLUGINS_FTP_REMOTE_DIR=ot-hello')
        Set-Content -Path $localEnv -Value $updated -NoNewline
    }
    Write-Host "FTP env ready at scripts\.env (REMOTE_DIR=ot-hello)."
} else {
    Write-Host "No sibling rewriter .env found. Copy scripts\.env.example to scripts\.env and fill OT_PLUGINS_FTP_*."
}

if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm install
}
if (Get-Command composer -ErrorAction SilentlyContinue) {
    composer install
}

Write-Host ""
Write-Host "OT Hello path: $Dest"
Write-Host "Next:"
Write-Host "  git push -u github main"
Write-Host "  npm run archive-release"
Write-Host "  npm run deploy-update"
