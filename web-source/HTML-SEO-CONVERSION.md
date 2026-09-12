# Decan Movie — HTML + SEO Conversion

The public movie frontend has been converted from `index.php` to `index.html` without intentionally removing the existing movie UI, navigation, player, TMDB routes, localStorage libraries, or embedded links.

## Files

- `index.html` — public movie frontend
- `admin.html` — Supabase admin dashboard frontend
- `tmdb-proxy.php` — retained server-side because it protects the TMDB bearer token
- `config.php` — retained server-side for the TMDB token
- `supabase-config.js` — public Supabase project URL/anon key configuration
- `robots.txt` — crawler rules
- `sitemap.xml` — basic sitemap
- `.htaccess` — serves `index.html` by default and redirects old `index.php` requests

## SEO added

- Title and meta description
- Canonical URL
- Open Graph metadata
- Twitter card metadata
- Robots directives
- Author/theme metadata
- WebSite JSON-LD
- WebApplication JSON-LD
- Dynamic Movie/TVSeries JSON-LD when a title is opened
- Dynamic movie/TV page title and description
- `robots.txt`
- `sitemap.xml`
- Existing Google verification file preserved

## Important

This is a static HTML frontend, but the TMDB proxy remains PHP because converting that proxy to browser-only JavaScript would expose the private TMDB bearer token. The public site itself is HTML/CSS/JavaScript.

Replace `/` in `sitemap.xml` with your full production domain before submitting the sitemap to Google Search Console.
