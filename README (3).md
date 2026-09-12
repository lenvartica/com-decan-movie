# Decan Movie Pro Account Update

Upload these files to the root of the existing Vercel/GitHub project:

- account.js
- account.html
- supabase-config.js
- supabase-account-migration.sql (run this once in Supabase; do not upload it as a public web page)

Then:
1. Put your Supabase URL and public anon/publishable key in supabase-config.js.
2. Run supabase-account-migration.sql in Supabase SQL Editor.
3. Ensure Supabase Auth Email provider is enabled.
4. In Supabase Auth URL Configuration, set the Site URL to:
   https://decan-konnect-movie.vercel.app
5. Add the redirect URL:
   https://decan-konnect-movie.vercel.app/account.html
6. Redeploy Vercel.

The existing movie site remains guest-first. account.js injects an Account / Sign In entry into the existing sidebar. It also syncs the existing localStorage watchlist, favorites, history, ratings, custom list and continue-watching data to Supabase after login.

No TMDB files or embedded watch/player URLs are changed by this patch.
