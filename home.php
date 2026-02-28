<?php
// home.php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';
require_once 'functions.php';

$mapaFunc = new MapaFunctions();
?>
<!DOCTYPE html>
<html lang="tet">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME — BFS Mapamentu</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --ink:    #0d1117;
        --ink2:   #1a2332;
        --paper:  #f4f1eb;
        --paper2: #ede9e0;
        --accent: #c8622a;
        --green:  #2d6a4f;
        --gold:   #d4a017;
        --muted:  #6b7280;
        --line:   #d4c9b8;
        --white:  #ffffff;
        --shadow: rgba(13,17,23,0.13);
    }

    html { scroll-behavior: smooth; }

    body {
        background-color: var(--paper);
        font-family: 'DM Sans', sans-serif;
        color: var(--ink);
        min-height: 100vh;
        background-image:
            repeating-linear-gradient(0deg, transparent, transparent 59px, rgba(139,99,60,0.055) 59px, rgba(139,99,60,0.055) 60px),
            repeating-linear-gradient(90deg, transparent, transparent 59px, rgba(139,99,60,0.055) 59px, rgba(139,99,60,0.055) 60px),
            repeating-linear-gradient(0deg, transparent, transparent 179px, rgba(139,99,60,0.10) 179px, rgba(139,99,60,0.10) 180px),
            repeating-linear-gradient(90deg, transparent, transparent 179px, rgba(139,99,60,0.10) 179px, rgba(139,99,60,0.10) 180px);
    }

    /* ══════════ NAVBAR ══════════ */
    .navbar {
        background: var(--ink);
        border-bottom: 3px solid transparent;
        border-image: linear-gradient(90deg, var(--accent), var(--green), var(--accent)) 1;
        position: sticky; top: 0; z-index: 1000;
        box-shadow: 0 4px 24px rgba(0,0,0,0.28);
    }
    .navbar-inner {
        display: flex; align-items: center; justify-content: space-between;
        max-width: 1200px; margin: 0 auto; padding: 0 28px; height: 64px;
    }
    .brand {
        display: flex; align-items: center; gap: 10px; text-decoration: none;
    }
    .brand-logo {
        width: 38px; height: 38px; background: var(--accent); border-radius: 9px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        box-shadow: 0 3px 12px rgba(200,98,42,0.4);
    }
    .brand-logo svg { width: 20px; height: 20px; }
    .brand-name {
        font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.05rem;
        color: var(--white); letter-spacing: 0.06em;
    }
    .brand-name em { color: var(--accent); font-style: normal; }

    .nav-links { display: flex; align-items: center; gap: 2px; list-style: none; }
    .nav-links a {
        display: flex; align-items: center; gap: 7px;
        color: rgba(255,255,255,0.6); text-decoration: none;
        font-size: 0.78rem; font-weight: 500; letter-spacing: 0.08em;
        text-transform: uppercase; padding: 7px 13px; border-radius: 6px;
        transition: all 0.2s; position: relative;
    }
    .nav-links a:hover { color: white; background: rgba(255,255,255,0.07); }
    .nav-links a.active {
        color: white; background: rgba(255,255,255,0.07);
    }
    .nav-links a.active::after {
        content: ''; position: absolute; bottom: -1px; left: 13px; right: 13px;
        height: 2px; background: var(--accent); border-radius: 2px 2px 0 0;
    }
    .nav-links svg { width: 14px; height: 14px; }

    /* User dropdown */
    .nav-user { position: relative; }
    .user-btn {
        display: flex; align-items: center; gap: 9px;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.10);
        border-radius: 50px; padding: 6px 14px 6px 7px;
        cursor: pointer; transition: all 0.2s;
    }
    .user-btn:hover { background: rgba(255,255,255,0.12); }
    .user-av {
        width: 28px; height: 28px; border-radius: 50%; background: var(--green);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.68rem; color: white;
    }
    .user-nm { font-size: 0.78rem; color: rgba(255,255,255,0.85); font-weight: 500; }
    .user-caret { font-size: 0.55rem; color: rgba(255,255,255,0.35); margin-left: 2px; }

    .drop-menu {
        position: absolute; top: calc(100% + 9px); right: 0;
        background: var(--ink2); border: 1px solid rgba(255,255,255,0.08);
        border-radius: 11px; min-width: 175px; padding: 6px;
        box-shadow: 0 16px 48px rgba(0,0,0,0.35);
        opacity: 0; pointer-events: none;
        transform: translateY(-8px);
        transition: all 0.22s cubic-bezier(0.22,1,0.36,1);
    }
    .nav-user.open .drop-menu { opacity: 1; pointer-events: all; transform: translateY(0); }
    .drop-item {
        display: flex; align-items: center; gap: 9px;
        padding: 9px 12px; border-radius: 7px;
        color: rgba(255,255,255,0.6); font-size: 0.8rem;
        text-decoration: none; transition: all 0.15s;
    }
    .drop-item:hover { background: rgba(255,255,255,0.06); color: white; }
    .drop-item.red { color: #f87171; }
    .drop-item.red:hover { background: rgba(248,113,113,0.10); }
    .drop-item svg { width: 14px; height: 14px; }
    .drop-sep { height: 1px; background: rgba(255,255,255,0.07); margin: 5px 0; }

    /* ══════════ HERO ══════════ */
    .hero {
        background: var(--ink);
        padding: 56px 0 40px;
        position: relative; overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute; inset: 0;
        background: repeating-linear-gradient(45deg, transparent, transparent 22px, rgba(255,255,255,0.016) 22px, rgba(255,255,255,0.016) 23px);
    }
    .hero-orb-a {
        position: absolute; top: -100px; left: -100px;
        width: 350px; height: 350px; border-radius: 50%;
        background: radial-gradient(circle, rgba(200,98,42,0.22) 0%, transparent 68%);
        pointer-events: none;
    }
    .hero-orb-b {
        position: absolute; bottom: -80px; right: -60px;
        width: 300px; height: 300px; border-radius: 50%;
        background: radial-gradient(circle, rgba(45,106,79,0.20) 0%, transparent 68%);
        pointer-events: none;
    }
    /* Contour circles decoration */
    .hero-rings {
        position: absolute; right: 180px; top: 50%; transform: translateY(-50%);
        width: 320px; height: 320px; pointer-events: none; opacity: 0.06;
    }
    .hero-rings circle { fill: none; stroke: white; }

    .hero-inner {
        max-width: 1200px; margin: 0 auto; padding: 0 28px;
        position: relative; z-index: 1;
    }
    .hero-chip {
        display: inline-flex; align-items: center; gap: 7px;
        font-family: 'DM Mono', monospace; font-size: 0.68rem;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: var(--accent); margin-bottom: 18px;
    }
    .chip-dot {
        width: 6px; height: 6px; border-radius: 50%; background: var(--green);
        animation: blink 2s infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }

    .hero-title {
        font-family: 'Syne', sans-serif; font-weight: 800;
        font-size: clamp(2rem, 4vw, 3rem);
        color: var(--white); line-height: 1.12; margin-bottom: 14px;
    }
    .hero-title .hl { color: var(--accent); }

    .hero-sub {
        font-size: 0.96rem; color: rgba(255,255,255,0.48);
        max-width: 520px; line-height: 1.75; margin-bottom: 30px;
    }

    .hero-route {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.10);
        border-radius: 8px; padding: 10px 18px;
        font-family: 'DM Mono', monospace; font-size: 0.78rem;
        color: rgba(255,255,255,0.65);
    }
    .rp { color: var(--accent); }
    .ra { color: var(--green); }

    /* ══════════ STATS ══════════ */
    .stats-row {
        max-width: 1200px; margin: -26px auto 0;
        padding: 0 28px;
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 18px; position: relative; z-index: 10;
    }
    .stat-card {
        background: var(--white); border-radius: 14px;
        padding: 22px 24px;
        box-shadow: 0 8px 32px var(--shadow);
        border-left: 3px solid transparent;
        display: flex; align-items: center; gap: 16px;
        animation: rise 0.5s cubic-bezier(0.22,1,0.36,1) both;
        transition: transform 0.25s, box-shadow 0.25s;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 18px 44px rgba(13,17,23,0.17); }
    .stat-card:nth-child(1) { border-left-color: var(--accent); animation-delay:.08s; }
    .stat-card:nth-child(2) { border-left-color: var(--green); animation-delay:.16s; }
    .stat-card:nth-child(3) { border-left-color: var(--gold); animation-delay:.24s; }

    .stat-ico {
        width: 50px; height: 50px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .stat-ico svg { width: 24px; height: 24px; }
    .stat-card:nth-child(1) .stat-ico { background: rgba(200,98,42,0.10); color: var(--accent); }
    .stat-card:nth-child(2) .stat-ico { background: rgba(45,106,79,0.10); color: var(--green); }
    .stat-card:nth-child(3) .stat-ico { background: rgba(212,160,23,0.10); color: var(--gold); }

    .stat-num {
        font-family: 'Syne', sans-serif; font-weight: 800;
        font-size: 1.9rem; color: var(--ink); line-height: 1; margin-bottom: 4px;
    }
    .stat-lbl {
        font-size: 0.70rem; color: var(--muted);
        text-transform: uppercase; letter-spacing: 0.09em; font-weight: 500;
    }

    @keyframes rise { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }

    /* ══════════ MAIN CONTENT ══════════ */
    .main {
        max-width: 1200px; margin: 44px auto 0; padding: 0 28px 80px;
    }

    .sec-lbl {
        display: flex; align-items: center; gap: 10px;
        font-family: 'Syne', sans-serif; font-weight: 700;
        font-size: 0.70rem; letter-spacing: 0.14em; text-transform: uppercase;
        color: var(--muted); margin-bottom: 20px;
    }
    .sec-lbl::before {
        content: ''; width: 22px; height: 2px;
        background: var(--accent); border-radius: 2px;
    }

    /* ── Big Feature Card ── */
    .fc {
        background: var(--white); border-radius: 18px;
        overflow: hidden; box-shadow: 0 6px 28px rgba(13,17,23,0.07);
        animation: rise 0.55s 0.3s both; margin-bottom: 24px;
    }
    .fc-head {
        background: var(--ink); padding: 28px 32px;
        display: flex; align-items: center; gap: 18px;
        position: relative; overflow: hidden;
    }
    .fc-head::before {
        content: ''; position: absolute; inset: 0;
        background: repeating-linear-gradient(45deg, transparent, transparent 14px, rgba(255,255,255,0.018) 14px, rgba(255,255,255,0.018) 15px);
    }
    .fc-head::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0;
        height: 2px; background: linear-gradient(90deg, var(--accent), var(--green));
    }
    .fc-icon {
        width: 56px; height: 56px; background: var(--accent); border-radius: 14px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        box-shadow: 0 6px 20px rgba(200,98,42,0.42); position: relative; z-index: 1;
    }
    .fc-icon svg { width: 28px; height: 28px; }
    .fc-head-text { position: relative; z-index: 1; }
    .fc-title {
        font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.25rem;
        color: var(--white); letter-spacing: 0.03em;
    }
    .fc-mono {
        font-family: 'DM Mono', monospace; font-size: 0.70rem;
        color: rgba(255,255,255,0.35); margin-top: 3px; letter-spacing: 0.04em;
    }

    .fc-body {
        padding: 32px;
        display: grid; grid-template-columns: 1fr 300px; gap: 32px; align-items: start;
    }

    .fc-desc {
        font-size: 0.95rem; color: var(--muted); line-height: 1.82; margin-bottom: 22px;
    }
    .fc-desc strong { color: var(--ink); font-weight: 600; }

    .fc-list {
        list-style: none; display: grid; grid-template-columns: 1fr 1fr; gap: 9px; margin-bottom: 28px;
    }
    .fc-list li {
        display: flex; align-items: center; gap: 9px;
        font-size: 0.82rem; color: var(--ink); font-weight: 500;
    }
    .chk {
        width: 20px; height: 20px; border-radius: 50%;
        background: rgba(45,106,79,0.10);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        color: var(--green);
    }
    .chk svg { width: 11px; height: 11px; }

    .btn-go {
        display: inline-flex; align-items: center; gap: 10px;
        background: var(--ink); color: var(--white); text-decoration: none;
        padding: 14px 28px; border-radius: 10px;
        font-family: 'Syne', sans-serif; font-weight: 700;
        font-size: 0.83rem; letter-spacing: 0.10em; text-transform: uppercase;
        position: relative; overflow: hidden; transition: transform 0.2s;
    }
    .btn-go::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0;
        width: 0; background: var(--accent);
        transition: width 0.32s ease; z-index: 0;
    }
    .btn-go:hover::before { width: 100%; }
    .btn-go:hover { transform: translateY(-2px); color: white; }
    .btn-go > * { position: relative; z-index: 1; }
    .btn-go svg { width: 16px; height: 16px; }

    /* Optimal side panel */
    .op-panel {
        background: var(--ink); border-radius: 14px; padding: 24px;
        position: relative; overflow: hidden;
    }
    .op-panel::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, var(--gold), var(--accent));
    }
    .op-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 0.63rem; font-weight: 700; letter-spacing: 0.13em;
        text-transform: uppercase; color: var(--gold);
        background: rgba(212,160,23,0.14); border: 1px solid rgba(212,160,23,0.28);
        padding: 3px 10px; border-radius: 20px; margin-bottom: 16px;
    }
    .op-lbl {
        font-family: 'DM Mono', monospace; font-size: 0.63rem;
        color: rgba(255,255,255,0.32); text-transform: uppercase; letter-spacing: 0.10em; margin-bottom: 4px;
    }
    .op-val {
        font-family: 'Syne', sans-serif; font-weight: 800;
        font-size: 1.55rem; color: white; line-height: 1; margin-bottom: 16px;
    }
    .op-val small { font-size: 0.82rem; color: var(--gold); font-weight: 600; margin-left: 6px; }
    .op-sep { height: 1px; background: rgba(255,255,255,0.07); margin: 14px 0; }
    .op-route {
        font-family: 'DM Mono', monospace; font-size: 0.63rem;
        color: rgba(255,255,255,0.35); line-height: 1.75;
    }
    .op-route strong { color: rgba(255,255,255,0.65); }

    /* BFS Node wave */
    .bfs-wrap { margin-top: 14px; }
    .bfs-nodes {
        display: flex; flex-wrap: wrap; align-items: center; gap: 3px; row-gap: 6px;
    }
    .bfs-nd {
        width: 26px; height: 26px; border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.05);
        display: flex; align-items: center; justify-content: center;
        font-family: 'DM Mono', monospace; font-size: 0.52rem;
        color: rgba(255,255,255,0.45);
        transition: all 0.25s;
        animation: nodeIn 0.4s ease both;
    }
    .bfs-nd.lit { background: var(--green); border-color: var(--green); color: white; }
    .bfs-arr { font-size: 0.52rem; color: rgba(255,255,255,0.18); }
    @keyframes nodeIn { from{opacity:0;transform:scale(0)} to{opacity:1;transform:scale(1)} }

    /* ── Bottom Grid ── */
    .btm-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
        animation: rise 0.55s 0.42s both;
    }
    .info-card {
        background: var(--white); border-radius: 14px; overflow: hidden;
        box-shadow: 0 4px 20px rgba(13,17,23,0.06);
        transition: transform 0.25s, box-shadow 0.25s;
    }
    .info-card:hover { transform: translateY(-4px); box-shadow: 0 14px 36px rgba(13,17,23,0.12); }
    .ic-head {
        padding: 14px 22px; border-bottom: 1px solid var(--paper2);
        background: var(--paper); display: flex; align-items: center; gap: 9px;
    }
    .ic-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .ic-head h6 {
        font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.75rem;
        letter-spacing: 0.09em; text-transform: uppercase; color: var(--ink); margin: 0;
    }
    .ic-body { padding: 6px 22px 18px; }
    .ir {
        display: flex; align-items: center; justify-content: space-between;
        padding: 11px 0; border-bottom: 1px solid var(--paper2); gap: 12px;
    }
    .ir:last-child { border-bottom: none; }
    .ir-k { font-family: 'DM Mono', monospace; font-size: 0.73rem; color: var(--muted); flex-shrink: 0; }
    .ir-v { font-size: 0.82rem; font-weight: 600; color: var(--ink); text-align: right; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }

    .tag {
        display: inline-block; font-size: 0.62rem; font-weight: 700;
        letter-spacing: 0.07em; text-transform: uppercase;
        padding: 2px 7px; border-radius: 4px;
    }
    .tg { background: rgba(45,106,79,0.1); color: var(--green); }
    .tb { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .tk { background: rgba(13,17,23,0.08); color: var(--ink); }
    .tgd{ background: rgba(212,160,23,0.12); color: var(--gold); }

    .clock { font-family: 'DM Mono', monospace; font-size: 1.05rem; font-weight: 500; }

    /* ══════════ FOOTER ══════════ */
    footer {
        background: var(--ink);
        border-top: 1px solid rgba(255,255,255,0.06);
        padding: 20px 0;
    }
    .foot-inner {
        max-width: 1200px; margin: 0 auto; padding: 0 28px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    }
    .foot-l { font-size: 0.73rem; color: rgba(255,255,255,0.32); }
    .foot-l strong { color: rgba(255,255,255,0.58); }
    .foot-r { display: flex; gap: 8px; }
    .foot-t {
        font-size: 0.62rem; font-family: 'DM Mono', monospace; letter-spacing: 0.07em;
        color: rgba(255,255,255,0.25); background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07); padding: 3px 8px; border-radius: 4px;
    }

    /* ══════════ RESPONSIVE ══════════ */
    @media (max-width: 960px) {
        .stats-row { grid-template-columns: 1fr 1fr; }
        .fc-body { grid-template-columns: 1fr; }
        .op-panel { display: none; }
        .fc-list { grid-template-columns: 1fr; }
        .btm-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .stats-row { grid-template-columns: 1fr; }
        .nav-links { display: none; }
        .hero { padding: 36px 0; }
        .hero-title { font-size: 1.9rem; }
    }
    </style>
</head>
<body>

<!-- ═══════ NAVBAR ═══════ -->
<nav class="navbar">
    <div class="navbar-inner">

        <a href="home.php" class="brand">
            <div class="brand-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
                    <line x1="9" y1="3" x2="9" y2="18"/>
                    <line x1="15" y1="6" x2="15" y2="21"/>
                </svg>
            </div>
            <span class="brand-name">BFS&nbsp;<em>MAPAMENTU</em></span>
        </a>

        <ul class="nav-links">
            <li><a href="home.php" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                HOME
            </a></li>
            <li><a href="interasaun.php">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>
                12 INTERASAUN
            </a></li>
        </ul>

        <div class="nav-user" id="navUser">
            <div class="user-btn" onclick="document.getElementById('navUser').classList.toggle('open')">
                <div class="user-av"><?= strtoupper(substr($_SESSION['username'],0,2)) ?></div>
                <span class="user-nm"><?= htmlspecialchars($_SESSION['username']) ?></span>
                <span class="user-caret">▼</span>
            </div>
            <div class="drop-menu">
                <a href="#" class="drop-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profile
                </a>
                <div class="drop-sep"></div>
                <a href="logout.php" class="drop-item red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </a>
            </div>
        </div>

    </div>
</nav>

<!-- ═══════ HERO ═══════ -->
<div class="hero">
    <div class="hero-orb-a"></div>
    <div class="hero-orb-b"></div>
    <!-- Decorative contour rings -->
    <svg class="hero-rings" viewBox="0 0 320 320">
        <circle cx="160" cy="160" r="150" stroke-width="1"/>
        <circle cx="160" cy="160" r="115" stroke-width="1"/>
        <circle cx="160" cy="160" r="80" stroke-width="1"/>
        <circle cx="160" cy="160" r="48" stroke-width="1"/>
        <circle cx="160" cy="160" r="18" stroke-width="1.5"/>
    </svg>

    <div class="hero-inner">
        <div class="hero-chip">
            <div class="chip-dot"></div>
            Sistema aktivo &nbsp;·&nbsp; BFS Breadth-First Search
        </div>
        <h1 class="hero-title">
            Bemvindu,<br><span class="hl"><?= htmlspecialchars($_SESSION['username']) ?>!</span>
        </h1>
        <p class="hero-sub">
            Sistema Mapamentu BFS ho 12 Interasaun. Analiza dalan husi 
            Residensia Becora Centro ba Kampus IPDC no haree rota ne'ebe optimal.
        </p>
        <div class="hero-route">
            <span class="rp">⬤</span>
            Residensia Becora Centro
            <span class="ra">——▶</span>
            Kampus IPDC
        </div>
    </div>
</div>

<!-- ═══════ STATS ═══════ -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>
            </svg>
        </div>
        <div>
            <div class="stat-num">12</div>
            <div class="stat-lbl">Interasaun Total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
        </div>
        <div>
            <div class="stat-num">200+</div>
            <div class="stat-lbl">Lokasaun</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M3 17l4-8 4 4 4-6 4 10"/><path d="M3 21h18"/>
            </svg>
        </div>
        <div>
            <div class="stat-num">9.95 <span style="font-size:1rem;font-weight:600;">km</span></div>
            <div class="stat-lbl">Dalan Optimal (Interasaun IV)</div>
        </div>
    </div>
</div>

<!-- ═══════ MAIN ═══════ -->
<main class="main">

    <div class="sec-lbl">Modulu Prinsipál</div>

    <!-- Feature card -->
    <div class="fc">
        <div class="fc-head">
            <div class="fc-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
                    <line x1="9" y1="3" x2="9" y2="18"/>
                    <line x1="15" y1="6" x2="15" y2="21"/>
                </svg>
            </div>
            <div class="fc-head-text">
                <div class="fc-title">SISTEMA MAPAMENTU 12 INTERASAUN</div>
                <div class="fc-mono">breadth_first_search &nbsp;·&nbsp; becora_centro → kampus_ipdc</div>
            </div>
        </div>
        <div class="fc-body">
            <div>
                <p class="fc-desc">
                    Sistema ida-ne'e uza Metodu <strong>Breadth-First Search (BFS)</strong> atu analiza 
                    hotu 12 interasaun husi Residensia Becora Centro ba Kampus IPDC. Admin bele hili 
                    lokasaun sira iha kada interasaun, kalkula total distansia, no identifika dalan 
                    ne'ebe besik liu ho efisiénsia máxima. Rezultadu salva automátiku ba MySQL database.
                </p>
                <ul class="fc-list">
                    <?php foreach([
                        'Vizualiza 12 interasaun hotu',
                        'Kalkula distansia kada panel',
                        'Identifika rota optimal (BFS)',
                        'Rai dadus ba MySQL database',
                        'Seleksiona / deseleksiona hotu',
                        'Analiza komparativa rota sira',
                    ] as $f): ?>
                    <li>
                        <div class="chk"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                        <?= $f ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="interasaun.php" class="btn-go">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    <span>HAHU ANALIZA INTERASAUN</span>
                </a>
            </div>

            <!-- Optimal panel -->
            <div class="op-panel">
                <div class="op-badge">★ Solusaun Optimal</div>
                <div class="op-lbl">Interasaun Terbai</div>
                <div class="op-val">Interasaun IV <small>9,952 m</small></div>

                <div class="op-lbl" style="margin-bottom:9px;">GRAPH BFS NODES</div>
                <div class="bfs-wrap">
                    <div class="bfs-nodes" id="bfsNodes">
                        <?php
                        $nodes = ['A','B','C','D','E','F','G','H','I','J','K','L'];
                        foreach($nodes as $i => $n) {
                            echo "<div class='bfs-nd' style='animation-delay:".($i*0.055)."s' data-idx='$i'>$n</div>";
                            if($i < count($nodes)-1) echo "<span class='bfs-arr'>›</span>";
                        }
                        ?>
                    </div>
                </div>

                <div class="op-sep"></div>
                <div class="op-lbl" style="margin-bottom:5px;">Rota Optimal</div>
                <div class="op-route">
                    <strong>A→B→C→E→G→I→L→R→S→U→Y<br>→AA→CC→EE→DD→FF→JJ→PP→NN→QQ→RR</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom info cards -->
    <div class="sec-lbl">Informasaun Sistema</div>
    <div class="btm-grid">

        <div class="info-card">
            <div class="ic-head">
                <div class="ic-dot" style="background:var(--green)"></div>
                <h6>Rezultadu BFS</h6>
            </div>
            <div class="ic-body">
                <div class="ir">
                    <span class="ir-k">Interasaun Optimal</span>
                    <span class="ir-v">Interasaun IV <span class="tag tg">TERBAI</span></span>
                </div>
                <div class="ir">
                    <span class="ir-k">Distansia Total</span>
                    <span class="ir-v">9,952.49 m</span>
                </div>
                <div class="ir">
                    <span class="ir-k">Total Interasaun</span>
                    <span class="ir-v">12 interasaun</span>
                </div>
                <div class="ir">
                    <span class="ir-k">Algoritmu</span>
                    <span class="ir-v"><span class="tag tb">BFS</span></span>
                </div>
                <div class="ir">
                    <span class="ir-k">Origem</span>
                    <span class="ir-v">Becora Centro</span>
                </div>
                <div class="ir">
                    <span class="ir-k">Destinasaun</span>
                    <span class="ir-v">Kampus IPDC</span>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="ic-head">
                <div class="ic-dot" style="background:var(--accent)"></div>
                <h6>Informasaun Sistema</h6>
            </div>
            <div class="ic-body">
                <div class="ir">
                    <span class="ir-k">Data/Oras Agora</span>
                    <span class="ir-v clock" id="clk"><?= date('d/m/Y H:i:s') ?></span>
                </div>
                <div class="ir">
                    <span class="ir-k">Uzuáriu Login</span>
                    <span class="ir-v"><?= htmlspecialchars($_SESSION['username']) ?> <span class="tag tg">AKTIVO</span></span>
                </div>
                <div class="ir">
                    <span class="ir-k">Versaun</span>
                    <span class="ir-v"><span class="tag tk">v1.0</span></span>
                </div>
                <div class="ir">
                    <span class="ir-k">Backend</span>
                    <span class="ir-v"><span class="tag tk">PHP 8.1+</span></span>
                </div>
                <div class="ir">
                    <span class="ir-k">Database</span>
                    <span class="ir-v"><span class="tag tk">MySQL</span></span>
                </div>
                <div class="ir">
                    <span class="ir-k">Frontend</span>
                    <span class="ir-v"><span class="tag tb">Bootstrap 5</span></span>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- ═══════ FOOTER ═══════ -->
<footer>
    <div class="foot-inner">
        <div class="foot-l">
            &copy; 2026 &nbsp;<strong>BFS Mapamentu</strong> &nbsp;·&nbsp;
            Residensia Becora Centro → Kampus IPDC &nbsp;·&nbsp;
            Login: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
        </div>
        <div class="foot-r">
            <span class="foot-t">PHP 8.1</span>
            <span class="foot-t">MySQL</span>
            <span class="foot-t">Bootstrap 5</span>
            <span class="foot-t">BFS v1.0</span>
        </div>
    </div>
</footer>

<script>
// ── Live clock
(function tick() {
    const n = new Date();
    const p = v => String(v).padStart(2,'0');
    const el = document.getElementById('clk');
    if (el) el.textContent = `${p(n.getDate())}/${p(n.getMonth()+1)}/${n.getFullYear()} ${p(n.getHours())}:${p(n.getMinutes())}:${p(n.getSeconds())}`;
    setTimeout(tick, 1000);
})();

// ── Dropdown close-outside
document.addEventListener('click', function(e) {
    const nu = document.getElementById('navUser');
    if (!nu.contains(e.target)) nu.classList.remove('open');
});

// ── BFS node wave animation
const nds = document.querySelectorAll('.bfs-nd');
let idx = 0;
setInterval(() => {
    nds.forEach(n => n.classList.remove('lit'));
    [idx % nds.length, (idx+1) % nds.length, (idx+2) % nds.length]
        .forEach(i => nds[i]?.classList.add('lit'));
    idx++;
}, 750);
</script>

</body>
</html>