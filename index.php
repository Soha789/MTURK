<?php /* index.php - Internal CSS & JS, Task Marketplace */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>MicroTasks • Marketplace</title>
<style>
  :root{
    --bg:#0b1022; --ink:#e5e7eb; --muted:#9ca3af; --border:#203057;
    --brand:#7c3aed; --brand2:#22c55e; --surface:#0f1730; --card:#0c1329; --chip:#1a2550;
    --accent:#60a5fa;
  }
  *{box-sizing:border-box}
  body{margin:0; font-family:ui-sans-serif,system-ui,Segoe UI,Roboto,Arial; background:radial-gradient(1200px 800px at 10% -10%, #1e293b55, transparent), var(--bg); color:var(--ink);}
  header{
    position:sticky; top:0; z-index:50; background:linear-gradient(145deg,#0b1022,#0b122a);
    border-bottom:1px solid var(--border); padding:14px 18px; display:flex; align-items:center; gap:16px;
  }
  .logo{font-weight:900; letter-spacing:.3px}
  nav{margin-left:auto; display:flex; gap:10px; align-items:center}
  .btn, .btn-sm{
    appearance:none; border:1px solid #2a3a66; background:linear-gradient(135deg,#121a38,#0d142b);
    color:#dbeafe; padding:10px 14px; border-radius:12px; cursor:pointer; font-weight:700;
  }
  .btn.brand{background:linear-gradient(135deg,var(--brand),#6d28d9); border-color:#5b21b6; color:white; box-shadow:0 6px 18px #7c3aed55}
  .btn-sm{padding:8px 10px; font-size:12px}
  .avatar{background:#1f2a4d; border:1px solid #2a3a66; color:#cbd5e1; padding:8px 12px; border-radius:999px; font-size:12px}
  main{width:min(1200px,100%); margin:24px auto; padding:0 16px; display:grid; grid-template-columns:1.2fr .8fr; gap:20px}
  .intro, .side{background:linear-gradient(145deg,#0b1022,#0b122a); border:1px solid var(--border); border-radius:20px; padding:18px}
  h1{margin:0 0 6px; font-size:26px}
  .muted{color:var(--muted)}
  .filters{display:flex; gap:10px; flex-wrap:wrap; margin-top:10px}
  .chip{background:var(--chip); border:1px solid #2a3a66; color:#cbd5e1; padding:8px 12px; border-radius:999px; font-size:12px; cursor:pointer}
  .chip.active{outline:2px solid var(--accent)}
  .grid{margin-top:14px; display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:14px}
  .card{
    background:linear-gradient(180deg,#0c1329,#0c142e); border:1px solid #203057; border-radius:18px; padding:16px;
    display:flex; flex-direction:column; gap:8px; box-shadow:0 6px 24px #0005;
  }
  .title{font-weight:800}
  .row{display:flex; gap:10px; align-items:center; flex-wrap:wrap}
  .pay{background:#0d1a37; border:1px solid #28406f; padding:6px 10px; border-radius:10px; font-size:12px}
  .deadline{font-size:12px; color:#cbd5e1}
  .actions{margin-top:auto; display:flex; gap:8px}
  .empty{padding:20px; text-align:center; color:#a3b2d7}
  .side h3{margin:0 0 8px}
  .stat{display:flex; justify-content:space-between; background:#0d1630; border:1px solid #223764; padding:10px 12px; border-radius:12px; margin-bottom:8px}
  .banner{
    background:linear-gradient(135deg,#111b3b,#0f1936 60%, #0a122a);
    border:1px dashed #304171; color:#cbd5e1; border-radius:16px; padding:14px; font-size:14px; margin:12px 0;
  }
  .modal{
    position:fixed; inset:0; background:#0008; display:none; align-items:center; justify-content:center; padding:20px;
  }
  .modal .box{width:min(540px,100%); background:#0c1329; border:1px solid #223764; border-radius:18px; padding:16px}
  label{font-size:12px; color:#cbd5e1}
  input, select, textarea{
    width:100%; padding:10px 12px; border-radius:12px; border:1px solid #26355b; background:#0a132b; color:#e2e8f0; outline:none; margin-top:6px
  }
  textarea{min-height:110px; resize:vertical}
  @media (max-width:1000px){ main{grid-template-columns:1fr} .grid{grid-template-columns:1fr} }
</style>
</head>
<body>
<header>
  <div class="logo">⚡ MicroTasks</div>
  <nav>
    <button class="btn-sm" onclick="goto('index.php')">Marketplace</button>
    <button class="btn-sm" onclick="goto('dashboard.php')">Dashboard</button>
    <span id="who" class="avatar"></span>
    <button class="btn brand" onclick="logout()">Logout</button>
  </nav>
</header>

<main>
  <section class="intro">
    <h1>Task Marketplace</h1>
    <div class="muted">Browse tasks, view details, and apply. Requesters can post tasks from the sidebar.</div>
    <div class="filters" id="cats"></div>
    <div id="taskGrid" class="grid"></div>
    <div id="empty" class="empty" style="display:none">No tasks found for this category.</div>
  </section>

  <aside class="side">
    <h3>Your Overview</h3>
    <div class="stat"><span>Role</span><strong id="roleStat">—</strong></div>
    <div class="stat"><span>Total Earnings</span><strong id="earnStat">$0.00</strong></div>
    <div class="stat"><span>Completed</span><strong id="compStat">0</strong></div>
    <div class="banner">Requester? Post a new task for the crowd 👇</div>
    <button class="btn brand" onclick="openPost()">+ Post Task</button>
  </aside>
</main>

<!-- Task Detail Modal -->
<div class="modal" id="detailModal">
  <div class="box">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px">
      <h3 id="dTitle" style="margin:0"></h3>
      <button class="btn-sm" onclick="closeDetail()">Close</button>
    </div>
    <div class="row" style="margin-bottom:8px">
      <div class="pay" id="dPay"></div>
      <div class="deadline" id="dDeadline"></div>
      <div class="deadline" id="dCat"></div>
      <div class="deadline" id="dRating"></div>
    </div>
    <p id="dDesc" class="muted" style="line-height:1.6"></p>
    <div class="row actions">
      <button class="btn brand" id="applyBtn">Apply</button>
      <button class="btn" onclick="goto('dashboard.php')">Go to Dashboard</button>
    </div>
  </div>
</div>

<!-- Post Task Modal -->
<div class="modal" id="postModal">
  <div class="box">
    <div style="display:flex; justify-content:space-between; align-items:center">
      <h3 style="margin:0">Post a New Task</h3>
      <button class="btn-sm" onclick="closePost()">Close</button>
    </div>
    <div style="height:10px"></div>
    <label>Title</label><input id="pTitle" placeholder="e.g., Tag 100 images by color"/>
    <label>Category</label>
    <select id="pCat">
      <option>Data Entry</option><option>Surveys</option><option>Transcription</option><option>Image Tagging</option><option>Categorization</option>
    </select>
    <label>Payment (USD)</label><input id="pPay" type="number" step="0.01" min="0"/>
    <label>Deadline</label><input id="pDeadline" type="date"/>
    <label>Description</label><textarea id="pDesc" placeholder="Explain the task clearly..."></textarea>
    <div class="row" style="margin-top:10px">
      <button class="btn brand" onclick="submitTask()">Publish Task</button>
      <button class="btn" onclick="closePost()">Cancel</button>
    </div>
  </div>
</div>

<script>
/* Utilities */
function load(k,f){ try{return JSON.parse(localStorage.getItem(k))??f}catch(e){return f} }
function save(k,v){ localStorage.setItem(k, JSON.stringify(v)); }
function goto(f){ location.href=f; }

function logout(){ localStorage.removeItem('currentUser'); location.href='login.php'; }

function ensureSeeds(){
  if(!localStorage.getItem('seeded_v1')){
    const tasks = [
      {id:'T-1001', title:'Categorize 50 product images', pay:1.50, deadline:'2025-10-25', category:'Image Tagging', desc:'Look at each product image and select the most relevant category.', postedBy:'demo@requester.com', rating:4.8},
      {id:'T-1002', title:'Short survey: shopping habits', pay:0.75, deadline:'2025-10-22', category:'Surveys', desc:'Answer 8 multiple-choice questions about your weekly shopping.', postedBy:'demo@requester.com', rating:4.6},
      {id:'T-1003', title:'Transcribe 2-minute audio', pay:2.20, deadline:'2025-10-21', category:'Transcription', desc:'Transcribe a short audio clip with clear speech.', postedBy:'demo@requester.com', rating:4.9},
    ]; save('tasks', tasks); localStorage.setItem('seeded_v1','1');
  }
  if(!load('applications', null)) save('applications', []); // {taskId, workerEmail, status: 'applied'|'accepted'|'completed', pay}
}

const categories = ['All','Data Entry','Surveys','Transcription','Image Tagging','Categorization'];
let activeCat = 'All';
let viewTask = null;

function renderUser(){
  const u = load('currentUser', null);
  if(!u){ location.href='login.php'; return; }
  document.getElementById('who').textContent = (u.name||'User') + ' • ' + u.role;
  document.getElementById('roleStat').textContent = u.role;
  document.getElementById('earnStat').textContent = '$' + Number(u.earnings||0).toFixed(2);
  document.getElementById('compStat').textContent = u.completed||0;
  // Requester banner visible by default; workers can still open Post (blocked in JS)
}

function renderCats(){
  const con = document.getElementById('cats'); con.innerHTML='';
  categories.forEach(c=>{
    const span = document.createElement('span'); span.className='chip'+(c===activeCat?' active':''); span.textContent=c;
    span.onclick=()=>{activeCat=c; renderCats(); renderGrid();}; con.appendChild(span);
  });
}

function formatDate(d){ try{ return new Date(d).toLocaleDateString(); }catch(e){ return d; } }

function renderGrid(){
  const grid = document.getElementById('taskGrid'); const empty = document.getElementById('empty');
  const tasks = load('tasks', []);
  const filtered = activeCat==='All' ? tasks : tasks.filter(t=>t.category===activeCat);
  grid.innerHTML='';
  filtered.forEach(t=>{
    const card = document.createElement('div'); card.className='card';
    const title = document.createElement('div'); title.className='title'; title.textContent=t.title;
    const row = document.createElement('div'); row.className='row';
    const pay = document.createElement('div'); pay.className='pay'; pay.textContent = '$'+t.pay.toFixed(2);
    const deadline = document.createElement('div'); deadline.className='deadline'; deadline.textContent = 'Due: '+formatDate(t.deadline);
    const cat = document.createElement('div'); cat.className='deadline'; cat.textContent = t.category;
    row.append(pay, deadline, cat);
    const desc = document.createElement('div'); desc.className='muted'; desc.textContent = t.desc.slice(0,120) + (t.desc.length>120?'...':'');
    const actions = document.createElement('div'); actions.className='actions';
    const b1=document.createElement('button'); b1.className='btn brand'; b1.textContent='View Details'; b1.onclick=()=>openDetail(t.id);
    const b2=document.createElement('button'); b2.className='btn'; b2.textContent='Apply'; b2.onclick=()=>applyTask(t.id);
    actions.append(b1,b2);
    card.append(title,row,desc,actions);
    grid.appendChild(card);
  });
  empty.style.display = filtered.length? 'none':'block';
}

function openDetail(id){
  const t = load('tasks', []).find(x=>x.id===id); if(!t) return;
  viewTask = t;
  document.getElementById('dTitle').textContent = t.title;
  document.getElementById('dPay').textContent = '$'+t.pay.toFixed(2);
  document.getElementById('dDeadline').textContent = 'Due: '+formatDate(t.deadline);
  document.getElementById('dCat').textContent = t.category;
  document.getElementById('dRating').textContent = '★ '+(t.rating||4.7).toFixed(1);
  document.getElementById('dDesc').textContent = t.desc;
  document.getElementById('applyBtn').onclick = ()=>applyTask(id, true);
  document.getElementById('detailModal').style.display='flex';
}
function closeDetail(){ document.getElementById('detailModal').style.display='none'; }

function applyTask(id, close){
  const u = load('currentUser', null); if(!u){ alert('Please log in first.'); return; }
  if(u.role!=='worker'){ alert('Only workers can apply for tasks. Switch to a worker account.'); return; }
  const apps = load('applications', []);
  if(apps.find(a=>a.taskId===id && a.workerEmail===u.email)){ alert('You already applied to this task.'); return; }
  const t = load('tasks', []).find(x=>x.id===id);
  apps.push({taskId:id, workerEmail:u.email, status:'applied', pay:t?.pay||0, title:t?.title||'Task'});
  save('applications', apps);
  alert('Applied! Track it in your dashboard.');
  if(close) closeDetail();
}

function openPost(){
  const u = load('currentUser', null);
  if(!u || u.role!=='requester'){ alert('Only requesters can post tasks.'); return; }
  document.getElementById('postModal').style.display='flex';
}
function closePost(){ document.getElementById('postModal').style.display='none'; }

function submitTask(){
  const title = document.getElementById('pTitle').value.trim();
  const category = document.getElementById('pCat').value;
  const pay = Number(document.getElementById('pPay').value||0);
  const deadline = document.getElementById('pDeadline').value;
  const desc = document.getElementById('pDesc').value.trim();
  const u = load('currentUser', null);
  if(!title || !pay || !deadline || !desc){ alert('Please fill all fields.'); return; }
  const id = 'T-' + Math.floor(1000 + Math.random()*9000);
  const tasks = load('tasks', []);
  tasks.unshift({id,title,category,pay,deadline,desc,postedBy:u.email, rating: (4.5 + Math.random()*0.5)});
  save('tasks', tasks);
  closePost(); renderGrid(); alert('Task published!');
}

/* Init */
(function(){
  ensureSeeds();
  renderUser();
  renderCats();
  renderGrid();
})();
</script>
</body>
</html>
