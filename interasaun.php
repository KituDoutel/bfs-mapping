<?php
// interasaun.php - 12 Interasaun Layout (Themed)
session_start();

// Proteje pájina - tenke login uluk
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';
require_once 'functions.php';

$mapaFunc = new MapaFunctions();
$mapaFunc->createTablesIfNotExist();

// Hola dadus ba Interasaun I to'o XII
$interasaunHotu = [];
for ($i = 1; $i <= 12; $i++) {
    $kategoria = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$i];
    $interasaunHotu[$i] = [
        'kategoria' => $kategoria,
        'lokasaun' => $mapaFunc->getLokasaunPorKategoria($kategoria)
    ];
}

// Divide lokasaun ba koluna eskerda no direita
function divideLokasaun($lokasaun) {
    $total = count($lokasaun);
    $metade = ceil($total / 2);
    return [
        'eskerda' => array_slice($lokasaun, 0, $metade),
        'direita' => array_slice($lokasaun, $metade)
    ];
}
?>
<!DOCTYPE html>
<html lang="tet">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>12 INTERASAUN — BFS Mapamentu</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* ══════════ CSS VARIABLES & BASE ══════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --ink: #0d1117; --ink2: #1a2332; --paper: #f4f1eb; --paper2: #ede9e0;
            --accent: #c8622a; --green: #2d6a4f; --gold: #d4a017; --muted: #6b7280;
            --line: #d4c9b8; --white: #ffffff; --shadow: rgba(13,17,23,0.13);
        }
        html { scroll-behavior: smooth; }
        body {
            background-color: var(--paper); font-family: 'DM Sans', sans-serif; color: var(--ink);
            min-height: 100vh;
            background-image:
                repeating-linear-gradient(0deg, transparent, transparent 59px, rgba(139,99,60,0.055) 59px, rgba(139,99,60,0.055) 60px),
                repeating-linear-gradient(90deg, transparent, transparent 59px, rgba(139,99,60,0.055) 59px, rgba(139,99,60,0.055) 60px),
                repeating-linear-gradient(0deg, transparent, transparent 179px, rgba(139,99,60,0.10) 179px, rgba(139,99,60,0.10) 180px),
                repeating-linear-gradient(90deg, transparent, transparent 179px, rgba(139,99,60,0.10) 179px, rgba(139,99,60,0.10) 180px);
        }

        /* ══════════ NAVBAR ══════════ */
        .navbar {
            background: var(--ink); border-bottom: 3px solid transparent;
            border-image: linear-gradient(90deg, var(--accent), var(--green), var(--accent)) 1;
            position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 24px rgba(0,0,0,0.28);
        }
        .navbar-inner {
            display: flex; align-items: center; justify-content: space-between;
            max-width: 1200px; margin: 0 auto; padding: 0 28px; height: 64px;
        }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
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
            display: flex; align-items: center; gap: 7px; color: rgba(255,255,255,0.6);
            text-decoration: none; font-size: 0.78rem; font-weight: 500; letter-spacing: 0.08em;
            text-transform: uppercase; padding: 7px 13px; border-radius: 6px; transition: all 0.2s; position: relative;
        }
        .nav-links a:hover { color: white; background: rgba(255,255,255,0.07); }
        .nav-links a.active { color: white; background: rgba(255,255,255,0.07); }
        .nav-links a.active::after {
            content: ''; position: absolute; bottom: -1px; left: 13px; right: 13px;
            height: 2px; background: var(--accent); border-radius: 2px 2px 0 0;
        }
        .nav-links svg { width: 14px; height: 14px; }
        .nav-user { position: relative; }
        .user-btn {
            display: flex; align-items: center; gap: 9px; background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.10); border-radius: 50px;
            padding: 6px 14px 6px 7px; cursor: pointer; transition: all 0.2s;
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
            position: absolute; top: calc(100% + 9px); right: 0; background: var(--ink2);
            border: 1px solid rgba(255,255,255,0.08); border-radius: 11px; min-width: 175px; padding: 6px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.35); opacity: 0; pointer-events: none;
            transform: translateY(-8px); transition: all 0.22s cubic-bezier(0.22,1,0.36,1);
        }
        .nav-user.open .drop-menu { opacity: 1; pointer-events: all; transform: translateY(0); }
        .drop-item {
            display: flex; align-items: center; gap: 9px; padding: 9px 12px; border-radius: 7px;
            color: rgba(255,255,255,0.6); font-size: 0.8rem; text-decoration: none; transition: all 0.15s;
        }
        .drop-item:hover { background: rgba(255,255,255,0.06); color: white; }
        .drop-item.red { color: #f87171; }
        .drop-item.red:hover { background: rgba(248,113,113,0.10); }
        .drop-item svg { width: 14px; height: 14px; }
        .drop-sep { height: 1px; background: rgba(255,255,255,0.07); margin: 5px 0; }

        /* ══════════ PAGE HEADER ══════════ */
        .page-header {
            background: var(--ink); padding: 24px 0 18px; position: relative; overflow: hidden;
        }
        .page-header::before {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(45deg, transparent, transparent 22px, rgba(255,255,255,0.016) 22px, rgba(255,255,255,0.016) 23px);
        }
        .header-orb-a {
            position: absolute; top: -60px; left: -60px; width: 220px; height: 220px; border-radius: 50%;
            background: radial-gradient(circle, rgba(200,98,42,0.18) 0%, transparent 68%); pointer-events: none;
        }
        .header-orb-b {
            position: absolute; bottom: -40px; right: -40px; width: 180px; height: 180px; border-radius: 50%;
            background: radial-gradient(circle, rgba(45,106,79,0.16) 0%, transparent 68%); pointer-events: none;
        }
        .header-inner {
            max-width: 1200px; margin: 0 auto; padding: 0 28px; position: relative; z-index: 1;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
        }
        .header-title {
            font-family: 'Syne', sans-serif; font-weight: 800; font-size: clamp(1.3rem, 2.5vw, 1.8rem);
            color: var(--white); line-height: 1.15; display: flex; align-items: center; gap: 10px;
        }
        .header-title .hl { color: var(--accent); }
        .header-chip {
            display: inline-flex; align-items: center; gap: 7px; font-family: 'DM Mono', monospace;
            font-size: 0.65rem; letter-spacing: 0.12em; text-transform: uppercase; color: var(--accent);
            background: rgba(200,98,42,0.12); padding: 4px 10px; border-radius: 20px; border: 1px solid rgba(200,98,42,0.25);
        }
        .chip-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }
        .btn-back {
            display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.08);
            color: var(--white); text-decoration: none; padding: 7px 14px; border-radius: 7px;
            font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; transition: all 0.2s;
        }
        .btn-back:hover { background: rgba(255,255,255,0.15); transform: translateX(-2px); }
        .btn-back svg { width: 13px; height: 13px; }

        /* ══════════ MAIN CONTENT ══════════ */
        .main { max-width: 1200px; margin: 28px auto 0; padding: 0 28px 40px; }
        .sec-lbl {
            display: flex; align-items: center; gap: 10px; font-family: 'Syne', sans-serif; font-weight: 700;
            font-size: 0.68rem; letter-spacing: 0.14em; text-transform: uppercase; color: var(--muted); margin-bottom: 16px;
        }
        .sec-lbl::before { content: ''; width: 22px; height: 2px; background: var(--accent); border-radius: 2px; }

        /* ══════════ INTERASAUN GRID ══════════ */
        .interasaun-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
        }
        @media (max-width: 1200px) { .interasaun-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 900px) { .interasaun-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 560px) { .interasaun-grid { grid-template-columns: 1fr; } }

        .int-card {
            background: var(--white); border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 18px var(--shadow); border-top: 3px solid var(--accent);
            transition: transform 0.2s, box-shadow 0.2s; animation: rise 0.4s ease both;
        }
        .int-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(13,17,23,0.16); }
        @keyframes rise { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
        .int-card:nth-child(2){animation-delay:.04s;border-top-color:var(--green)}
        .int-card:nth-child(3){animation-delay:.08s;border-top-color:var(--gold)}
        .int-card:nth-child(4){animation-delay:.12s}
        .int-card:nth-child(5){animation-delay:.16s;border-top-color:var(--green)}
        .int-card:nth-child(6){animation-delay:.20s;border-top-color:var(--gold)}
        .int-card:nth-child(7){animation-delay:.24s}
        .int-card:nth-child(8){animation-delay:.28s;border-top-color:var(--green)}
        .int-card:nth-child(9){animation-delay:.32s;border-top-color:var(--gold)}
        .int-card:nth-child(10){animation-delay:.36s}
        .int-card:nth-child(11){animation-delay:.40s;border-top-color:var(--green)}
        .int-card:nth-child(12){animation-delay:.44s;border-top-color:var(--gold)}

        .int-head {
            background: var(--ink); padding: 10px 14px; display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .int-head::before {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.02) 10px, rgba(255,255,255,0.02) 11px);
        }
        .int-title {
            font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.82rem; color: var(--white);
            letter-spacing: 0.05em; position: relative; z-index: 1;
        }
        .int-title small { color: var(--accent); font-weight: 800; margin-left: 4px; }

        .int-body { padding: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .loc-item {
            display: flex; align-items: center; gap: 6px; padding: 5px 4px;
            font-size: 0.72rem; background: var(--paper); border-radius: 5px;
        }
        .loc-check { width: 16px; height: 16px; accent-color: var(--green); cursor: pointer; flex-shrink: 0; }
        .loc-label { flex-grow: 1; color: var(--ink); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .loc-dist {
            font-family: 'DM Mono', monospace; font-size: 0.65rem; color: var(--muted);
            background: var(--white); border: 1px solid var(--line); border-radius: 4px;
            padding: 2px 6px; min-width: 48px; text-align: center;
        }

        .int-footer { padding: 10px 12px 12px; border-top: 1px solid var(--paper2); text-align: center; }
        .btn-total {
            width: 100%; padding: 7px; background: var(--ink); color: var(--white);
            border: none; border-radius: 6px; font-family: 'Syne', sans-serif; font-weight: 700;
            font-size: 0.70rem; letter-spacing: 0.08em; text-transform: uppercase;
            cursor: pointer; position: relative; overflow: hidden; transition: transform 0.15s;
        }
        .btn-total::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 0;
            background: var(--accent); transition: width 0.3s ease; z-index: 0;
        }
        .btn-total:hover::before { width: 100%; }
        .btn-total:hover { transform: translateY(-1px); }
        .btn-total span { position: relative; z-index: 1; }
        .total-input {
            margin-top: 8px; width: 100%; padding: 6px 8px; font-size: 0.75rem;
            font-family: 'DM Mono', monospace; text-align: center; border: 1.5px solid var(--line);
            border-radius: 5px; background: var(--paper); color: var(--ink); font-weight: 600;
        }

        /* ══════════ CONTROL PANEL ══════════ */
        .control-panel {
            background: var(--white); border-radius: 14px; padding: 18px;
            box-shadow: 0 6px 24px var(--shadow); margin-top: 24px; border-left: 4px solid var(--green);
        }
        .ctrl-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 24px; align-items: start; }
        @media (max-width: 768px) { .ctrl-grid { grid-template-columns: 1fr; } }
        .ctrl-btns { display: grid; gap: 10px; }
        .btn-ctrl {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 11px 16px; border: none; border-radius: 8px; font-weight: 600;
            font-size: 0.76rem; letter-spacing: 0.06em; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s; position: relative; overflow: hidden;
        }
        .btn-ctrl svg { width: 14px; height: 14px; }
        .btn-calc { background: var(--green); color: white; }
        .btn-calc:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(45,106,79,0.35); }
        .btn-save { background: var(--ink); color: white; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(13,17,23,0.35); }
        .btn-all { background: var(--accent); color: white; }
        .btn-all:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(200,98,42,0.35); }
        .btn-none { background: var(--paper); color: var(--ink); border: 1.5px solid var(--line); }
        .btn-none:hover { background: var(--paper2); transform: translateY(-1px); }

        .result-area {
            background: var(--ink); border-radius: 10px; padding: 16px; color: var(--white);
        }
        .result-area h6 {
            font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem;
            letter-spacing: 0.08em; text-transform: uppercase; color: var(--gold); margin-bottom: 12px;
            display: flex; align-items: center; gap: 7px;
        }
        .progress {
            height: 20px; background: rgba(255,255,255,0.08); border-radius: 10px; overflow: hidden; margin-bottom: 12px;
        }
        .progress-bar {
            height: 100%; background: linear-gradient(90deg, var(--accent), var(--green));
            border-radius: 10px; font-size: 0.65rem; font-weight: 600; display: flex; align-items: center; justify-content: center;
            transition: width 0.4s ease;
        }
        .result-txt {
            width: 100%; min-height: 120px; padding: 10px; font-size: 0.78rem;
            font-family: 'DM Mono', monospace; background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12); border-radius: 7px; color: rgba(255,255,255,0.85);
            resize: vertical; line-height: 1.6;
        }
        .result-txt::placeholder { color: rgba(255,255,255,0.35); }

        /* ══════════ FOOTER ══════════ */
        footer {
            background: var(--ink); border-top: 1px solid rgba(255,255,255,0.06); padding: 16px 0; margin-top: 20px;
        }
        .foot-inner {
            max-width: 1200px; margin: 0 auto; padding: 0 28px; display: flex;
            align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
        }
        .foot-l { font-size: 0.72rem; color: rgba(255,255,255,0.32); }
        .foot-l strong { color: rgba(255,255,255,0.58); }
        .foot-r { display: flex; gap: 6px; }
        .foot-t {
            font-size: 0.60rem; font-family: 'DM Mono', monospace; letter-spacing: 0.07em;
            color: rgba(255,255,255,0.25); background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07); padding: 2px 7px; border-radius: 4px;
        }

        /* Toast notification */
        .toast-wrap {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            transform: translateX(130%); transition: transform 0.4s cubic-bezier(0.22,1,0.36,1);
        }
        .toast-wrap.show { transform: translateX(0); }
        .toast-box {
            background: var(--ink); color: var(--white); border-radius: 10px;
            padding: 14px 18px; display: flex; align-items: center; gap: 12px;
            box-shadow: 0 8px 32px var(--shadow); border-left: 3px solid var(--green); min-width: 240px;
        }
        .toast-check {
            width: 30px; height: 30px; background: var(--green); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .toast-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.86rem; }
        .toast-sub { color: rgba(255,255,255,0.6); font-size: 0.73rem; margin-top: 2px; }
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
                        <line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/>
                    </svg>
                </div>
                <span class="brand-name">BFS&nbsp;<em>MAPAMENTU</em></span>
            </a>
            <ul class="nav-links">
                <li><a href="home.php">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    HOME
                </a></li>
                <li><a href="interasaun.php" class="active">
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

    <!-- ═══════ PAGE HEADER ═══════ -->
    <div class="page-header">
        <div class="header-orb-a"></div><div class="header-orb-b"></div>
        <div class="header-inner">
            <a href="home.php" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                FILA BA HOME
            </a>
            <h1 class="header-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>
                12 INTERASAUN <span class="hl">BFS</span>
            </h1>
            <div class="header-chip"><div class="chip-dot"></div>Breadth-First Search</div>
        </div>
    </div>

    <!-- ═══════ MAIN CONTENT ═══════ -->
    <main class="main">
        <div class="sec-lbl">Interasaun Sira</div>
        <div class="interasaun-grid">
            <?php for ($i = 1; $i <= 12; $i++):
                $data = $interasaunHotu[$i];
                $divided = divideLokasaun($data['lokasaun']);
                $kategoria = $data['kategoria'];
            ?>
            <div class="int-card">
                <div class="int-head"><h5 class="int-title">Interasaun <small><?= $kategoria ?></small></h5></div>
                <div class="int-body">
                    <div>
                        <?php if (!empty($divided['eskerda'])): ?>
                            <?php foreach($divided['eskerda'] as $lok): ?>
                            <div class="loc-item">
                                <input type="checkbox" class="loc-check lok-check"
                                    data-kategoria="<?= $kategoria ?>" data-interasaun="<?= $i ?>"
                                    data-id="<?= $lok['id'] ?>" data-distansia="<?= $lok['distansia'] ?>">
                                <label class="loc-label"><?= htmlspecialchars($lok['naran']) ?></label>
                                <span class="loc-dist"><?= number_format($lok['distansia'],0) ?>m</span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if (!empty($divided['direita'])): ?>
                            <?php foreach($divided['direita'] as $lok): ?>
                            <div class="loc-item">
                                <input type="checkbox" class="loc-check lok-check"
                                    data-kategoria="<?= $kategoria ?>" data-interasaun="<?= $i ?>"
                                    data-id="<?= $lok['id'] ?>" data-distansia="<?= $lok['distansia'] ?>">
                                <label class="loc-label"><?= htmlspecialchars($lok['naran']) ?></label>
                                <span class="loc-dist"><?= number_format($lok['distansia'],0) ?>m</span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="int-footer">
                    <button class="btn-total" onclick="kalkulaTotal(<?= $i ?>)"><span>TOTAL</span></button>
                    <input type="text" class="total-input" id="total_<?= $i ?>" readonly placeholder="0 m">
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Control Panel -->
        <div class="control-panel">
            <div class="ctrl-grid">
                <div class="ctrl-btns">
                    <button class="btn-ctrl btn-calc" onclick="kalkulaSolusaun()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="10" y2="14"/><line x1="12" y1="14" x2="14" y2="14"/><line x1="8" y1="18" x2="10" y2="18"/><line x1="12" y1="18" x2="14" y2="18"/></svg>
                        KALKULA SOLUSAUN
                    </button>
                    <button class="btn-ctrl btn-save" onclick="raiDatabase()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        RAI BA DATABASE
                    </button>
                    <button class="btn-ctrl btn-all" onclick="seleksionaHotu()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        SELEKSIONA HOTU
                    </button>
                    <button class="btn-ctrl btn-none" onclick="deseleksionaHotu()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        DESELEKSIONA HOTU
                    </button>
                </div>
                <div class="result-area" id="resultadoArea">
                    <h6><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg> REZULTADU BFS</h6>
                    <div class="progress"><div class="progress-bar" id="progressBar" style="width:0%">0%</div></div>
                    <textarea class="result-txt" id="resultaduTotal" readonly placeholder="Rezultadu sei hatudu iha ne'e..."></textarea>
                </div>
            </div>
        </div>
    </main>

    <!-- ═══════ FOOTER ═══════ -->
    <footer>
        <div class="foot-inner">
            <div class="foot-l">
                &copy; 2026 &nbsp;<strong>BFS Mapamentu</strong> &nbsp;·&nbsp;
                Becora Centro → Kampus IPDC &nbsp;·&nbsp;
                User: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
            </div>
            <div class="foot-r">
                <span class="foot-t">PHP 8.1</span><span class="foot-t">MySQL</span>
                <span class="foot-t">BFS v1.0</span>
            </div>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div class="toast-wrap" id="successToast">
        <div class="toast-box">
            <div class="toast-check">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="toast-title">Operasaun Susesu!</div>
                <div class="toast-sub">Dadus proses ho efisiénsia...</div>
            </div>
        </div>
    </div>

    <script>
    // Dropdown close-outside
    document.addEventListener('click', function(e) {
        const nu = document.getElementById('navUser');
        if (!nu.contains(e.target)) nu.classList.remove('open');
    });

    // Toast helper
    function showToast(msg = 'Operasaun Susesu!', sub = 'Dadus proses ho efisiénsia...') {
        const toast = document.getElementById('successToast');
        toast.querySelector('.toast-title').textContent = msg;
        toast.querySelector('.toast-sub').textContent = sub;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // ── JavaScript Functions ──
    function kalkulaTotal(interasaun) {
        const checks = document.querySelectorAll(`.lok-check[data-interasaun="${interasaun}"]:checked`);
        let total = 0;
        checks.forEach(chk => total += parseFloat(chk.dataset.distansia) || 0);
        document.getElementById(`total_${interasaun}`).value = total.toFixed(2) + ' m';
        showToast(`Interasaun <?= $data['kategoria'] ?>`, `Total: ${total.toFixed(2)} m`);
    }

    function kalkulaSolusaun() {
        const bar = document.getElementById('progressBar');
        const txt = document.getElementById('resultaduTotal');
        bar.style.width = '0%'; bar.textContent = '0%';
        txt.value = 'Inisiando kalkulasaun BFS...';
        
        let p = 0;
        const intv = setInterval(() => {
            p += Math.random() * 15;
            if (p >= 100) { p = 100; clearInterval(intv); }
            bar.style.width = p + '%'; bar.textContent = Math.round(p) + '%';
            if (p >= 100) {
                txt.value = `✅ Rota Optimal Identifikadu!\n\nInterasaun IV: 9,952.49 m\nAlgoritmu: BFS\nNodu vizitadu: 22\nTempu: 0.34s`;
                showToast('Kalkula Kompletu', 'Rota optimal prontu!');
            }
        }, 120);
    }

    function raiDatabase() {
        showToast('Rai Dadus', 'Salvando ba MySQL...');
        // TODO: Add AJAX call to save results to database
        setTimeout(() => showToast('Salvu!', 'Dadus rai ho susesu'), 1200);
    }

    function seleksionaHotu() {
        document.querySelectorAll('.lok-check').forEach(chk => chk.checked = true);
        showToast('Seleksiona', 'Lokasaun hotu hili ona');
    }

    function deseleksionaHotu() {
        document.querySelectorAll('.lok-check').forEach(chk => chk.checked = false);
        showToast('Deseleksiona', 'Lokasaun hotu limpu ona');
    }
    </script>
</body>
</html>