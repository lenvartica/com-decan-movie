/* Decan Movie account page. Uses the single shared client from account.js. */
(function(){
'use strict';
const $=id=>document.getElementById(id);
const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
let sb=null,user=null,profile=null;

function message(id,text,ok=false){const el=$(id);if(el)el.innerHTML='<div class="msg '+(ok?'ok':'')+'">'+esc(text)+'</div>';}
function local(k,f){try{return JSON.parse(localStorage.getItem(k)||JSON.stringify(f))}catch{return f}}
function counts(){
 const v={watchlist:local('decan_watchlist',[]).length,favorites:local('decan_favorites',[]).length,history:local('decan_history',[]).length,ratings:Object.keys(local('decan_ratings',{})).length,cont:local('decan_continue_watching',[]).length,custom:local('decan_customlist',[]).length};
 [['watchlistCount','watchlist'],['libraryWatch','watchlist'],['favoriteCount','favorites'],['libraryFav','favorites'],['ratingCount','ratings'],['libraryRatings','ratings'],['libraryHistory','history'],['libraryContinue','cont'],['libraryCustom','custom']].forEach(([id,key])=>{if($(id))$(id).textContent=v[key]});
}
function avatar(name,url){return url||('https://ui-avatars.com/api/?name='+encodeURIComponent(name||'Member')+'&background=10151d&color=00e5ff&size=240')}
function showAuth(){ $('authView').classList.remove('hidden');$('profileView').classList.add('hidden') }
function showDashboard(){ $('authView').classList.add('hidden');$('profileView').classList.remove('hidden') }
function setAuthTab(name){document.querySelectorAll('[data-auth-tab]').forEach(b=>b.classList.toggle('active',b.dataset.authTab===name));['signinForm','signupForm','resetForm'].forEach(id=>$(id).classList.add('hidden'));$(name==='signin'?'signinForm':name==='signup'?'signupForm':'resetForm').classList.remove('hidden');}
document.querySelectorAll('[data-auth-tab]').forEach(b=>b.addEventListener('click',()=>setAuthTab(b.dataset.authTab)));
function setPanel(name){document.querySelectorAll('[data-panel]').forEach(b=>b.classList.toggle('active',b.dataset.panel===name));document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));if($(name))$(name).classList.add('active');if(name==='libraryPanel')counts();if(name==='recommendPanel')loadRecommendations();history.replaceState(null,'','#'+name.replace('Panel',''));}
document.querySelectorAll('[data-panel]').forEach(b=>b.addEventListener('click',()=>setPanel(b.dataset.panel)));
function initialPanel(){const hash=location.hash.replace('#','');const id=hash?hash+'Panel':'overviewPanel';if($(id))setPanel(id)}

async function loadProfile(){
 if(!sb||!user)return;
 let {data,error}=await sb.from('profiles').select('*').eq('id',user.id).maybeSingle();
 if(error) console.warn('Profile read:',error.message);
 if(!data){
   const fallback={id:user.id,display_name:user.user_metadata?.display_name||user.email?.split('@')[0]||'Member',username:user.user_metadata?.username||null};
   const r=await sb.from('profiles').upsert(fallback,{onConflict:'id'}).select().maybeSingle();
   data=r.data||fallback;
 }
 profile=data||{};
 const name=profile.display_name||user.user_metadata?.display_name||user.email?.split('@')[0]||'Member';
 $('memberName').textContent=name;$('sideName').textContent=name;$('sideUsername').textContent=profile.username?'@'+profile.username:'';$('memberEmail').textContent=user.email||'';$('userId').textContent=user.id;
 $('profileName').value=profile.display_name||'';$('profileUsername').value=profile.username||'';$('profileLocation').value=profile.location||'';$('profileGenre').value=profile.favorite_genre||'';$('profileBio').value=profile.bio||'';
 $('coinCount').textContent=Number(profile.coins||0).toLocaleString();$('rewardCoins').textContent=Number(profile.coins||0).toLocaleString();$('streakCount').textContent=Number(profile.streak_days||0)+' days';
 const pic=avatar(name,profile.avatar_url);$('heroAvatar').src=pic;$('sideAvatar').src=pic;counts();
}

$('profileForm').addEventListener('submit',async e=>{
 e.preventDefault();if(!sb||!user)return message('profileMsg','Your account session is not ready. Refresh and try again.');
 const username=$('profileUsername').value.trim().toLowerCase();
 if(username&&!/^[a-z0-9_]{3,30}$/.test(username))return message('profileMsg','Username must be 3–30 characters using letters, numbers or underscores.');
 const payload={id:user.id,display_name:$('profileName').value.trim()||null,username:username||null,location:$('profileLocation').value.trim()||null,favorite_genre:$('profileGenre').value.trim()||null,bio:$('profileBio').value.trim()||null,updated_at:new Date().toISOString()};
 const {data,error}=await sb.from('profiles').upsert(payload,{onConflict:'id'}).select().maybeSingle();if(error)return message('profileMsg',error.message);profile=data||payload;await loadProfile();await loadNotifications();message('profileMsg','Profile saved successfully.',true);
});

$('signinForm').addEventListener('submit',async e=>{
 e.preventDefault();if(!sb)return message('authMsg','Account service is not ready. Please wait a moment and try again.');
 message('authMsg','Signing in…',true);
 const {data,error}=await sb.auth.signInWithPassword({email:$('signinEmail').value.trim(),password:$('signinPassword').value});
 if(error){let m=error.message;if(/invalid login credentials/i.test(m))m='Email or password is incorrect. If you just created the account, confirm your email first.';return message('authMsg',m)}
 user=data.user;showDashboard();await loadProfile();initialPanel();message('profileMsg','Signed in successfully.',true);
});

$('signupForm').addEventListener('submit',async e=>{
 e.preventDefault();if(!sb)return message('authMsg','Account service is not ready. Please wait a moment and try again.');
 const email=$('signupEmail').value.trim(),password=$('signupPassword').value,name=$('signupName').value.trim(),username=$('signupUsername').value.trim().toLowerCase();
 if(username&&!/^[a-z0-9_]{3,30}$/.test(username))return message('authMsg','Username must be 3–30 characters using letters, numbers or underscores.');
 const {data,error}=await sb.auth.signUp({email,password,options:{data:{display_name:name,username},emailRedirectTo:location.origin+location.pathname}});
 if(error)return message('authMsg',error.message);
 if(data.user&&!data.session)return message('authMsg','Account created. Check your email, confirm it, then return here to sign in.',true);
 if(data.user){user=data.user;await sb.from('profiles').upsert({id:user.id,display_name:name,username:username||null},{onConflict:'id'});showDashboard();await loadProfile();initialPanel();}
});

$('resetForm').addEventListener('submit',async e=>{
 e.preventDefault();if(!sb)return message('authMsg','Account service is not ready.');
 const {error}=await sb.auth.resetPasswordForEmail($('resetEmail').value.trim(),{redirectTo:location.origin+location.pathname+'?recovery=1'});
 if(error)return message('authMsg',error.message);message('authMsg','Password reset email sent. Check your inbox and open the secure link.',true);
});

$('changePassword').addEventListener('click',async()=>{if(!sb)return;const a=$('newPassword').value,b=$('confirmPassword').value;if(a.length<8||a!==b)return message('securityMsg','Passwords must match and contain at least 8 characters.');const {error}=await sb.auth.updateUser({password:a});if(error)return message('securityMsg',error.message);$('newPassword').value='';$('confirmPassword').value='';message('securityMsg','Password changed successfully.',true);});
$('changeEmail').addEventListener('click',async()=>{if(!sb)return;const email=$('newEmail').value.trim();if(!email)return message('securityMsg','Enter a new email address.');const {error}=await sb.auth.updateUser({email});if(error)return message('securityMsg',error.message);message('securityMsg','Email update requested. Check the confirmation email.',true);});

$('uploadAvatar').addEventListener('click',async()=>{
 if(!sb||!user)return;const file=$('avatarFile').files[0];if(!file)return message('profileMsg','Choose a JPG, PNG or WebP image first.');if(file.size>2*1024*1024)return message('profileMsg','Profile picture must be 2 MB or smaller.');if(!/^image\/(jpeg|png|webp)$/.test(file.type))return message('profileMsg','Use JPG, PNG or WebP.');
 const ext=(file.name.split('.').pop()||'jpg').toLowerCase();const path=user.id+'/avatar.'+ext;const {error:up}=await sb.storage.from('avatars').upload(path,file,{upsert:true,contentType:file.type,cacheControl:'3600'});if(up)return message('profileMsg',up.message);const {data}=sb.storage.from('avatars').getPublicUrl(path);const {error}=await sb.from('profiles').update({avatar_url:data.publicUrl+'?v='+Date.now(),updated_at:new Date().toISOString()}).eq('id',user.id);if(error)return message('profileMsg',error.message);$('avatarFile').value='';await loadProfile();message('profileMsg','Profile picture updated.',true);
});

$('claimReward').addEventListener('click',async()=>{if(!sb)return;const {data,error}=await sb.rpc('claim_daily_reward');if(error)return message('rewardMsg',error.message);$('coinCount').textContent=Number(data?.coins||0).toLocaleString();$('rewardCoins').textContent=Number(data?.coins||0).toLocaleString();$('streakCount').textContent=Number(data?.streak||0)+' days';message('rewardMsg',data?.claimed?`You earned ${data.amount||0} coins today!`:'Daily reward already claimed today.',true);});

$('submitReport')?.addEventListener('click',async()=>{
  if(!sb||!user)return message('reportMsg','Please sign in before sending a report.');
  const category=$('reportCategory').value;
  const subject=$('reportSubject').value.trim();
  const reportMessage=$('reportMessage').value.trim();
  if(!subject||!reportMessage)return message('reportMsg','Subject and message are required.');
  const result=await sb.from('support_reports').insert({
    user_id:user.id,
    reporter_email:user.email||null,
    reporter_name:profile?.display_name||profile?.username||user.email?.split('@')[0]||null,
    category,
    subject,
    message:reportMessage,
    page_url:location.href
  });
  if(result.error)return message('reportMsg',result.error.message);
  $('reportSubject').value='';
  $('reportMessage').value='';
  message('reportMsg','Report sent successfully. Thank you.',true);
});

async function loadNotifications(){
  if(!sb||!user)return;
  const list=$('notificationsList');
  if(!list)return;
  const result=await sb.from('notifications').select('id,title,message,type,read_at,created_at').order('created_at',{ascending:false}).limit(50);
  if(result.error){list.innerHTML=`<p class="help">${result.error.message}</p>`;return;}
  const rows=result.data||[];
  if(!rows.length){list.innerHTML='<p class="help">No notifications yet.</p>';return;}
  list.innerHTML=rows.map(n=>`<div class="card" style="margin-bottom:10px"><div class="section-title"><h3>${String(n.title||'Notification').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]))}</h3><span class="badge">${new Date(n.created_at).toLocaleString()}</span></div><p class="help">${String(n.message||'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]))}</p></div>`).join('');
  const unread=rows.filter(n=>!n.read_at).map(n=>n.id);
  if(unread.length)await sb.from('notifications').update({read_at:new Date().toISOString()}).in('id',unread);
}

$('refreshNotifications')?.addEventListener('click',loadNotifications);

async function syncLibrary(){if(!window.DecanAccount?.sync)return message('profileMsg','Cloud synchronization is unavailable.');$('syncStatus').textContent='Syncing…';try{await window.DecanAccount.sync();$('syncStatus').textContent='Synced';counts();message('profileMsg','Your library is synchronized.',true)}catch(e){$('syncStatus').textContent='Error';message('profileMsg',e.message||'Sync failed.')}}
$('syncNow').addEventListener('click',syncLibrary);$('librarySync').addEventListener('click',syncLibrary);
$('copyUserId').addEventListener('click',async()=>{try{await navigator.clipboard.writeText(user.id);message('profileMsg','Account ID copied.',true)}catch{message('profileMsg','Clipboard access is unavailable on this browser.')}});
$('logout').addEventListener('click',async()=>{if(sb)await sb.auth.signOut();location.href='index.html'});

async function loadRecommendations(){const box=$('recommendations');box.innerHTML='<div class="empty" style="grid-column:1/-1">Loading recommendations…</div>';try{const r=await fetch('/api/tmdb/trending/movie/week');if(!r.ok)throw new Error('Recommendation service unavailable.');const data=await r.json();const items=(data.results||[]).filter(x=>x.poster_path).slice(0,10);if(!items.length)return box.innerHTML='<div class="empty" style="grid-column:1/-1">No recommendations available right now.</div>';box.innerHTML=items.map(x=>`<a class="media-card" href="index.html?title=movie&id=${encodeURIComponent(x.id)}"><img loading="lazy" src="https://image.tmdb.org/t/p/w342${x.poster_path}" alt="${esc(x.title||'Movie')} poster"><div class="mi"><b>${esc(x.title||'Untitled')}</b><span>${esc((x.release_date||'').slice(0,4)||'Movie')} · ★ ${Number(x.vote_average||0).toFixed(1)}</span></div></a>`).join('')}catch(e){box.innerHTML='<div class="empty" style="grid-column:1/-1">'+esc(e.message)+'</div>'}}
$('refreshRecommendations').addEventListener('click',loadRecommendations);

async function boot(){
 const result=await (window.DecanAccount?.ready||Promise.resolve({client:null,user:null}));sb=result.client;user=result.user;
 if(!sb){showAuth();message('authMsg','Account service is unavailable. Add SUPABASE_URL and SUPABASE_ANON_KEY to Vercel Environment Variables, or configure the public anon key in supabase-config.js.');return;}
 if(user){showDashboard();await loadProfile();initialPanel();}else showAuth();
 window.addEventListener('decan:auth-changed',async e=>{user=e.detail?.user||null;if(user){showDashboard();await loadProfile();initialPanel();}else showAuth();});
 const recovery=new URLSearchParams(location.search).get('recovery');
 if(recovery){setAuthTab('signin');message('authMsg','Recovery link accepted. Sign in after setting your new password, or use Security if a recovery session is active.',true);}
}
boot();
})();

window.addEventListener('decan:account-ready',()=>loadNotifications());
