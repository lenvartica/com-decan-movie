DECAN MOVIE - ACCOUNT PAGE FIX

Problem fixed:
The main index page signs in successfully, but account.html reports
"You are not signed in." This happened because account.html was not loading
account.js, so window.DecanAccount was never initialized on that page.

Update ONLY:
account.html

Upload this file to the root of your GitHub/Vercel project and replace the
existing account.html.

Do NOT replace index.html, account.js, Supabase SQL, TMDB files, or other files
for this particular fix.

The important change is that account.html now loads account.js immediately
after supabase-config.js, before its dashboard boot code runs.

After deployment:
1. Sign in from index.html.
2. Wait for "Signed in successfully. Opening your account...".
3. account.html should now detect the same persisted Supabase session.
4. The account dashboard should appear.

If the dashboard still says not signed in after this exact fix, the next thing
to inspect is the browser's Supabase storage/session behavior, not the login
credentials or database tables.
