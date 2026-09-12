# Decan Movie GitHub Actions

## 1. Push the project to GitHub

Create a repository and upload the contents of this directory. The `.github/workflows` directory must be at the repository root.

Recommended repository structure:

```text
decan_movie_android/
  app/
  gradle/
  .github/
    workflows/
      android.yml
      release.yml
      release-unsigned.yml
  build.gradle.kts
  settings.gradle.kts
  gradlew
  gradlew.bat
```

## 2. Automatic debug APK

Every push to `main` or `master` runs `android.yml`.

Open GitHub:

`Actions` → `Decan Movie Android Build` → choose a completed run → `Artifacts` → `decan-movie-debug-apk`.

The downloaded artifact contains the debug APK.

## 3. Unsigned release build

Run:

`Actions` → `Decan Movie Unsigned Release` → `Run workflow`.

This is useful for checking that the release variant compiles. It is not the APK you should distribute to customers.

## 4. Signed customer release

For customer distribution, use `release.yml`.

Create a release keystore once and keep it private. Never commit the `.jks` file or its passwords to GitHub.

Create these GitHub Actions repository secrets:

- `DECAN_KEYSTORE_BASE64`
- `DECAN_KEYSTORE_PASSWORD`
- `DECAN_KEY_ALIAS`
- `DECAN_KEY_PASSWORD`

Convert the keystore to base64 before adding it as a secret. On Linux/macOS:

```bash
base64 -w 0 decan-release.jks
```

On PowerShell:

```powershell
[Convert]::ToBase64String([IO.File]::ReadAllBytes('.\\decan-release.jks'))
```

Then create a Git tag such as `v1.0.0` and push it. The release workflow builds both a signed APK and AAB and uploads SHA-256 checksums.

## 5. Important signing rule

The signing keystore is part of your application's identity. Back it up securely. If you lose the signing credentials, future update distribution can become difficult or impossible depending on the distribution channel.

## 6. Version updates

Before releasing a new customer version, update `versionCode` and `versionName` in `app/build.gradle.kts`.

Example:

```kotlin
versionCode = 2
versionName = "1.1.0"
```

Then tag it:

```bash
git add .
git commit -m "Release Decan Movie 1.1.0"
git push origin main
git tag v1.1.0
git push origin v1.1.0
```

## 7. Customer delivery

Use the signed APK produced by `release.yml` for direct Android distribution. Do not send customers the debug or unsigned APK.

## 8. Website deployment

The Android workflow builds the Android project only. Your existing Decan Movie website remains independently deployed. If the website changes, the application loads the updated website when customers open it.

## 9. Secrets

Never commit:

- TMDB private/server tokens
- Supabase service-role keys
- Android keystores
- keystore passwords
- API credentials

Public Supabase client configuration may remain public only when the Supabase project is correctly protected with Row Level Security and appropriate policies.
