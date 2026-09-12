<?php
/**
 * DECAN MOVIE V2.9 - UNIVERSAL CROSS-DEVICE STREAMING ENGINE + ATOM BRANDING
 * File: index.php
 * Architect: Lenny Muriuki (Decan Konnect)
 */

header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Content-Type: text/html; charset=UTF-8");

$siteName = "Decan Movie";
$siteUrl  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$pageTitle = "Decan Movie - Universal Multi-Device Portal | Anime & Smart Discovery";
$metaDesc  = "Decan Movie is a universal multi-device movie, anime and TV discovery portal with smart local recommendations, anime genre discovery and personal libraries.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/svg+xml" href="decan-atom-logo.svg">
    
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($siteUrl) ?>">
    <meta name="theme-color" content="#0f1115">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="manifest.json">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f1115;
            --card-bg: #1a1d24;
            --neon-cyan: #00e5ff;
            --neon-pink: #ff007f;
            --neon-green: #00ffaa;
            --neon-yellow: #ffb703;
            --text-main: #f0f0f5;
            --text-muted: #8b8f9e;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            overflow-x: hidden;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation */
        nav {
            width: var(--sidebar-width);
            background: #14161c;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; bottom: 0; left: 0;
            z-index: 200;
            transition: transform 0.3s ease;
        }

        .nav-header {
            padding: 24px 20px;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .nav-header span { color: var(--neon-cyan); }

        .menu-category-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #555570;
            padding: 20px 20px 8px 20px;
            font-weight: 700;
        }

        .menu-items {
            list-style: none;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #a4a7b5;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .menu-link:hover, .menu-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            border-left: 4px solid var(--neon-cyan);
        }

        .menu-link i, .menu-link svg { width: 20px; height: 20px; font-size: 1.1rem; color: #5a5f73; transition: 0.2s; fill: currentColor; flex-shrink: 0; }
        .menu-link.active i, .menu-link:hover i, .menu-link.active svg, .menu-link:hover svg { color: var(--neon-cyan); fill: var(--neon-cyan); }

        /* Sidebar Footer Social Links */
        .sidebar-footer {
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: #101217;
        }

        .sidebar-social-grid {
            display: flex;
            gap: 8px;
            justify-content: space-between;
            margin-top: 8px;
        }

        .social-icon-btn {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a4a7b5;
            text-decoration: none;
            transition: 0.2s;
        }

        .social-icon-btn svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .social-icon-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--neon-cyan);
            border-color: var(--neon-cyan);
        }

        /* App Container */
        .app-container {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            display: flex;
            flex-direction: column;
            transition: margin 0.3s ease, width 0.3s ease;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 4%;
            background: rgba(15, 17, 21, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 15px;
            flex-wrap: wrap;
        }

        .brand-logo-area {
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.5px;
            display: none;
            align-items: center;
            gap: 9px;
            line-height: 1;
            white-space: nowrap;
        }
        .brand-logo-area span { color: var(--neon-cyan); }

        /* Additive Decan Movie atom branding */
        .decan-atom-logo {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: inline-block;
            filter: drop-shadow(0 0 7px rgba(0, 229, 255, .55));
            animation: decanAtomPulse 3s ease-in-out infinite;
        }
        .decan-atom-logo .atom-orbit {
            fill: none;
            stroke: currentColor;
            stroke-width: 1.55;
            opacity: .95;
        }
        .decan-atom-logo .atom-core {
            fill: currentColor;
            filter: drop-shadow(0 0 4px currentColor);
        }
        .decan-atom-logo .atom-spark {
            fill: #fff;
            opacity: .9;
        }
        .nav-header .decan-atom-logo {
            width: 40px;
            height: 40px;
            flex-basis: 40px;
        }
        .nav-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }
        .decan-logo-text {
            display: inline-flex;
            flex-direction: column;
            line-height: .92;
        }
        .decan-logo-text .decan-word {
            color: #fff;
            font-size: 1rem;
            letter-spacing: 1.6px;
            font-weight: 900;
        }
        .decan-logo-text .movie-word {
            color: var(--neon-cyan);
            font-size: .72rem;
            letter-spacing: 2.5px;
            font-weight: 800;
        }
        @keyframes decanAtomPulse {
            0%, 100% { transform: scale(1); opacity: .92; }
            50% { transform: scale(1.07); opacity: 1; }
        }
        @media (prefers-reduced-motion: reduce) {
            .decan-atom-logo { animation: none; }
        }

        .toggle-menu-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            display: none;
        }

        .header-controls {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
            flex-grow: 1;
            justify-content: flex-end;
        }

        .filter-group {
            display: flex;
            gap: 8px;
        }

        .filter-select {
            background: #1a1d24;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 8px 12px;
            border-radius: 8px;
            outline: none;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .search-box {
            display: flex;
            background: #1a1d24;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            transition: 0.3s;
            flex-grow: 1;
            max-width: 300px;
        }

        .search-box:focus-within {
            border-color: var(--neon-cyan);
        }

        .search-box input {
            background: transparent;
            border: none;
            padding: 10px 18px;
            color: #fff;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
        }

        .search-box button {
            background: transparent;
            border: none;
            color: #8b8f9e;
            padding: 0 15px;
            cursor: pointer;
        }

        main { padding: 30px 4%; flex-grow: 1; }
        .section-header-box { margin-bottom: 25px; }
        .section-title { font-size: 1.6rem; font-weight: 700; text-transform: capitalize; }

        /* Universal Grid Layout */
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 16px;
        }

        .movie-card {
            background: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            position: relative;
        }

        .movie-card:active { transform: scale(0.97); }

        .movie-card img { width: 100%; height: 220px; object-fit: cover; background: #111; }
        .movie-info { padding: 10px; }
        .movie-info h3 { font-size: 0.85rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
        .rating-badge { display: inline-flex; align-items: center; gap: 3px; background: rgba(255, 183, 3, 0.15); color: var(--neon-yellow); padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; }
        .year-badge { display: inline-block; float: right; color: var(--text-muted); font-size: 0.75rem; margin-top: 2px; }

        /* Skeleton Loading */
        .skeleton-card {
            height: 280px;
            background: linear-gradient(90deg, #1a1d24 25%, #242830 50%, #1a1d24 75%);
            background-size: 200% 100%;
            border-radius: 10px;
            animation: loadingSkeleton 1.5s infinite;
        }

        @keyframes loadingSkeleton { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        .pagination-container { display: flex; justify-content: center; margin-top: 40px; }
        .load-more-btn {
            background: #1a1d24; border: 1px solid rgba(255,255,255,0.1); color: #fff;
            padding: 12px 35px; font-size: 1rem; font-weight: 600; border-radius: 30px; cursor: pointer; transition: 0.2s;
        }
        .load-more-btn:hover { background: rgba(255,255,255,0.1); }

        /* Modal Sheet */
        .modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(10, 11, 15, 0.98); backdrop-filter: blur(10px); z-index: 1000;
            justify-content: center; align-items: center;
        }

        .modal-content {
            background: #14161c; width: 100%; max-width: 1100px; border-radius: 16px;
            position: relative; overflow: hidden; height: 95vh; display: flex; flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8); border: 1px solid rgba(255,255,255,0.05);
        }

        .close-modal { 
            position: absolute; top: 20px; left: 20px; z-index: 1010; 
            background: rgba(0,0,0,0.6); padding: 10px; border-radius: 50%;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(255,255,255,0.1); transition: 0.2s;
        }
        .close-modal svg { width: 24px; height: 24px; fill: none; stroke: #fff; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        .modal-body { overflow-y: auto; flex-grow: 1; -webkit-overflow-scrolling: touch; }

        .modal-backdrop-header {
            height: 320px; background-size: cover; background-position: center top; position: relative;
            display: flex; align-items: flex-end; padding: 25px;
        }
        .modal-backdrop-header::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(180deg, rgba(20,22,28,0) 0%, rgba(20,22,28,1) 100%);
        }

        .modal-header-details { position: relative; z-index: 10; display: flex; gap: 20px; align-items: flex-end; width: 100%; }
        .modal-poster { width: 130px; height: 195px; border-radius: 10px; object-fit: cover; box-shadow: 0 10px 30px rgba(0,0,0,0.5); flex-shrink: 0; }

        .action-button-toolbar {
            display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; padding: 15px;
            background: rgba(0,0,0,0.2); border-radius: 12px;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e0e0e6; padding: 10px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; text-decoration: none;
            flex-grow: 1;
        }

        .action-btn:hover { background: rgba(255, 255, 255, 0.1); }
        .action-btn.primary { background: #fff; color: #000; border: none; font-weight: 700; }
        .action-btn.primary:hover { background: var(--neon-cyan); }
        .action-btn.trailer-btn { background: rgba(255, 0, 127, 0.15); color: var(--neon-pink); border-color: rgba(255, 0, 127, 0.3); }
        .action-btn.trailer-btn:hover { background: rgba(255, 0, 127, 0.3); color: #fff; }

        .stat-badge-grid { display: flex; flex-wrap: wrap; gap: 8px; margin: 12px 0; }
        .stat-badge { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; color: #b0b0c5; }
        .stat-badge strong { color: #fff; }

        .cast-row { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; margin-top: 10px; -webkit-overflow-scrolling: touch; }
        .cast-card { min-width: 90px; text-align: center; font-size: 0.75rem; }
        .cast-card img { width: 75px; height: 75px; border-radius: 50%; object-fit: cover; margin-bottom: 6px; border: 1px solid rgba(255,255,255,0.1); }

        .episode-box { max-height: 280px; overflow-y: auto; background: rgba(0,0,0,0.3); border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); margin-top: 10px; -webkit-overflow-scrolling: touch; }
        .episode-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 0.85rem; cursor: pointer; transition: 0.2s; }
        .episode-item:hover { background: rgba(255, 255, 255, 0.05); }

        /* Universal Device-Proof Theater Mode Player Box */
        #theater-container {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #000; z-index: 3000; display: none; flex-direction: column;
            justify-content: center; align-items: center;
        }
        .theater-header { 
            position: absolute; top: 0; left: 0; right: 0; padding: 15px 25px; 
            display: flex; justify-content: space-between; align-items: center; 
            background: linear-gradient(180deg, rgba(15,17,21,0.95) 0%, rgba(15,17,21,0) 100%); 
            z-index: 3010; 
        }
        
        .theater-viewport-wrapper {
            position: relative;
            width: 100%;
            max-width: 1400px;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
        }

        .theater-ratio-box {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 ratio wrapper */
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.9);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .theater-iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .horizontal-scroll { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 15px; margin-top: 10px; -webkit-overflow-scrolling: touch; }
        .similar-card { min-width: 130px; cursor: pointer; }
        .similar-card img { width: 130px; height: 195px; border-radius: 8px; object-fit: cover; }
        .similar-card h4 { font-size: 0.8rem; margin-top: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff; }

        .toast {
            position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.9); border: 1px solid rgba(255,255,255,0.1); color: #fff;
            padding: 12px 24px; border-radius: 30px; font-weight: 600; font-size: 0.9rem;
            z-index: 4000; display: none; box-shadow: 0 10px 30px rgba(0,0,0,0.5); text-align: center;
        }


        /* ===== DECAN MOVIE V2.7 ADDITIVE FEATURE LAYER ===== */
        .feature-toolbar {
            display: none;
            margin: 0 0 24px 0;
            padding: 16px;
            background: rgba(255,255,255,0.035);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            backdrop-filter: blur(16px);
            box-shadow: 0 14px 35px rgba(0,0,0,0.18);
        }
        .feature-toolbar.active { display: block; }
        .feature-toolbar-title {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:12px;
        }
        .feature-toolbar-title h3 { font-size:1rem; }
        .feature-toolbar-title span { color:var(--text-muted); font-size:.75rem; }
        .filter-chip-row { display:flex; flex-wrap:wrap; gap:8px; }
        .filter-chip {
            border:1px solid rgba(255,255,255,.09);
            background:rgba(255,255,255,.045);
            color:#cfd1dc;
            padding:8px 12px;
            border-radius:999px;
            cursor:pointer;
            font-size:.78rem;
            font-weight:700;
            transition:.2s ease;
        }
        .filter-chip:hover, .filter-chip.active {
            color:#fff;
            border-color:rgba(0,229,255,.55);
            background:rgba(0,229,255,.12);
            box-shadow:0 0 18px rgba(0,229,255,.08);
        }
        .anime-control-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
            gap:10px;
        }
        .anime-control-grid select {
            width:100%;
        }
        .universal-control-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
            gap:10px;
        }
        .universal-control-grid select {
            width:100%;
        }
        .responsive-chip-row {
            display:flex;
            gap:8px;
            overflow-x:auto;
            padding:2px 2px 8px;
            scrollbar-width:thin;
            -webkit-overflow-scrolling:touch;
        }
        .responsive-chip-row .filter-chip {
            flex:0 0 auto;
            min-height:42px;
            white-space:nowrap;
        }
        .mobile-filter-note {
            color:var(--text-muted);
            font-size:.72rem;
            margin-top:8px;
            display:none;
        }
        .library-actions {
            display:flex;
            gap:10px;
            align-items:center;
            justify-content:space-between;
            flex-wrap:wrap;
            margin-bottom:16px;
            padding:12px 14px;
            border-radius:14px;
            background:rgba(255,255,255,.03);
            border:1px solid rgba(255,255,255,.07);
        }
        .library-count { color:var(--text-muted); font-size:.8rem; }
        .danger-btn {
            background:rgba(255,0,127,.10);
            border:1px solid rgba(255,0,127,.28);
            color:#ff5cab;
            padding:9px 13px;
            border-radius:9px;
            cursor:pointer;
            font-weight:700;
        }
        .danger-btn:hover { background:rgba(255,0,127,.2); color:#fff; }
        .empty-state {
            grid-column:1/-1;
            padding:55px 20px;
            text-align:center;
            border:1px dashed rgba(255,255,255,.1);
            border-radius:16px;
            color:var(--text-muted);
            background:rgba(255,255,255,.02);
        }
        .empty-state i { font-size:2rem; color:var(--neon-cyan); margin-bottom:12px; }
        .movie-card .quick-actions {
            position:absolute;
            top:8px;
            right:8px;
            display:flex;
            gap:6px;
            opacity:0;
            transform:translateY(-4px);
            transition:.2s ease;
        }
        .movie-card:hover .quick-actions,
        .movie-card:focus-within .quick-actions { opacity:1; transform:translateY(0); }
        .quick-action-btn {
            width:32px;
            height:32px;
            border-radius:50%;
            border:1px solid rgba(255,255,255,.14);
            background:rgba(0,0,0,.68);
            color:#fff;
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            backdrop-filter:blur(10px);
        }
        .quick-action-btn:hover { border-color:var(--neon-cyan); color:var(--neon-cyan); }
        .recommendation-banner {
            display:none;
            margin-bottom:22px;
            padding:18px;
            border-radius:16px;
            background:linear-gradient(135deg,rgba(0,229,255,.09),rgba(255,0,127,.07));
            border:1px solid rgba(255,255,255,.08);
        }
        .recommendation-banner.active { display:block; }
        .recommendation-banner h3 { margin-bottom:6px; }
        .recommendation-banner p { color:var(--text-muted); font-size:.82rem; }
        .hero-strip {
            display:none;
            margin-bottom:24px;
            min-height:260px;
            border-radius:20px;
            overflow:hidden;
            position:relative;
            background:#151820;
            border:1px solid rgba(255,255,255,.07);
        }
        .hero-strip.active { display:flex; }
        .hero-strip-bg {
            position:absolute;
            inset:0;
            background-size:cover;
            background-position:center;
            opacity:.42;
            filter:saturate(1.15);
        }
        .hero-strip::after {
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(90deg,rgba(15,17,21,.97) 0%,rgba(15,17,21,.74) 48%,rgba(15,17,21,.25) 100%);
        }
        .hero-strip-content { position:relative; z-index:2; padding:30px; max-width:760px; display:flex; flex-direction:column; justify-content:center; }
        .hero-kicker { color:var(--neon-cyan); font-size:.72rem; text-transform:uppercase; letter-spacing:1.8px; font-weight:800; margin-bottom:8px; }
        .hero-strip h2 { font-size:2rem; line-height:1.08; margin-bottom:10px; }
        .hero-strip p { color:#c2c5d0; line-height:1.55; max-width:650px; font-size:.88rem; }
        .hero-buttons { display:flex; gap:9px; flex-wrap:wrap; margin-top:15px; }
        .hero-mini-btn {
            border:1px solid rgba(255,255,255,.1);
            background:rgba(255,255,255,.06);
            color:#fff;
            border-radius:10px;
            padding:10px 14px;
            cursor:pointer;
            font-weight:700;
        }
        .hero-mini-btn.primary { background:#fff; color:#000; border:none; }
        .hero-mini-btn:hover { border-color:var(--neon-cyan); }
        .stats-strip {
            display:none;
            grid-template-columns:repeat(auto-fit,minmax(130px,1fr));
            gap:10px;
            margin-bottom:22px;
        }
        .stats-strip.active { display:grid; }
        .mini-stat {
            padding:13px 14px;
            border-radius:13px;
            background:rgba(255,255,255,.03);
            border:1px solid rgba(255,255,255,.07);
        }
        .mini-stat strong { display:block; font-size:1.05rem; color:#fff; }
        .mini-stat span { color:var(--text-muted); font-size:.7rem; }
        .surprise-btn {
            background:linear-gradient(135deg,rgba(0,229,255,.13),rgba(255,0,127,.11));
            border:1px solid rgba(0,229,255,.25);
            color:#fff;
            padding:10px 14px;
            border-radius:10px;
            cursor:pointer;
            font-weight:800;
        }
        .surprise-btn:hover { border-color:var(--neon-cyan); box-shadow:0 0 20px rgba(0,229,255,.08); }
        .rating-panel { margin-top:18px; padding:14px; border:1px solid rgba(255,255,255,.07); border-radius:13px; background:rgba(255,255,255,.025); }
        .rating-stars { display:flex; gap:5px; margin-top:8px; }
        .rating-star-btn { border:0; background:transparent; color:#555a68; font-size:1.3rem; cursor:pointer; padding:2px; }
        .rating-star-btn.active, .rating-star-btn:hover { color:var(--neon-yellow); }
        .favorite-active { color:var(--neon-pink) !important; }
        .profile-less-note { color:var(--text-muted); font-size:.72rem; margin-top:7px; }
        @media (max-width: 900px) {
            .hero-strip { min-height:300px; }
            .hero-strip h2 { font-size:1.55rem; }
            .hero-strip-content { padding:22px; }
            .movie-card .quick-actions { opacity:1; transform:none; }
        }

        @media (max-width: 900px) {
            nav { transform: translateX(-100%); }
            nav.open { transform: translateX(0); }
            .app-container { margin-left: 0; width: 100%; }
            .toggle-menu-btn { display: block; }
            .brand-logo-area { display: block; }
            .header-controls { justify-content: flex-start; width: 100%; }
            .search-box { max-width: 100%; width: 100%; }
            .modal-header-details { flex-direction: column; align-items: flex-start; }
            .modal-poster { width: 110px; height: 165px; }
            .movie-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
            .movie-card img { height: 160px; }
            .feature-toolbar {
                margin-bottom:16px;
                padding:12px;
                border-radius:14px;
            }
            .feature-toolbar-title {
                align-items:flex-start;
            }
            .feature-toolbar-title h3 { font-size:.95rem; }
            .feature-toolbar-title span { display:block; line-height:1.45; margin-top:3px; }
            .anime-control-grid, .universal-control-grid {
                grid-template-columns:repeat(2,minmax(0,1fr));
                gap:8px;
            }
            .anime-control-grid select, .universal-control-grid select {
                min-height:44px;
                font-size:.8rem;
            }
            .responsive-chip-row {
                margin-left:-2px;
                margin-right:-2px;
                padding-left:2px;
                padding-right:2px;
            }
            .responsive-chip-row .filter-chip {
                min-height:44px;
                padding:10px 12px;
            }
            .mobile-filter-note { display:block; }
        }
        @media (max-width: 520px) {
            .anime-control-grid, .universal-control-grid {
                grid-template-columns:1fr;
            }
            .feature-toolbar-title .surprise-btn {
                width:100%;
                min-height:44px;
            }
        }
    </style>
</head>
<body>

    <div class="toast" id="toast-msg"></div>

    <!-- Navigation Hub -->
    <nav id="sidebar-menu">
        <div class="nav-header" aria-label="Decan Movie">
            <svg class="decan-atom-logo" viewBox="0 0 64 64" role="img" aria-label="Decan Movie atom logo">
                <ellipse class="atom-orbit" cx="32" cy="32" rx="25" ry="10" transform="rotate(0 32 32)"></ellipse>
                <ellipse class="atom-orbit" cx="32" cy="32" rx="25" ry="10" transform="rotate(60 32 32)"></ellipse>
                <ellipse class="atom-orbit" cx="32" cy="32" rx="25" ry="10" transform="rotate(120 32 32)"></ellipse>
                <circle class="atom-core" cx="32" cy="32" r="5.2"></circle>
                <circle class="atom-spark" cx="32" cy="32" r="1.7"></circle>
            </svg>
            <span class="decan-logo-text"><span class="decan-word">DECAN</span><span class="movie-word">MOVIE</span></span>
        </div>
        <ul class="menu-items">
            <div class="menu-category-title">Platform Hub</div>
            <li class="menu-link active" onclick="switchCategory('trending', this)"><i class="fas fa-fire"></i> Global Trending</li>
            <li class="menu-link" onclick="switchCategory('netflix', this)"><i class="fab fa-netflix"></i> Netflix Trends</li>
            <li class="menu-link" onclick="switchCategory('amazon', this)"><i class="fab fa-amazon"></i> Amazon Prime</li>
            <li class="menu-link" onclick="switchCategory('disney', this)"><i class="fas fa-magic"></i> Disney+ Exclusives</li>
            <li class="menu-link" onclick="switchCategory('apple', this)"><i class="fab fa-apple"></i> Apple TV+</li>

            <div class="menu-category-title">Movie Box Categories</div>
            <li class="menu-link" onclick="switchCategory('top_movies', this)"><i class="fas fa-film"></i> Top Movies</li>
            <li class="menu-link" onclick="switchCategory('movie_portal', this)"><i class="fas fa-sliders"></i> Movie Portal</li>
            <li class="menu-link" onclick="switchCategory('series', this)"><i class="fas fa-tv"></i> Top Series</li>
            <li class="menu-link" onclick="switchCategory('top_anime', this)"><i class="fas fa-journal-whills"></i> Top Anime</li>
            <li class="menu-link" onclick="switchCategory('anime_portal', this)"><i class="fas fa-dragon"></i> Anime Portal</li>
            <li class="menu-link" onclick="switchCategory('cartoons', this)"><i class="fas fa-child"></i> Top Cartoons</li>
            <li class="menu-link" onclick="switchCategory('afro', this)"><i class="fas fa-globe-africa"></i> Afro Cinema</li>
            <li class="menu-link" onclick="switchCategory('upcoming', this)"><i class="fas fa-calendar-star"></i> Upcoming Movies</li>
            <li class="menu-link" onclick="showContinueWatching()"><i class="fas fa-forward"></i> Continue Watching</li>
            <li class="menu-link" onclick="showReleaseCalendar()"><i class="fas fa-calendar-days"></i> Release Calendar</li>
            <li class="menu-link" onclick="showMoodDiscovery()"><i class="fas fa-face-smile"></i> Mood Discovery</li>

            <div class="menu-category-title">User Library</div>
            <li class="menu-link" onclick="showWatchlist()"><i class="fas fa-bookmark"></i> My Watchlist</li>
            <li class="menu-link" onclick="showFavorites()"><i class="fas fa-heart"></i> My Favorites</li>
            <li class="menu-link" onclick="showRatings()"><i class="fas fa-star"></i> My Ratings</li>
            <li class="menu-link" onclick="showRecommendations()"><i class="fas fa-wand-magic-sparkles"></i> For You</li>
            <li class="menu-link" onclick="showCustomList()"><i class="fas fa-list"></i> Custom Collections</li>
            <li class="menu-link" onclick="showHistory()"><i class="fas fa-history"></i> Viewing History</li>

            <div class="menu-category-title">Community & Support</div>
            <a class="menu-link" href="https://chat.whatsapp.com/II5OcY121lE7tQYSZHBVha?s=cl&p=a&mlu=0&amv=1" target="_blank">
                <svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448zM12 2.138c-5.442 0-9.871 4.428-9.873 9.87-.001 1.748.463 3.46 1.343 4.966l-.942 3.441 3.529-.925c1.439.784 3.09 1.198 4.773 1.198 5.442 0 9.872-4.428 9.874-9.871.002-5.444-4.426-9.872-9.876-9.879zm5.397 13.916c-.229-.115-1.355-.668-1.565-.745-.21-.078-.364-.115-.518.115-.154.229-.597.745-.733.899-.136.154-.272.174-.501.058-.229-.115-1.066-.393-2.032-1.253-.753-.672-1.261-1.503-1.41-1.732-.154-.229-.016-.353.1-.468.103-.103.229-.272.344-.408.115-.136.154-.229.229-.382.076-.153.038-.287-.019-.402-.058-.115-.518-1.249-.711-1.711-.187-.451-.377-.39-.518-.397l-.443-.008c-.154 0-.404.058-.615.287-.211.229-.81.794-.81 1.936s.83 2.012.945 2.167c.115.154 1.636 2.497 3.962 3.501.554.239.986.382 1.325.489.558.177 1.066.152 1.467.092.448-.067 1.355-.554 1.547-1.089.192-.534.192-.992.134-1.089-.057-.097-.211-.154-.44-.27z"/></svg>
                WhatsApp Group
            </a>
            <a class="menu-link" href="https://whatsapp.com/channel/0029VbDE4yDLNSa4gNx8jT0E" target="_blank">
                <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 2.182c5.422 0 9.818 4.396 9.818 9.818 0 5.422-4.396 9.818-9.818 9.818-5.422 0-9.818-4.396-9.818-9.818 0-5.422 4.396-9.818 9.818-9.818zm1.091 4.364h-2.182v5.455l4.364 2.618 1.091-1.745-3.273-1.964V6.546z"/></svg>
                WhatsApp Channel
            </a>
            <a class="menu-link" href="https://chat.whatsapp.com/FXwCMeUqWCcJImjBacK5B6?s=cl&p=a&mlu=0&amv=1" target="_blank">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/></svg>
                Idea Labs WhatsApp
            </a>
        </ul>

        <!-- Footer with Social Links & SVGs -->
        <div class="sidebar-footer">
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 6px;">Connect & Explore</div>
            <div class="sidebar-social-grid">
                <!-- GitHub -->
                <a href="https://github.com/lenvartica" class="social-icon-btn" target="_blank" title="GitHub">
                    <svg viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                </a>
                <!-- Instagram -->
                <a href="https://www.instagram.com/its._.decan?igsh=bXB5eTZjeDdmMmZ1" class="social-icon-btn" target="_blank" title="Instagram">
                    <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <!-- Facebook -->
                <a href="https://www.facebook.com/Lenny.Decan.01" class="social-icon-btn" target="_blank" title="Facebook">
                    <svg viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                </a>
                <!-- WhatsApp -->
                <a href="https://chat.whatsapp.com/II5OcY121lE7tQYSZHBVha?s=cl&p=a&mlu=0&amv=1" class="social-icon-btn" target="_blank" title="WhatsApp Group">
                    <svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448zM12 2.138c-5.442 0-9.871 4.428-9.873 9.87-.001 1.748.463 3.46 1.343 4.966l-.942 3.441 3.529-.925c1.439.784 3.09 1.198 4.773 1.198 5.442 0 9.872-4.428 9.874-9.871.002-5.444-4.426-9.872-9.876-9.879zm5.397 13.916c-.229-.115-1.355-.668-1.565-.745-.21-.078-.364-.115-.518.115-.154.229-.597.745-.733.899-.136.154-.272.174-.501.058-.229-.115-1.066-.393-2.032-1.253-.753-.672-1.261-1.503-1.41-1.732-.154-.229-.016-.353.1-.468.103-.103.229-.272.344-.408.115-.136.154-.229.229-.382.076-.153.038-.287-.019-.402-.058-.115-.518-1.249-.711-1.711-.187-.451-.377-.39-.518-.397l-.443-.008c-.154 0-.404.058-.615.287-.211.229-.81.794-.81 1.936s.83 2.012.945 2.167c.115.154 1.636 2.497 3.962 3.501.554.239.986.382 1.325.489.558.177 1.066.152 1.467.092.448-.067 1.355-.554 1.547-1.089.192-.534.192-.992.134-1.089-.057-.097-.211-.154-.44-.27z"/></svg>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="app-container">
        <header>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button class="toggle-menu-btn" id="menu-toggle"><i class="fas fa-bars"></i></button>
                <div class="brand-logo-area" aria-label="Decan Movie">
                    <svg class="decan-atom-logo" viewBox="0 0 64 64" role="img" aria-label="Decan Movie atom logo">
                        <ellipse class="atom-orbit" cx="32" cy="32" rx="25" ry="10"></ellipse>
                        <ellipse class="atom-orbit" cx="32" cy="32" rx="25" ry="10" transform="rotate(60 32 32)"></ellipse>
                        <ellipse class="atom-orbit" cx="32" cy="32" rx="25" ry="10" transform="rotate(120 32 32)"></ellipse>
                        <circle class="atom-core" cx="32" cy="32" r="5.2"></circle>
                        <circle class="atom-spark" cx="32" cy="32" r="1.7"></circle>
                    </svg>
                    <span>DECAN <span>MOVIE</span></span>
                </div>
            </div>
            <div class="header-controls">
                <div class="filter-group">
                    <select id="filter-year" class="filter-select" onchange="applyAdvancedFilters()">
                        <option value="">Year</option>
                        <script>
                            const currentYear = new Date().getFullYear();
                            for(let i = currentYear; i >= 1980; i--) {
                                document.write(`<option value="${i}">${i}</option>`);
                            }
                        </script>
                    </select>
                    <select id="filter-rating" class="filter-select" onchange="applyAdvancedFilters()">
                        <option value="">Rating</option>
                        <option value="8">8.0+</option>
                        <option value="7">7.0+</option>
                    </select>
                </div>
                <div class="search-box">
                    <input type="text" id="search-input" placeholder="Search movies, anime, series...">
                    <button id="search-btn" onclick="handleSearch()"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </header>

        <main>
            <section class="hero-strip" id="hero-strip">
                <div class="hero-strip-bg" id="hero-strip-bg"></div>
                <div class="hero-strip-content">
                    <div class="hero-kicker" id="hero-kicker">DECAN MOVIE</div>
                    <h2 id="hero-title">Discover something incredible.</h2>
                    <p id="hero-description">Explore movies, anime and series with smarter discovery tools.</p>
                    <div class="hero-buttons">
                        <button class="hero-mini-btn primary" id="hero-primary-btn" onclick="heroPrimaryAction()"><i class="fas fa-play"></i> Explore</button>
                        <button class="hero-mini-btn" onclick="surpriseMe()"><i class="fas fa-dice"></i> Surprise Me</button>
                    </div>
                </div>
            </section>

            <section class="stats-strip" id="stats-strip">
                <div class="mini-stat"><strong id="stat-history">0</strong><span>History</span></div>
                <div class="mini-stat"><strong id="stat-watchlist">0</strong><span>Watchlist</span></div>
                <div class="mini-stat"><strong id="stat-favorites">0</strong><span>Favorites</span></div>
                <div class="mini-stat"><strong id="stat-rated">0</strong><span>My Ratings</span></div>
                <div class="mini-stat"><strong id="stat-continue">0</strong><span>Continue Watching</span></div>
            </section>

            <section class="feature-toolbar" id="anime-toolbar">
                <div class="feature-toolbar-title">
                    <div>
                        <h3><i class="fas fa-dragon" style="color:var(--neon-pink);"></i> Anime Discovery Portal</h3>
                        <span>Filter anime by genre, trend, rating, popularity and year.</span>
                    </div>
                    <button class="surprise-btn" onclick="surpriseAnime()"><i class="fas fa-dice"></i> Random Anime</button>
                </div>
                <div class="anime-control-grid">
                    <select id="anime-genre" class="filter-select" onchange="applyAnimePortal()">
                        <option value="all">All Anime</option>
                        <option value="action">⚔️ Action</option>
                        <option value="drama">🎭 Drama</option>
                        <option value="romance">❤️ Romance</option>
                        <option value="supernatural">👻 Supernatural</option>
                        <option value="fantasy">🧙 Fantasy</option>
                        <option value="comedy">😂 Comedy</option>
                        <option value="psychological">🧠 Psychological</option>
                        <option value="adventure">🥷 Adventure</option>
                        <option value="horror">💀 Horror</option>
                        <option value="scifi">🚀 Sci-Fi</option>
                        <option value="mystery">🔎 Mystery</option>
                        <option value="action_supernatural">⚔️👻 Action + Supernatural</option>
                        <option value="action_romance">⚔️❤️ Action + Romance</option>
                        <option value="drama_romance">🎭❤️ Drama + Romance</option>
                    </select>
                    <select id="anime-sort" class="filter-select" onchange="applyAnimePortal()">
                        <option value="popularity.desc">🔥 Trending / Popular</option>
                        <option value="vote_average.desc">⭐ Highest Rated</option>
                        <option value="primary_release_date.desc">🆕 Newest</option>
                        <option value="primary_release_date.asc">📅 Oldest</option>
                        <option value="vote_count.desc">👑 Most Rated</option>
                    </select>
                    <select id="anime-year" class="filter-select" onchange="applyAnimePortal()">
                        <option value="">Any Year</option>
                        <script>
                            for(let y = new Date().getFullYear(); y >= 1980; y--) document.write(`<option value="${y}">${y}</option>`);
                        </script>
                    </select>
                    <select id="anime-rating" class="filter-select" onchange="applyAnimePortal()">
                        <option value="">Any Rating</option>
                        <option value="8.5">8.5+</option>
                        <option value="8">8.0+</option>
                        <option value="7.5">7.5+</option>
                        <option value="7">7.0+</option>
                    </select>
                </div>
                <div class="filter-chip-row" style="margin-top:12px;">
                    <button class="filter-chip active" onclick="setAnimePreset('all', 'popularity.desc', this)">🔥 Trending Anime</button>
                    <button class="filter-chip" onclick="setAnimePreset('romance', 'popularity.desc', this)">❤️ Trending Romance</button>
                    <button class="filter-chip" onclick="setAnimePreset('action', 'popularity.desc', this)">⚔️ Trending Action</button>
                    <button class="filter-chip" onclick="setAnimePreset('supernatural', 'popularity.desc', this)">👻 Trending Supernatural</button>
                    <button class="filter-chip" onclick="setAnimePreset('drama', 'vote_average.desc', this)">🎭 Top Drama</button>
                    <button class="filter-chip" onclick="setAnimePreset('romance', 'vote_average.desc', this)">⭐ Top Romance</button>
                    <button class="filter-chip" onclick="setAnimePreset('action', 'vote_average.desc', this)">⭐ Top Action</button>
                    <button class="filter-chip" onclick="setAnimePreset('action_supernatural', 'popularity.desc', this)">⚔️👻 Trending Action + Supernatural</button>
                    <button class="filter-chip" onclick="setAnimePreset('action_romance', 'popularity.desc', this)">⚔️❤️ Trending Action + Romance</button>
                    <button class="filter-chip" onclick="setAnimePreset('drama_romance', 'vote_average.desc', this)">🎭❤️ Top Drama + Romance</button>
                </div>
            </section>

            <section class="feature-toolbar" id="movie-toolbar">
                <div class="feature-toolbar-title">
                    <div>
                        <h3><i class="fas fa-film" style="color:var(--neon-cyan);"></i> Movie Discovery Portal</h3>
                        <span>Sort movies by genre, trending, rating, popularity and year on every device.</span>
                    </div>
                    <button class="surprise-btn" onclick="surpriseMovie()"><i class="fas fa-dice"></i> Random Movie</button>
                </div>
                <div class="universal-control-grid">
                    <select id="movie-genre" class="filter-select" onchange="applyMoviePortal()">
                        <option value="all">All Movies</option>
                        <option value="28">⚔️ Action</option>
                        <option value="18">🎭 Drama</option>
                        <option value="10749">❤️ Romance</option>
                        <option value="27">👻 Horror</option>
                        <option value="14">🧙 Fantasy</option>
                        <option value="35">😂 Comedy</option>
                        <option value="878">🚀 Sci-Fi</option>
                        <option value="53">😱 Thriller</option>
                        <option value="9648">🔎 Mystery</option>
                        <option value="12">🥷 Adventure</option>
                        <option value="80">🕵️ Crime</option>
                        <option value="36">📜 History</option>
                        <option value="16">🎨 Animation</option>
                        <option value="10752">⚔️ War</option>
                        <option value="37">🤠 Western</option>
                    </select>
                    <select id="movie-sort" class="filter-select" onchange="applyMoviePortal()">
                        <option value="popularity.desc">🔥 Trending / Popular</option>
                        <option value="vote_average.desc">⭐ Highest Rated</option>
                        <option value="vote_count.desc">👑 Most Rated</option>
                        <option value="primary_release_date.desc">🆕 Newest</option>
                        <option value="primary_release_date.asc">📅 Oldest</option>
                    </select>
                    <select id="movie-year" class="filter-select" onchange="applyMoviePortal()">
                        <option value="">Any Year</option>
                        <script>
                            for(let y = new Date().getFullYear(); y >= 1980; y--) document.write(`<option value="${y}">${y}</option>`);
                        </script>
                    </select>
                    <select id="movie-rating" class="filter-select" onchange="applyMoviePortal()">
                        <option value="">Any Rating</option>
                        <option value="9">9.0+</option>
                        <option value="8.5">8.5+</option>
                        <option value="8">8.0+</option>
                        <option value="7.5">7.5+</option>
                        <option value="7">7.0+</option>
                    </select>
                </div>
                <div class="responsive-chip-row" style="margin-top:12px;">
                    <button class="filter-chip active" onclick="setMoviePreset('all','popularity.desc',this)">🔥 Trending Movies</button>
                    <button class="filter-chip" onclick="setMoviePreset('10749','popularity.desc',this)">❤️ Trending Romance</button>
                    <button class="filter-chip" onclick="setMoviePreset('28','popularity.desc',this)">⚔️ Trending Action</button>
                    <button class="filter-chip" onclick="setMoviePreset('27','popularity.desc',this)">👻 Trending Horror</button>
                    <button class="filter-chip" onclick="setMoviePreset('878','popularity.desc',this)">🚀 Trending Sci-Fi</button>
                    <button class="filter-chip" onclick="setMoviePreset('14','popularity.desc',this)">🧙 Trending Fantasy</button>
                    <button class="filter-chip" onclick="setMoviePreset('18','vote_average.desc',this)">⭐ Top Drama</button>
                    <button class="filter-chip" onclick="setMoviePreset('28','vote_average.desc',this)">⭐ Top Action</button>
                    <button class="filter-chip" onclick="setMoviePreset('10749','vote_average.desc',this)">⭐ Top Romance</button>
                    <button class="filter-chip" onclick="setMoviePreset('35','vote_average.desc',this)">⭐ Top Comedy</button>
                    <button class="filter-chip" onclick="setMoviePreset('28,27','popularity.desc',this)">⚔️👻 Trending Action + Horror</button>
                    <button class="filter-chip" onclick="setMoviePreset('28,14','popularity.desc',this)">⚔️🧙 Trending Action + Fantasy</button>
                    <button class="filter-chip" onclick="setMoviePreset('28,10749','popularity.desc',this)">⚔️❤️ Trending Action + Romance</button>
                    <button class="filter-chip" onclick="setMoviePreset('18,10749','vote_average.desc',this)">🎭❤️ Top Drama + Romance</button>
                </div>
                <div class="mobile-filter-note"><i class="fas fa-hand-pointer"></i> Swipe the genre presets sideways on smaller screens.</div>
            </section>

            <section class="recommendation-banner" id="recommendation-banner">
                <h3 id="recommendation-title">Recommended for you</h3>
                <p id="recommendation-text">Recommendations are based only on this device's local watch history, favorites and ratings. No account is required.</p>
            </section>

            <div class="section-header-box">
                <h2 class="section-title" id="grid-title">Global Trending</h2>
            </div>
            <div class="movie-grid" id="movie-grid"></div>
            <div class="pagination-container">
                <button class="load-more-btn" onclick="fetchNextPage()">Load More</button>
            </div>
        </main>
    </div>

    <!-- Universal Device-Proof Theater Mode Player -->
    <div id="theater-container">
        <div class="theater-header">
            <h3 style="color:#fff; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70%;" id="theater-title">Direct Stream</h3>
            <div class="close-modal" style="position:relative; top:0; left:0;" onclick="closeTheater()">
                <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </div>
        </div>
        <div class="theater-viewport-wrapper" style="position: relative;">
            <div id="player-adblock-shield" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 3015; display: none; background: transparent;" onclick="handleAdShieldClick(event)"></div>
            <div class="theater-ratio-box" id="theater-iframe-wrapper"></div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal" id="movie-modal">
        <div class="modal-content" id="modal-container">
            <div class="close-modal" onclick="closeModalWindow()">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
            </div>
            <div class="modal-body" id="modal-body"></div>
        </div>
    </div>

    <script>
        const TMDB_BASE_URL = 'tmdb-proxy.php';
        const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p/w500';
        const ORIGINAL_IMAGE_URL = 'https://image.tmdb.org/t/p/original';

        const movieGrid = document.getElementById('movie-grid');
        const searchInput = document.getElementById('search-input');
        const gridTitle = document.getElementById('grid-title');
        const modal = document.getElementById('movie-modal');
        const modalBody = document.getElementById('modal-body');
        const sidebarMenu = document.getElementById('sidebar-menu');

        let currentCategory = 'trending';
        let currentPage = 1;
        let isSearchMode = false;
        let isFilterMode = false;
        let currentModalDetails = null;
        let cachedSeasonEpisodes = {};

        let userWatchlist = JSON.parse(localStorage.getItem('decan_watchlist')) || [];
        let userCustomList = JSON.parse(localStorage.getItem('decan_customlist')) || [];
        let userHistory = JSON.parse(localStorage.getItem('decan_history')) || [];
        let userFavorites = JSON.parse(localStorage.getItem('decan_favorites')) || [];
        let userRatings = JSON.parse(localStorage.getItem('decan_ratings')) || {};
        let continueWatching = JSON.parse(localStorage.getItem('decan_continue_watching')) || [];
        let heroMovie = null;
        let animePortalMode = false;
        let moviePortalMode = false;
        let libraryMode = null;
        

        const fetchOptions = {
            method: 'GET',
            headers: { accept: 'application/json' }
        };

        document.getElementById('menu-toggle').addEventListener('click', () => sidebarMenu.classList.toggle('open'));

        let searchDebounce = null;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(() => { handleSearch(); }, 600);
        });

        document.addEventListener('DOMContentLoaded', () => { updateLibraryStats(); switchCategory('trending'); setTimeout(openSharedTitleFromUrl, 500); });

        function openSharedTitleFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const id = Number(params.get('id'));
            const type = params.get('title');
            if(id && (type === 'movie' || type === 'tv')) openMovieDetails(id, type);
        }

        function showToast(msg) {
            const toast = document.getElementById('toast-msg');
            toast.textContent = msg; toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        function switchCategory(category, element = null) {
            currentCategory = category; currentPage = 1; isSearchMode = false; isFilterMode = false; libraryMode = null;
            animePortalMode = category === 'anime_portal';
            moviePortalMode = category === 'movie_portal' || category === 'top_movies';

            document.getElementById('filter-year').value = "";
            document.getElementById('filter-rating').value = "";
            document.getElementById('anime-genre').value = 'all';
            document.getElementById('anime-sort').value = 'popularity.desc';
            document.getElementById('anime-year').value = '';
            document.getElementById('anime-rating').value = '';
            document.getElementById('movie-genre').value = 'all';
            document.getElementById('movie-sort').value = 'popularity.desc';
            document.getElementById('movie-year').value = '';
            document.getElementById('movie-rating').value = '';
            document.querySelectorAll('#anime-toolbar .filter-chip, #movie-toolbar .filter-chip').forEach((chip, index) => chip.classList.toggle('active', index === 0));

            document.getElementById('anime-toolbar').classList.toggle('active', animePortalMode);
            document.getElementById('movie-toolbar').classList.toggle('active', moviePortalMode);
            document.getElementById('recommendation-banner').classList.remove('active');
            document.getElementById('hero-strip').classList.toggle('active', category === 'trending');
            document.getElementById('stats-strip').classList.toggle('active', category === 'trending');

            if(element) {
                document.querySelectorAll('.menu-link').forEach(link => link.classList.remove('active'));
                element.classList.add('active');
                if(window.innerWidth <= 900) sidebarMenu.classList.remove('open');
            }
            gridTitle.textContent = category === 'anime_portal' ? 'Anime Portal' : (moviePortalMode ? 'Movie Portal' : category.replaceAll('_', ' '));
            fetchCategoryData();
            updateLibraryStats();
        }

        function applyAdvancedFilters() {
            isFilterMode = true; isSearchMode = false; currentPage = 1;
            fetchCategoryData();
        }

        function showSkeletons() {
            movieGrid.innerHTML = Array(12).fill('<div class="skeleton-card"></div>').join('');
        }

        async function fetchCategoryData() {
            if (currentPage === 1) showSkeletons();
            let endpoint = '';
            let forcedTypeSetting = null;

            const year = document.getElementById('filter-year').value;
            const rating = document.getElementById('filter-rating').value;

            if (animePortalMode) {
                fetchAnimePortalData();
                return;
            }
            if (moviePortalMode) {
                fetchMoviePortalData();
                return;
            }

            if (isFilterMode) {
                gridTitle.textContent = "Filtered Results";
                endpoint = `${TMDB_BASE_URL}/discover/movie?sort_by=popularity.desc&page=${currentPage}`;
                if(year) endpoint += `&primary_release_year=${year}`;
                if(rating) endpoint += `&vote_average.gte=${rating}&vote_count.gte=100`;
                forcedTypeSetting = 'movie';
            } else {
                switch(currentCategory) {
                    case 'trending': endpoint = `${TMDB_BASE_URL}/trending/all/day?page=${currentPage}`; break;
                    case 'netflix': endpoint = `${TMDB_BASE_URL}/discover/tv?with_watch_providers=8&watch_region=US&sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'tv'; break;
                    case 'amazon': endpoint = `${TMDB_BASE_URL}/discover/tv?with_watch_providers=9&watch_region=US&sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'tv'; break;
                    case 'disney': endpoint = `${TMDB_BASE_URL}/discover/tv?with_watch_providers=337&watch_region=US&sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'tv'; break;
                    case 'apple': endpoint = `${TMDB_BASE_URL}/discover/tv?with_watch_providers=350&watch_region=US&sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'tv'; break;
                    case 'upcoming': endpoint = `${TMDB_BASE_URL}/movie/upcoming?page=${currentPage}`; forcedTypeSetting = 'movie'; break;
                    case 'top_movies': endpoint = `${TMDB_BASE_URL}/discover/movie?sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'movie'; break;
                    case 'series': endpoint = `${TMDB_BASE_URL}/discover/tv?sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'tv'; break;
                    case 'top_anime': endpoint = `${TMDB_BASE_URL}/discover/tv?with_genres=16&with_original_language=ja&sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'tv'; break;
                    case 'cartoons': endpoint = `${TMDB_BASE_URL}/discover/tv?with_genres=16&with_original_language=en&sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'tv'; break;
                    case 'afro': endpoint = `${TMDB_BASE_URL}/discover/movie?with_origin_country=NG|ZA|KE&sort_by=popularity.desc&page=${currentPage}`; forcedTypeSetting = 'movie'; break;
                }
            }

            try {
                const res = await fetch(endpoint, fetchOptions);
                const data = await res.json();
                displayMovies(data.results, forcedTypeSetting, currentPage > 1);
                if(currentCategory === 'trending' && currentPage === 1 && data.results && data.results.length) renderHero(data.results[Math.floor(Math.random() * Math.min(5, data.results.length))]);
            } catch (err) {
                if(currentPage === 1) movieGrid.innerHTML = `<div style="padding:20px; color:var(--text-muted);">Failed to load content.</div>`;
            }
        }


        function animeGenreQuery(genre) {
            const genres = {
                action: '10759',
                drama: '18',
                romance: '10749',
                supernatural: '10765',
                fantasy: '10765',
                comedy: '35',
                psychological: '9648',
                adventure: '10759',
                horror: '27',
                scifi: '10765',
                mystery: '9648',
                action_supernatural: '10759,10765',
                action_romance: '10759,10749',
                drama_romance: '18,10749'
            };
            return genres[genre] || '';
        }

        async function fetchAnimePortalData() {
            if(currentPage === 1) showSkeletons();
            const genre = document.getElementById('anime-genre').value;
            const sort = document.getElementById('anime-sort').value || 'popularity.desc';
            const year = document.getElementById('anime-year').value;
            const rating = document.getElementById('anime-rating').value;

            const genreId = animeGenreQuery(genre);
            const combinedGenres = genreId ? `16,${genreId}` : '16';
            let endpoint = `${TMDB_BASE_URL}/discover/tv?with_genres=${combinedGenres}&with_original_language=ja&sort_by=${encodeURIComponent(sort)}&page=${currentPage}&vote_count.gte=20`;
            if(year) endpoint += `&first_air_date_year=${year}`;
            if(rating) endpoint += `&vote_average.gte=${rating}`;
            if(sort === 'vote_average.desc') endpoint += '&vote_count.gte=100';

            try {
                const res = await fetch(endpoint, fetchOptions);
                if(!res.ok) throw new Error('Anime discovery request failed');
                const data = await res.json();
                const genreName = genre === 'all' ? 'All Anime' : document.getElementById('anime-genre').selectedOptions[0].textContent.replace(/^[^A-Za-z]+/, '');
                const sortName = document.getElementById('anime-sort').selectedOptions[0].textContent;
                gridTitle.textContent = `Anime • ${genreName} • ${sortName}`;
                displayMovies(data.results, 'tv', currentPage > 1);
            } catch(err) {
                if(currentPage === 1) movieGrid.innerHTML = `<div class="empty-state"><i class="fas fa-triangle-exclamation"></i><h3>Anime portal could not load</h3><p>Check your connection and try again.</p><button class="surprise-btn" style="margin-top:12px;" onclick="fetchAnimePortalData()">Retry</button></div>`;
            }
        }

        function applyAnimePortal() {
            if(!animePortalMode) return;
            currentPage = 1;
            document.querySelectorAll('.filter-chip').forEach(chip => chip.classList.remove('active'));
            fetchAnimePortalData();
        }

        function setAnimePreset(genre, sort, button) {
            document.getElementById('anime-genre').value = genre;
            document.getElementById('anime-sort').value = sort;
            document.getElementById('anime-year').value = '';
            document.getElementById('anime-rating').value = '';
            document.querySelectorAll('.filter-chip').forEach(chip => chip.classList.remove('active'));
            if(button) button.classList.add('active');
            animePortalMode = true;
            moviePortalMode = false;
            currentCategory = 'anime_portal';
            document.getElementById('movie-toolbar').classList.remove('active');
            currentPage = 1;
            document.getElementById('anime-toolbar').classList.add('active');
            fetchAnimePortalData();
        }

        async function fetchMoviePortalData() {
            const genre = document.getElementById('movie-genre').value;
            const sort = document.getElementById('movie-sort').value || 'popularity.desc';
            const year = document.getElementById('movie-year').value;
            const rating = document.getElementById('movie-rating').value;
            let endpoint = `${TMDB_BASE_URL}/discover/movie?sort_by=${encodeURIComponent(sort)}&page=${currentPage}&vote_count.gte=${sort === 'vote_average.desc' ? 100 : 20}`;
            if(genre !== 'all') endpoint += `&with_genres=${encodeURIComponent(genre)}`;
            if(year) endpoint += `&primary_release_year=${encodeURIComponent(year)}`;
            if(rating) endpoint += `&vote_average.gte=${encodeURIComponent(rating)}`;
            try {
                const res = await fetch(endpoint, fetchOptions);
                if(!res.ok) throw new Error('Movie discovery request failed');
                const data = await res.json();
                const genreName = genre === 'all' ? 'All Movies' : document.getElementById('movie-genre').selectedOptions[0].textContent.replace(/^[^A-Za-z]+/, '');
                const sortName = document.getElementById('movie-sort').selectedOptions[0].textContent;
                gridTitle.textContent = `Movies • ${genreName} • ${sortName}`;
                displayMovies(data.results || [], 'movie', currentPage > 1);
            } catch(e) {
                if(currentPage === 1) movieGrid.innerHTML = `<div class="empty-state"><i class="fas fa-triangle-exclamation"></i><h3>Movie portal could not load</h3><p>Check your connection and try again.</p><button class="surprise-btn" style="margin-top:12px;" onclick="fetchMoviePortalData()">Retry</button></div>`;
            }
        }

        function applyMoviePortal() {
            if(!moviePortalMode) return;
            currentPage = 1;
            isSearchMode = false;
            isFilterMode = false;
            document.querySelectorAll('#movie-toolbar .filter-chip').forEach(chip => chip.classList.remove('active'));
            fetchMoviePortalData();
        }

        function setMoviePreset(genre, sort, button) {
            document.getElementById('movie-genre').value = genre;
            document.getElementById('movie-sort').value = sort;
            document.getElementById('movie-year').value = '';
            document.getElementById('movie-rating').value = '';
            moviePortalMode = true;
            animePortalMode = false;
            currentCategory = 'movie_portal';
            currentPage = 1;
            document.getElementById('anime-toolbar').classList.remove('active');
            document.getElementById('movie-toolbar').classList.add('active');
            document.querySelectorAll('#movie-toolbar .filter-chip').forEach(chip => chip.classList.remove('active'));
            if(button) button.classList.add('active');
            fetchMoviePortalData();
        }

        function surpriseMovie() {
            moviePortalMode = true;
            currentPage = 1;
            const genres = ['all','28','18','10749','27','14','35','878','53','9648','12'];
            const genre = genres[Math.floor(Math.random() * genres.length)];
            document.getElementById('movie-genre').value = genre;
            document.getElementById('movie-sort').value = 'popularity.desc';
            document.getElementById('movie-year').value = '';
            document.getElementById('movie-rating').value = '';
            fetchMoviePortalData().then(() => {
                const cards = movieGrid.querySelectorAll('.movie-card');
                if(cards.length) cards[Math.floor(Math.random() * cards.length)].scrollIntoView({behavior:'smooth', block:'center'});
            });
        }

        function updateLibraryStats() {
            document.getElementById('stat-history').textContent = userHistory.length;
            document.getElementById('stat-watchlist').textContent = userWatchlist.length;
            document.getElementById('stat-favorites').textContent = userFavorites.length;
            document.getElementById('stat-rated').textContent = Object.keys(userRatings).length;
            document.getElementById('stat-continue').textContent = continueWatching.length;
        }

        function showLibraryHeader(title, count, clearButton = false) {
            gridTitle.textContent = title;
            const existing = document.getElementById('library-actions');
            if(existing) existing.remove();
            if(clearButton) {
                const bar = document.createElement('div');
                bar.id = 'library-actions';
                bar.className = 'library-actions';
                bar.innerHTML = `<span class="library-count"><i class="fas fa-history"></i> ${count} saved title${count === 1 ? '' : 's'} on this device</span><button class="danger-btn" onclick="clearHistory()"><i class="fas fa-trash"></i> Clear History</button>`;
                movieGrid.before(bar);
            }
        }


        function saveContinueWatching(item) {
            const key = `${item.type || item.media_type || 'movie'}:${item.id}:${item.season || 0}:${item.episode || 0}`;
            continueWatching = continueWatching.filter(x => `${x.type || x.media_type || 'movie'}:${x.id}:${x.season || 0}:${x.episode || 0}` !== key);
            continueWatching.unshift(item);
            if(continueWatching.length > 20) continueWatching.pop();
            localStorage.setItem('decan_continue_watching', JSON.stringify(continueWatching));
            updateLibraryStats();
        }

        function showContinueWatching() {
            resetFeatureSurfaces();
            gridTitle.textContent = 'Continue Watching';
            if(!continueWatching.length) {
                movieGrid.innerHTML = `<div class="empty-state"><i class="fas fa-forward"></i><h3>Nothing to continue yet</h3><p>Open a title and press Watch Now to save it here.</p></div>`;
            } else {
                displayMovies(continueWatching, null, false);
            }
            if(window.innerWidth <= 900) sidebarMenu.classList.remove('open');
        }

        async function showReleaseCalendar() {
            resetFeatureSurfaces();
            gridTitle.textContent = 'Release Calendar';
            showSkeletons();
            try {
                const [moviesRes, tvRes] = await Promise.all([
                    fetch(`${TMDB_BASE_URL}/movie/upcoming?page=1`, fetchOptions),
                    fetch(`${TMDB_BASE_URL}/tv/on_the_air?page=1`, fetchOptions)
                ]);
                const movies = moviesRes.ok ? (await moviesRes.json()).results || [] : [];
                const tv = tvRes.ok ? (await tvRes.json()).results || [] : [];
                const combined = [
                    ...movies.map(x => ({...x, media_type:'movie'})),
                    ...tv.map(x => ({...x, media_type:'tv'}))
                ].sort((a,b) => String(a.release_date || a.first_air_date || '').localeCompare(String(b.release_date || b.first_air_date || '')));
                displayMovies(combined, null, false);
            } catch(e) {
                movieGrid.innerHTML = `<div class="empty-state"><i class="fas fa-calendar-xmark"></i><h3>Release calendar unavailable</h3><p>Please try again.</p></div>`;
            }
        }

        function showMoodDiscovery() {
            resetFeatureSurfaces();
            document.getElementById('recommendation-banner').classList.add('active');
            document.getElementById('recommendation-title').textContent = 'What are you in the mood for?';
            document.getElementById('recommendation-text').innerHTML = 'Choose a mood and Decan Movie will discover titles without needing an account.';
            movieGrid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;">
                <i class="fas fa-face-smile"></i><h3>Pick your mood</h3>
                <div class="filter-chip-row" style="justify-content:center;margin-top:18px;">
                    <button class="filter-chip" onclick="loadMood('action')">🔥 Adrenaline</button>
                    <button class="filter-chip" onclick="loadMood('romance')">❤️ Romance</button>
                    <button class="filter-chip" onclick="loadMood('comedy')">😂 Laugh</button>
                    <button class="filter-chip" onclick="loadMood('horror')">👻 Scared</button>
                    <button class="filter-chip" onclick="loadMood('drama')">😭 Emotional</button>
                    <button class="filter-chip" onclick="loadMood('scifi')">🧠 Think</button>
                    <button class="filter-chip" onclick="loadMood('fantasy')">✨ Escape</button>
                </div>
            </div>`;
        }

        async function loadMood(mood) {
            const genres = {action:'28', romance:'10749', comedy:'35', horror:'27', drama:'18', scifi:'878', fantasy:'14'};
            const genre = genres[mood] || '18';
            gridTitle.textContent = `Mood • ${mood}`;
            showSkeletons();
            try {
                const res = await fetch(`${TMDB_BASE_URL}/discover/movie?with_genres=${genre}&sort_by=popularity.desc&vote_count.gte=50&page=1`, fetchOptions);
                const data = await res.json();
                displayMovies(data.results || [], 'movie', false);
            } catch(e) {
                movieGrid.innerHTML = `<div class="empty-state"><i class="fas fa-triangle-exclamation"></i><h3>Could not load mood results</h3></div>`;
            }
        }

        async function openPersonDetails(personId) {
            modalBody.innerHTML = `<div style="padding:60px;text-align:center;color:var(--neon-cyan);"><i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Loading person...</div>`;
            modal.style.display = 'flex';
            try {
                const [personRes, creditsRes] = await Promise.all([
                    fetch(`${TMDB_BASE_URL}/person/${personId}?language=en-US`, fetchOptions),
                    fetch(`${TMDB_BASE_URL}/person/${personId}/combined_credits?language=en-US`, fetchOptions)
                ]);
                if(!personRes.ok) throw new Error('Person lookup failed');
                const person = await personRes.json();
                const credits = creditsRes.ok ? await creditsRes.json() : {cast:[],crew:[]};
                const known = [...(credits.cast || []), ...(credits.crew || [])].filter(x => x.poster_path).sort((a,b) => (b.popularity || 0) - (a.popularity || 0));
                const image = person.profile_path ? `${IMAGE_BASE_URL}${person.profile_path}` : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=300';
                modalBody.innerHTML = `<div style="padding:25px;overflow-y:auto;">
                    <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
                        <img src="${image}" alt="${person.name}" style="width:150px;height:220px;object-fit:cover;border-radius:14px;">
                        <div style="flex:1;min-width:220px;">
                            <div class="hero-kicker">PERSON</div><h2 style="font-size:1.8rem;margin-bottom:8px;">${person.name}</h2>
                            <div class="stat-badge-grid"><span class="stat-badge">${person.known_for_department || 'Entertainment'}</span>${person.birthday ? `<span class="stat-badge">Born ${person.birthday}</span>` : ''}</div>
                            <p style="color:#c0c2cc;line-height:1.6;">${person.biography || 'Biography unavailable.'}</p>
                        </div>
                    </div>
                    <div style="margin-top:28px;"><h3>Popular Credits</h3><div class="horizontal-scroll">${known.slice(0,12).map(x => `<div class="similar-card" onclick="openMovieDetails(${x.id}, '${x.media_type || (x.first_air_date ? 'tv' : 'movie')}')"><img src="${IMAGE_BASE_URL}${x.poster_path}" alt="${x.title || x.name}"><h4>${x.title || x.name}</h4></div>`).join('')}</div></div>
                </div>`;
            } catch(e) {
                modalBody.innerHTML = `<div class="empty-state"><i class="fas fa-user-slash"></i><h3>Person details unavailable</h3></div>`;
            }
        }

        function showFavorites() {
            libraryMode = 'favorites';
            animePortalMode = false;
            document.getElementById('anime-toolbar').classList.remove('active');
            document.getElementById('hero-strip').classList.remove('active');
            document.getElementById('stats-strip').classList.remove('active');
            showLibraryHeader('My Favorites', userFavorites.length);
            displayMovies(userFavorites, null, false);
            updateLibraryStats();
        }

        function showRatings() {
            libraryMode = 'ratings';
            animePortalMode = false;
            document.getElementById('anime-toolbar').classList.remove('active');
            document.getElementById('hero-strip').classList.remove('active');
            document.getElementById('stats-strip').classList.remove('active');
            const rated = Object.values(userRatings).map(item => item.data).filter(Boolean);
            showLibraryHeader('My Ratings', rated.length);
            displayMovies(rated, null, false);
            updateLibraryStats();
        }

        function showRecommendations() {
            libraryMode = 'recommendations';
            animePortalMode = false;
            document.getElementById('anime-toolbar').classList.remove('active');
            document.getElementById('hero-strip').classList.remove('active');
            document.getElementById('stats-strip').classList.remove('active');
            document.getElementById('recommendation-banner').classList.add('active');
            document.getElementById('recommendation-title').textContent = 'For You • Local Recommendations';
            gridTitle.textContent = 'Recommended For You';
            buildLocalRecommendations();
        }

        async function buildLocalRecommendations() {
            showSkeletons();
            const source = [...userFavorites, ...userHistory, ...Object.values(userRatings).map(x => x.data).filter(Boolean)];
            const genreCounts = {};
            source.forEach(item => (item.genre_ids || []).forEach(id => genreCounts[id] = (genreCounts[id] || 0) + 1));
            const favoriteGenre = Object.entries(genreCounts).sort((a,b) => b[1] - a[1])[0]?.[0];
            let endpoint = `${TMDB_BASE_URL}/discover/${source.some(x => x.media_type === 'tv' || x.first_air_date) ? 'tv' : 'movie'}?sort_by=popularity.desc&page=1&vote_count.gte=20`;
            if(favoriteGenre) endpoint += `&with_genres=${favoriteGenre}`;
            try {
                const res = await fetch(endpoint, fetchOptions);
                const data = await res.json();
                const excluded = new Set(source.map(x => x.id));
                const results = (data.results || []).filter(x => !excluded.has(x.id));
                displayMovies(results, null, false);
            } catch(e) {
                movieGrid.innerHTML = `<div class="empty-state"><i class="fas fa-wand-magic-sparkles"></i><h3>Recommendations unavailable</h3><p>Open a few titles first, then try again.</p></div>`;
            }
        }

        function clearHistory() {
            if(!userHistory.length) { showToast('Viewing history is already empty'); return; }
            const confirmed = window.confirm('Clear your entire viewing history? This cannot be undone.');
            if(!confirmed) return;
            userHistory = [];
            localStorage.removeItem('decan_history');
            showToast('Viewing history cleared successfully');
            showHistory();
            updateLibraryStats();
        }

        function toggleFavorite(id) {
            if(!currentModalDetails) return;
            const index = userFavorites.findIndex(item => item.id === id);
            if(index > -1) {
                userFavorites.splice(index, 1);
                showToast('Removed from Favorites');
            } else {
                userFavorites.push(currentModalDetails);
                showToast('Added to Favorites');
            }
            localStorage.setItem('decan_favorites', JSON.stringify(userFavorites));
            updateLibraryStats();
            openMovieDetails(currentModalDetails.id, currentModalDetails.media_type);
        }

        function setPersonalRating(id, rating) {
            if(!currentModalDetails) return;
            if(rating < 1 || rating > 5) return;
            userRatings[id] = { rating, data: currentModalDetails };
            localStorage.setItem('decan_ratings', JSON.stringify(userRatings));
            showToast(`Your rating: ${rating}/5`);
            updateLibraryStats();
            renderPersonalRatingPanel(id);
        }

        function renderPersonalRatingPanel(id) {
            const panel = document.getElementById('personal-rating-panel');
            if(!panel) return;
            const current = userRatings[id]?.rating || 0;
            panel.innerHTML = `<div style="font-size:.82rem;font-weight:700;">Your Rating</div><div class="rating-stars">${[1,2,3,4,5].map(i => `<button class="rating-star-btn ${i <= current ? 'active' : ''}" onclick="setPersonalRating(${id},${i})" aria-label="Rate ${i} out of 5"><i class="fas fa-star"></i></button>`).join('')}</div><div class="profile-less-note">Saved only on this device. No account required.</div>`;
        }

        function surpriseAnime() {
            if(!animePortalMode) {
                switchCategory('anime_portal');
                setTimeout(surpriseAnime, 700);
                return;
            }
            const genreOptions = ['all','action','drama','romance','supernatural','fantasy','comedy','psychological','adventure','horror','scifi','mystery'];
            document.getElementById('anime-genre').value = genreOptions[Math.floor(Math.random() * genreOptions.length)];
            document.getElementById('anime-sort').value = Math.random() > .45 ? 'popularity.desc' : 'vote_average.desc';
            document.querySelectorAll('.filter-chip').forEach(chip => chip.classList.remove('active'));
            currentPage = 1;
            fetchAnimePortalData();
        }

        function surpriseMe() {
            const source = [...userWatchlist, ...userFavorites, ...userHistory];
            if(source.length) {
                const pick = source[Math.floor(Math.random() * source.length)];
                openMovieDetails(pick.id, pick.media_type || pick.type || (pick.first_air_date ? 'tv' : 'movie'));
                return;
            }
            switchCategory('trending');
            setTimeout(() => {
                const cards = movieGrid.querySelectorAll('.movie-card');
                if(!cards.length) return;
                cards[Math.floor(Math.random() * cards.length)].click();
            }, 900);
        }

        function heroPrimaryAction() {
            if(heroMovie) openMovieDetails(heroMovie.id, heroMovie.media_type || (heroMovie.first_air_date ? 'tv' : 'movie'));
            else switchCategory('trending');
        }

        function renderHero(movie) {
            if(!movie) return;
            heroMovie = movie;
            const title = movie.title || movie.name || 'Featured Title';
            document.getElementById('hero-title').textContent = title;
            document.getElementById('hero-kicker').textContent = movie.media_type === 'tv' ? 'FEATURED SERIES' : 'FEATURED MOVIE';
            document.getElementById('hero-description').textContent = movie.overview || 'Discover a new favorite on Decan Movie.';
            document.getElementById('hero-strip-bg').style.backgroundImage = movie.backdrop_path ? `url('${ORIGINAL_IMAGE_URL}${movie.backdrop_path}')` : '';
            document.getElementById('hero-primary-btn').innerHTML = '<i class="fas fa-play"></i> Open Details';
        }

        async function handleSearch() {
            resetFeatureSurfaces();
            const query = searchInput.value.trim();
            if(!query) { switchCategory('trending'); return; }
            
            isSearchMode = true; isFilterMode = false; currentPage = 1; showSkeletons();
            gridTitle.textContent = `Search: "${query}"`;
            
            const endpoint = `${TMDB_BASE_URL}/search/multi?query=${encodeURIComponent(query)}&page=${currentPage}`;
            try {
                const res = await fetch(endpoint, fetchOptions);
                const data = await res.json();
                displayMovies(data.results, null, false);
            } catch (err) { }
        }

        function fetchNextPage() { currentPage++; if(animePortalMode) fetchAnimePortalData(); else if(moviePortalMode) fetchMoviePortalData(); else fetchCategoryData(); }

        function displayMovies(movies, forcedType = null, appendMode = false) {
            if(!appendMode) movieGrid.innerHTML = '';
            if(!movies || movies.length === 0) {
                if(!appendMode) movieGrid.innerHTML = `<div style="padding:20px; color:var(--text-muted);">No records found.</div>`;
                return;
            }

            movies.forEach(movie => {
                if(!movie.title && !movie.name) return;
                const title = movie.title || movie.name;
                const poster = movie.poster_path ? `${IMAGE_BASE_URL}${movie.poster_path}` : 'https://images.unsplash.com/photo-1594322436404-5a0526db4d13?q=80&w=400';
                const determinedType = forcedType || movie.media_type || (movie.first_air_date ? 'tv' : 'movie');
                const year = (movie.release_date || movie.first_air_date || 'N/A').split('-')[0];

                const card = document.createElement('div');
                card.classList.add('movie-card');
                card.innerHTML = `
                    <img src="${poster}" alt="${title}" loading="lazy">
                    <div class="quick-actions">
                        <button class="quick-action-btn" title="${userFavorites.some(item => item.id === movie.id) ? 'Remove favorite' : 'Add favorite'}" onclick="event.stopPropagation(); quickToggleFavorite(${movie.id}, '${determinedType}')"><i class="fas fa-heart ${userFavorites.some(item => item.id === movie.id) ? 'favorite-active' : ''}"></i></button>
                        <button class="quick-action-btn" title="Open details" onclick="event.stopPropagation(); openMovieDetails(${movie.id}, '${determinedType}')"><i class="fas fa-info"></i></button>
                    </div>
                    <div class="movie-info">
                        <h3>${title}</h3>
                        <span class="rating-badge"><i class="fas fa-star"></i> ${movie.vote_average ? movie.vote_average.toFixed(1) : 'NR'}</span>
                        <span class="year-badge">${year}</span>
                    </div>
                `;
                card.addEventListener('click', () => openMovieDetails(movie.id, determinedType));
                movieGrid.appendChild(card);
            });
        }

        function quickToggleFavorite(id, type) {
            const existing = userFavorites.find(item => item.id === id);
            if(existing) {
                userFavorites = userFavorites.filter(item => item.id !== id);
                localStorage.setItem('decan_favorites', JSON.stringify(userFavorites));
                showToast('Removed from Favorites');
                updateLibraryStats();
                return;
            }
            const source = userHistory.find(item => item.id === id) || { id, media_type:type };
            userFavorites.push(source);
            localStorage.setItem('decan_favorites', JSON.stringify(userFavorites));
            showToast('Added to Favorites');
            updateLibraryStats();
        }

        function getDecanViewSessionId() {
            let sessionId = localStorage.getItem('decan_view_session_id');
            if (!sessionId) {
                sessionId = crypto.randomUUID ? crypto.randomUUID() : 'session_' + Date.now() + '_' + Math.random().toString(36).slice(2);
                localStorage.setItem('decan_view_session_id', sessionId);
            }
            return sessionId;
        }

        async function recordRealMovieView(tmdbId, mediaType, title, seasonNumber = null, episodeNumber = null) {
            try {
                const sb = window.DecanAccount?.getClient?.();
                if (!sb) return null;
                const { data, error } = await sb.rpc('record_real_view', {
                    p_tmdb_id: Number(tmdbId),
                    p_media_type: mediaType,
                    p_title: title,
                    p_session_id: getDecanViewSessionId(),
                    p_season_number: seasonNumber,
                    p_episode_number: episodeNumber
                });
                if (error) {
                    console.error('View tracking failed:', error);
                    return null;
                }
                return Number(data || 0);
            } catch (error) {
                console.error('View tracking failed:', error);
                return null;
            }
        }

        async function getRealMovieViews(tmdbId, mediaType) {
            try {
                const sb = window.DecanAccount?.getClient?.();
                if (!sb) return 0;
                const { data, error } = await sb.rpc('get_real_movie_views', {
                    p_tmdb_id: Number(tmdbId),
                    p_media_type: mediaType
                });
                if (error) {
                    console.error('View count failed:', error);
                    return 0;
                }
                return Number(data || 0);
            } catch (error) {
                console.error('View count failed:', error);
                return 0;
            }
        }

        async function openMovieDetails(id, type) {
            modalBody.innerHTML = `<div style="padding:60px; text-align:center; color:var(--neon-cyan);"><i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Loading data...</div>`;
            modal.style.display = 'flex';
            cachedSeasonEpisodes = {};


            try {
                const [detailRes, creditsRes, similarRes, videosRes] = await Promise.all([
                    fetch(`${TMDB_BASE_URL}/${type}/${id}?language=en-US`, fetchOptions).catch(() => null),
                    fetch(`${TMDB_BASE_URL}/${type}/${id}/credits?language=en-US`, fetchOptions).catch(() => null),
                    fetch(`${TMDB_BASE_URL}/${type}/${id}/similar?language=en-US`, fetchOptions).catch(() => null),
                    fetch(`${TMDB_BASE_URL}/${type}/${id}/videos?language=en-US`, fetchOptions).catch(() => null)
                ]);

                if(!detailRes || !detailRes.ok) throw new Error("API Detail Failed");

                const detail = await detailRes.json();
                const credits = creditsRes && creditsRes.ok ? await creditsRes.json() : { cast: [] };
                const similar = similarRes && similarRes.ok ? await similarRes.json() : { results: [] };
                const videos = videosRes && videosRes.ok ? await videosRes.json() : { results: [] };

                currentModalDetails = { ...detail, media_type: type };

                const historyItem = { id: detail.id, type: type, media_type: type, title: detail.title || detail.name, poster_path: detail.poster_path, vote_average: detail.vote_average, release_date: detail.release_date || detail.first_air_date, genre_ids: (detail.genres || []).map(g => g.id) };
                userHistory = userHistory.filter(item => item.id !== detail.id);
                userHistory.unshift(historyItem);
                if(userHistory.length > 30) userHistory.pop();
                localStorage.setItem('decan_history', JSON.stringify(userHistory));

                const backdrop = detail.backdrop_path ? `${ORIGINAL_IMAGE_URL}${detail.backdrop_path}` : '';
                const poster = detail.poster_path ? `${IMAGE_BASE_URL}${detail.poster_path}` : 'https://images.unsplash.com/photo-1594322436404-5a0526db4d13?q=80&w=400';
                const title = detail.title || detail.name;
                const releaseDate = detail.release_date || detail.first_air_date || 'N/A';
                const genres = detail.genres ? detail.genres.map(g => g.name).join(', ') : '';
                const realViews = await getRealMovieViews(detail.id, type);
                
                const isInWatchlist = userWatchlist.some(item => item.id === detail.id);
                const isInCustomList = userCustomList.some(item => item.id === detail.id);

                let officialTrailer = videos.results.find(v => v.type === 'Trailer' && v.site === 'YouTube') || videos.results.find(v => v.site === 'YouTube');

                let seasonsHtml = '';
                let versionsHtml = '';
                
                if(type === 'tv' && detail.seasons) {
                    const totalSeasonsCount = detail.number_of_seasons || detail.seasons.length;
                    const totalEpisodesCount = detail.number_of_episodes || detail.seasons.reduce((acc, s) => acc + (s.episode_count || 0), 0);

                    seasonsHtml = `
                        <div style="margin-top:20px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <h4 style="font-size:0.95rem;">Seasons & Complete Episodes List</h4>
                                <span style="font-size:0.8rem; color:var(--neon-cyan); background:rgba(0,229,255,0.1); padding:3px 8px; border-radius:6px;">
                                    <strong>${totalSeasonsCount}</strong> Seasons &bull; <strong>${totalEpisodesCount}</strong> Episodes
                                </span>
                            </div>
                            <div class="filter-group" style="margin-bottom:10px;">
                                <select id="season-selector" class="filter-select" style="width:100%;" onchange="loadSeasonEpisodes(${detail.id}, this.value)">
                                    ${detail.seasons.map(s => `<option value="${s.season_number}">Season ${s.season_number}: ${s.name} (${s.episode_count || 0} Episodes)</option>`).join('')}
                                </select>
                            </div>
                            <div class="episode-box" id="season-episodes-container">
                                <div style="padding:20px; text-align:center; color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Loading episodes...</div>
                            </div>
                        </div>
                    `;
                }

                versionsHtml = `
                    <div style="margin-top:20px;">
                        <h4 style="font-size:0.95rem; margin-bottom:8px;">Available Versions & Quality Streams</h4>
                        <div class="action-button-toolbar" style="margin: 0; background: rgba(0,0,0,0.1);">
                            <button class="action-btn" onclick="launchTheaterPlayer(${detail.id}, '${type}', '${encodeURIComponent(title + ' (1080p HD)')}')"><i class="fas fa-video" style="color:var(--neon-cyan);"></i> 1080p Full HD</button>
                            <button class="action-btn" onclick="launchTheaterPlayer(${detail.id}, '${type}', '${encodeURIComponent(title + ' (720p)')}')"><i class="fas fa-film"></i> 720p HD</button>
                            <button class="action-btn" style="opacity: 0.6; cursor: not-allowed;" title="Alternative version currently locked"><i class="fas fa-lock"></i> 4K Ultra (Locked)</button>
                        </div>
                    </div>
                `;

                modalBody.innerHTML = `
                    <div class="modal-backdrop-header" style="background-image: url('${backdrop}');">
                        <div class="modal-header-details">
                            <img src="${poster}" class="modal-poster" alt="${title}">
                            <div style="flex-grow:1;">
                                <h2 style="font-size:1.8rem; font-weight:800; color:#fff; text-shadow:0 2px 10px rgba(0,0,0,0.8);">${title}</h2>
                                <div class="stat-badge-grid">
                                    <span class="stat-badge"><i class="fas fa-star" style="color:var(--neon-yellow);"></i> <strong>${detail.vote_average ? detail.vote_average.toFixed(1) : 'NR'}</strong> (Real Score)</span>
                                    <span class="stat-badge"><i class="fas fa-eye" style="color:var(--neon-cyan);"></i> <strong>${realViews.toLocaleString()}</strong> Views</span>
                                    <span class="stat-badge"><strong>${releaseDate.split('-')[0]}</strong></span>
                                    <span class="stat-badge"><strong>${type.toUpperCase()}</strong></span>
                                    ${detail.number_of_seasons ? `<span class="stat-badge"><strong>${detail.number_of_seasons} Seasons</strong></span>` : ''}
                                    ${detail.number_of_episodes ? `<span class="stat-badge"><strong>${detail.number_of_episodes} Episodes</strong></span>` : ''}
                                    ${detail.runtime ? `<span class="stat-badge"><strong>${detail.runtime} min</strong></span>` : ''}
                                </div>
                                <p style="font-size:0.85rem; color:#b0b0c5; margin-top:4px;">${genres}</p>
                            </div>
                        </div>
                    </div>

                    <div style="padding:25px;">
                        <div class="action-button-toolbar">
                            <button class="action-btn primary" onclick="launchTheaterPlayer(${detail.id}, '${type}', '${encodeURIComponent(title)}')">
                                <i class="fas fa-play"></i> Watch Now
                            </button>
                            ${officialTrailer ? `
                                <button class="action-btn trailer-btn" onclick="launchTrailerModal('${officialTrailer.key}', '${encodeURIComponent(title + ' - Trailer')}')">
                                    <i class="fas fa-film"></i> Watch Trailer
                                </button>
                            ` : ''}
                            <button class="action-btn" onclick="handleDownloadClick()">
                                <i class="fas fa-download"></i> Download Now
                            </button>
                            <button class="action-btn" onclick="copyMovieDetailsToClipboard()">
                                <i class="fas fa-copy" style="color:var(--neon-cyan);"></i> Copy Details
                            </button>
                            <button class="action-btn" onclick="shareCurrentTitle()">
                                <i class="fas fa-share-nodes" style="color:var(--neon-cyan);"></i> Share
                            </button>
                            <button class="action-btn" onclick="toggleWatchlist(${detail.id})">
                                <i class="fas fa-bookmark" style="${isInWatchlist ? 'color:var(--neon-cyan);' : ''}"></i> ${isInWatchlist ? 'In Watchlist' : 'Watchlist'}
                            </button>
                            <button class="action-btn" onclick="toggleCustomList(${detail.id})">
                                <i class="fas fa-list" style="${isInCustomList ? 'color:var(--neon-cyan);' : ''}"></i> ${isInCustomList ? 'In Collection' : 'Custom List'}
                            </button>
                            <button class="action-btn" onclick="toggleFavorite(${detail.id})">
                                <i class="fas fa-heart ${userFavorites.some(item => item.id === detail.id) ? 'favorite-active' : ''}"></i> ${userFavorites.some(item => item.id === detail.id) ? 'Favorited' : 'Favorite'}
                            </button>
                        </div>

                        <p style="font-size:0.9rem; line-height:1.6; color:#c0c2cc; margin-top:15px;">${detail.overview || 'No overview available.'}</p>
                        <div class="rating-panel" id="personal-rating-panel"></div>

                        ${versionsHtml}
                        ${seasonsHtml}

                        <div style="margin-top:25px;">
                            <h4 style="font-size:0.95rem; margin-bottom:8px;">Top Cast</h4>
                            <div class="cast-row">
                                ${credits.cast.slice(0, 10).map(c => `
                                    <div class="cast-card" onclick="openPersonDetails(${c.id})" style="cursor:pointer;">
                                        <img src="${c.profile_path ? IMAGE_BASE_URL + c.profile_path : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200'}" alt="${c.name}">
                                        <div style="font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${c.name}</div>
                                        <div style="color:var(--text-muted); font-size:0.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${c.character || ''}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        <div style="margin-top:30px;">
                            <h4 style="font-size:0.95rem; margin-bottom:8px;">Similar Recommendations</h4>
                            <div class="horizontal-scroll">
                                ${similar.results.slice(0, 10).map(s => `
                                    <div class="similar-card" onclick="openMovieDetails(${s.id}, '${type}')">
                                        <img src="${s.poster_path ? IMAGE_BASE_URL + s.poster_path : 'https://images.unsplash.com/photo-1594322436404-5a0526db4d13?q=80&w=400'}" alt="${s.title || s.name}">
                                        <h4>${s.title || s.name}</h4>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                `;

                renderPersonalRatingPanel(detail.id);
                if(type === 'tv' && detail.seasons && detail.seasons.length > 0) {
                    loadSeasonEpisodes(detail.id, detail.seasons[0].season_number);
                }

            } catch (err) {
                modalBody.innerHTML = `<div style="padding:40px; text-align:center; color:var(--text-muted);">Failed to load full movie details.</div>`;
            }
        }

        async function loadSeasonEpisodes(tvId, seasonNumber) {
            const container = document.getElementById('season-episodes-container');
            if(!container) return;

            if(cachedSeasonEpisodes[seasonNumber]) {
                renderEpisodesList(tvId, seasonNumber, cachedSeasonEpisodes[seasonNumber]);
                return;
            }

            container.innerHTML = `<div style="padding:15px; text-align:center; color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Fetching full episode list...</div>`;

            try {
                const res = await fetch(`${TMDB_BASE_URL}/tv/${tvId}/season/${seasonNumber}?language=en-US`, fetchOptions);
                if(!res.ok) throw new Error("Failed to fetch season episodes");
                const data = await res.json();
                cachedSeasonEpisodes[seasonNumber] = data.episodes || [];
                renderEpisodesList(tvId, seasonNumber, cachedSeasonEpisodes[seasonNumber]);
            } catch(e) {
                container.innerHTML = `<div style="padding:15px; text-align:center; color:#ff007f;">Failed to load episodes for Season ${seasonNumber}.</div>`;
            }
        }

        function renderEpisodesList(tvId, seasonNumber, episodes) {
            const container = document.getElementById('season-episodes-container');
            if(!container) return;

            if(!episodes || episodes.length === 0) {
                container.innerHTML = `<div style="padding:15px; text-align:center; color:var(--text-muted);">No episodes found for this season.</div>`;
                return;
            }

            const title = currentModalDetails ? (currentModalDetails.title || currentModalDetails.name) : 'Series';

            container.innerHTML = episodes.map(ep => `
                <div class="episode-item" onclick="launchTheaterPlayer(${tvId}, 'tv', '${encodeURIComponent(title + ' - S' + seasonNumber + 'E' + ep.episode_number + ' (' + ep.name + ')')}', ${seasonNumber}, ${ep.episode_number})">
                    <div style="display:flex; align-items:center; gap:10px; overflow:hidden;">
                        <span style="color:var(--neon-cyan); font-weight:700; min-width:30px;">E${ep.episode_number}</span>
                        <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${ep.name}">${ep.name}</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:15px; flex-shrink:0;">
                        <span style="color:var(--text-muted); font-size:0.75rem;">${ep.air_date || 'N/A'}</span>
                        <span style="color:var(--neon-yellow); font-size:0.75rem;"><i class="fas fa-star"></i> ${ep.vote_average ? ep.vote_average.toFixed(1) : 'NR'}</span>
                        <i class="fas fa-play-circle" style="color:var(--neon-cyan);"></i>
                    </div>
                </div>
            `).join('');
        }

        function closeModalWindow() { modal.style.display = 'none'; }

        // Desktop convenience shortcuts. These are additive and do not replace existing controls.
        document.addEventListener('keydown', (event) => {
            if(event.target && ['INPUT','SELECT','TEXTAREA'].includes(event.target.tagName)) return;
            if(event.key === '/' && !modal.style.display) { event.preventDefault(); searchInput.focus(); }
            if(event.key === 'Escape') { closeModalWindow(); closeTheater(); }
        });

        async function launchTheaterPlayer(id, type, encodedTitle, seasonNum = 1, episodeNum = 1) {
            const title = decodeURIComponent(encodedTitle);
            document.getElementById('theater-title').textContent = title;

            const iframeWrapper = document.getElementById('theater-iframe-wrapper');
            const streamUrl = type === 'movie' ? `https://vidsrc.me/embed/movie?tmdb=${id}` : `https://vidsrc.me/embed/tv?tmdb=${id}&season=${seasonNum}&episode=${episodeNum}`;

            iframeWrapper.innerHTML = `<iframe class="theater-iframe" src="${streamUrl}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>`;

            const shield = document.getElementById('player-adblock-shield');
            shield.style.display = 'block';
            setTimeout(() => { shield.style.display = 'none'; }, 4000);

            document.getElementById('theater-container').style.display = 'flex';

            await recordRealMovieView(
                id,
                type,
                title,
                type === 'tv' ? seasonNum : null,
                type === 'tv' ? episodeNum : null
            );

            saveContinueWatching({ id, type, media_type:type, title, season:seasonNum, episode:episodeNum, poster_path:currentModalDetails?.poster_path || null, vote_average:currentModalDetails?.vote_average || 0, release_date:currentModalDetails?.release_date || currentModalDetails?.first_air_date || '' });
        }

        function launchTrailerModal(youtubeKey, encodedTitle) {
            const title = decodeURIComponent(encodedTitle);
            document.getElementById('theater-title').textContent = title;
            
            const iframeWrapper = document.getElementById('theater-iframe-wrapper');
            let streamUrl = `https://www.youtube.com/embed/${youtubeKey}?autoplay=1`;

            iframeWrapper.innerHTML = `<iframe class="theater-iframe" src="${streamUrl}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>`;
            
            document.getElementById('player-adblock-shield').style.display = 'none';
            document.getElementById('theater-container').style.display = 'flex';
        }

        function handleAdShieldClick(event) {
            event.stopPropagation();
            const shield = document.getElementById('player-adblock-shield');
            shield.style.display = 'none';
            showToast("Decan AdBlocker intercepted popup/redirect successfully.");
        }

        function closeTheater() {
            document.getElementById('theater-container').style.display = 'none';
            document.getElementById('theater-iframe-wrapper').innerHTML = '';
            document.getElementById('player-adblock-shield').style.display = 'none';
        }

        function handleDownloadClick() {
            showToast("Download button is coming soon!");
        }

        async function shareCurrentTitle() {
            if(!currentModalDetails) return;
            const title = currentModalDetails.title || currentModalDetails.name || 'Title';
            const shareUrl = `${window.location.origin}${window.location.pathname}?title=${encodeURIComponent(currentModalDetails.media_type || 'movie')}&id=${encodeURIComponent(currentModalDetails.id)}`;
            const payload = { title: `Watch ${title} on Decan Movie`, text: `Check out ${title} on Decan Movie.`, url: shareUrl };
            try {
                if(navigator.share) await navigator.share(payload);
                else {
                    await navigator.clipboard.writeText(shareUrl);
                    showToast('Share link copied');
                }
            } catch(e) {
                if(e && e.name !== 'AbortError') showToast('Unable to share right now');
            }
        }

        function copyMovieDetailsToClipboard() {
            if(!currentModalDetails) return;
            const d = currentModalDetails;
            const title = d.title || d.name;
            const release = d.release_date || d.first_air_date || 'N/A';
            const rating = d.vote_average ? d.vote_average.toFixed(1) : 'NR';
            const seasons = d.number_of_seasons ? `Seasons: ${d.number_of_seasons}` : '';
            const episodes = d.number_of_episodes ? `Episodes: ${d.number_of_episodes}` : '';
            const overview = d.overview || '';
            const textToCopy = `Title: ${title}\nRelease Date: ${release}\nRating: ${rating}/10\n${seasons}\n${episodes}\nOverview: ${overview}\nStreamed via Decan Movie Portal`;

            navigator.clipboard.writeText(textToCopy).then(() => {
                showToast("Movie details copied to clipboard!");
            }).catch(() => {
                showToast("Failed to copy details.");
            });
        }

        function toggleWatchlist(id) {
            if(!currentModalDetails) return;
            const index = userWatchlist.findIndex(item => item.id === id);
            if(index > -1) {
                userWatchlist.splice(index, 1);
                showToast("Removed from Watchlist");
            } else {
                userWatchlist.push(currentModalDetails);
                showToast("Added to Watchlist");
            }
            localStorage.setItem('decan_watchlist', JSON.stringify(userWatchlist));
            openMovieDetails(currentModalDetails.id, currentModalDetails.media_type);
        }

        function toggleCustomList(id) {
            if(!currentModalDetails) return;
            const index = userCustomList.findIndex(item => item.id === id);
            if(index > -1) {
                userCustomList.splice(index, 1);
                showToast("Removed from Custom List");
            } else {
                userCustomList.push(currentModalDetails);
                showToast("Added to Custom List");
            }
            localStorage.setItem('decan_customlist', JSON.stringify(userCustomList));
            openMovieDetails(currentModalDetails.id, currentModalDetails.media_type);
        }

        function resetFeatureSurfaces() {
            animePortalMode = false;
            moviePortalMode = false;
            libraryMode = null;
            document.getElementById('anime-toolbar').classList.remove('active');
            document.getElementById('movie-toolbar').classList.remove('active');
            document.getElementById('recommendation-banner').classList.remove('active');
            document.getElementById('hero-strip').classList.remove('active');
            document.getElementById('stats-strip').classList.remove('active');
            const existing = document.getElementById('library-actions');
            if(existing) existing.remove();
        }

        function showWatchlist() {
            resetFeatureSurfaces();
            gridTitle.textContent = "My Watchlist";
            displayMovies(userWatchlist, null, false);
            if(window.innerWidth <= 900) sidebarMenu.classList.remove('open');
        }

        function showCustomList() {
            resetFeatureSurfaces();
            gridTitle.textContent = "Custom Collections";
            displayMovies(userCustomList, null, false);
            if(window.innerWidth <= 900) sidebarMenu.classList.remove('open');
        }

        function showHistory() {
            resetFeatureSurfaces();
            showLibraryHeader('Viewing History', userHistory.length, true);
            displayMovies(userHistory, null, false);
            if(window.innerWidth <= 900) sidebarMenu.classList.remove('open');
        }
    </script>
    <script src="supabase-config.js"></script>
    <script src="movie-analytics.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('service-worker.js').catch(() => {}));
        }
    </script>
</body>
</html>