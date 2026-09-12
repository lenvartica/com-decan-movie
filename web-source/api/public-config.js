export default function handler(req, res) {
  const url = process.env.SUPABASE_URL || process.env.NEXT_PUBLIC_SUPABASE_URL || '';
  const anonKey = process.env.SUPABASE_ANON_KEY || process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY || '';
  if (!url || !anonKey) return res.status(503).json({ error: 'Supabase public configuration is not configured.' });
  res.setHeader('Cache-Control','public, max-age=300, s-maxage=300');
  res.status(200).json({ url, anonKey });
}
