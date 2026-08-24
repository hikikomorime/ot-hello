#Requires -Version 5.1
<#
.SYNOPSIS
  Push main to hikikomorime/ot-hello. Uses gh if logged in, otherwise git remote github.
#>
$ErrorActionPreference = 'Stop'
Set-Location (Join-Path $PSScriptRoot '..')

$Repo = 'https://github.com/hikikomorime/ot-hello.git'
$existing = git remote
if ($existing -contains 'github') {
    git remote set-url github $Repo
} else {
    git remote add github $Repo
}

if (Get-Command gh -ErrorAction SilentlyContinue) {
    $status = gh auth status 2>&1 | Out-String
    if ($status -match 'Logged in') {
        git push -u github main
        exit $LASTEXITCODE
    }
}

Write-Host "Pushing to $Repo"
git push -u github main
