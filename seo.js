const SITE = "https://decan-konnect-movie.vercel.app";
const TMDB_API = "https://api.themoviedb.org/3";
const TMDB_IMAGE = "https://image.tmdb.org/t/p/w1280";

function escapeHtml(value = "") {
  return String(value).replace(/[&<>"']/g, (char) => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;"
  }[char]));
}

function slugify(value = "") {
  return String(value)
    .toLowerCase()
    .normalize("NFKD")
    .replace(/[^\w\s-]/g, "")
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
}

function cleanDescription(value = "") {
  const text = String(value).replace(/\s+/g, " ").trim();

  if (!text) {
    return "Discover movies and TV shows on Decan Movie.";
  }

  if (text.length <= 155) {
    return text;
  }

  return text.slice(0, 152).replace(/\s+\S*$/, "") + "...";
}

async function getTMDB(path, token) {
  const response = await fetch(`${TMDB_API}${path}`, {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json"
    }
  });

  if (!response.ok) {
    throw new Error(`TMDB request failed: ${response.status}`);
  }

  return response.json();
}

function buildStructuredData(type, movie, canonicalUrl) {
  const title = movie.title || movie.name || "Decan Movie";

  const description =
    movie.overview ||
    `Discover ${title} on Decan Movie.`;

  const image = movie.backdrop_path
    ? `${TMDB_IMAGE}${movie.backdrop_path}`
    : movie.poster_path
      ? `${TMDB_IMAGE}${movie.poster_path}`
      : `${SITE}/decan-atom-logo.svg`;

  const releaseDate =
    movie.release_date ||
    movie.first_air_date ||
    undefined;

  const data = {
    "@context": "https://schema.org",
    "@type": type === "tv" ? "TVSeries" : "Movie",
    name: title,
    description,
    url: canonicalUrl,
    image: image
  };

  if (releaseDate) {
    data.dateCreated = releaseDate;
  }

  if (Array.isArray(movie.genres) && movie.genres.length) {
    data.genre = movie.genres.map((genre) => genre.name);
  }

  if (movie.vote_average > 0) {
    data.aggregateRating = {
      "@type": "AggregateRating",
      ratingValue: Number(movie.vote_average).toFixed(1),
      bestRating: "10",
      worstRating: "0",
      ratingCount: Math.max(
        1,
        Number(movie.vote_count || 1)
      )
    };
  }

  if (
    Array.isArray(movie.credits?.cast) &&
    movie.credits.cast.length
  ) {
    data.actor = movie.credits.cast
      .slice(0, 10)
      .map((actor) => ({
        "@type": "Person",
        name: actor.name
      }));
  }

  if (type === "tv") {
    if (movie.number_of_seasons) {
      data.numberOfSeasons = movie.number_of_seasons;
    }

    if (movie.number_of_episodes) {
      data.numberOfEpisodes = movie.number_of_episodes;
    }
  }

  return data;
}

function renderPage(type, movie, canonicalUrl) {
  const title = movie.title || movie.name || "Decan Movie";

  const releaseDate =
    movie.release_date ||
    movie.first_air_date ||
    "";

  const year = releaseDate
    ? releaseDate.substring(0, 4)
    : "";

  const description = cleanDescription(
    movie.overview ||
    `Discover ${title} on Decan Movie.`
  );

  const image = movie.backdrop_path
    ? `${TMDB_IMAGE}${movie.backdrop_path}`
    : movie.poster_path
      ? `${TMDB_IMAGE}${movie.poster_path}`
      : `${SITE}/decan-atom-logo.svg`;

  const rating = Number(movie.vote_average || 0);

  const genres = Array.isArray(movie.genres)
    ? movie.genres
        .slice(0, 6)
        .map((genre) => genre.name)
    : [];

  const structuredData =
    buildStructuredData(
      type,
      movie,
      canonicalUrl
    );

  const pageTitle =
    `${title}${year ? ` (${year})` : ""} | Decan Movie`;

  return `<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta
  name="viewport"
  content="width=device-width, initial-scale=1.0"
>

<title>${escapeHtml(pageTitle)}</title>

<meta
  name="description"
  content="${escapeHtml(description)}"
>

<meta
  name="robots"
  content="index, follow, max-image-preview:large"
>

<link
  rel="canonical"
  href="${escapeHtml(canonicalUrl)}"
>

<meta
  property="og:type"
  content="website"
>

<meta
  property="og:site_name"
  content="Decan Movie"
>

<meta
  property="og:title"
  content="${escapeHtml(pageTitle)}"
>

<meta
  property="og:description"
  content="${escapeHtml(description)}"
>

<meta
  property="og:url"
  content="${escapeHtml(canonicalUrl)}"
>

<meta
  property="og:image"
  content="${escapeHtml(image)}"
>

<meta
  name="twitter:card"
  content="summary_large_image"
>

<meta
  name="twitter:title"
  content="${escapeHtml(pageTitle)}"
>

<meta
  name="twitter:description"
  content="${escapeHtml(description)}"
>

<meta
  name="twitter:image"
  content="${escapeHtml(image)}"
>

<script type="application/ld+json">
${JSON.stringify(structuredData).replace(/</g, "\\u003c")}
</script>

<style>
* {
  box-sizing: border-box;
}

html,
body {
  margin: 0;
  padding: 0;
  background: #07090d;
  color: #ffffff;
  font-family:
    Inter,
    system-ui,
    -apple-system,
    BlinkMacSystemFont,
    "Segoe UI",
    sans-serif;
}

body {
  min-height: 100vh;
}

.seo-page {
  width: min(1100px, 94%);
  margin: 0 auto;
  padding: 40px 0 70px;
}

.seo-card {
  overflow: hidden;
  background: #10141c;
  border: 1px solid rgba(255,255,255,.10);
  border-radius: 24px;
  box-shadow:
    0 30px 90px rgba(0,0,0,.45);
}

.seo-backdrop {
  width: 100%;
  height: min(55vw, 520px);
  min-height: 250px;
  display: block;
  object-fit: cover;
  background: #151922;
}

.seo-content {
  padding: 30px;
}

.seo-label {
  color: #00e5ff;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 1.5px;
  text-transform: uppercase;
}

.seo-title {
  margin: 8px 0 16px;
  font-size: clamp(30px, 5vw, 56px);
  line-height: 1.05;
}

.seo-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 22px;
}

.seo-meta span {
  padding: 7px 11px;
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 999px;
  color: #dce3ee;
  font-size: 13px;
}

.seo-overview {
  max-width: 850px;
  color: #aeb7c7;
  font-size: 17px;
  line-height: 1.8;
}

.seo-button {
  display: inline-flex;
  margin-top: 14px;
  padding: 13px 18px;
  border-radius: 12px;
  background: #00e5ff;
  color: #031016;
  font-weight: 900;
  text-decoration: none;
}

.seo-button:hover {
  opacity: .9;
}

@media (max-width: 600px) {
  .seo-page {
    width: 94%;
    padding-top: 20px;
  }

  .seo-content {
    padding: 22px;
  }

  .seo-title {
    font-size: 32px;
  }

  .seo-overview {
    font-size: 15px;
  }
}
</style>

</head>

<body>

<main class="seo-page">

<article class="seo-card">

<img
  class="seo-backdrop"
  src="${escapeHtml(image)}"
  alt="${escapeHtml(title)}"
  loading="eager"
>

<div class="seo-content">

<div class="seo-label">
  Decan Movie · ${type === "tv" ? "TV Series" : "Movie"}
</div>

<h1 class="seo-title">
  ${escapeHtml(title)}
  ${year ? ` (${escapeHtml(year)})` : ""}
</h1>

<div class="seo-meta">

${
  year
    ? `<span>${escapeHtml(year)}</span>`
    : ""
}

${
  rating
    ? `<span>★ ${rating.toFixed(1)}/10</span>`
    : ""
}

${genres
  .map(
    (genre) =>
      `<span>${escapeHtml(genre)}</span>`
  )
  .join("")}

</div>

<p class="seo-overview">
${escapeHtml(
  movie.overview ||
  "Discover this title on Decan Movie."
)}
</p>

<a
  class="seo-button"
  href="/?title=${type}&id=${encodeURIComponent(movie.id)}"
>
  Open ${type === "tv" ? "Series" : "Movie"} in Decan Movie
</a>

</div>

</article>

</main>

</body>
</html>`;
}

export default async function handler(req, res) {
  try {
    const token =
      process.env.TMDB_ACCESS_TOKEN;

    if (!token) {
      return res
        .status(500)
        .send(
          "TMDB_ACCESS_TOKEN is not configured."
        );
    }

    const type =
      req.query?.type === "tv"
        ? "tv"
        : "movie";

    const slug =
      String(
        req.query?.slug ||
        req.query?.id ||
        ""
      );

    /*
      Examples:

      /movie/inception-27205
      /movie/27205
      /tv/stranger-things-66732
      /tv/66732
    */

    const idMatch =
      slug.match(/(\d+)$/);

    if (!idMatch) {
      return res
        .status(400)
        .send("Invalid movie or TV ID.");
    }

    const id =
      Number(idMatch[1]);

    if (!Number.isInteger(id) || id <= 0) {
      return res
        .status(400)
        .send("Invalid title ID.");
    }

    const movie =
      await getTMDB(
        `/${type}/${id}?language=en-US&append_to_response=credits`,
        token
      );

    const title =
      movie.title ||
      movie.name ||
      "movie";

    const canonicalUrl =
      `${SITE}/${type}/${slugify(title)}-${id}`;

    const html =
      renderPage(
        type,
        movie,
        canonicalUrl
      );

    res.setHeader(
      "Content-Type",
      "text/html; charset=utf-8"
    );

    res.setHeader(
      "Cache-Control",
      "s-maxage=21600, stale-while-revalidate=43200"
    );

    return res
      .status(200)
      .send(html);

  } catch (error) {

    console.error(
      "SEO renderer error:",
      error
    );

    return res
      .status(404)
      .send(
        "Movie or TV show not found."
      );
  }
}
