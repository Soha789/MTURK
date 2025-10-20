<?php /* login.php - Internal CSS & JS only */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login • MicroTasks</title>
<style>
  :root{
    --bg:#0f172a; --panel:#0b1022; --ink:#e5e7eb; --muted:#94a3b8;
    --brand:#7c3aed; --card:#111836; --chip:#1f2a4d;
  }
  body{margin:0; font-family:ui-sans-serif,system-ui,Segoe UI,Roboto,Arial; background:var(--bg); color:var(--ink); min-height:100vh; display:grid; place-items:center; padding:24px}
  .card{width:min(460px,100%); background:linear-gradient(145deg,#0b1022,#0b122a); border:1px solid #1a2444; border-radius:24px; padding:24px; box-shadow:0 8px 30px #0006}
  h1{margin:0 0 8px}
  p{margin:0 0 16px; color:var(--muted)}
  label{font-size:13px; color:#cbd5e1}
  input{width:100%; padding:12px 14px; border-radius:14px; border:1px solid #26355b; background:#0a132b; color:#e2e8f0; outline:none; margin-top:6px}
  input:focus{border-color:#3b82f6}
  .btn{width:100%; margin-top:12px; appearance:none; border:0; padding:12px 16px; border-radius:14px; cursor:pointer; font-weight:700;
       background:linear-gradient(135deg,var(--brand),#6d28d9); color:white; box-shadow:0 8px 20px #7c3aed55}
  .row{display:flex; justify-content:space-between; gap:12px; margin-top:8px}a
  a.link{color:#93c5fd; text-decoration:none}
  .err{background:#3b0a0a; border:1px solid #7f1d1d; color:#fecaca; padding:10px 12px; border-radius:12px; display:none; margin-bottom:12px}
</style>
</head>
<body>
  <div class="card">
    <h1>Welcome back</h1>
    <p>Log in to access tasks and your dashboard.</p>
    <div id="err" class="err"></div>
    <label>Email</label>
    <input id="email" type="email" placeholder="you@example.com"/>
    <label style="margin-top:10px">Password</label>
    <input id="pass" type="password" placeholder="••••"/>
    <button class="btn" onclick="login()">Log In</button>
    <div class="row">
      <a class="link" href="javascript:void(0)" onclick="goSignup()">Create account</a>
      <a class="link" href="javascript:void(0)" onclick="guest()">Continue as guest</a>
    </div>
  </div>

<script>
function load(k, f){ try{return JSON.parse(localStorage.getItem(k))??f}catch(e){return f} }
function save(k,v){ localStorage.setItem(k, JSON.stringify(v)); }
function goSignup(){ location.href='signup.php'; }

function login(){
  const email = (document.getElementById('email').value||'').trim().toLowerCase();
  const pass  = (document.getElementById('pass').value||'').trim();
  const err = document.getElementById('err'); err.style.display='none';

  const users = load('users', []);
  const u = users.find(x => x.email===email && x.pass===pass);
  if(!u){ err.textContent='Invalid email or password.'; err.style.display='block'; return; }
  save('currentUser', u);
  // create seed tasks if missing
  if(!localStorage.getItem('seeded_v1')){
    localStorage.setItem('seeded_v1','1');
  }
  location.href='index.php';
}

function guest(){
  const g = {name:'Guest', role:'worker', email:'guest@demo', pass:'', createdAt:Date.now(), earnings:0, bio:'', country:'', rating:5, completed:0};
  save('currentUser', g);
  location.href='index.php';
}
</script>
</body>
</html>
