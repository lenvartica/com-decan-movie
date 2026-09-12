// Dynamic SEO sitemap for Decan Movie.
// Uses clean, indexable URLs from the TMDB catalog.

const SITE = 'https://decan-konnect-movie.vercel.app';
const TMDB = 'https://api.themoviedb.org/3';
const MAX_ITEMS = 1200;

function esc(v) {
  return String(v).replace(/[&<>"']/g, c => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&apos;'
  }[c]));
}

function slugify(value = '') {
  return String(value)
    .toLowerCase()
    .normalize('NFKD')
    .replace(/[^\w\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
}

async function get(path, token) {
  const r = await fetch(`${TMDB}${path}`, {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: 'application/json'
    }
  });

  if (!r.ok) {
    throw new Error(`TMDB ${r.status}`);
  }

  return r.json();
}

export default async function handler(req, res) {
  try {
    const token = process.env.TMDB_ACCESS_TOKEN;

    if (!token) {
      throw new Error(
        'TMDB_ACCESS_TOKEN is not configured.'
      );
    }

    const paths = [
      '/trending/movie/week',
      '/trending/tv/week',

      '/movie/popular?page=1',
      '/movie/popular?page=2',
      '/movie/popular?page=3',
      '/movie/popular?page=4',
      '/movie/popular?page=5',

      '/tv/popular?page=1',
      '/tv/popular?page=2',

      '/movie/now_playing?page=1',
      '/movie/now_playing?page=2',

      '/tv/on_the_air?page=1',

      '/movie/top_rated?page=1',
      '/movie/top_rated?page=2',

      '/tv/top_rated?page=1'
    ];

    const results = await Promise.all(
      paths.map(p => get(p, token))
    );

    const urls = new Map();

    for (const data of results) {
      for (const item of data.results || []) {
        if (!item?.id) continue;

        const isTV =
          item.media_type === 'tv' ||
          Boolean(item.first_air_date);

        const title =
          item.title ||
          item.name ||
          'title';

        const slug = slugify(title);

        const url =
          `${SITE}/${isTV ? 'tv' : 'movie'}/${slug}-${item.id}`;

        urls.set(url, item);
      }
    }

    let xml =
      `<?xml version="1.0" encoding="UTF-8"?>\n`;

    xml +=
      `<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n`;

    // Homepage
    xml += `  <url>\n`;
    xml += `    <loc>${esc(SITE)}/</loc>\n`;
    xml += `    <changefreq>daily</changefreq>\n`;
    xml += `    <priority>1.0</priority>\n`;
    xml += `  </url>\n`;

    // Movie and TV pages
    for (
      const [url, item] of
      Array.from(urls.entries()).slice(0, MAX_ITEMS)
    ) {
      const date =
        item.release_date ||
        item.first_air_date;

      xml += `  <url>\n`;

      xml += `    <loc>${esc(url)}</loc>\n`;

      if (
        date &&
        /^\d{4}-\d{2}-\d{2}$/.test(date)
      ) {
        xml += `    <lastmod>${date}</lastmod>\n`;
      }

      xml += `    <changefreq>weekly</changefreq>\n`;

      const popularity =
        Number(item.popularity || 0);

      const priority =
        popularity > 100
          ? '0.9'
          : popularity > 30
            ? '0.8'
            : '0.7';

      xml += `    <priority>${priority}</priority>\n`;

      xml += `  </url>\n`;
    }

    xml += `</urlset>`;

    res.setHeader(
      'Content-Type',
      'application/xml; charset=utf-8'
    );

    res.setHeader(
      'Cache-Control',
      's-maxage=21600, stale-while-revalidate=43200'
    );

    return res.status(200).send(xml);

  } catch (e) {
    console.error('Sitemap error:', e);

    res.setHeader(
      'Content-Type',
      'application/xml; charset=utf-8'
    );

    return res.status(500).send(
      `<?xml version="1.0" encoding="UTF-8"?>
<error>${esc(e.message)}</error>`
    );
  }
}
