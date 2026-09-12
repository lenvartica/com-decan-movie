/* Decan Movie Supabase upgrade - additive only.
   Loads after the existing app and wraps existing functions instead of replacing
   the movie engine, TMDB proxy, player URLs, embedded links, or local libraries. */
(function () {
  'use strict';

  const cfg = window.DECAN_SUPABASE_CONFIG || {};
  const configured = cfg.url && cfg.anonKey &&
    !String(cfg.url).includes('YOUR-PROJECT') &&
    !String(cfg.anonKey).includes('YOUR_SUPABASE');

  const SESSION_KEY = 'decan_analytics_session';
  let sessionId = localStorage.getItem(SESSION_KEY);
  if (!sessionId) {
    sessionId = (crypto.randomUUID ? crypto.randomUUID() : 's-' + Date.now() + '-' + Math.random().toString(36).slice(2));
    localStorage.setItem(SESSION_KEY, sessionId);
  }

  let sb = null;
  let currentUser = null;
  let profile = null;
  let isAdmin = false;

  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));

  function toast(msg) {
    if (typeof window.showToast === 'function') window.showToast(msg);
    else console.log('[Decan]', msg);
  }

  function localLibrary() {
    return {
      watchlist: JSON.parse(localStorage.getItem('decan_watchlist') || '[]'),
      favorites: JSON.parse(localStorage.getItem('decan_favorites') || '[]'),
      history: JSON.parse(localStorage.getItem('decan_history') || '[]'),
      customlist: JSON.parse(localStorage.getItem('decan_customlist') || '[]'),
      continue: JSON.parse(localStorage.getItem('decan_continue_watching') || '[]')
    };
  }

  function addStyles() {
    const s = document.createElement('style');
    s.textContent = `
      .decan-account-btn{display:inline-flex;align-items:center;gap:7px;border:1px solid rgba(0,229,255,.28);background:rgba(0,229,255,.07);color:#fff;border-radius:10px;padding:9px 12px;cursor:pointer;font-weight:800;white-space:nowrap}
      .decan-account-btn:hover{border-color:var(--neon-cyan);box-shadow:0 0 18px rgba(0,229,255,.1)}
      .decan-coin-pill{display:inline-flex;align-items:center;gap:5px;color:#ffb703;background:rgba(255,183,3,.08);border:1px solid rgba(255,183,3,.2);border-radius:999px;padding:7px 10px;font-weight:800;font-size:.78rem}
      .decan-auth-backdrop,.decan-request-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.78);backdrop-filter:blur(8px);z-index:5000;display:flex;align-items:center;justify-content:center;padding:18px}
      .decan-auth-card,.decan-request-card{width:min(440px,100%);background:#151820;border:1px solid rgba(255,255,255,.1);border-radius:18px;box-shadow:0 30px 90px rgba(0,0,0,.55);padding:22px}
      .decan-auth-card h2,.decan-request-card h2{margin-bottom:5px}.decan-muted{color:var(--text-muted);font-size:.82rem;line-height:1.5}
      .decan-field{width:100%;margin-top:10px;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,.1);background:#0e1015;color:#fff;outline:none}
      .decan-field:focus{border-color:var(--neon-cyan)}.decan-auth-actions{display:flex;gap:8px;margin-top:13px;flex-wrap:wrap}
      .decan-primary{background:var(--neon-cyan);color:#061015;border:0;border-radius:10px;padding:11px 14px;font-weight:900;cursor:pointer}
      .decan-secondary{background:rgba(255,255,255,.06);color:#fff;border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:11px 14px;font-weight:800;cursor:pointer}
      .decan-danger{background:rgba(255,0,127,.08);color:#fff;border:1px solid rgba(255,0,127,.3);border-radius:10px;padding:11px 14px;font-weight:800;cursor:pointer}
      .decan-account-pop{position:fixed;right:18px;top:72px;width:min(330px,calc(100vw - 36px));background:#151820;border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:15px;z-index:4500;box-shadow:0 25px 70px rgba(0,0,0,.5)}
      .decan-account-pop .row{display:flex;justify-content:space-between;gap:10px;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06)}
      .decan-account-pop .row:last-child{border-bottom:0}.decan-float-request{position:fixed;right:18px;bottom:18px;z-index:2100;border:1px solid rgba(0,229,255,.3);background:#11151b;color:#fff;border-radius:999px;padding:12px 15px;font-weight:900;cursor:pointer;box-shadow:0 12px 35px rgba(0,0,0,.35)}
      .decan-admin-link{display:none}.decan-admin-link.show{display:flex}
      @media(max-width:700px){.decan-account-btn span{display:none}.decan-coin-pill{display:none}.decan-float-request{bottom:12px;right:12px}}
    `;
    document.head.appendChild(s);
  }

  async function showTopWatchedToday() {
    if (!sb) { toast('Top watched data is not connected yet.'); return; }
    try {
      if (typeof window.resetFeatureSurfaces === 'function') window.resetFeatureSurfaces();
      const {data,error}=await sb.rpc('top_watched_public',{limit_count:24});
      if(error) throw error;
      const ids=(data||[]).map(x=>x.movie_id).filter(Boolean);
      if(!ids.length){ document.getElementById('grid-title').textContent='Top Watched Today'; document.getElementById('movie-grid').innerHTML='<div class="empty-state"><i class="fas fa-chart-line"></i><h3>No watch data yet</h3><p>Come back after viewers start watching.</p></div>'; return; }
      const results=[];
      for(const row of data.slice(0,18)){
        try{
          const media=row.media_type==='tv'?'tv':'movie';
          const r=await fetch(`${'/api/tmdb'}/${media}/${row.movie_id}?language=en-US`,{method:'GET',headers:{accept:'application/json'}});
          if(r.ok){const d=await r.json(); results.push({...d,media_type:media,watch_count:row.watches});}
        }catch(_){}
      }
      document.getElementById('grid-title').textContent='🔥 Top Watched Today';
      if(typeof window.displayMovies==='function') window.displayMovies(results,null,false);
    } catch(e) { toast('Top watched is unavailable right now.'); }
    if(window.innerWidth<=900) document.getElementById('sidebar-menu')?.classList.remove('open');
  }

  function addUI() {
    const headerControls = document.querySelector('.header-controls');
    if (headerControls && !document.getElementById('decan-account-btn')) {
      const btn = document.createElement('button');
      btn.id = 'decan-account-btn';
      btn.className = 'decan-account-btn';
      btn.onclick = openAccountPanel;
      btn.innerHTML = '<i class="fas fa-user-circle"></i><span>Account</span>';
      headerControls.appendChild(btn);
    }

    const req = document.createElement('button');
    req.className = 'decan-float-request';
    req.innerHTML = '<i class="fas fa-paper-plane"></i> Request';
    req.onclick = openRequestModal;
    document.body.appendChild(req);

    const menu = document.querySelector('.menu-items');
    if (menu && !document.getElementById('decan-request-menu')) {
      const li = document.createElement('li');
      li.id = 'decan-request-menu';
      li.className = 'menu-link';
      li.onclick = openRequestModal;
      li.innerHTML = '<i class="fas fa-paper-plane"></i> Request a Movie';
      const hubTitle = Array.from(menu.querySelectorAll('.menu-category-title')).find(x => x.textContent.includes('Movie Box Categories'));
      if (hubTitle && !document.getElementById('decan-top-watched-menu')) {
        const top = document.createElement('li');
        top.id='decan-top-watched-menu'; top.className='menu-link';
        top.onclick=showTopWatchedToday;
        top.innerHTML='<i class="fas fa-chart-line"></i> Top Watched Today';
        hubTitle.parentNode.insertBefore(top, hubTitle.nextElementSibling);
      }
      const supportTitle = Array.from(menu.querySelectorAll('.menu-category-title')).find(x => x.textContent.includes('Community'));
      if (supportTitle) menu.insertBefore(li, supportTitle.nextElementSibling);
    }
  }

  function openAccountPanel() {
    const old = document.getElementById('decan-account-pop');
    if (old) { old.remove(); return; }
    const pop = document.createElement('div');
    pop.id = 'decan-account-pop';
    pop.className = 'decan-account-pop';
    if (!configured) {
      pop.innerHTML = `<h3>Guest Mode</h3><p class="decan-muted" style="margin-top:6px">The site works without an account. Supabase is not configured yet, so your existing device-based library continues to work.</p><div class="decan-auth-actions"><button class="decan-secondary" onclick="this.closest('.decan-account-pop').remove()">Close</button></div>`;
    } else if (!currentUser) {
      pop.innerHTML = `<h3>Welcome to Decan Movie</h3><p class="decan-muted" style="margin-top:6px">Account is optional. Sign in to sync ratings, watch activity, coins and your library across devices.</p><div class="decan-auth-actions"><button class="decan-primary" onclick="window.DecanSupabase.openAuth('signin')">Sign In</button><button class="decan-secondary" onclick="window.DecanSupabase.openAuth('signup')">Create Account</button></div>`;
    } else {
      pop.innerHTML = `<h3>${esc(profile?.display_name || profile?.username || currentUser.email?.split('@')[0] || 'Member')}</h3>
        <p class="decan-muted">${esc(currentUser.email || '')}</p>
        <div class="row"><span>🪙 Coins</span><strong>${profile?.coins ?? 0}</strong></div>
        <div class="row"><span>⭐ XP</span><strong>${profile?.xp ?? 0}</strong></div>
        <div class="row"><span>🔥 Streak</span><strong>${profile?.streak_days ?? 0} days</strong></div>
        <div class="decan-auth-actions"><button class="decan-primary" onclick="window.DecanSupabase.claimReward()">🎁 Daily Reward</button>${isAdmin ? '<button class="decan-secondary" onclick="location.href=\'admin.php\'">Admin Dashboard</button>' : ''}<button class="decan-danger" onclick="window.DecanSupabase.signOut()">Sign Out</button></div>`;
    }
    document.body.appendChild(pop);
  }

  function openAuth(mode='signin') {
    document.getElementById('decan-auth-backdrop')?.remove();
    const b = document.createElement('div');
    b.id = 'decan-auth-backdrop';
    b.className = 'decan-auth-backdrop';
    b.innerHTML = `<div class="decan-auth-card">
      <div style="display:flex;justify-content:space-between;gap:10px"><div><h2>${mode==='signup'?'Create your account':'Welcome back'}</h2><p class="decan-muted">${mode==='signup'?'Your account is optional.':'Sign in to sync your movie life across devices.'}</p></div><button class="decan-secondary" onclick="this.closest('.decan-auth-backdrop').remove()">✕</button></div>
      <input id="decan-auth-email" class="decan-field" type="email" placeholder="Email address" autocomplete="email">
      <input id="decan-auth-password" class="decan-field" type="password" placeholder="Password" autocomplete="${mode==='signup'?'new-password':'current-password'}">
      <input id="decan-auth-name" class="decan-field" style="${mode==='signup'?'':'display:none'}" placeholder="Display name">
      <div class="decan-auth-actions"><button class="decan-primary" onclick="window.DecanSupabase.submitAuth('${mode}')">${mode==='signup'?'Create Account':'Sign In'}</button><button class="decan-secondary" onclick="window.DecanSupabase.openAuth('${mode==='signup'?'signin':'signup'}')">${mode==='signup'?'I already have an account':'Create an account'}</button></div>
      <p class="decan-muted" id="decan-auth-status" style="margin-top:12px"></p>
    </div>`;
    document.body.appendChild(b);
  }

  async function submitAuth(mode) {
    if (!sb) return;
    const email = document.getElementById('decan-auth-email')?.value.trim();
    const password = document.getElementById('decan-auth-password')?.value;
    const name = document.getElementById('decan-auth-name')?.value.trim();
    const status = document.getElementById('decan-auth-status');
    if (!email || !password) { status.textContent='Email and password are required.'; return; }
    status.textContent='Please wait…';
    try {
      let result;
      if (mode === 'signup') {
        result = await sb.auth.signUp({email,password,options:{data:{display_name:name || email.split('@')[0]}}});
        if (result.error) throw result.error;
        status.textContent = result.data.session ? 'Account created.' : 'Account created. Check your email if confirmation is enabled.';
      } else {
        result = await sb.auth.signInWithPassword({email,password});
        if (result.error) throw result.error;
        document.getElementById('decan-auth-backdrop')?.remove();
      }
    } catch (e) { status.textContent = e.message || 'Authentication failed.'; }
  }

  async function loadUser() {
    if (!sb) return;
    const {data} = await sb.auth.getUser();
    currentUser = data?.user || null;
    if (currentUser) {
      const p = await sb.from('profiles').select('*').eq('id', currentUser.id).maybeSingle();
      profile = p.data || null;
      if (profile?.status === 'suspended') { await sb.auth.signOut(); currentUser=null; profile=null; toast('This account has been suspended.'); return; }
      const r = await sb.from('user_roles').select('role').eq('user_id', currentUser.id).maybeSingle();
      isAdmin = r.data?.role === 'admin';
      await syncLocalLibrary();
      await syncLocalRatings();
    } else {
      profile = null; isAdmin = false;
    }
    updateAccountButton();
  }

  function updateAccountButton() {
    const btn = document.getElementById('decan-account-btn');
    if (!btn) return;
    if (currentUser) {
      btn.innerHTML = `<i class="fas fa-user-check"></i><span>${esc(profile?.display_name || profile?.username || 'Account')}</span>`;
      btn.title = `🪙 ${profile?.coins ?? 0} coins`;
    } else {
      btn.innerHTML = '<i class="fas fa-user-circle"></i><span>Account</span>';
    }
  }

  async function syncLocalLibrary() {
    if (!currentUser || !sb) return;
    const lib = localLibrary();
    for (const [type, items] of Object.entries(lib)) {
      if (!Array.isArray(items)) continue;
      for (const item of items.slice(0, 50)) {
        if (!item?.id) continue;
        await sb.from('user_library').upsert({
          user_id: currentUser.id,
          library_type: type,
          movie_id: Number(item.id),
          media_type: item.media_type || item.type || 'movie',
          item,
          updated_at: new Date().toISOString()
        }, {onConflict:'user_id,library_type,movie_id'});
      }
    }
  }

  async function syncLocalRatings() {
    if (!currentUser || !sb) return;
    const ratings = JSON.parse(localStorage.getItem('decan_ratings') || '{}');
    for (const [movieId, value] of Object.entries(ratings)) {
      if (!value?.rating) continue;
      const item = value.data || {};
      await sb.from('movie_ratings').upsert({
        user_id: currentUser.id,
        movie_id: Number(movieId),
        media_type: item.media_type || item.type || 'movie',
        rating: Number(value.rating),
        title: item.title || item.name || null,
        poster_path: item.poster_path || null,
        updated_at: new Date().toISOString()
      }, {onConflict:'user_id,movie_id,media_type'});
    }
  }

  async function event(event_type, item, extra={}) {
    if (!sb || !configured) return;
    try {
      await sb.from('watch_events').insert({
        user_id: currentUser?.id || null,
        session_id: sessionId,
        movie_id: Number(item?.id || extra.movie_id || 0),
        media_type: item?.media_type || item?.type || extra.media_type || 'movie',
        title: item?.title || item?.name || extra.title || null,
        poster_path: item?.poster_path || null,
        event_type,
        season: extra.season || null,
        episode: extra.episode || null,
        progress_seconds: extra.progress_seconds || null,
        duration_seconds: extra.duration_seconds || null
      });
    } catch (_) {}
  }

  async function visit() {
    if (!sb || !configured) return;
    const dedupe = 'decan_visit_' + new Date().toISOString().slice(0,10);
    if (sessionStorage.getItem(dedupe)) return;
    sessionStorage.setItem(dedupe, '1');
    try {
      await sb.from('site_visits').insert({
        user_id: currentUser?.id || null,
        session_id: sessionId,
        path: location.pathname + location.search,
        referrer: document.referrer || null,
        user_agent: navigator.userAgent.slice(0,500)
      });
    } catch (_) {}
  }

  async function claimReward() {
    if (!currentUser) { openAuth('signin'); return; }
    const {data,error} = await sb.rpc('claim_daily_reward');
    if (error) { toast(error.message || 'Daily reward unavailable'); return; }
    if (data?.claimed) toast(`🎁 +${data.amount} coins • 🔥 ${data.streak} day streak`);
    else toast(`🎁 Already claimed today • 🪙 ${data.coins} coins`);
    await loadUser();
    document.getElementById('decan-account-pop')?.remove();
  }

  async function saveRating(id, rating, details) {
    if (!currentUser || !sb) return;
    await sb.from('movie_ratings').upsert({
      user_id: currentUser.id, movie_id:Number(id),
      media_type:details?.media_type || details?.type || 'movie',
      rating:Number(rating), title:details?.title || details?.name || null,
      poster_path:details?.poster_path || null, updated_at:new Date().toISOString()
    }, {onConflict:'user_id,movie_id,media_type'});
    toast('Rating synced to your account ⭐');
  }

  function openRequestModal() {
    document.getElementById('decan-request-backdrop')?.remove();
    const b=document.createElement('div');
    b.id='decan-request-backdrop'; b.className='decan-request-backdrop';
    b.innerHTML=`<div class="decan-request-card">
      <div style="display:flex;justify-content:space-between;gap:10px"><div><h2>Request a Movie</h2><p class="decan-muted">Suggest a movie, series or anime for the catalog.</p></div><button class="decan-secondary" onclick="this.closest('.decan-request-backdrop').remove()">✕</button></div>
      <input id="decan-request-title" class="decan-field" placeholder="Movie / series title" maxlength="180">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px"><input id="decan-request-year" class="decan-field" type="number" min="1900" max="2100" placeholder="Year"><select id="decan-request-type" class="decan-field"><option value="movie">Movie</option><option value="tv">Series</option><option value="anime">Anime</option></select></div>
      <input id="decan-request-name" class="decan-field" placeholder="Your name (optional)" maxlength="80">
      <textarea id="decan-request-message" class="decan-field" rows="4" placeholder="Anything else? (optional)" maxlength="1000"></textarea>
      <div class="decan-auth-actions"><button class="decan-primary" onclick="window.DecanSupabase.submitRequest()">Send Request</button><button class="decan-secondary" onclick="this.closest('.decan-request-backdrop').remove()">Cancel</button></div>
      <p id="decan-request-status" class="decan-muted" style="margin-top:10px"></p>
    </div>`;
    document.body.appendChild(b);
  }

  async function submitRequest() {
    const title=document.getElementById('decan-request-title')?.value.trim();
    const status=document.getElementById('decan-request-status');
    if(!title){status.textContent='Please enter a title.';return;}
    if(!sb || !configured){status.textContent='Requests are unavailable until Supabase is configured.';return;}
    status.textContent='Sending…';
    const {error}=await sb.from('movie_requests').insert({
      user_id:currentUser?.id || null, session_id:sessionId,
      requester_name:document.getElementById('decan-request-name')?.value.trim() || null,
      title, year:Number(document.getElementById('decan-request-year')?.value) || null,
      media_type:document.getElementById('decan-request-type')?.value || 'movie',
      message:document.getElementById('decan-request-message')?.value.trim() || null
    });
    if(error){status.textContent=error.message;return;}
    status.textContent='Request sent successfully. Thank you!';
    setTimeout(()=>document.getElementById('decan-request-backdrop')?.remove(),1200);
  }

  async function signOut() {
    await sb?.auth.signOut();
    currentUser=null; profile=null; isAdmin=false;
    document.getElementById('decan-account-pop')?.remove();
    updateAccountButton();
    toast('Signed out. Guest mode remains available.');
  }

  function wrapFunctions() {
    if (window.__decanSupabaseWrapped) return;
    window.__decanSupabaseWrapped = true;

    if (typeof window.openMovieDetails === 'function') {
      const original = window.openMovieDetails;
      window.openMovieDetails = async function(id, type) {
        const result = await original.apply(this, arguments);
        setTimeout(()=> {
          const details=window.currentModalDetails || currentModalDetails;
          if(details) event('detail', details);
        }, 250);
        return result;
      };
    }

    if (typeof window.launchTheaterPlayer === 'function') {
      const original = window.launchTheaterPlayer;
      window.launchTheaterPlayer = function(id,type,title,season,episode) {
        return original.apply(this,arguments);
      };
    }

    if (typeof window.setPersonalRating === 'function') {
      const original = window.setPersonalRating;
      window.setPersonalRating = function(id,rating) {
        const result=original.apply(this,arguments);
        const details=window.currentModalDetails || currentModalDetails;
        saveRating(id,rating,details);
        event('detail',details || {id,media_type:'movie'});
        return result;
      };
    }

    if (typeof window.toggleWatchlist === 'function') {
      const original=window.toggleWatchlist;
      window.toggleWatchlist=function(id){
        const before=Array.isArray(window.userWatchlist)?window.userWatchlist.length:0;
        const result=original.apply(this,arguments);
        const after=Array.isArray(window.userWatchlist)?window.userWatchlist.length:before;
        if(after>before) event('watchlist_add',window.currentModalDetails||{id});
        return result;
      };
    }
  }

  async function init() {
    addStyles();
    addUI();
    if (!configured) {
      updateAccountButton();
      return;
    }
    const script=document.createElement('script');
    script.src='https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2';
    script.onload=async()=>{
      try {
        sb=window.supabase.createClient(cfg.url,cfg.anonKey);
        window.DecanSupabase={openAuth,submitAuth,claimReward,signOut,openRequestModal,submitRequest};
        sb.auth.onAuthStateChange(async()=>{ await loadUser(); await visit(); });
        await loadUser();
        await visit();
        wrapFunctions();
      } catch(e) { console.warn('Supabase enhancement disabled:',e); }
    };
    script.onerror=()=>console.warn('Supabase client could not load; guest mode remains active.');
    document.head.appendChild(script);
  }

  window.DecanSupabase = { openAuth, submitAuth, claimReward, signOut, openRequestModal, submitRequest, showTopWatchedToday };
  window.addEventListener('load', init);
})();
