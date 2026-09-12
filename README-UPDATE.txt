# Decan Movie Pro SEO Upgrade

Upload/replace:

api/seo.js
api/sitemap.js
vercel.json
robots.txt

This adds:
- /movie/{TMDB_ID} clean URLs
- /tv/{TMDB_ID} clean URLs
- server-rendered title/description/image metadata
- Open Graph + Twitter cards
- Movie/TV JSON-LD structured data
- canonical URLs
- dynamic sitemap using clean URLs
- richer TMDB catalog coverage
- private account/admin/API paths excluded from robots crawling

Existing app URLs such as:
  /?title=movie&id=123
  /?title=tv&id=456

remain supported. The clean SEO pages link into the existing app so the existing player and embedded watch flow are not replaced.

Important:
- Keep TMDB_ACCESS_TOKEN in Vercel Environment Variables.
- If your current vercel.json contains additional rewrites/functions that are required by the existing project, merge them instead of deleting them.
