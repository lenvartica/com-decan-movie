DECAN MOVIE — MOBILE MENU + ACCOUNT AUTH BUG FIX

Files in this patch:
  index.html
  account.html
  account.js
  account-page.js
  supabase-config.js
  supabase-auth-repair.sql
  api/public-config.js

WHAT THIS FIXES
1. Mobile menu now opens/closes reliably with a backdrop, Escape key and resize handling.
2. Sidebar Account and header Account are one authentication destination: account.html.
3. Prevents duplicate Supabase clients/auth listeners.
4. Prevents duplicate account.js initialization.
5. Account page waits for the shared Supabase session before rendering the member dashboard.
6. Sign-in gives useful errors instead of silently doing nothing.
7. New accounts explain email confirmation when Supabase requires it.
8. Password reset uses a real recovery redirect.
9. Existing accounts are repaired into profiles/user_roles by the SQL migration.
10. Fixes the common "public.is_admin() does not exist" / "public.user_roles does not exist" ordering problem.
11. Profile, avatar, cloud library and daily coins use the same authenticated session.
12. Guest movie browsing remains available without an account.

VERCEL ENVIRONMENT VARIABLES
Set these in Vercel for the public-config endpoint:
  SUPABASE_URL=https://YOUR-PROJECT.supabase.co
  SUPABASE_ANON_KEY=YOUR_PUBLIC_ANON_KEY

If your site already has real values in supabase-config.js, you can keep them; the file in this patch is only a safe fallback template.

DO NOT expose:
  SUPABASE_SERVICE_ROLE_KEY
  any service-role/secret key
in frontend files.

SUPABASE
Run supabase-auth-repair.sql once in Supabase SQL Editor. It is intended to repair the account tables/functions and create missing auth roles before RLS policies reference them.

DEPLOY
Upload/replace only the files in this patch. Do not delete your existing TMDB API, player, embedded watch links, sitemap, or other Vercel functions.
After deployment, test:
  /index.html
  /account.html
  /api/public-config

If /api/public-config returns 503, set SUPABASE_URL and SUPABASE_ANON_KEY in Vercel and redeploy.
