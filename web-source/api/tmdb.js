// Decan Movie - Vercel TMDB serverless proxy
// Uses TMDB_ACCESS_TOKEN from Vercel Environment Variables.
// Never put the TMDB token in browser JavaScript.

export default async function handler(req, res) {
  if (req.method === 'OPTIONS') {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
    return res.status(204).end();
  }

  if (req.method !== 'GET') {
    return res.status(405).json({
      error: true,
      message: 'Method not allowed'
    });
  }

  const token = process.env.TMDB_ACCESS_TOKEN;

  if (!token) {
    return res.status(500).json({
      error: true,
      message: 'TMDB_ACCESS_TOKEN is not configured in Vercel Environment Variables.'
    });
  }

  // Vercel may expose the dynamic path through req.query.path
  // and the frontend also sends normal query parameters.
  let requestedPath = req.query?.path;

  if (Array.isArray(requestedPath)) {
    requestedPath = '/' + requestedPath.join('/');
  } else if (typeof requestedPath === 'string') {
    requestedPath = requestedPath.startsWith('/')
      ? requestedPath
      : '/' + requestedPath;
  } else {
    requestedPath = '';
  }

  // Support /api/tmdb/<path> and /api/tmdb?path=/...
  if (!requestedPath && req.url) {
    const pathname = new URL(req.url, 'http://localhost').pathname;
    const marker = '/api/tmdb/';
    if (pathname.startsWith(marker)) {
      requestedPath = pathname.slice(marker.length - 1);
    }
  }

  if (!requestedPath || requestedPath.length > 180 || requestedPath.includes('://') || requestedPath.includes('..')) {
    return res.status(400).json({
      error: true,
      message: 'Missing TMDB endpoint path.'
    });
  }

  // Only permit TMDB API resources used by the movie application.
  const allowed = [
    '/movie',
    '/tv',
    '/search',
    '/discover',
    '/trending',
    '/person',
    '/genre',
    '/configuration',
    '/collection',
    '/keyword',
    '/company',
    '/network',
    '/credit'
  ];

  const isAllowed = allowed.some(prefix =>
    requestedPath === prefix || requestedPath.startsWith(prefix + '/')
  );

  if (!isAllowed) {
    return res.status(403).json({
      error: true,
      message: 'TMDB endpoint is not allowed.',
      path: requestedPath
    });
  }

  const url = new URL(
    `https://api.themoviedb.org/3${requestedPath}`
  );

  // Copy query parameters except the internal "path".
  for (const [key, value] of Object.entries(req.query || {})) {
    if (key === 'path') continue;
    if (Array.isArray(value)) {
      for (const item of value) url.searchParams.append(key, String(item));
    } else if (value !== undefined) {
      url.searchParams.set(key, String(value));
    }
  }

  try {
    const response = await fetch(url.toString(), {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    });

    const text = await response.text();

    let data;
    try {
      data = JSON.parse(text);
    } catch {
      data = { raw: text };
    }

    res.setHeader('Cache-Control', 's-maxage=300, stale-while-revalidate=600');
    res.setHeader('X-Content-Type-Options', 'nosniff');
    res.setHeader('Access-Control-Allow-Origin', '*');

    return res.status(response.status).json(data);
  } catch (error) {
    return res.status(502).json({
      error: true,
      message: 'Unable to connect to TMDB.',
      details: error?.message || 'Unknown error'
    });
  }
}
