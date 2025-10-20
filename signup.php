<?php /* signup.php - Internal CSS & JS only */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Sign Up • MicroTasks</title>
<style>
  :root{
    --bg:#0f172a; --panel:#0b1022; --ink:#e5e7eb; --muted:#94a3b8;
    --brand:#7c3aed; --brand2:#22c55e; --card:#111836; --chip:#1f2a4d;
    --ok:#16a34a; --warn:#f59e0b; --err:#ef4444;
  }
  *{box-sizing:border-box}
  body{
    margin:0; font-family:ui-sans-serif,system-ui,Segoe UI,Roboto,Arial;
    background: radial-gradient(1200px 600px at 80% -20%, #1e293b55, transparent) , var(--bg);
    color:var(--ink); display:grid; min-height:100vh; place-items:center; padding:24px;
  }
  .wrap{
    width:min(900px,100%); display:grid; grid-template-columns:1.1fr 1fr; gap:24px;
  }
  .hero{
    background:linear-gradient(145deg,#0a0f22,#0b1330 60%, #0a0f22);
    border:1px solid #18203b; border-radius:24px; padding:28px;
    box-shadow:0 8px 40px #0006;
  }
  .hero h1{margin:0 0 8px; font-size:34px; letter-spacing:.3px}
  .hero p{margin:0; color:var(--muted)}
  .badges{margin-top:18px; display:flex; gap:10px; flex-wrap:wrap}
  .chip{background:var(--chip); color:#cbd5e1; padding:8px 12px; border-radius:999px; font-size:12px; border:1px solid #24385c}
  .panel{
    background:linear-gradient(145deg,#0b1022,#0b122a);
    border:1px solid #1a2444; border-radius:24px; padding:24px;
    box-shadow:0 8px 30px #0006;
  }
  h2{margin:0 0 12px; font-size:22px}
  form{display:grid; gap:12px}
  label{font-size:13px; color:#cbd5e1}
  input,select{
    width:100%; padding:12px 14px; border-radius:14px; border:1px solid #26355b; background:#0a132b; color:#e2e8f0; outline:none;
  }
  input:focus,select:focus{border-color:#3b82f6}
  .row{display:grid; grid-template-columns:1fr 1fr; gap:12px}
  .btn{
    appearance:none; border:0; padding:12px 16px; border-radius:14px; cursor:pointer; font-weight:700;
    background:linear-gradient(135deg,var(--brand),#6d28d9 60%, #4c1d95); color:white; box-shadow:0 8px 20px #7c3aed55;
  }
  .btn.alt{background:linear-gradient(135deg,#10b981,#059669); box-shadow:0 8px 20px #10b98155}
  .notice{font-size:12px; color:var(--muted)}
  .link{color:#93c5fd; text-decoration:none}
  .err{background:#3b0a0a; border:1px solid #7f1d1d; color:#fecaca; padding:10px 12px; border-radius:12px; display:none}
  .success{background:#0b2a17; border:1px solid #14532d; color:#bbf7d0; padding:10px 12px; border-radius:12px; display:none}
  @media (max-width:900px){ .wrap{grid-template-columns:1fr; gap:16px} }
</style>
</head>
<body>
  <div class="wrap">
    <section class="hero">
      <h1>Join MicroTasks</h1>
      <p>Earn by completing small tasks or hire the crowd to get work done—fast.</p>
      <div class="badges">
        <span class="chip">Data Entry</span>
        <span class="chip">Surveys</span>
        <span class="chip">Transcription</span>
        <span class="chip">Image Tagging</span>
        <span class="chip">Categorization</span>
      </div>
      <div style="margin-top:18px; font-size:13px; color:#9ca3af">
        Already have an account? <a class="link" href="javascript:void(0)" onclick="goLogin()">Log in</a>
      </div>
    </section>

    <section class="panel">
      <h2>Create your account</h2>
      <div id="msgErr" class="err"></div>
      <div id="msgOk" class="success"></div>
      <form id="signupForm" onsubmit="return false;">
        <div class="row">
          <div>
            <label>Full Name</label>
            <input id="name" required placeholder="e.g., Fatima Azeem"/>
          </div>
          <div>
            <label>Role</label>
            <select id="role" required>
              <option value="worker">Worker (earn)</option>
              <option value="requester">Requester (post tasks)</option>
            </select>
          </div>
        </div>
        <label>Email</label>
        <input id="email" type="email" required placeholder="you@example.com"/>
        <label>Password</label>
        <input id="pass" type="password" minlength="4" required placeholder="At least 4 characters"/>
        <button class="btn" onclick="signup()">Create Account</button>
        <button class="btn alt" type="button" onclick="goLogin()">Go to Login</button>
        <div class="notice">By signing up, you agree to our simple demo terms (localStorage only).</div>
      </form>
    </section>
  </div>

<script>
function goLogin(){ location.href = 'login.php'; }

function load(key, fallback){ try{ return JSON.parse(localStorage.getItem(key)) ?? fallback; }catch(e){ return fallback; } }
function save(key, val){ localStorage.setItem(key, JSON.stringify(val)); }

function ensureSeeds(){
  // Seed sample tasks once
  const seeded = localStorage.getItem('seeded_v1');
  if(!seeded){
    const tasks = [
      {id:'T-1001', title:'Categorize 50 product images', pay:1.50, deadline:'2025-10-25', category:'Image Tagging', desc:'Look at each product image and select the most relevant category.', postedBy:'demo@requester.com', rating:4.8},
      {id:'T-1002', title:'Short survey: shopping habits', pay:0.75, deadline:'2025-10-22', category:'Surveys', desc:'Answer 8 multiple-choice questions about your weekly shopping.', postedBy:'demo@requester.com', rating:4.6},
      {id:'T-1003', title:'Transcribe 2-minute audio', pay:2.20, deadline:'2025-10-21', category:'Transcription', desc:'Transcribe a short audio clip with clear speech.', postedBy:'demo@requester.com', rating:4.9},
    ];
    save('tasks', tasks);
    localStorage.setItem('seeded_v1','1');
  }
}

function signup(){
  const name = document.getElementById('name').value.trim();
  const role = document.getElementById('role').value;
  const email = document.getElementById('email').value.trim().toLowerCase();
  const pass = document.getElementById('pass').value;

  const msgErr = document.getElementById('msgErr');
  const msgOk = document.getElementById('msgOk');
  msgErr.style.display = 'none'; msgOk.style.display='none';

  if(!name || !email || !pass){ showErr('Please fill all fields.'); return; }

  const users = load('users', []);
  if(users.find(u => u.email === email)){ showErr('Email already registered. Please log in.'); return; }

  const user = {name, role, email, pass, createdAt:Date.now(), earnings:0, bio:'', country:'', rating:5, completed:0};
  users.push(user);
  save('users', users);
  ensureSeeds();
  msgOk.textContent = 'Account created! Redirecting to login...';
  msgOk.style.display = 'block';
  setTimeout(()=> location.href='login.php', 800);
}

function showErr(t){ const m=document.getElementById('msgErr'); m.textContent=t; m.style.display='block'; }
</script>
</body>
</html>
