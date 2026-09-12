@echo off
set APP_HOME=%~dp0
if exist "%GRADLE_HOME%\bin\gradle.bat" (
  call "%GRADLE_HOME%\bin\gradle.bat" -p "%APP_HOME%" %*
  exit /b %ERRORLEVEL%
)
echo Gradle is not installed. Open this project in Android Studio or install Gradle 8.9+.
exit /b 1
