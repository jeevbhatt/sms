@echo off
cd /d E:\xampp
call xampp_shell.bat
:menu
echo.
echo Pick an app to run:
echo 1. Composer
echo 2. PHP Interactive Shell
echo 3. Exit
set /p choice="Enter your choice (1-3): "
if "%choice%"=="1" (
    composer
    goto :menu
) else if "%choice%"=="2" (
    php -a
    REM After exiting PHP interactive shell, return to menu
    goto :menu
) else (
    exit
)
