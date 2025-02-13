$zpath = $MyInvocation.MyCommand.Path
$zpath =Split-Path $zpath -Parent
cd "$zpath"

[System.Console]::Title = "PHP_Server"

Write-Host "------- Lancer PHP"
$command = "C:\PROGRA~1\php-8.4.1-nts-Win32-vs17-x64\php.exe -S localhost:8000 -t $zpath"
Invoke-Expression $command