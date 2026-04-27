@echo off
cd /d C:\xampp\htdocs\teste_front\frontend
start "Angular Dev Server" cmd /k "npm.cmd run start"
timeout /t 8 >nul
start "" http://localhost:4200
