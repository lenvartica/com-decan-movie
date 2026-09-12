# Decan Movie Android

A native Android shell for the existing Decan Movie web platform at https://decan-konnect-movie.vercel.app/.

## What this project does

- Opens the existing Decan Movie platform in a hardened Android WebView.
- Keeps the website's existing account, search, movie, series, anime and backend flows on the live site.
- Adds native Android lifecycle handling, back navigation, loading state, offline/retry state, downloads, file chooser support, external-link handling and renderer-crash recovery.
- Uses the package name `com.decan.movie`.
- Supports Android 6.0/API 23 and newer.
- Targets Android 15/API 35.

## Important source inspection result

The uploaded website project contains server-side TMDB credentials in `.env` and `config.php`. Those credentials are not required by the Android APK and must never be embedded in the APK. The copy of the web source included in this Android project has those credentials redacted. Rotate the exposed TMDB token on the server before production use.

The Android application itself does not contain the TMDB token, Supabase service-role key, or any server secret.

## Build with Android Studio

1. Install a current Android Studio release.
2. Open this folder as an existing Gradle project.
3. Allow Android Studio to download the Gradle and Android dependencies.
4. Set the Gradle JDK to JDK 17 or a compatible supported JDK.
5. Install Android SDK Platform 35 and the Android SDK Build-Tools selected by Android Studio.
6. Run `:app:assembleDebug` for a test APK.
7. The debug APK is produced under `app/build/outputs/apk/debug/`.

## Build from a terminal

If Gradle is installed locally:

```text
gradle :app:assembleDebug
gradle :app:assembleRelease
gradle :app:bundleRelease
```

The project intentionally does not contain a private signing key. Create your own release keystore and keep it outside Git and customer source packages.

## Release signing

Set these environment variables before a release build:

```text
DECAN_KEYSTORE_FILE=/absolute/path/to/decan-release.jks
DECAN_KEYSTORE_PASSWORD=your-keystore-password
DECAN_KEY_ALIAS=decan-release
DECAN_KEY_PASSWORD=your-key-password
```

Then run:

```text
gradle :app:assembleRelease
gradle :app:bundleRelease
```

The APK is suitable for direct customer distribution after signing. The AAB is useful if you later publish through an app store.

## First-device test plan

1. Install the debug APK.
2. Launch while online.
3. Confirm the Decan Movie home page opens.
4. Open a movie and a TV/series page.
5. Test search.
6. Test Account and sign-in/sign-up flows if enabled on the website.
7. Test external links such as WhatsApp and social links.
8. Press Android Back on a detail page and confirm it returns to the previous web page before exiting the app.
9. Background the app, reopen it and confirm the WebView state is preserved.
10. Turn connectivity off, attempt navigation, confirm the retry screen appears.
11. Restore connectivity and tap Try again.
12. Test media playback supported by the website.
13. Test any legitimate download action.
14. Rotate the device and confirm the page does not restart unnecessarily.
15. Test Android 6, 8, 10, 12, 13, 14 and 15 when physical/emulated devices are available.
16. Test at least one low-memory Android device.
17. Test Wi-Fi and mobile data separately.

## Direct customer distribution

For direct sales, distribute the signed release APK from your own trusted download channel. Give customers installation instructions for Android's permission to install apps from the browser/file manager they used. Do not distribute an unsigned release build.

Never send your release keystore, keystore password, TMDB token, Supabase service-role key or other backend secrets to customers.

## Website dependency

The Android app intentionally uses the live Decan Movie website rather than copying the whole web platform into the APK. This keeps server-side TMDB proxying, Supabase authentication and site updates on the server. Customers therefore need internet access for the online movie platform unless the website itself implements offline content.

## Legal note

Only distribute movie or streaming content for which you have the required rights or authorization. The application shell does not grant distribution rights to third-party movies, streams or copyrighted material.
