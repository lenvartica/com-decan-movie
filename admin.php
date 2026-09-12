<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Decan Movie — Admin Dashboard</title>
<style>
:root{--bg:#0b0e13;--card:#151a22;--muted:#8991a3;--text:#f5f7fb;--cyan:#00e5ff;--pink:#ff007f;--green:#00ffaa;--yellow:#ffb703}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,Segoe UI,Arial,sans-serif}button,input,select{font:inherit}.wrap{max-width:1400px;margin:auto;padding:24px}.top{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:20px;flex-wrap:wrap}.brand{font-size:1.4rem;font-weight:900}.brand span{color:var(--cyan)}.actions{display:flex;gap:8px;flex-wrap:wrap}button{border:1px solid rgba(255,255,255,.1);background:#151a22;color:#fff;border-radius:10px;padding:10px 13px;cursor:pointer;font-weight:800}button:hover{border-color:var(--cyan)}.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.card{background:var(--card);border:1px solid rgba(255,255,255,.07);border-radius:15px;padding:17px}.stat small{color:var(--muted)}.stat strong{display:block;font-size:1.8rem;margin-top:7px}.cyan{color:var(--cyan)}.yellow{color:var(--yellow)}.green{color:var(--green)}.pink{color:var(--pink)}.section{margin-top:16px}.section h2{font-size:1rem;margin:0 0 10px}.two{display:grid;grid-template-columns:1fr 1fr;gap:12px}.table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse;min-width:720px}.table th,.table td{padding:11px 9px;border-bottom:1px solid rgba(255,255,255,.06);text-align:left;font-size:.82rem}.table th{color:var(--muted);font-weight:700}.badge{padding:4px 7px;border-radius:999px;background:rgba(0,229,255,.08);color:var(--cyan);font-size:.72rem}.badge.pending{color:var(--yellow);background:rgba(255,183,3,.08)}.badge.suspended{color:var(--pink);background:rgba(255,0,127,.08)}.empty{color:var(--muted);padding:20px;text-align:center}.login{max-width:420px;margin:12vh auto}.field{width:100%;padding:12px;margin-top:9px;background:#0e1117;color:#fff;border:1px solid rgba(255,255,255,.1);border-radius:10px;outline:0}.field:focus{border-color:var(--cyan)}.hidden{display:none!important}.error{color:#ff668f;margin-top:10px;font-size:.85rem}.success{color:var(--green);margin-top:10px;font-size:.85rem}@media(max-width:950px){.grid{grid-template-columns:repeat(2,1fr)}.two{grid-template-columns:1fr}}@media(max-width:550px){.wrap{padding:14px}.grid{grid-template-columns:1fr}.stat strong{font-size:1.5rem}}
</style>
</head>
<body>
<div id="loginView" class="wrap login">
  <div class="card">
    <div class="brand">DECAN <span>MOVIE</span> / ADMIN</div>
    <p style="color:var(--muted);line-height:1.5">Administrator access is separate from normal movie browsing. Sign in with the Supabase account that has the <b>admin</b> role.</p>
    <input id="email" class="field" type="email" placeholder="Admin email">
    <input id="password" class="field" type="password" placeholder="Password">
    <button style="margin-top:12px;width:100%" onclick="adminLogin()">Sign In</button>
    <div id="loginStatus"></div>
  </div>
</div>

<div id="app" class="wrap hidden">
  <div class="top">
    <div><div class="brand">DECAN <span>MOVIE</span> / ADMIN</div><div style="color:var(--muted);margin-top:4px">Live platform overview</div></div>
    <div class="actions"><button onclick="location.href='index.php'">← Movie Site</button><button onclick="refreshAll()">↻ Refresh</button><button onclick="logout()">Sign Out</button></div>
  </div>
  <div id="status" class="card" style="margin-bottom:12px">Loading dashboard…</div>
  <div id="stats" class="grid"></div>
  <div class="two section">
    <div class="card"><h2>🔥 Top Watched Today</h2><div id="topWatched"></div></div>
    <div class="card"><h2>📨 Latest Requests</h2><div id="requests"></div></div>
  </div>
  <div class="card section"><h2>👥 Users</h2><div class="table-wrap"><div id="users"></div></div></div>
</div>

<script src="supabase-config.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script>
let sb, me;
const cfg=window.DECAN_SUPABASE_CONFIG||{};
const configured=cfg.url&&!String(cfg.url).includes('YOUR-PROJECT')&&cfg.anonKey&&!String(cfg.anonKey).includes('YOUR_SUPABASE');

function msg(text,kind=''){document.getElementById('status').textContent=text;document.getElementById('status').className='card '+kind}
function esc(s){return String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]))}

async function adminLogin(){
 if(!configured){document.getElementById('loginStatus').textContent='Configure Supabase first.';return}
 const email=document.getElementById('email').value.trim(), password=document.getElementById('password').value;
 const {data,error}=await sb.auth.signInWithPassword({email,password});
 if(error){document.getElementById('loginStatus').textContent=error.message;return}
 await boot(data.user);
}
async function boot(user){
 me=user;
 const {data:role}=await sb.from('user_roles').select('role').eq('user_id',user.id).maybeSingle();
 if(role?.role!=='admin'){await sb.auth.signOut();document.getElementById('loginStatus').textContent='This account is not an administrator.';return}
 document.getElementById('loginView').classList.add('hidden');document.getElementById('app').classList.remove('hidden');
 await refreshAll();
}
async function refreshAll(){
 msg('Refreshing live data…');
 const {data:stats,error}=await sb.rpc('admin_dashboard_stats');
 if(error){msg(error.message,'error');return}
 document.getElementById('stats').innerHTML=[
 ['Users',stats.total_users,'cyan'],['New Users Today',stats.users_today,'green'],['Visitors Today',stats.visitors_today,'cyan'],['Visits Today',stats.visits_today,'cyan'],
 ['Ratings',stats.ratings,'yellow'],['Rating Users',stats.rating_users,'yellow'],['Coins In Circulation',stats.coins,'yellow'],['Watch Events Today',stats.watch_events_today,'pink'],['Pending Requests',stats.pending_requests,'pink'],['All Requests',stats.total_requests,'pink']
 ].map(x=>`<div class="card stat"><small>${x[0]}</small><strong class="${x[2]}">${Number(x[1]||0).toLocaleString()}</strong></div>`).join('');
 await Promise.all([loadTop(),loadRequests(),loadUsers()]);
 msg('Dashboard updated • '+new Date().toLocaleString(),'success');
}
async function loadTop(){
 const {data,error}=await sb.from('top_watched_today').select('*').limit(12);
 const el=document.getElementById('topWatched');
 if(error){el.innerHTML='<div class="empty">'+esc(error.message)+'</div>';return}
 el.innerHTML=data?.length?data.map((x,i)=>`<div style="display:flex;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.06)"><span>${i+1}. ${esc(x.title||('Movie #'+x.movie_id))}</span><b>${Number(x.watches||0)} plays</b></div>`).join(''):'<div class="empty">No watches recorded today yet.</div>';
}
async function loadRequests(){
 const {data,error}=await sb.from('movie_requests').select('id,title,year,media_type,status,requester_name,created_at').order('created_at',{ascending:false}).limit(12);
 const el=document.getElementById('requests');
 if(error){el.innerHTML='<div class="empty">'+esc(error.message)+'</div>';return}
 el.innerHTML=data?.length?data.map(x=>`<div style="padding:10px 0;border-bottom:1px solid rgba(255,255,255,.06)"><div><b>${esc(x.title)}</b> ${x.year?'('+x.year+')':''}</div><div style="color:var(--muted);font-size:.76rem">${esc(x.media_type)} • ${esc(x.requester_name||'Guest')} • ${new Date(x.created_at).toLocaleString()}</div><select onchange="setRequestStatus(${x.id},this.value)" class="field" style="margin-top:6px"><option ${x.status==='pending'?'selected':''}>pending</option><option ${x.status==='reviewing'?'selected':''}>reviewing</option><option ${x.status==='approved'?'selected':''}>approved</option><option ${x.status==='rejected'?'selected':''}>rejected</option><option ${x.status==='completed'?'selected':''}>completed</option></select></div>`).join(''):'<div class="empty">No requests yet.</div>';
}
async function setRequestStatus(id,status){const {error}=await sb.from('movie_requests').update({status,updated_at:new Date().toISOString()}).eq('id',id);if(error)alert(error.message);else loadRequests()}
async function loadUsers(){
 const {data,error}=await sb.rpc('admin_users',{limit_count:150});
 const el=document.getElementById('users');
 if(error){el.innerHTML='<div class="empty">'+esc(error.message)+'</div>';return}
 el.innerHTML=`<table class="table"><thead><tr><th>User</th><th>Role</th><th>Status</th><th>Coins</th><th>XP</th><th>Joined</th><th>Actions</th></tr></thead><tbody>`+
 (data||[]).map(u=>`<tr><td><b>${esc(u.display_name||u.username||'User')}</b><br><span style="color:var(--muted)">${esc(u.email||'')}</span></td><td><span class="badge">${esc(u.role)}</span></td><td><span class="badge ${u.status==='suspended'?'suspended':''}">${esc(u.status)}</span></td><td>${Number(u.coins||0).toLocaleString()}</td><td>${Number(u.xp||0).toLocaleString()}</td><td>${new Date(u.created_at).toLocaleDateString()}</td><td><button onclick="adjustCoins('${u.id}',1)">+1🪙</button> <button onclick="adjustCoins('${u.id}',-1)">-1🪙</button> <button onclick="setUserStatus('${u.id}','${u.status==='active'?'suspended':'active'}')">${u.status==='active'?'Suspend':'Activate'}</button></td></tr>`).join('')+
 '</tbody></table>';
}
async function adjustCoins(id,amount){const reason=prompt('Reason for coin adjustment?','Admin adjustment');if(reason===null)return;const {error}=await sb.rpc('admin_adjust_coins',{target_user:id,amount,reason});if(error)alert(error.message);else refreshAll()}
async function setUserStatus(id,status){if(!confirm('Set this user to '+status+'?'))return;const {error}=await sb.rpc('admin_set_user_status',{target_user:id,new_status:status});if(error)alert(error.message);else loadUsers()}
async function logout(){await sb.auth.signOut();location.reload()}
(async()=>{if(!configured){document.getElementById('loginStatus').textContent='Supabase is not configured. Add your project URL and anon key to supabase-config.js.';return}sb=window.supabase.createClient(cfg.url,cfg.anonKey);const {data}=await sb.auth.getUser();if(data.user)await boot(data.user)})();
</script>
</body>
</html>
