# Decan Movie Android

Native Android WebView application for Decan Movie.

## Build with GitHub Actions

Push this project to a GitHub repository with the project files at the repository root. The workflow in `.github/workflows/main.yml` builds a debug APK on pushes to `main` or `master`, pull requests, and manual runs.

The workflow installs Java 17, Android SDK 35, Android Build Tools 35.0.0, and Gradle 8.9 before building.

The debug APK is published as the `decan-movie-debug-apk` workflow artifact.

## Project

- Application ID: `com.decan.movie`
- Namespace: `com.decan.movie`
- Minimum SDK: 23
- Target SDK: 35
- Compile SDK: 35
- Android Gradle Plugin: 8.7.3
- Kotlin: 2.0.21
- Gradle: 8.9
- Java: 17
- Website: `https://decan-konnect-movie.vercel.app/`

## Release signing

Do not commit keystores or passwords. Configure release signing through GitHub Actions secrets before creating a production release.
