@echo off
cd /d C:\xampp\htdocs\teste_front
start "" "C:\xampp\htdocs\teste_front\start-backend-xampp.bat"
timeout /t 4 >nul
start "" "C:\xampp\htdocs\teste_front\start-frontend.bat"
