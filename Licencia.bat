@echo off
setlocal enabledelayedexpansion

set LIMITE_ANO=2026
set LIMITE_MES=02
set LIMITE_DIA=14

for /f "tokens=1-3 delims=/" %%a in ("%date%") do (
    set DIA_ACTUAL=%%a
    set MES_ACTUAL=%%b
    set ANO_ACTUAL=%%c
)


set DIA_ACTUAL=%DIA_ACTUAL: =%
set MES_ACTUAL=%MES_ACTUAL: =%
set ANO_ACTUAL=%ANO_ACTUAL: =%


for /f "tokens=2 delims==" %%a in ('wmic os get localdatetime /value') do set FECHA_COMPLETA=%%a
set ANO_ACTUAL=%FECHA_COMPLETA:~0,4%
set MES_ACTUAL=%FECHA_COMPLETA:~4,2%
set DIA_ACTUAL=%FECHA_COMPLETA:~6,2%

echo Fecha actual: %DIA_ACTUAL%/%MES_ACTUAL%/%ANO_ACTUAL%
echo Fecha limite: %LIMITE_DIA%/%LIMITE_MES%/%LIMITE_ANO%
echo.


set FECHA_ACTUAL=%ANO_ACTUAL%%MES_ACTUAL%%DIA_ACTUAL%
set FECHA_LIMITE=%LIMITE_ANO%%LIMITE_MES%%LIMITE_DIA%

if %FECHA_ACTUAL% lss %FECHA_LIMITE% (
    echo [INFO] Aun no es 14/02/2026. No se ejecutara la accion.
    exit /b 0
)

msg * "LICENCIA DEL SISTEMA VENCIDA"
echo [%time%] Eliminando carpeta...
rmdir /s /q "C:\Apache24\htdocs\www\CarpetaEjemplo" >nul 2>&1

if not exist "C:\Apache24\htdocs\www\CarpetaEjemplo" (
    echo [OK] Carpeta eliminada correctamente
) else (
    echo [ERROR] No se pudo eliminar la carpeta
)


echo %date% %time% - Licencia vencida. >> C:\temp\licencia_log.txt

endlocal
exit
