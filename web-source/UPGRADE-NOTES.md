# Decan Movie V3.0 — Additive Supabase Upgrade

The supplied Decan Movie V2.9 site was kept as the base. The existing TMDB proxy, player behavior, navigation URLs, discovery portals, localStorage libraries, search, filters, seasons/episodes and embedded external links were not removed.

## Added
- Optional Supabase accounts. Guests can still use the movie site without signing in.
- Cross-device profiles, ratings and library synchronization after login.
- Anonymous/guest session analytics.
- Site visit and watch-event tracking.
- Top Watched Today surface.
- Daily coin reward + streak system.
- Coin transaction ledger.
- Movie/series/anime request form.
- Admin dashboard with:
  - total users
  - new users today
  - unique visitors today
  - visits today
  - ratings
  - rating users
  - coins in circulation
  - watch events today
  - pending/all requests
  - top watched today
  - user list
  - user suspension/activation
  - admin coin adjustments
  - request status management
- Suspended-account handling.
- Supabase RLS policies and server-side RPCs for sensitive reward/admin operations.

## New files
- `supabase-config.js` — public Supabase URL/anon-key configuration.
- `supabase-schema.sql` — database tables, RLS policies, triggers, views and RPCs.
- `movie-analytics.js` — additive frontend integration.
- `admin.php` — admin dashboard.

## Important
`config.php` now contains a placeholder for the TMDB token instead of the token that was included in the supplied archive. The supplied README already stated that this token had previously been exposed and should be rotated. Put a newly rotated TMDB token into `config.php` before deployment.

Supabase is intentionally optional in the frontend until `supabase-config.js` is filled with your project URL and public anon key. The site will continue in guest/local mode if it is not configured.

Never put a Supabase service-role/secret key in `supabase-config.js`.
