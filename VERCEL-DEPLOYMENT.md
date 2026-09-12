# Decan Movie - Vercel Architecture

This version uses a Vercel Serverless Function for TMDB instead of the PHP proxy.

## Vercel Environment Variable

Add this in Vercel:
TMDB_ACCESS_TOKEN = your NEW TMDB API Read Access Token

Do NOT put the token in index.html or browser JavaScript.

## TMDB endpoint

The frontend uses:
`/api/tmdb`

Examples:
`/api/tmdb/trending/movie/day`
`/api/tmdb/movie/550`
`/api/tmdb/search/multi?query=avatar`

## Important

The old `tmdb-proxy.php` and `config.php` may remain in the source archive for compatibility, but they are not used by the Vercel frontend after this conversion.

Rotate any TMDB token previously exposed in the project or chat before production deployment.
