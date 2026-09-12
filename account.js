/* Decan Movie shared account bridge.
 * One Supabase client, one auth session, optional accounts, cloud library sync.
 */
(function () {
  'use strict';
  if (window.__DECAN_ACCOUNT_APP__) return;
  window.__DECAN_ACCOUNT_APP__ = true;

  const CDN_URLS = [
    'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2',
    'https://unpkg.com/@supabase/supabase-js@2/dist/umd/supabase.min.js'
  ];

  let sb = null;
  let currentUser = null;
  let syncTimer = null;
  let syncing = false;
  let readyResolve;
  const ready = new Promise(resolve => { readyResolve = resolve; });
  let readyDone = false;

  const LIBRARY = {
    decan_watchlist: 'watchlist',
    decan_favorites: 'favorites',
    decan_history: 'history',
    decan_customlist: 'customlist',
    decan_continue_watching: 'continue'
  };

  function finishReady(value) {
    if (readyDone) return;
    readyDone = true;
    readyResolve(value);
  }

  function validConfig(c) {
    return !!(c && /^https:\/\/[^\s]+\.supabase\.co$/i.test(String(c.url)) &&
      c.anonKey && !String(c.anonKey).includes('YOUR_SUPABASE'));
  }

  async function getConfig() {
    const local = window.DECAN_SUPABASE_CONFIG || {};
    if (validConfig(local)) return local;

    try {
      const r = await fetch('/api/public-config', {
        cache: 'no-store',
        headers: { accept: 'application/json' }
      });
      if (r.ok) {
        const c = await r.json();
        if (validConfig(c)) {
          window.DECAN_SUPABASE_CONFIG = c;
          return c;
        }
      }
    } catch (_) {}
    return null;
  }

  function loadScript(src) {
    return new Promise(resolve => {
      const existing = document.querySelector(`script[data-decan-supabase-src="${src}"]`);
      if (existing) {
        if (window.supabase?.createClient) return resolve(window.supabase);
        existing.addEventListener('load', () => resolve(window.supabase || null), { once: true });
        existing.addEventListener('error', () => resolve(null), { once: true });
        return;
      }

      const script = document.createElement('script');
      script.src = src;
      script.async = true;
      script.dataset.decanSupabaseSrc = src;
      let done = false;
      const finish = () => {
        if (done) return;
        done = true;
        resolve(window.supabase || null);
      };
      script.onload = finish;
      script.onerror = finish;
      document.head.appendChild(script);
      setTimeout(finish, 10000);
    });
  }

  async function loadSupabase() {
    if (window.supabase?.createClient) return window.supabase;
    for (const src of CDN_URLS) {
      const api = await loadScript(src);
      if (api?.createClient) return api;
    }
    return null;
  }

  function getLocal(key, fallback) {
    try { return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback)); }
    catch (_) { return fallback; }
  }

  function updateAccountLinks() {
    const name = currentUser
      ? (currentUser.user_metadata?.display_name || currentUser.email?.split('@')[0] || 'Member')
      : null;
    const label = document.getElementById('decan-account-label');
    const pill = document.getElementById('decan-account-pill');
    const pillLabel = document.getElementById('decan-account-pill-label');
    const menu = document.getElementById('decan-account-menu');
    if (label) label.textContent = name ? `Member: ${name}` : 'Account / Sign In';
    if (pillLabel) pillLabel.textContent = name || 'Account';
    if (pill) pill.classList.toggle('member', !!currentUser);
    if (menu) menu.classList.toggle('member', !!currentUser);
  }

  function ensureIndexAccountLink() {
    const sidebar = document.getElementById('sidebar-menu');
    if (!sidebar) return;
    let item = document.getElementById('decan-account-menu');
    if (!item) {
      const ul = sidebar.querySelector('.menu-items');
      if (!ul) return;
      item = document.createElement('li');
      item.id = 'decan-account-menu';
      item.className = 'menu-link decan-account-menu';
      item.innerHTML = '<i class="fas fa-user-circle"></i><span id="decan-account-label">Account / Sign In</span>';
      ul.appendChild(item);
    }
    item.setAttribute('role', 'button');
    item.setAttribute('tabindex', '0');
    item.onclick = () => { if (typeof window.DecanOpenAccount === 'function') window.DecanOpenAccount(); else window.location.href = 'account.html'; };
    item.onkeydown = e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        if (typeof window.DecanOpenAccount === 'function') window.DecanOpenAccount(); else window.location.href = 'account.html';
      }
    };
    updateAccountLinks();
  }

  function librarySnapshot() {
    const rows = [];
    for (const [key, type] of Object.entries(LIBRARY)) {
      const value = getLocal(key, []);
      if (!Array.isArray(value)) continue;
      value.forEach(item => {
        if (item && item.id != null) rows.push({
          library_type: type,
          movie_id: Number(item.id),
          media_type: item.media_type || item.type || null,
          item
        });
      });
    }
    const ratings = getLocal('decan_ratings', {});
    Object.entries(ratings || {}).forEach(([id, entry]) => {
      if (entry?.data) rows.push({
        library_type: 'ratings',
        movie_id: Number(id),
        media_type: entry.data.media_type || null,
        item: entry.data
      });
    });
    return rows;
  }

  async function sync() {
    if (!sb || !currentUser || syncing) return;
    syncing = true;
    try {
      const rows = librarySnapshot();
      const types = [...new Set(rows.map(r => r.library_type))];
      for (const type of types) {
        const { error } = await sb.from('user_library')
          .delete().eq('user_id', currentUser.id).eq('library_type', type);
        if (error) throw error;
      }
      if (rows.length) {
        const { error } = await sb.from('user_library').upsert(rows.map(r => ({
          user_id: currentUser.id,
          library_type: r.library_type,
          movie_id: r.movie_id,
          media_type: r.media_type,
          item: r.item,
          updated_at: new Date().toISOString()
        })), { onConflict: 'user_id,library_type,movie_id' });
        if (error) throw error;
      }
    } finally {
      syncing = false;
    }
  }

  async function pull() {
    if (!sb || !currentUser) return;
    const { data, error } = await sb.from('user_library')
      .select('library_type,movie_id,media_type,item').eq('user_id', currentUser.id);
    if (error || !data) return;
    const map = {
      watchlist: 'decan_watchlist',
      favorites: 'decan_favorites',
      history: 'decan_history',
      customlist: 'decan_customlist',
      continue: 'decan_continue_watching'
    };
    const grouped = {};
    data.forEach(r => (grouped[r.library_type] ||= []).push(r.item));
    Object.entries(map).forEach(([type, key]) => {
      if (grouped[type]) localStorage.setItem(key, JSON.stringify(grouped[type]));
    });
    const ratings = {};
    data.filter(r => r.library_type === 'ratings').forEach(r => {
      ratings[r.movie_id] = { rating: r.item?.user_rating || 0, data: r.item };
    });
    if (Object.keys(ratings).length) localStorage.setItem('decan_ratings', JSON.stringify(ratings));
  }

  function hookStorage() {
    if (window.__DECAN_ACCOUNT_STORAGE_HOOKED) return;
    window.__DECAN_ACCOUNT_STORAGE_HOOKED = true;
    const original = localStorage.setItem.bind(localStorage);
    localStorage.setItem = function(key, value) {
      original(key, value);
      if (sb && currentUser && (LIBRARY[key] || key === 'decan_ratings')) {
        clearTimeout(syncTimer);
        syncTimer = setTimeout(() => sync().catch(() => {}), 1000);
      }
    };
  }

  async function init() {
    try {
      hookStorage();
      ensureIndexAccountLink();
      const config = await getConfig();
      if (!config) {
        finishReady({ client: null, user: null, config: null, error: 'Supabase public configuration is unavailable.' });
        return;
      }

      const api = await loadSupabase();
      if (!api?.createClient) {
        finishReady({ client: null, user: null, config, error: 'Supabase JavaScript client could not be loaded.' });
        return;
      }

      try {
        sb = api.createClient(config.url, config.anonKey, {
          auth: { persistSession: true, autoRefreshToken: true, detectSessionInUrl: true }
        });
      } catch (e) {
        finishReady({ client: null, user: null, config, error: e?.message || 'Supabase client initialization failed.' });
        return;
      }

      const { data, error } = await sb.auth.getSession();
      if (error) console.warn('Decan auth session:', error.message);
      currentUser = data?.session?.user || null;
      updateAccountLinks();

      if (currentUser) {
        await pull();
        setTimeout(() => sync().catch(() => {}), 1200);
      }

      finishReady({ client: sb, user: currentUser, config, error: null });

      sb.auth.onAuthStateChange((event, session) => {
        currentUser = session?.user || null;
        updateAccountLinks();
        if (currentUser) {
          setTimeout(async () => {
            await pull();
            window.dispatchEvent(new CustomEvent('decan:cloud-library-updated'));
          }, 0);
        }
        window.dispatchEvent(new CustomEvent('decan:auth-changed', {
          detail: { event, user: currentUser }
        }));
      });
    } catch (e) {
      finishReady({ client: null, user: null, config: window.DECAN_SUPABASE_CONFIG || null, error: e?.message || 'Account initialization failed.' });
    }
  }

  window.DecanAccount = {
    ready,
    getClient: () => sb,
    getUser: () => currentUser,
    getConfig,
    sync,
    pull
  };

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
  else init();
})();
