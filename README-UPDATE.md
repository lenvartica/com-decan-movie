# Decan Movie V2.8 Additive Update

This is an additive update to the existing Decan Movie V2.7 package. No account/login system has been added and the existing TMDB proxy, player, watchlist, custom collections, history, search, filters, seasons and episodes are retained.

## New in V2.8

- Dedicated Movie Portal with the same discovery style as the Anime Portal.
- Movie filtering by Action, Drama, Romance, Horror, Fantasy, Comedy, Sci-Fi, Thriller, Mystery, Adventure, Crime, History, Animation, War and Western.
- Movie sorting by Trending/Popular, Highest Rated, Most Rated, Newest and Oldest.
- Movie year and minimum-rating filters.
- Movie presets for Trending Romance, Action, Horror, Sci-Fi, Fantasy and genre combinations.
- Combined Movie discovery such as Action + Horror, Action + Fantasy, Action + Romance and Drama + Romance.
- Combined Anime discovery such as Action + Supernatural, Action + Romance and Drama + Romance.
- Anime Portal controls remain available on phones and tablets.
- Movie Portal controls are responsive on phones, tablets and desktop.
- Touch-friendly 44px controls and horizontally swipeable genre preset chips on small screens.
- Movie Portal added to the navigation without removing Top Movies.
- Mobile layouts collapse filter controls into one-column/compact layouts on very small screens.
- Updated service-worker cache name so the V2.8 UI can replace the previous cached shell.

## No account system

Everything remains device-local. No registration, login, email, password or user database is required for these features.

## Hosting

Keep your existing `tmdb-proxy.php` beside `index.php`. Do not replace or expose your TMDB credentials in the browser. Place `manifest.json` and `service-worker.js` beside `index.php`.


V2.9 ADDITION: Added a responsive futuristic atom logo to the Decan Movie navigation/header and a standalone SVG favicon (decan-atom-logo.svg). Existing functionality remains intact.


V3.0 ADDITIVE SUPABASE UPGRADE: Optional accounts, cross-device profile/rating/library sync, analytics, daily coins/streaks, top-watched tracking, movie requests and a protected admin dashboard were added without removing existing discovery/player/link functionality. See UPGRADE-NOTES.md and supabase-schema.sql.
