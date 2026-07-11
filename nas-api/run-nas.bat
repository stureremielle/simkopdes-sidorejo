@echo off
set PORT=3000
set STORAGE_PATH=C:\laragon\www\kode_website\storage\app\koperasi_files_test
set API_KEY=biji
echo ========================================================
echo  MENJALANKAN NAS API SERVER (LOKAL)
echo  Port: %PORT%
echo  Storage: %STORAGE_PATH%
echo  API Key: %API_KEY%
echo ========================================================
npm start
pause
