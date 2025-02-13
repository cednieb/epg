$zpath = $MyInvocation.MyCommand.Path
$zpath =Split-Path $zpath -Parent

cd "$zpath"

[System.Console]::Title = "Epg_Script"

Write-Host "------- Remove Files"
Remove-Item -path 'xmltv_fr.zip' -Force | Wait-Process
Remove-Item -path 'xmltv_fr.xml' -Force | Wait-Process

Write-Host "------- Debut du download"
Invoke-WebRequest https://xmltvfr.fr/xmltv/xmltv_fr.zip -OutFile xmltv_fr.zip | Wait-Process
Write-Host "------- Fin du download"

Write-Host "------- Debut unzip"
Expand-Archive -Path xmltv_fr.zip -DestinationPath . | Wait-Process
Write-Host "------- Fin unzip"

# si besoin de lancer le php dans un navigateur
# https://www.php.net/manual/fr/features.commandline.webserver.php
#Write-Host "------- Lancer server PHP"
#invoke-expression 'cmd /c start powershell -NoExit -File $zpath/epg_launch_php_server.ps1'
#Start-Sleep 10
#start http://localhost:8000/epg3.php

Write-Host "------- Lancer le script PHP"
Invoke-Expression "C:\PROGRA~1\php-8.4.1-nts-Win32-vs17-x64\php.exe -f $zpath\epg3.php" | Out-Null

#Start-Sleep 20
Write-Host "------- Afficher Epg"
start $zpath\epg3.html

#Start-Sleep 10
#Write-Host "------- Tuer serveur php"
#$phpProcess = Get-Process php |Select -First 1
#$phpProcess.Kill()

#Start-Sleep 5
#Write-Host "------- Tuer la fenetre hote de serveur php"
#get-process powershell | select ProcessName, Id, CPU, Path, StartTime, MainWindowTitle  | where-object {$_.MainWindowTitle -eq "PHP_Server"}  | Stop-Process -Force

#Start-Sleep 5
Write-Host "------- Tuer le script principal"
get-process powershell | select ProcessName, Id, CPU, Path, StartTime, MainWindowTitle  | where-object {$_.MainWindowTitle -Match "Epg_Script"}  | Stop-Process -Force

exit