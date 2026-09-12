DECAN MOVIE COMPLETE UPGRADE OVERLAY

Upload/merge these files into the existing project. Do NOT delete the rest of your project.

Files:
- index.html: existing Movie Box with only additive menu/link/trending/footer improvements. Existing streaming/player code is preserved.
- characters.html: Characters universe page.
- movie-hub.html: advanced search, rich details, people, recommendations, collections/provider-ready discovery, upcoming releases and TV season overview. The existing Movie Box remains the player.
- community.html: review entry page using the existing Supabase client.
- supabase-social-gamification.sql: OPTIONAL migration for reviews, likes, follows and achievements.
- api/tmdb.js: hardened version of the existing TMDB proxy.

Important:
1. Do not upload .env to GitHub/Vercel.
2. Keep TMDB_ACCESS_TOKEN only in Vercel Environment Variables.
3. The existing embedded player/streaming URL in index.html was intentionally NOT replaced.
4. TV episodes still open through the existing Movie Box player so the current episode/player implementation remains intact.
5. The optional SQL must be run in Supabase only after the existing schema is installed.
6. Provider availability is informational and comes from TMDB's watch-provider data; it does not replace the existing player.
