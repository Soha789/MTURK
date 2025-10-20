<?php /* dashboard.php - Internal CSS & JS, Worker Dashboard */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Dashboard • MicroTasks</title>
<style>
  :root{
    --bg:#0b1022; --ink:#e5e7eb; --muted:#9ca3af; --border:#203057;
    --brand:#7c3aed; --ok:#22c55e; --warn:#f59e0b; --err:#ef4444;
  }
  *{box-sizing:border-box}
  body{margin:0; font-family:ui-sans-serif,system-ui,Segoe UI,Roboto,Arial; background:radial-gradient(1200px 800px at 90% -10%, #1e293b55, transparent), var(--bg); color:var(--ink);}
  header{position:sticky; top:0; z-index:50; background:linear-gradient(145deg,#0b1022,#0b122a); border-bottom:1px solid var(--border); padding:14px 18px; display:flex; align-items:center; gap:16px}
  .logo{font-weight:900}
  nav{margin-left:auto; display:flex; gap:10px; align-items:center}
  .btn,.btn-sm{appearance:none; border:1px solid #2a3a66; background:linear-gradient(135deg,#121a38,#0d142b); color:#dbeafe; padding:10px 14px; border-radius:12px; cursor:pointer; font-weight:700}
  .btn.brand{background:linear-gradient(135deg,var(--brand),#6d28d9); border-color:#5b21b6; color:white; box-shadow:0 6px 18px #7c3aed55}
  .btn-sm{padding:8px 10px; font-size:12px}
  .avatar{background:#1f2a4d; border:1px solid #2a3a66; color:#cbd5e1; padding:8px 12px; border-radius:999px; font-size:12px}
  main{width:min(1200px,100%); margin:24px auto; padding:0 16px; display:grid; grid-template-columns:1fr .9fr; gap:18px}
  .panel{background:linear-gradient(145deg,#0b1022,#0b122a); border:1px solid var(--border); border-radius:20px; padding:18px}
  h2{margin:0 0 10px}
  .tabs{display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px}
  .tab{padding:8px 12px; border-radius:12px; border:1px solid #28406f; background:#0d1630; cursor:pointer; font-size:12px}
  .tab.active{outline:2px solid #60a5fa}
  .list{display:grid; gap:10px}
  .item{display:flex; justify-content:space-between; gap:12px; align-items:center; background:#0c1329; border:1px solid #223764; padding:12px; border-radius:14px}
  .muted{color:var(--muted)}
  .badge{font-size:12px; padding:6px 10px; border-radius:999px; border:1px solid #345089}
  .applied{background:#0c1a35}
  .accepted{background:#0e2a1a; border-color:#1f6f49}
  .completed{background:#2b1731; border-color:#7a2e7e}
  .stat{display:flex; justify-content:space-between; background:#0d1630; border:1px solid #223764; padding:10px 12px; border-radius:12px; margin-bottom:8px}
  .profile label{font-size:12px; color:#cbd5e1}
  .profile input, .profile textarea{
    width:100%; padding:10px 12px; border-radius:12px; border:1px solid #26355b; background:#0a132b; color:#e2e8f0; outline:none; margin-top:6px
  }
  .profile textarea{min-height:100px}
  @media (max-width:1000px){ main{grid-template-columns:1fr} }
</style>
</head>
<body>
<header>
  <div class="logo">⚡ MicroTasks</div>
  <nav>
    <button class="btn-sm" onclick="goto('index.php')">Marketplace</button>
    <span id="who" class="avatar"></span>
    <button class="btn brand" onclick="logout()">Logout</button>
  </nav>
</header>

<main>
  <section class="panel">
    <h2>My Tasks</h2>
    <div class="tabs">
      <div id="tab-applied" class="tab active" onclick="switchTab('applied')">Applied</div>
      <div id="tab-accepted" class="tab" onclick="switchTab('accepted')">Accepted</div>
      <div id="tab-completed" class="tab" onclick="switchTab('completed')">Completed</div>
    </div>
    <div id="list" class="list"></div>
  </section>

  <aside class="panel">
    <h2>Overview</h2>
    <div class="stat"><span>Total Earnings</span><strong id="earn">$0.00</strong></div>
    <div class="stat"><span>Completed</span><strong id="comp">0</strong></div>
    <div class="stat"><span>Rating</span><strong id="rate">5.0</strong></div>
    <div class="stat"><span>Withdraw</span><button class="btn-sm" onclick="withdraw()">Request Payout</button></div>

    <div style="height:10px"></div>
    <h2>Profile</h2>
    <div class="profile">
      <label>Name</label><input id="pName"/>
      <label>Country</label><input id="pCountry" placeholder="Saudi Arabia"/>
      <label>Bio</label><textarea id="pBio" placeholder="Short intro..."></textarea>
      <div style="height:8px"></div>
      <button class="btn brand" onclick="saveProfile()">Save Profile</button>
    </div>
  </aside>
</main>

<script>
function load(k,f){ try{return JSON.parse(localStorage.getItem(k))??f}catch(e){return f} }
function save(k,v){ localStorage.setItem(k, JSON.stringify(v)); }
function goto(f){ location.href=f; }
function logout(){ localStorage.removeItem('currentUser'); location.href='login.php'; }

let curr = null, tab='applied';

function init(){
  curr = load('currentUser', null);
  if(!curr){ location.href='login.php'; return; }
  document.getElementById('who').textContent = (curr.name||'User')+' • '+curr.role;
  if(curr.role!=='worker'){
    // Requesters can still see dashboard but with note
    alert('Tip: Switch to a worker account to track applications.');
  }
  renderStats();
  renderProfileForm();
  renderList();
}
function renderStats(){
  document.getElementById('earn').textContent = '$'+Number(curr.earnings||0).toFixed(2);
  document.getElementById('comp').textContent = curr.completed||0;
  document.getElementById('rate').textContent = (curr.rating||5).toFixed(1);
}
function renderProfileForm(){
  document.getElementById('pName').value = curr.name||'';
  document.getElementById('pCountry').value = curr.country||'';
  document.getElementById('pBio').value = curr.bio||'';
}
function saveProfile(){
  const users = load('users', []);
  const idx = users.findIndex(u=>u.email===curr.email);
  if(idx>=0){
    users[idx].name = document.getElementById('pName').value.trim();
    users[idx].country = document.getElementById('pCountry').value.trim();
    users[idx].bio = document.getElementById('pBio').value.trim();
    save('users', users);
    curr = users[idx]; save('currentUser', curr);
    alert('Profile saved!');
    document.getElementById('who').textContent = curr.name+' • '+curr.role;
  }else{
    // guest route
    curr.name = document.getElementById('pName').value.trim();
    curr.country = document.getElementById('pCountry').value.trim();
    curr.bio = document.getElementById('pBio').value.trim();
    save('currentUser', curr);
    alert('Profile saved (guest).');
  }
}
function switchTab(t){
  tab=t;
  ['applied','accepted','completed'].forEach(x=>{
    document.getElementById('tab-'+x).classList.toggle('active', x===t);
  });
  renderList();
}
function renderList(){
  const list = document.getElementById('list'); list.innerHTML='';
  const apps = load('applications', []).filter(a=>a.workerEmail===curr.email);
  const tasks = load('tasks', []);
  const sel = apps.filter(a=>a.status===tab);
  if(sel.length===0){
    const empty = document.createElement('div'); empty.className='muted'; empty.textContent='No tasks here yet.';
    list.appendChild(empty); return;
  }
  sel.forEach(a=>{
    const t = tasks.find(z=>z.id===a.taskId) || {title:a.title||'Task', pay:a.pay||0, deadline:'—'};
    const row = document.createElement('div'); row.className='item';
    const left = document.createElement('div');
    left.innerHTML = `<div style="font-weight:800">${t.title}</div>
      <div class="muted">$${(t.pay||0).toFixed(2)} • Due: ${new Date(t.deadline||Date.now()).toLocaleDateString()}</div>`;
    const right = document.createElement('div'); right.style.display='flex'; right.style.gap='8px'; right.style.alignItems='center';
    const badge = document.createElement('span'); badge.className='badge '+a.status; badge.textContent=a.status.toUpperCase();
    right.appendChild(badge);

    // actions
    if(a.status==='applied'){
      const accept = document.createElement('button'); accept.className='btn-sm'; accept.textContent='Mark Accepted';
      accept.onclick=()=>updateStatus(a.taskId,'accepted');
      right.appendChild(accept);
    }
    if(a.status==='accepted'){
      const done = document.createElement('button'); done.className='btn-sm'; done.textContent='Mark Completed';
      done.onclick=()=>completeTask(a.taskId);
      right.appendChild(done);
    }
    if(a.status==='completed'){
      const review = document.createElement('button'); review.className='btn-sm'; review.textContent='Rate Task ★';
      review.onclick=()=>rateTask(a.taskId);
      right.appendChild(review);
    }

    const openBtn = document.createElement('button'); openBtn.className='btn-sm'; openBtn.textContent='View in Marketplace';
    openBtn.onclick=()=>{ location.href='index.php'; setTimeout(()=>alert('Scroll to find the task or use category filters.'), 200); };
    right.appendChild(openBtn);

    row.append(left,right); list.appendChild(row);
  });
}
function updateStatus(taskId, status){
  const apps = load('applications', []);
  const idx = apps.findIndex(a=>a.taskId===taskId && a.workerEmail===curr.email);
  if(idx>=0){ apps[idx].status=status; save('applications', apps); renderList(); }
}
function completeTask(taskId){
  const apps = load('applications', []);
  const idx = apps.findIndex(a=>a.taskId===taskId && a.workerEmail===curr.email);
  if(idx>=0){
    apps[idx].status='completed';
    // credit earnings
    const add = Number(apps[idx].pay||0);
    curr.earnings = Number(curr.earnings||0) + add;
    curr.completed = Number(curr.completed||0) + 1;
    const users = load('users', []);
    const uidx = users.findIndex(u=>u.email===curr.email);
    if(uidx>=0){ users[uidx]=curr; save('users', users); }
    save('currentUser', curr);
    save('applications', apps);
    renderStats();
    renderList();
    alert('Task marked completed! Earnings updated.');
  }
}
function rateTask(taskId){
  const tasks = load('tasks', []);
  const t = tasks.find(z=>z.id===taskId); if(!t){ alert('Task not found.'); return; }
  let s = prompt('Rate this task (1-5 stars):', '5');
  if(!s) return; s = Math.max(1, Math.min(5, Number(s)));
  t.rating = Number(((t.rating||5) + s)/2).toFixed(1);
  save('tasks', tasks);
  alert('Thanks for rating!');
}
function withdraw(){
  const amt = Number(curr.earnings||0);
  if(amt<=0){ alert('No earnings to withdraw.'); return; }
  alert('Payout requested for $'+amt.toFixed(2)+'. (Demo only)');
}
init();
</script>
</body>
</html>
