<?php
// login.php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Favor prense username ho password!';
    } elseif ($username === 'admin' && $password === 'admin') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'Administrador';
        $_SESSION['logged_in'] = true;
        $_SESSION['login_success'] = true;
        header('Location: home.php');
        exit();
    } else {
        $error = 'Username ka Password sala!';
    }
}
?>
<!DOCTYPE html>
<html lang="tet">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN — Sistema Mapamentu BFS</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:    #0d1117;
            --paper:  #f4f1eb;
            --accent: #c8622a;
            --green:  #2d6a4f;
            --muted:  #6b7280;
            --line:   #d4c9b8;
            --white:  #ffffff;
            --shadow: rgba(13,17,23,0.18);
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--paper);
            background-image:
                repeating-linear-gradient(0deg,   transparent, transparent 39px, rgba(139,99,60,0.10) 39px, rgba(139,99,60,0.10) 40px),
                repeating-linear-gradient(90deg,  transparent, transparent 39px, rgba(139,99,60,0.10) 39px, rgba(139,99,60,0.10) 40px),
                repeating-linear-gradient(0deg,   transparent, transparent 119px, rgba(139,99,60,0.18) 119px, rgba(139,99,60,0.18) 120px),
                repeating-linear-gradient(90deg,  transparent, transparent 119px, rgba(139,99,60,0.18) 119px, rgba(139,99,60,0.18) 120px);
            font-family: 'DM Sans', sans-serif;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: -120px; left: -120px;
            width: 500px; height: 500px;
            border-radius: 50%;
            border: 2px solid rgba(200,98,42,0.18);
            box-shadow: 0 0 0 40px rgba(200,98,42,0.06), 0 0 0 80px rgba(200,98,42,0.04), 0 0 0 130px rgba(200,98,42,0.02);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -80px; right: -80px;
            width: 380px; height: 380px;
            border-radius: 50%;
            border: 2px solid rgba(45,106,79,0.15);
            box-shadow: 0 0 0 40px rgba(45,106,79,0.05), 0 0 0 80px rgba(45,106,79,0.03);
            pointer-events: none;
        }

        .pin { position: fixed; pointer-events: none; animation: float 6s ease-in-out infinite; }
        .pin svg { display: block; }
        .pin:nth-child(1) { top: 12%; left: 8%;  animation-delay: 0s;   opacity: 0.22; }
        .pin:nth-child(2) { top: 70%; left: 5%;  animation-delay: 1.5s; opacity: 0.18; }
        .pin:nth-child(3) { top: 20%; right: 7%; animation-delay: 0.8s; opacity: 0.20; }
        .pin:nth-child(4) { top: 75%; right: 9%; animation-delay: 2.2s; opacity: 0.16; }
        .pin:nth-child(5) { top: 45%; left: 3%;  animation-delay: 3.0s; opacity: 0.14; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }

        .compass { position: fixed; top: 28px; right: 32px; width: 64px; height: 64px; opacity: 0.22; animation: spin-slow 30s linear infinite; pointer-events: none; }
        @keyframes spin-slow { to { transform: rotate(360deg); } }

        .card-wrap { width: 420px; position: relative; z-index: 10; animation: rise 0.7s cubic-bezier(0.22,1,0.36,1) both; }
        @keyframes rise { from{opacity:0;transform:translateY(40px)} to{opacity:1;transform:translateY(0)} }

        .ruler {
            height: 10px;
            background: repeating-linear-gradient(90deg,
                var(--accent) 0px, var(--accent) 2px,
                transparent 2px, transparent 10px,
                var(--accent) 10px, var(--accent) 11px,
                transparent 11px, transparent 20px);
            border-radius: 12px 12px 0 0;
        }

        .card {
            background: var(--white);
            border-radius: 0 0 16px 16px;
            box-shadow: 0 2px 0 var(--accent), 0 20px 60px var(--shadow), 0 4px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header { background: var(--ink); padding: 28px 32px 24px; position: relative; overflow: hidden; }
        .card-header::before {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(45deg, transparent, transparent 12px, rgba(255,255,255,0.025) 12px, rgba(255,255,255,0.025) 13px);
        }
        .card-header::after {
            content: ''; position: absolute; bottom: -1px; left: 0; right: 0;
            height: 3px; background: linear-gradient(90deg, var(--accent), var(--green), var(--accent));
        }

        .brand-row { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .brand-icon { width: 44px; height: 44px; background: var(--accent); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(200,98,42,0.45); }
        .brand-icon svg { width: 24px; height: 24px; }
        .brand-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.15rem; color: var(--white); letter-spacing: 0.04em; line-height: 1.15; }
        .brand-sub { font-size: 0.68rem; color: rgba(255,255,255,0.45); font-weight: 400; letter-spacing: 0.12em; text-transform: uppercase; }
        .header-tagline { font-size: 0.78rem; color: rgba(255,255,255,0.5); padding: 8px 12px; background: rgba(255,255,255,0.06); border-left: 2px solid var(--accent); border-radius: 0 6px 6px 0; }
        .header-tagline strong { color: rgba(255,255,255,0.85); font-weight: 500; }

        .card-body { padding: 32px; }

        .coord-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 0.68rem; color: var(--muted); font-family: 'Courier New', monospace; background: var(--paper); border: 1px solid var(--line); border-radius: 4px; padding: 4px 8px; margin-bottom: 22px; letter-spacing: 0.05em; }
        .coord-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: blink 2s ease-in-out infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        .form-label { display: block; font-size: 0.72rem; font-weight: 600; color: var(--ink); letter-spacing: 0.10em; text-transform: uppercase; margin-bottom: 8px; }
        .form-label .icon { color: var(--accent); margin-right: 5px; }

        .input-wrap { position: relative; margin-bottom: 20px; }
        .input-wrap input {
            width: 100%; padding: 13px 14px 13px 44px;
            border: 1.5px solid var(--line); border-radius: 8px;
            font-family: 'DM Sans', sans-serif; font-size: 0.92rem;
            color: var(--ink); background: var(--paper);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; outline: none;
        }
        .input-wrap input:focus { border-color: var(--accent); background: var(--white); box-shadow: 0 0 0 3px rgba(200,98,42,0.12); }
        .input-wrap input.is-error { border-color: #dc3545; box-shadow: 0 0 0 3px rgba(220,53,69,0.12); }
        .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); transition: color 0.2s; pointer-events: none; }
        .input-wrap:focus-within .input-icon { color: var(--accent); }

        .alert-err {
            background: #fff5f5; border: 1px solid #fecaca; border-left: 3px solid #dc3545;
            border-radius: 6px; padding: 10px 14px; font-size: 0.82rem; color: #b91c1c;
            margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
            animation: shake 0.4s ease;
        }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-4px)} 75%{transform:translateX(4px)} }

        .btn-login {
            width: 100%; padding: 14px; background: var(--ink); color: var(--white);
            border: none; border-radius: 8px; font-family: 'Syne', sans-serif; font-weight: 700;
            font-size: 0.9rem; letter-spacing: 0.12em; text-transform: uppercase;
            cursor: pointer; position: relative; overflow: hidden; transition: transform 0.15s; margin-top: 4px;
        }
        .btn-login::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 0; background: var(--accent); transition: width 0.35s ease; z-index: 0; }
        .btn-login:hover::before { width: 100%; }
        .btn-login:hover { transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }
        .btn-login span { position: relative; z-index: 1; }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .divider { display: flex; align-items: center; gap: 12px; margin: 22px 0 18px; color: var(--muted); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase; }
        .divider::before, .divider::after { content:''; flex:1; height:1px; background: var(--line); }

        .card-footer { background: var(--paper); border-top: 1px solid var(--line); padding: 14px 32px; display: flex; align-items: center; justify-content: space-between; }
        .footer-info { font-size: 0.70rem; color: var(--muted); }
        .footer-badge { font-size: 0.65rem; background: var(--ink); color: var(--white); padding: 3px 8px; border-radius: 4px; font-family: 'Syne', sans-serif; font-weight: 700; letter-spacing: 0.08em; }

        /* ── TOAST — hidden by default ── */
        .toast-wrap {
            position: fixed;
            top: 20px; right: 20px;
            z-index: 9999;
            transform: translateX(130%);   /* off-screen to the right */
            transition: transform 0.4s cubic-bezier(0.22,1,0.36,1);
        }
        .toast-wrap.show { transform: translateX(0); }  /* slide in only when .show is added */
        .toast-box { background: var(--ink); color: var(--white); border-radius: 10px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; box-shadow: 0 8px 32px var(--shadow); border-left: 3px solid var(--green); min-width: 260px; }
        .toast-check { width: 32px; height: 32px; background: var(--green); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .toast-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.88rem; }
        .toast-sub   { color: rgba(255,255,255,0.6); font-size: 0.75rem; margin-top: 2px; }
    </style>
</head>
<body>

    <!-- Floating map pins -->
    <div class="pin"><svg width="28" height="36" viewBox="0 0 28 36"><path d="M14 0C6.268 0 0 6.268 0 14c0 9.333 14 22 14 22S28 23.333 28 14C28 6.268 21.732 0 14 0z" fill="#c8622a"/><circle cx="14" cy="14" r="5" fill="white"/></svg></div>
    <div class="pin"><svg width="22" height="28" viewBox="0 0 28 36"><path d="M14 0C6.268 0 0 6.268 0 14c0 9.333 14 22 14 22S28 23.333 28 14C28 6.268 21.732 0 14 0z" fill="#2d6a4f"/><circle cx="14" cy="14" r="5" fill="white"/></svg></div>
    <div class="pin"><svg width="24" height="30" viewBox="0 0 28 36"><path d="M14 0C6.268 0 0 6.268 0 14c0 9.333 14 22 14 22S28 23.333 28 14C28 6.268 21.732 0 14 0z" fill="#c8622a"/><circle cx="14" cy="14" r="5" fill="white"/></svg></div>
    <div class="pin"><svg width="20" height="26" viewBox="0 0 28 36"><path d="M14 0C6.268 0 0 6.268 0 14c0 9.333 14 22 14 22S28 23.333 28 14C28 6.268 21.732 0 14 0z" fill="#2d6a4f"/><circle cx="14" cy="14" r="5" fill="white"/></svg></div>
    <div class="pin"><svg width="18" height="22" viewBox="0 0 28 36"><path d="M14 0C6.268 0 0 6.268 0 14c0 9.333 14 22 14 22S28 23.333 28 14C28 6.268 21.732 0 14 0z" fill="#c8622a"/><circle cx="14" cy="14" r="5" fill="white"/></svg></div>

    <!-- Compass -->
    <svg class="compass" viewBox="0 0 64 64" fill="none">
        <circle cx="32" cy="32" r="30" stroke="#0d1117" stroke-width="1.5"/>
        <circle cx="32" cy="32" r="24" stroke="#0d1117" stroke-width="0.8" stroke-dasharray="2 4"/>
        <polygon points="32,4 35,30 32,26 29,30" fill="#c8622a"/>
        <polygon points="32,60 35,34 32,38 29,34" fill="#0d1117"/>
        <polygon points="4,32 30,29 26,32 30,35" fill="#0d1117"/>
        <polygon points="60,32 34,29 38,32 34,35" fill="#0d1117"/>
        <text x="32" y="17" text-anchor="middle" font-size="6" font-weight="bold" fill="#c8622a" font-family="Syne,sans-serif">N</text>
        <circle cx="32" cy="32" r="3" fill="#0d1117"/>
    </svg>

    <!-- ══════════════ LOGIN CARD ══════════════ -->
    <div class="card-wrap">
        <div class="ruler"></div>
        <div class="card">

            <div class="card-header">
                <div class="brand-row">
                    <div class="brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
                            <line x1="9" y1="3" x2="9" y2="18"/>
                            <line x1="15" y1="6" x2="15" y2="21"/>
                        </svg>
                    </div>
                    <div>
                        <div class="brand-title">SISTEMA MAPAMENTU BFS</div>
                        <div class="brand-sub">Breadth-First Search · Web App</div>
                    </div>
                </div>
                <div class="header-tagline">
                    <strong>12 Interasaun</strong> — Residensia Becora Centro ba Kampus IPDC
                </div>
            </div>

            <div class="card-body">

                <div class="coord-badge">
                    <div class="coord-dot"></div>
                    <span>-8.5569° S, 125.5736° E &nbsp;·&nbsp; Becora, Díli</span>
                </div>

                <!-- PHP error message (username/password sala) -->
                <?php if ($error): ?>
                <div class="alert-err">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="" novalidate>
                    <div>
                        <label class="form-label"><span class="icon">▸</span> Username</label>
                        <div class="input-wrap">
                            <input type="text" name="username" id="username"
                                   placeholder="Hatama username"
                                   autocomplete="username" required>
                            <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label class="form-label"><span class="icon">▸</span> Password</label>
                        <div class="input-wrap">
                            <input type="password" name="password" id="password"
                                   placeholder="Hatama password"
                                   autocomplete="current-password" required>
                            <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                    </div>

                    <button type="submit" class="btn-login" id="btnLogin">
                        <span id="btnText">&#9654; LOGIN BA SISTEMA</span>
                    </button>
                </form>
                <div class="divider">aksesu seguru</div>
            </div>
        </div>
    </div>

    <!-- ══ TOAST — ada di DOM tapi off-screen. Hanya muncul bila JS tambah class .show ══ -->
    <div class="toast-wrap" id="successToast">
        <div class="toast-box">
            <div class="toast-check">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="toast-title">Login Susesu!</div>
                <div class="toast-sub">Halibur ba home page...</div>
            </div>
        </div>
    </div>

    <script>
    const form          = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const btnLogin      = document.getElementById('btnLogin');
    const btnText       = document.getElementById('btnText');
    const toast         = document.getElementById('successToast');

    usernameInput.focus();

    // Hapus border merah ketika user mulai mengetik
    [usernameInput, passwordInput].forEach(el => {
        el.addEventListener('input', () => el.classList.remove('is-error'));
    });

    form.addEventListener('submit', function(e) {
        // Tahan submit dulu — kita proses manual
        e.preventDefault();

        const uVal = usernameInput.value.trim();
        const pVal = passwordInput.value.trim();

        // ── CASE 1: Field kosong → tampilkan border merah, STOP
        let hasError = false;
        if (!uVal) { usernameInput.classList.add('is-error'); hasError = true; }
        if (!pVal) { passwordInput.classList.add('is-error'); hasError = true; }
        if (hasError) return;

        // ── CASE 2: Username/password BENAR → tampilkan toast → submit ke PHP
        if (uVal === 'admin' && pVal === 'admin') {
            toast.classList.add('show');          // toast muncul
            btnLogin.disabled = true;
            btnText.textContent = '⏳ Halibur...';
            setTimeout(() => form.submit(), 1500); // baru submit ke PHP setelah 1.5 detik
            return;
        }

        // ── CASE 3: Username/password SALAH → langsung submit ke PHP (PHP tampilkan $error)
        form.submit();
    });
    </script>
</body>
</html>