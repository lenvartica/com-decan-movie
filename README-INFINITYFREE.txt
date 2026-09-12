DECAN MOVIE - TMDB SECURITY PATCH
=================================

Purpose
-------
This package makes the smallest practical security change to the supplied
Decan Movie website: the TMDB bearer token is removed from browser JavaScript
and is used only by a PHP proxy on the server.

No UI redesign, framework upgrade, or feature removal is included.

Files
-----
index.php           Existing website with only the TMDB client endpoint changed.
tmdb-proxy.php      Server-side TMDB request proxy.
config.php          Server-side TMDB credential/configuration.
.htaccess           Blocks browser access to config.php and .env files.
.env.example        Reference only; not used by InfinityFree free hosting.

InfinityFree upload
-------------------
1. Open the htdocs directory in the InfinityFree File Manager.
2. Upload ALL files from this folder into htdocs.
3. Keep the filenames exactly as provided.
4. Visit the website and test Trending, Search, Details, and TV Seasons.

Important credential step
-------------------------
The token copied from the old source was already publicly exposed. Rotate it
in TMDB and replace the value of TMDB_ACCESS_TOKEN in config.php with the NEW
one before or immediately after deployment.

Do NOT put the token back into index.php or any JavaScript file.

Security check
--------------
Try opening:
  https://YOUR-DOMAIN/config.php

It should be denied by .htaccess. Do not continue if the file is downloadable.

Proxy architecture
-------------------
Browser -> tmdb-proxy.php -> config.php -> TMDB API

The browser no longer receives the TMDB bearer token.

Notes
-----
- This package intentionally does not use Composer or a framework.
- .env.example is documentation only. InfinityFree free hosting does not
  provide normal server-side environment variables, so config.php is used.
- If your InfinityFree account reports an Apache .htaccess compatibility
  error, remove only the <FilesMatch> block and use the hosting control panel
  or an equivalent access-denial rule for config.php. Do not expose config.php.
