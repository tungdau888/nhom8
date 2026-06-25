<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCT - Mạng Xã Hội Âm Nhạc NCT</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-main: #1a2128;
            --bg-sidebar: #161d24;
            --bg-player: #161d24;
            --bg-card: #1e2730;
            --bg-hover: rgba(255,255,255,0.06);
            --accent-color: #00d4d4;
            --text-main: #ffffff;
            --text-sub: #8a9bb0;
            --border-color: rgba(255,255,255,0.07);
        }
        * { box-sizing: border-box; }
        body { background-color: var(--bg-main); color: var(--text-main); font-family: 'Inter', sans-serif; margin: 0; overflow-x: hidden; }
        a { text-decoration: none; cursor: pointer; color: inherit; }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #3a4a5a; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #5a6a7a; }

        /* ===== SIDEBAR ===== */
        #sidebar {
            width: 220px;
            height: calc(100vh - 72px);
            position: fixed;
            top: 0; left: 0;
            background-color: var(--bg-sidebar);
            padding: 0;
            overflow-y: auto;
            z-index: 100;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }
        .logo-box {
            padding: 20px 20px 18px;
            display: flex; align-items: center; gap: 10px;
            cursor: pointer;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 8px;
        }
        .logo-icon {
            width: 36px; height: 36px;
            background: var(--accent-color);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #000; font-size: 18px; font-weight: 900;
        }
        .logo-text .brand { font-size: 16px; font-weight: 800; color: white; letter-spacing: 0.5px; }
        .logo-text .tagline { font-size: 10px; color: var(--text-sub); font-weight: 400; }
        .menu-section { padding: 4px 0 12px; }
        .menu-section-title { padding: 6px 20px 4px; font-size: 10px; font-weight: 700; color: #4a6070; text-transform: uppercase; letter-spacing: 1.2px; }
        .menu-list { list-style: none; padding: 0 10px; margin: 0; }
        .menu-list li a {
            display: flex; align-items: center; gap: 12px;
            padding: 9px 12px; color: var(--text-sub);
            font-size: 13px; font-weight: 500; border-radius: 8px; transition: 0.15s;
        }
        .menu-list li a i { font-size: 16px; width: 20px; text-align: center; }
        .menu-list li a:hover { background-color: rgba(255,255,255,0.06); color: white; }
        .menu-list li a.active { background-color: rgba(0, 212, 212, 0.12); color: var(--accent-color); }
        .menu-list li a.active i { color: var(--accent-color); }
        .sidebar-bottom { margin-top: auto; padding: 16px 14px; border-top: 1px solid var(--border-color); }
        .btn-sidebar-login {
            background-color: rgba(255,255,255,0.08);
            color: white; border: 1px solid rgba(255,255,255,0.12);
            padding: 8px 16px; border-radius: 20px;
            font-size: 12px; font-weight: 600; width: 100%; transition: 0.2s; cursor: pointer;
        }
        .btn-sidebar-login:hover { background-color: rgba(255,255,255,0.14); }

        /* ===== HEADER ===== */
        #main-wrapper { margin-left: 220px; padding-bottom: 90px; min-height: 100vh; }
        #top-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0 32px; height: 64px;
            position: sticky; top: 0;
            background-color: rgba(26, 33, 40, 0.97);
            z-index: 90; backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
        }
        .header-left { display: flex; align-items: center; gap: 10px; }
        .nav-btn {
            width: 32px; height: 32px; border-radius: 50%;
            background-color: rgba(255,255,255,0.08);
            border: none; color: var(--text-sub);
            display: inline-flex; align-items: center; justify-content: center;
            transition: 0.2s; font-size: 14px; cursor: pointer;
        }
        .nav-btn:hover { color: white; background: rgba(255,255,255,0.14); }
        .search-bar {
            background-color: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px; padding: 8px 16px;
            display: flex; align-items: center; gap: 10px;
            width: 380px; transition: 0.2s;
        }
        .search-bar:focus-within { border-color: rgba(0, 212, 212, 0.5); background-color: rgba(255,255,255,0.1); }
        .search-bar input { background: none; border: none; color: white; outline: none; width: 100%; font-size: 13px; }
        .search-bar input::placeholder { color: var(--text-sub); }
        .search-bar i { color: var(--text-sub); font-size: 14px; }
        .header-right { display: flex; align-items: center; gap: 10px; }
        .btn-upload { background: none; border: none; color: var(--text-sub); font-size: 18px; cursor: pointer; padding: 6px; transition: 0.2s; }
        .btn-upload:hover { color: white; }
        .btn-code {
            background-color: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px; padding: 6px 14px;
            color: white; font-size: 12px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s;
        }
        .btn-code:hover { background: rgba(255,255,255,0.12); }
        .btn-vip {
            background-color: transparent;
            border: 1.5px solid #f5a623;
            border-radius: 20px; padding: 6px 14px;
            color: #f5a623; font-size: 12px; font-weight: 700;
            cursor: pointer; transition: 0.2s; white-space: nowrap;
        }
        .btn-vip:hover { background-color: rgba(245, 166, 35, 0.1); }
        .header-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: 2px solid rgba(255,255,255,0.2);
            overflow: hidden; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; color: white;
        }
        .btn-settings { background: none; border: none; color: var(--text-sub); font-size: 16px; cursor: pointer; transition: 0.2s; padding: 6px; }
        .btn-settings:hover { color: white; }

        /* ===== CONTENT ===== */
        .content-area { padding: 0 32px 24px; }
        .section-title { font-size: 22px; font-weight: 800; margin: 32px 0 18px; color: white; letter-spacing: -0.3px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin: 32px 0 18px; }
        .section-header h3 { font-size: 20px; font-weight: 800; margin: 0; color: white; }
        .section-header a { font-size: 13px; color: var(--text-sub); transition: 0.2s; }
        .section-header a:hover { color: white; }

        /* ===== BANNER ===== */
        .banner-container { position: relative; width: 100%; overflow: hidden; border-radius: 12px; }
        .banner-track { display: flex; transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1); }
        .banner-item { flex: 0 0 50%; padding-right: 16px; }
        .banner-item:last-child { padding-right: 0; }
        .banner-item img { width: 100%; height: 210px; object-fit: cover; border-radius: 12px; cursor: pointer; }
        .banner-btn {
            position: absolute; top: 50%; transform: translateY(-50%);
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(0,0,0,0.55); color: white;
            border: none; z-index: 10; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; transition: 0.2s; backdrop-filter: blur(4px);
        }
        .banner-btn:hover { background: rgba(0,0,0,0.85); transform: translateY(-50%) scale(1.08); }
        .banner-btn.prev { left: 8px; }
        .banner-btn.next { right: 8px; }

        /* ===== TOPIC GRID ===== */
        .topic-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
        .topic-card {
            height: 96px; border-radius: 10px; position: relative;
            overflow: hidden; cursor: pointer; transition: 0.2s;
        }
        .topic-card:hover { transform: scale(1.03); filter: brightness(1.1); }
        .topic-card h3 {
            font-size: 14px; font-weight: 700; color: white;
            margin: 0; padding: 10px 12px;
            position: absolute; bottom: 0; left: 0; right: 0;
            z-index: 2; line-height: 1.3;
            background: linear-gradient(to top, rgba(0,0,0,0.72) 0%, transparent 100%);
        }
        .topic-card img {
            position: absolute; inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: 0;
            transition: transform 0.3s;
        }
        .topic-card:hover img { transform: scale(1.07); }
        .topic-card::after {
            content: '';
            position: absolute; inset: 0; z-index: 1;
            background: linear-gradient(160deg, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0.05) 50%, rgba(0,0,0,0.38) 100%);
        }

        /* ===== BXH ===== */
        .bxh-container { display: flex; gap: 16px; }
        .bxh-col { flex: 1; border-radius: 12px; padding: 20px; min-width: 0; border: 1px solid var(--border-color); }
        .bxh-col.bxh-1 { background: linear-gradient(160deg, #2a3a2e 0%, #1e2730 100%); }
        .bxh-col.bxh-2 { background: linear-gradient(160deg, #2e3535 0%, #1e2730 100%); }
        .bxh-col.bxh-3 { background: linear-gradient(160deg, #3a2a35 0%, #1e2730 100%); }
        .bxh-col-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .bxh-col-header a { display: flex; align-items: center; gap: 6px; font-size: 16px; font-weight: 800; color: white; }
        .bxh-col-header a i { font-size: 13px; color: var(--text-sub); }
        .bxh-play-btn {
            background: var(--accent-color); border: none;
            border-radius: 20px; padding: 5px 16px;
            font-size: 12px; font-weight: 700; color: #000;
            cursor: pointer; display: flex; align-items: center; gap: 5px; transition: 0.2s;
        }
        .bxh-play-btn:hover { opacity: 0.85; }
        .bxh-item { display: flex; align-items: center; margin-bottom: 10px; border-radius: 8px; padding: 6px 8px; cursor: pointer; transition: 0.15s; }
        .bxh-item:hover { background-color: rgba(255,255,255,0.07); }
        .bxh-rank { font-size: 17px; font-weight: 800; width: 24px; text-align: center; margin-right: 12px; color: white; flex-shrink: 0; }
        .bxh-img { width: 46px; height: 46px; border-radius: 6px; object-fit: cover; margin-right: 10px; flex-shrink: 0; }
        .bxh-info { flex-grow: 1; min-width: 0; }
        .bxh-info h6 { font-size: 13px; font-weight: 600; color: white; margin: 0 0 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bxh-info .bxh-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .lossless-badge { font-size: 9px; font-weight: 700; color: var(--accent-color); border: 1px solid var(--accent-color); border-radius: 3px; padding: 1px 4px; letter-spacing: 0.3px; flex-shrink: 0; }
        .bxh-info p { font-size: 11px; color: var(--text-sub); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bxh-label { display: flex; align-items: center; gap: 4px; font-size: 10px; color: var(--text-sub); }
        .bxh-rank-group { display: flex; flex-direction: column; align-items: center; width: 24px; margin-right: 12px; flex-shrink: 0; }
        .bxh-rank-arrow { font-size: 10px; color: #666; text-align: center; }

        /* ===== ARTIST GRID ===== */
        .artist-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .artist-big-card { border-radius: 12px; overflow: hidden; cursor: pointer; position: relative; transition: 0.2s; background: var(--bg-card); border: 1px solid var(--border-color); }
        .artist-big-card:hover .artist-cover { filter: brightness(0.75); }
        .artist-cover { width: 100%; height: 200px; object-fit: cover; display: block; transition: 0.3s; }
        .artist-big-info { padding: 14px 16px 16px; }
        .artist-big-info h5 { font-size: 16px; font-weight: 700; margin: 0 0 4px; color: white; }
        .artist-big-info .followers { font-size: 12px; color: var(--text-sub); margin-bottom: 12px; }
        .artist-big-footer { display: flex; align-items: center; justify-content: space-between; }
        .btn-follow-outline {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.25);
            color: white; border-radius: 20px;
            padding: 5px 16px; font-size: 12px; font-weight: 600;
            cursor: pointer; transition: 0.2s;
        }
        .btn-follow-outline:hover { background: rgba(255,255,255,0.18); }
        .artist-latest-song { display: flex; align-items: center; gap: 8px; }
        .artist-latest-thumb { width: 36px; height: 36px; border-radius: 5px; object-fit: cover; }
        .artist-latest-info h6 { font-size: 12px; font-weight: 600; color: white; margin: 0 0 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }
        .artist-latest-info p { font-size: 11px; color: var(--text-sub); margin: 0; }

        /* ===== SCROLL CARDS ===== */
        .scrolling-wrapper { display: flex; gap: 16px; overflow-x: auto; padding-bottom: 8px; scrollbar-width: none; }
        .scrolling-wrapper::-webkit-scrollbar { display: none; }
        .playlist-card { min-width: 220px; width: 220px; cursor: pointer; flex-shrink: 0; }
        .playlist-img-box { width: 100%; height: 160px; border-radius: 10px; overflow: hidden; position: relative; margin-bottom: 10px; }
        .playlist-img-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .playlist-card:hover .playlist-img-box img { transform: scale(1.05); filter: brightness(0.7); }
        .play-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); opacity: 0; transition: 0.3s; color: white; font-size: 42px; }
        .playlist-card:hover .play-overlay { opacity: 1; }
        .play-overlay-sm { position: absolute; top: 8px; right: 8px; opacity: 0; transition: 0.3s; }
        .play-overlay-sm i { background: var(--accent-color); color: #000; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 12px; }
        .playlist-card:hover .play-overlay-sm { opacity: 1; }
        .playlist-card h6 { font-size: 13px; font-weight: 600; color: white; margin: 0 0 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .playlist-card p { font-size: 11px; color: var(--text-sub); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .square-card { min-width: 175px; width: 175px; cursor: pointer; flex-shrink: 0; }
        .square-img-box { width: 100%; aspect-ratio: 1; border-radius: 8px; overflow: hidden; margin-bottom: 10px; position: relative; }
        .square-img-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .square-card:hover .square-img-box img { transform: scale(1.05); filter: brightness(0.6); }
        .square-card h6 { font-size: 13px; font-weight: 600; color: white; margin: 0 0 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .square-card p { font-size: 11px; color: var(--text-sub); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ===== SONG TABLE ===== */
        .song-table { width: 100%; color: var(--text-main); border-collapse: separate; border-spacing: 0; }
        .song-table th { color: var(--text-sub); font-size: 11px; font-weight: 600; text-transform: uppercase; padding: 12px 15px; border-bottom: 1px solid #2a3540; letter-spacing: 0.5px; }
        .song-table td { padding: 10px 15px; vertical-align: middle; border-bottom: 1px solid #232d36; }
        .song-table tbody tr { cursor: pointer; transition: 0.15s; }
        .song-table tbody tr:hover { background-color: rgba(255,255,255,0.05); }

        /* ===== LYRICS ===== */
        #lyrics-scroll-box { position: relative; max-height: 480px; overflow-y: auto; scroll-behavior: smooth; padding-bottom: 24px; padding-top: 8px; }
        #lyrics-scroll-box::-webkit-scrollbar { width: 3px; }
        #lyrics-scroll-box::-webkit-scrollbar-thumb { background: rgba(0,212,212,0.3); border-radius: 3px; }
        .lyric-line {
            font-size: 15px; line-height: 2.2; color: rgba(255,255,255,0.75);
            margin-bottom: 0px; padding: 2px 8px;
            border-radius: 6px;
            white-space: pre-wrap;
        }
        .lyric-line.lyric-blank {
            height: 10px; display: block;
        }

        /* ===== PLAYER ===== */
        #player {
            height: 72px; width: 100%; position: fixed; bottom: 0; left: 0;
            background-color: var(--bg-player);
            border-top: 1px solid var(--border-color);
            z-index: 1050; display: flex; align-items: center;
            justify-content: space-between; padding: 0 24px;
        }
        .player-left { display: flex; align-items: center; gap: 12px; width: 28%; }
        .player-left img { width: 48px; height: 48px; border-radius: 6px; object-fit: cover; }
        .player-song-name { font-size: 13px; font-weight: 600; color: white; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
        .player-artist-name { font-size: 11px; color: var(--text-sub); margin: 2px 0 0; }
        .player-center { display: flex; flex-direction: column; align-items: center; width: 44%; gap: 8px; }
        .controls { display: flex; align-items: center; gap: 4px; }
        .controls i { font-size: 16px; color: var(--text-sub); cursor: pointer; padding: 6px 8px; transition: 0.15s; border-radius: 6px; }
        .controls i:hover { color: white; }
        .controls .play-btn { font-size: 36px; color: white; padding: 0 6px; }
        .controls .play-btn:hover { opacity: 0.85; transform: scale(1.05); }
        .progress-container { display: flex; align-items: center; gap: 10px; width: 100%; }
        .progress-container span { font-size: 10px; color: var(--text-sub); width: 32px; text-align: center; flex-shrink: 0; }
        .player-right { display: flex; align-items: center; justify-content: flex-end; gap: 12px; width: 28%; }
        .quality-badge { font-size: 10px; border: 1px solid #3a5060; border-radius: 4px; padding: 2px 7px; color: #6a8090; font-weight: 600; }
        .player-right i { font-size: 16px; color: var(--text-sub); cursor: pointer; transition: 0.15s; }
        .player-right i:hover { color: white; }
        #volume-bar, #progress-bar { accent-color: var(--accent-color); }

        .spin { animation: spin 10s linear infinite; }
        .spin-paused { animation-play-state: paused; }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* ===== MODAL AUTH ===== */
        .auth-modal .modal-content { background-color: #1e2a35; color: white; border-radius: 16px; border: 1px solid var(--border-color); }
        .auth-modal .modal-header { border-bottom: none; padding: 24px 24px 0; }
        .auth-modal .modal-body { padding: 24px; }
        .auth-input { background-color: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); color: white; border-radius: 8px; padding: 12px 16px; width: 100%; margin-bottom: 14px; font-size: 13px; transition: 0.2s; }
        .auth-input:focus { background-color: rgba(255,255,255,0.1); border-color: var(--accent-color); outline: none; color: white; }
        .btn-cyan { background-color: var(--accent-color); color: black; font-weight: 700; border-radius: 30px; padding: 12px; width: 100%; border: none; font-size: 14px; cursor: pointer; transition: 0.2s; }
        .btn-cyan:hover { opacity: 0.88; }
        .auth-link { color: var(--accent-color); font-weight: 600; }

        /* ===== CỦA TUI - Profile ===== */
        .profile-hero {
            background: linear-gradient(180deg, #2a3a4a 0%, var(--bg-main) 100%);
            padding: 40px 32px 24px;
            margin: 0 -32px;
        }
        .profile-avatar-wrap {
            position: relative;
            width: 100px; height: 100px;
            margin-bottom: 16px;
        }
        .profile-avatar-wrap img {
            width: 100px; height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.15);
        }
        .profile-avatar-wrap .edit-btn {
            position: absolute; bottom: 0; right: 0;
            width: 28px; height: 28px;
            background: var(--accent-color); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #000; font-size: 12px; cursor: pointer;
            border: 2px solid var(--bg-main);
        }
        .profile-name { font-size: 28px; font-weight: 800; color: white; margin: 0 0 4px; }
        .profile-meta { display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--text-sub); margin-bottom: 16px; flex-wrap: wrap; }
        .profile-meta .sep { color: #3a5060; }
        .profile-badge {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px; padding: 3px 12px;
            font-size: 11px; font-weight: 600; color: var(--text-sub);
        }
        .profile-stat { text-align: center; }
        .profile-stat span { font-size: 16px; font-weight: 800; color: white; display: block; }
        .profile-stat small { font-size: 11px; color: var(--text-sub); }
        .profile-card {
            border-radius: 12px; padding: 20px;
            display: flex; align-items: center; gap: 16px;
            cursor: pointer; transition: 0.2s;
            border: 1px solid var(--border-color);
        }
        .profile-card:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.12); }
        .profile-card-icon {
            width: 56px; height: 56px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; flex-shrink: 0;
        }
        .profile-card h5 { font-size: 16px; font-weight: 700; color: white; margin: 0 0 4px; }
        .profile-card p { font-size: 12px; color: var(--text-sub); margin: 0; }

        /* ===== RECENTLY PLAYED TABS ===== */
        .rp-tabs {
            display: flex; gap: 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px; margin-top: 8px;
        }
        .rp-tab {
            padding: 10px 18px;
            font-size: 14px; font-weight: 600;
            color: var(--text-sub); cursor: pointer;
            transition: 0.15s;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .rp-tab:hover { color: white; }
        .rp-tab.active { color: white; border-bottom-color: var(--accent-color); }

        /* ===== DÀNH CHO BẠN ===== */
        .for-you-hero {
            background: linear-gradient(135deg, #1a2a3a 0%, #2a1a3a 50%, #1a2a2a 100%);
            border-radius: 16px; padding: 28px 28px 24px;
            margin-bottom: 24px; position: relative; overflow: hidden;
        }
        .for-you-hero::before {
            content: ''; position: absolute;
            top: -60px; right: -60px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(0,212,212,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .for-you-hero h2 { font-size: 24px; font-weight: 800; margin: 0 0 6px; }
        .for-you-hero p { font-size: 13px; color: var(--text-sub); margin: 0; }
        .mood-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 8px; }
        .mood-card {
            border-radius: 12px; padding: 16px;
            cursor: pointer; transition: 0.2s;
            position: relative; overflow: hidden; min-height: 90px;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .mood-card:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .mood-card h5 { font-size: 14px; font-weight: 700; color: white; margin: 0; position: relative; z-index: 2; }
        .mood-card p { font-size: 11px; color: rgba(255,255,255,0.6); margin: 0; position: relative; z-index: 2; }
        .mood-card i { font-size: 28px; position: absolute; bottom: 10px; right: 14px; opacity: 0.4; }

        /* ===== FAVORITE / PLAYLIST VIEW ===== */
        .fav-cover {
            width: 220px; height: 220px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .fav-cover i { font-size: 70px; }
        .empty-state {
            text-align: center; padding: 60px 20px;
            display: flex; flex-direction: column; align-items: center; gap: 16px;
        }
        .empty-state-icon { font-size: 72px; opacity: 0.3; }
        .empty-state h4 { font-size: 18px; font-weight: 700; margin: 0; }
        .empty-state p { font-size: 13px; color: var(--text-sub); margin: 0; max-width: 300px; }
        .btn-outline-pill {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white; border-radius: 30px;
            padding: 8px 24px; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: 0.2s;
        }
        .btn-outline-pill:hover { background: rgba(255,255,255,0.16); }
        .btn-play-all {
            background: var(--accent-color); color: #000;
            border: none; border-radius: 30px;
            padding: 10px 28px; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: 0.2s;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-play-all:hover { opacity: 0.88; }
        .btn-download {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white; border-radius: 30px;
            padding: 9px 22px; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: 0.2s;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-download:hover { background: rgba(255,255,255,0.16); }

        /* Song row heart toggle */
        .heart-btn { background: none; border: none; color: var(--text-sub); font-size: 16px; cursor: pointer; padding: 4px; transition: 0.2s; }
        .heart-btn:hover { color: #e05c6d; }
        .heart-btn.liked { color: #e05c6d; }

        /* ===== SONG DETAIL HERO ===== */
        .song-detail-hero {
            position: relative; margin: 0 -32px 0; padding: 40px 32px 32px;
            overflow: hidden;
        }
        .song-detail-hero::before {
            content: ''; position: absolute; inset: 0;
            background: inherit; filter: blur(0); z-index: 0;
        }
        .song-detail-bg {
            position: absolute; inset: 0; z-index: 0;
            background-size: cover; background-position: center;
            filter: blur(60px) saturate(1.8) brightness(0.35);
            transform: scale(1.15);
        }
        .song-detail-bg::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(26,33,40,0.3) 0%, rgba(26,33,40,0.92) 100%);
        }
        .song-detail-content { position: relative; z-index: 1; display: flex; gap: 32px; align-items: flex-end; }
        .song-cover-wrap {
            position: relative; flex-shrink: 0;
            width: 240px; height: 240px;
        }
        .song-cover-wrap img {
            width: 240px; height: 240px; border-radius: 14px; object-fit: cover;
            box-shadow: 0 20px 60px rgba(0,0,0,0.7), 0 0 40px rgba(0,212,212,0.15);
            display: block;
        }
        .song-cover-wrap.playing img {
            animation: coverPulse 3s ease-in-out infinite;
        }
        @keyframes coverPulse {
            0%, 100% { box-shadow: 0 20px 60px rgba(0,0,0,0.7), 0 0 30px rgba(0,212,212,0.12); }
            50% { box-shadow: 0 20px 60px rgba(0,0,0,0.7), 0 0 55px rgba(0,212,212,0.35); }
        }
        .wave-bars {
            position: absolute; bottom: 12px; left: 12px;
            display: flex; align-items: flex-end; gap: 3px; height: 26px;
            opacity: 0; transition: opacity 0.3s;
        }
        .song-cover-wrap.playing .wave-bars { opacity: 1; }
        .wave-bar {
            width: 3px; border-radius: 2px;
            background: var(--accent-color);
            animation: waveAnim 0.8s ease-in-out infinite alternate;
        }
        .wave-bar:nth-child(1) { height: 60%; animation-duration: 0.6s; }
        .wave-bar:nth-child(2) { height: 100%; animation-duration: 0.4s; }
        .wave-bar:nth-child(3) { height: 75%; animation-duration: 0.7s; }
        .wave-bar:nth-child(4) { height: 50%; animation-duration: 0.5s; }
        .wave-bar:nth-child(5) { height: 85%; animation-duration: 0.9s; }
        @keyframes waveAnim {
            from { transform: scaleY(0.3); opacity: 0.7; }
            to   { transform: scaleY(1); opacity: 1; }
        }
        .song-detail-meta { padding-bottom: 4px; }
        .song-detail-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--accent-color); margin-bottom: 10px; }
        .song-detail-title { font-size: 42px; font-weight: 900; line-height: 1.05; margin: 0 0 14px; color: white; letter-spacing: -1px; }
        .song-detail-artist { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .song-detail-artist img { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.2); }
        .song-detail-artist span { font-weight: 700; font-size: 15px; color: white; }
        .song-detail-stats { display: flex; gap: 20px; margin-bottom: 22px; }
        .stat-item { display: flex; align-items: center; gap: 6px; font-size: 13px; color: rgba(255,255,255,0.55); }
        .stat-item i { font-size: 15px; }
        .song-detail-actions { display: flex; align-items: center; gap: 12px; }
        .btn-play-detail {
            background: var(--accent-color); color: #000;
            border: none; border-radius: 30px;
            padding: 11px 30px; font-size: 14px; font-weight: 800;
            cursor: pointer; transition: 0.2s;
            display: inline-flex; align-items: center; gap: 8px;
            letter-spacing: 0.3px;
        }
        .btn-play-detail:hover { filter: brightness(1.1); transform: scale(1.03); }
        .btn-action-round {
            width: 42px; height: 42px; border-radius: 50%;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.18);
            color: white; font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .btn-action-round:hover { background: rgba(255,255,255,0.18); transform: scale(1.08); }
        .btn-action-round.liked { color: #e05c6d; border-color: rgba(224,92,109,0.4); }

        /* ===== LYRICS PANEL ===== */
        .lyrics-panel {
            background: rgba(15,22,28,0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px; padding: 28px; overflow: hidden;
        }
        .lyrics-panel-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px; padding-bottom: 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .lyrics-panel-header img { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }
        .lyrics-panel-header span { font-size: 12px; color: var(--text-sub); }
        /* ===== ARTIST PANEL ===== */
        .artist-panel {
            background: rgba(15,22,28,0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px; padding: 24px; text-align: center;
        }
        .artist-panel-avatar {
            width: 120px; height: 120px; border-radius: 50%;
            object-fit: cover; margin: 0 auto 14px; display: block;
            box-shadow: 0 4px 24px rgba(0,0,0,0.5);
            border: 3px solid rgba(0,212,212,0.25);
        }
        .artist-panel h5 { font-size: 18px; font-weight: 800; margin: 0 0 4px; }
        .artist-panel .followers { font-size: 12px; color: var(--text-sub); margin-bottom: 18px; }

        .label-icon {
            width: 22px; height: 22px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 12px; flex-shrink: 0; margin-right: 4px;
        }
        .label-icon.sony { background: #e03030; color: white; }
        .label-icon.warner { background: #3070c0; color: white; }
        .label-icon.nct { background: var(--accent-color); color: #000; }
        .label-icon.susu { background: #404040; color: white; }
        .label-icon.sky { background: #1a8080; color: white; }

        .publisher-cell { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; }

        /* ===== ADMIN ACTION BUTTONS IN BXH ===== */
        .bxh-admin-btns { display: flex; gap: 4px; margin-left: auto; opacity: 0; transition: 0.2s; flex-shrink: 0; }
        .bxh-item:hover .bxh-admin-btns { opacity: 1; }
        .btn-bxh-action {
            width: 26px; height: 26px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 11px; border: 1px solid rgba(255,255,255,0.15);
            background: rgba(0,0,0,0.5); color: white; cursor: pointer; transition: 0.15s;
        }
        .btn-bxh-edit:hover { border-color: #ffc107; color: #ffc107; background: rgba(255,193,7,0.15); }
        .btn-bxh-del:hover  { border-color: #e05c6d; color: #e05c6d; background: rgba(224,92,109,0.15); }
    </style>
</head>
<body>

    <!-- ===== SIDEBAR ===== -->
    <aside id="sidebar">
        <div class="logo-box" onclick="renderHomeView()">
            <div class="logo-icon"><i class="bi bi-soundwave"></i></div>
            <div class="logo-text">
                <div class="brand">NCT</div>
                <div class="tagline">Mạng Xã Hội Âm Nhạc NCT</div>
            </div>
        </div>

        <div class="menu-section">
            <ul class="menu-list">
                <li><a onclick="renderHomeView()" id="nav-home"><i class="bi bi-grid-1x2-fill"></i> Khám Phá</a></li>
                <li><a onclick="renderForYouView()" id="nav-foryou"><i class="bi bi-stars"></i> Dành Cho Bạn</a></li>
                <li><a onclick="renderMyView()" id="nav-my"><i class="bi bi-person-circle"></i> Của Tui</a></li>
            </ul>
        </div>

        <div class="menu-section">
            <div class="menu-section-title">Thư Viện</div>
            <ul class="menu-list">
                <li><a onclick="renderFavoritesView()" id="nav-fav"><i class="bi bi-heart-fill" style="color: #e05c6d;"></i> Bài hát Yêu thích</a></li>
                <li><a onclick="renderRecentView('songs')" id="nav-recent"><i class="bi bi-clock-history"></i> Nghe gần đây</a></li>
            </ul>
        </div>

        <div class="menu-section">
            <div class="menu-section-title" style="display:flex;align-items:center;justify-content:space-between;padding-right:14px;">
                Playlist Đã Tạo
                <i class="bi bi-plus-circle" style="font-size:14px;color:var(--text-sub);cursor:pointer;"></i>
            </div>
            <ul class="menu-list">
                <li><a href="#"><i class="bi bi-music-note-list"></i> Danh sách của tôi</a></li>
            </ul>
        </div>

        <div class="sidebar-bottom">
            <?php if(isset($_SESSION['user_id'])): ?>
                <p style="font-size:12px;color:var(--text-sub);margin-bottom:10px;">Chào, <strong class="text-white"><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <button class="btn-sidebar-login mb-2" onclick="window.location.href='admin.php'">Trang Admin</button>
                <?php endif; ?>
                <button class="btn-sidebar-login" onclick="window.location.href='logout.php'">Đăng xuất</button>
            <?php else: ?>
                <p style="font-size:12px;color:var(--text-sub);margin-bottom:10px;text-align:center;">Đăng nhập để khám phá nhạc hay</p>
                <button class="btn-sidebar-login" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button>
            <?php endif; ?>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <main id="main-wrapper">
        <header id="top-header">
            <div class="header-left">
                <button class="nav-btn" id="btn-back"><i class="bi bi-chevron-left"></i></button>
                <button class="nav-btn" id="btn-forward"><i class="bi bi-chevron-right"></i></button>
                <div class="search-bar" style="margin-left:8px;">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Tìm bài hát, nghệ sĩ, lời bài hát..." id="global-search-input" oninput="handleSearchInput(this.value)">
                </div>
            </div>
            <div class="header-right">
                <button class="btn-upload"><i class="bi bi-upload"></i></button>
                <button class="btn-code"><i class="bi bi-ticket-perforated"></i> Nhập code</button>
                <button class="btn-vip">Trung tâm VIP</button>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="header-avatar" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="bi bi-person"></i></div>
                <?php else: ?>
                    <div class="header-avatar" onclick="renderMyView()" style="cursor:pointer;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                <?php endif; ?>
                <button class="btn-settings"><i class="bi bi-gear"></i></button>
            </div>
        </header>

        <div class="content-area" id="main-content">
            <div class="text-center py-5"><div class="spinner-border" style="color: var(--accent-color);"></div></div>
        </div>
    </main>

    <!-- ===== PLAYER ===== -->
    <footer id="player">
        <div class="player-left">
            <img id="player-thumb" src="https://placehold.co/48x48/1e2730/FFF?text=NCT" alt="Cover" class="spin spin-paused">
            <div style="min-width:0;">
                <p id="player-title" class="player-song-name">Hãy chọn bài hát</p>
                <p id="player-artist" class="player-artist-name">NCT Music</p>
            </div>
            <button class="heart-btn ms-2" id="player-heart" onclick="togglePlayerHeart()" style="flex-shrink:0;"><i class="bi bi-suit-heart"></i></button>
            <i class="bi bi-three-dots ms-1" style="font-size:16px;color:var(--text-sub);cursor:pointer;flex-shrink:0;"></i>
        </div>
        <div class="player-center">
            <div class="controls">
                <i class="bi bi-shuffle" id="btn-shuffle"></i>
                <i class="bi bi-skip-backward-fill" onclick="playPrev()"></i>
                <i class="bi bi-play-circle-fill play-btn" id="play-btn" onclick="togglePlay()"></i>
                <i class="bi bi-skip-forward-fill" onclick="playNext()"></i>
                <i class="bi bi-arrow-repeat" id="btn-repeat"></i>
            </div>
            <div class="progress-container">
                <span id="current-time">00:00</span>
                <input type="range" id="progress-bar" value="0" step="1" min="0" class="form-range mx-1" style="height:3px;flex:1;">
                <span id="total-time">00:00</span>
            </div>
        </div>
        <div class="player-right">
            <span class="quality-badge">128 kbps</span>
            <i class="bi bi-volume-up" id="vol-icon"></i>
            <input type="range" id="volume-bar" value="100" min="0" max="100" class="form-range" style="width: 80px; height: 3px;">
            <i class="bi bi-music-note-list ms-1" onclick="renderRecentView('songs')" title="Nghe gần đây"></i>
        </div>
    </footer>

    <!-- MODALS -->
    <div class="modal fade auth-modal" id="loginModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h4 class="modal-title fw-bold">Đăng nhập</h4><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><form action="action_login.php" method="POST"><input type="text" class="auth-input" name="username" placeholder="Nhập email/username" required><input type="password" class="auth-input" name="password" placeholder="Nhập mật khẩu" required><button type="submit" class="btn-cyan mb-3">Đăng nhập</button><div class="text-center" style="font-size: 13px; color: #8a9bb0;">Chưa có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" class="auth-link">Đăng ký ngay</a></div></form></div></div></div></div>
    <div class="modal fade auth-modal" id="registerModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h4 class="modal-title fw-bold">Thông tin đăng ký</h4><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><form action="action_register.php" method="POST"><input type="text" class="auth-input" name="username" placeholder="Tên đăng nhập" required><input type="email" class="auth-input" name="email" placeholder="Địa chỉ Email" required><input type="password" class="auth-input mb-4" name="password" placeholder="Mật khẩu bảo mật" required><button type="submit" class="btn-cyan">Hoàn thành đăng ký</button></form></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // ===== DATA =====
    let allSongsData = []; let allCategoriesData = []; let allAlbumsData = [];
    let allBannersData = []; let allArtistsData = []; let allSiteConfig = {};
    let likedSongs = JSON.parse(localStorage.getItem('nct_liked') || '[]');
    let recentSongs = JSON.parse(localStorage.getItem('nct_recent') || '[]');
    let currentPlayingId = null;
    let currentQueue = [];
    let currentQueueIndex = -1;
    const mainContent = document.getElementById('main-content');
    const isAdmin = <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'true' : 'false' ?>;

    // Nav history
    let navHistory = []; let navFuture = [];

    document.addEventListener('DOMContentLoaded', () => {
        fetchAllDataFromServer();
        document.getElementById('btn-back').addEventListener('click', navBack);
        document.getElementById('btn-forward').addEventListener('click', navForward);
    });

    function fetchAllDataFromServer() {
        fetch('api_get_data.php')
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    allSongsData = data.songs || [];
                    allCategoriesData = data.categories || [];
                    allAlbumsData = data.albums || [];
                    allBannersData = data.banners || [];
                    allArtistsData = data.artists || [];
                    allSiteConfig = data.site_config || {};
                    renderHomeView(false);
                }
            })
            .catch(() => {
                mainContent.innerHTML = '<div class="text-danger p-5 text-center">Lỗi kết nối máy chủ API!</div>';
            });
    }

    // Làm mới dữ liệu từ server mà không reload trang (đồng bộ với admin)
    function refreshDataFromServer(silent = true) {
        fetch('api_get_data.php')
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    allSongsData       = data.songs       || [];
                    allCategoriesData  = data.categories  || [];
                    allAlbumsData      = data.albums      || [];
                    allBannersData     = data.banners     || [];
                    allArtistsData     = data.artists     || [];
                    allSiteConfig      = data.site_config || {};
                    if (!silent) renderHomeView(false);
                }
            })
            .catch(() => {});
    }

    // Tự động làm mới khi chuyển tab trở lại (đồng bộ với admin)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) refreshDataFromServer(true);
    });

    // ===== NAVIGATION =====
    function setActiveNav(id) {
        document.querySelectorAll('.menu-list a').forEach(a => a.classList.remove('active'));
        const el = document.getElementById(id);
        if (el) el.classList.add('active');
    }

    function navBack() {
        if (navHistory.length > 1) {
            navFuture.push(navHistory.pop());
            const prev = navHistory[navHistory.length - 1];
            if (prev) prev(true);
        }
    }
    function navForward() {
        if (navFuture.length > 0) {
            const next = navFuture.pop();
            if (next) next(true);
        }
    }
    function pushNav(fn) {
        navHistory.push(fn);
        navFuture = [];
    }

    // ===== SEARCH =====
    function handleSearchInput(q) {
        if (!q.trim()) return;
        renderSearchResults(q);
    }

    function renderSearchResults(q) {
        setActiveNav('');
        pushNav(() => renderSearchResults(q));
        window.scrollTo(0, 0);
        const found = allSongsData.filter(s =>
            s.title.toLowerCase().includes(q.toLowerCase()) ||
            s.artist.toLowerCase().includes(q.toLowerCase())
        );
        let html = `<h2 class="section-title">Kết quả tìm kiếm: "${q}"</h2>`;
        if (found.length === 0) {
            html += `<div class="empty-state"><div class="empty-state-icon"><i class="bi bi-search"></i></div><h4>Không tìm thấy kết quả</h4><p>Thử tìm với từ khóa khác</p></div>`;
        } else {
            html += buildSongTable(found, 'Tiêu đề', true);
        }
        mainContent.innerHTML = html;
    }

    // ===== BANNER SLIDER =====
    let currentBannerIdx = 0;
    window.slideBanner = function(direction) {
        const track = document.getElementById('banner-track');
        if (!track) return;
        const items = track.querySelectorAll('.banner-item');
        if (items.length <= 2) return;
        const maxIdx = items.length - 2;
        currentBannerIdx += direction;
        if (currentBannerIdx > maxIdx) currentBannerIdx = 0;
        if (currentBannerIdx < 0) currentBannerIdx = maxIdx;
        track.style.transform = `translateX(calc(-${currentBannerIdx * 50}%))`;
    };

    // ===== HOME VIEW =====
    function renderHomeView(fromHistory) {
        setActiveNav('nav-home');
        if (!fromHistory) pushNav(() => renderHomeView(true));
        window.scrollTo(0, 0);
        currentBannerIdx = 0;
        document.getElementById('global-search-input').value = '';

        const hour = new Date().getHours();
        let greeting = 'Chào buổi sáng ☀️';
        if (hour >= 12 && hour < 18) greeting = 'Chào buổi chiều 🌤️';
        else if (hour >= 18) greeting = 'Chào buổi tối 🌙';

        let html = `<h2 class="section-title">${greeting}</h2>`;

        // BANNER
        html += `<div class="banner-container mb-2">`;
        if (allBannersData.length > 2) {
            html += `<button class="banner-btn prev" onclick="slideBanner(-1)"><i class="bi bi-chevron-left"></i></button>
                     <button class="banner-btn next" onclick="slideBanner(1)"><i class="bi bi-chevron-right"></i></button>`;
        }
        html += `<div class="banner-track" id="banner-track">`;
        if (allBannersData.length > 0) {
            allBannersData.forEach(b => {
                html += `<div class="banner-item"><img src="${b.image_url}" onerror="this.src='https://placehold.co/800x210/2a3540/FFF?text=Banner'"></div>`;
            });
        } else {
            html += `<div class="banner-item"><img src="https://placehold.co/800x210/2d3a46/FFF?text=Gen+Gì+Gen"></div>
                     <div class="banner-item"><img src="https://placehold.co/800x210/2a3e4a/FFF?text=Indie+Việt"></div>
                     <div class="banner-item"><img src="https://placehold.co/800x210/1a3038/FFF?text=Nhạc+Trẻ"></div>`;
        }
        html += `</div></div>`;

        // CHỦ ĐỀ
        if (allCategoriesData.length > 0) {
            const topicColors = [
                'linear-gradient(135deg,#1a3a6a 0%,#2a5a9a 100%)',
                'linear-gradient(135deg,#1a5a4a 0%,#2a8a6a 100%)',
                'linear-gradient(135deg,#5a3a1a 0%,#9a6a2a 100%)',
                'linear-gradient(135deg,#4a1a3a 0%,#7a2a5a 100%)',
                'linear-gradient(135deg,#1a2a5a 0%,#2a3a8a 100%)',
                'linear-gradient(135deg,#3a1a1a 0%,#7a2a2a 100%)',
                'linear-gradient(135deg,#1a3a2a 0%,#2a6a4a 100%)',
                'linear-gradient(135deg,#2a1a5a 0%,#4a2a8a 100%)',
                'linear-gradient(135deg,#5a4a1a 0%,#8a7a2a 100%)',
                'linear-gradient(135deg,#1a4a4a 0%,#2a7a7a 100%)',
            ];
            html += `<div class="section-header mt-4"><h3>Chủ Đề</h3><a onclick="renderAllCategoriesView()">Thêm</a></div>`;
            html += `<div class="topic-grid mb-4">`;
            allCategoriesData.slice(0, 10).forEach((cat, idx) => {
                const bg = topicColors[idx % topicColors.length];
                const safeN = cat.name.replace(/'/g, "\\'");
                const imgSrc = cat.image_url || '';
                html += `<div class="topic-card" style="background:${bg};"
                            onclick="renderCategoryView(${cat.id},'${safeN}')">
                            <h3>${cat.name}</h3>
                            ${imgSrc ? `<img src="${imgSrc}" onerror="this.style.display='none'">` : ''}
                         </div>`;
            });
            html += `</div>`;
        }

        // BXH
        html += `<div class="section-header mt-4"><h3>Bảng Xếp Hạng</h3></div><div class="bxh-container mb-2">`;
        const sortedByViews = [...allSongsData].sort((a,b) => (b.views||0) - (a.views||0));
        const bxhClasses = ['bxh-1', 'bxh-2', 'bxh-3'];
        const defBxhTitles = ['Top 50 Bài Hát Thịnh Hành', 'Top 50 Nhạc Việt', 'Top 50 Nhạc Hot'];
        const bxhBuilt = [];
        for (let ci = 1; ci <= 3; ci++) {
            const title = (allSiteConfig['bxh_col'+ci+'_title'] || defBxhTitles[ci-1]);
            const songIdsRaw = allSiteConfig['bxh_col'+ci+'_songs'] || '';
            let songs;
            if (songIdsRaw.trim() !== '') {
                const ids = songIdsRaw.split(',').map(x=>x.trim()).filter(Boolean);
                songs = ids.map(id => allSongsData.find(s => String(s.id) === id)).filter(Boolean);
            } else {
                const usedIds = bxhBuilt.flatMap(b => b.data.map(s=>s.id));
                songs = sortedByViews.filter(s => !usedIds.includes(s.id)).slice(0, 10);
            }
            bxhBuilt.push({ title, cls: bxhClasses[ci-1], data: songs });
        }

        bxhBuilt.forEach(col => {
            html += '<div class="bxh-col ' + col.cls + '">';
            html += '<div class="bxh-col-header">';
            html += '<a href="#" onclick="return false;">' + col.title + ' <i class="bi bi-chevron-right"></i></a>';
            html += '<button class="bxh-play-btn"><i class="bi bi-play-fill"></i> Phát</button>';
            html += '</div>';
            col.data.forEach((song, i) => {
                const safeTitle  = song.title.replace(/'/g, "\\'");
                const safeArtist = song.artist.replace(/'/g, "\\'");
                const safeComp   = (song.composer   || '').replace(/'/g, "\\'");
                const safeLyrics = (song.lyrics      || '').replace(/'/g, "\\'").replace(/\n/g, '\\n');
                const safeMood   = song.mood   || '';
                const safeStatus = song.status || 'approved';
                const safeAlbId  = song.album_id || '';
                const safeCatId  = song.category_id || 0;
                let adminBtns = '';
                if (isAdmin) {
                    adminBtns = '<div class="bxh-admin-btns" onclick="event.stopPropagation()">'
                        + '<button class="btn-bxh-action btn-bxh-edit" title="Sửa"'
                        + ' onclick="openEditSongModal_bxh(' + song.id + ',\'' + safeTitle + '\',\'' + safeArtist + '\',\'' + safeComp + '\',' + safeCatId + ',\'' + safeAlbId + '\',\'' + safeLyrics + '\',\'' + safeMood + '\',\'' + safeStatus + '\')">'
                        + '<i class="bi bi-pencil"></i></button>'
                        + '<button class="btn-bxh-action btn-bxh-del" title="Xóa"'
                        + ' onclick="deleteSong_bxh(' + song.id + ',\'' + safeTitle + '\')">'
                        + '<i class="bi bi-trash3"></i></button>'
                        + '</div>';
                }
                html += '<div class="bxh-item" onclick="playSongFromList(' + song.id + ', allSongsData)">'
                    + '<div class="bxh-rank-group">'
                    + '<span class="bxh-rank">' + (i+1) + '</span>'
                    + '<span class="bxh-rank-arrow">—</span>'
                    + '</div>'
                    + '<img src="' + song.image_url + '" class="bxh-img" onerror="this.src=\'https://placehold.co/46\'">'
                    + '<div class="bxh-info">'
                    + '<h6>' + song.title + '</h6>'
                    + '<div class="bxh-meta">'
                    + '<span class="lossless-badge">Lossless</span>'
                    + '<span style="font-size:11px;color:var(--text-sub);">' + song.artist + '</span>'
                    + '</div>'
                    + '<div class="bxh-label mt-1">'
                    + '<i class="bi bi-headphones" style="color:var(--accent-color);"></i>'
                    + '<span>' + Number(song.views||0).toLocaleString('vi-VN') + ' lượt nghe</span>'
                    + '</div>'
                    + '</div>'
                    + adminBtns
                    + '</div>';
            });
            if (col.data.length === 0) html += '<div class="text-secondary text-center py-4" style="font-size:13px;">Chưa có bài hát</div>';
            html += '</div>';
        });
        html += '</div>';

        // NGHỆ SĨ
        html += `<div class="section-header mt-4"><h3>Nghệ Sĩ Thịnh Hành</h3><a>Thêm</a></div><div class="artist-grid mb-2">`;
        const displayArtists = allArtistsData.length > 0 ? allArtistsData.slice(0,4) : [
            {name:'Sơn Tùng M-TP',image_url:'https://placehold.co/400x200/2a3540/FFF?text=Sơn+Tùng'},
            {name:'Binz',image_url:'https://placehold.co/400x200/2a3540/FFF?text=Binz'},
            {name:'VSTRA',image_url:'https://placehold.co/400x200/2a3540/FFF?text=VSTRA'},
            {name:'Low G',image_url:'https://placehold.co/400x200/2a3540/FFF?text=Low+G'}
        ];
        const placeholderFollowers = ['133,810','20,832','11,521','42,739'];
        displayArtists.forEach((art, i) => {
            const latestSong = allSongsData.find(s => s.artist && s.artist.includes(art.name)) || null;
            const followers = (art.followers != null && art.followers !== '')
                ? parseInt(art.followers).toLocaleString('vi-VN')
                : (allArtistsData.length === 0 ? placeholderFollowers[i] : '—');
            html += `<div class="artist-big-card">
                <img src="${art.image_url}" class="artist-cover" onerror="this.src='https://placehold.co/400x200/2a3540/FFF?text=Artist'">
                <div class="artist-big-info">
                    <h5>${art.name}</h5>
                    <p class="followers">${followers} người theo dõi</p>
                    <div class="artist-big-footer">
                        <button class="btn-follow-outline">Theo dõi</button>
                        ${latestSong ? `<div class="artist-latest-song">
                            <img src="${latestSong.image_url}" class="artist-latest-thumb" onerror="this.src='https://placehold.co/36'">
                            <div class="artist-latest-info"><h6>${latestSong.title}</h6><p>${latestSong.artist}</p></div>
                        </div>` : ''}
                    </div>
                </div>
            </div>`;
        });
        html += `</div>`;

        // DYNAMIC SECTIONS
        let homeSections = [];
        try {
            const raw = allSiteConfig['homepage_sections_json'];
            if (raw) { const parsed = JSON.parse(raw); if (Array.isArray(parsed)) homeSections = parsed; }
        } catch(e) {}

        homeSections.forEach((sec, si) => {
            const secTitle = sec.title || ('Section ' + (si+1));
            const secType  = sec.type  || 'scroll';
            const selIds = (sec.album_ids || '').split(',').map(x=>x.trim()).filter(Boolean);
            let secAlbums;
            if (selIds.length > 0) {
                secAlbums = selIds.map(id => allAlbumsData.find(a => String(a.id) === String(id))).filter(Boolean);
            } else {
                secAlbums = allAlbumsData;
            }
            html += `<div class="section-header mt-4"><h3>${secTitle}</h3></div>`;
            html += `<div class="scrolling-wrapper mb-2">`;
            if (secAlbums.length > 0) {
                secAlbums.forEach(alb => {
                    const safeT = alb.title.replace(/'/g,"\\'");
                    const safeImg = alb.image_url || '';
                    const albArtists = [...new Set(allSongsData.filter(s=>s.album_id==alb.id).map(s=>s.artist))].slice(0,3).join(', ') || 'NCT Tuyển chọn';
                    if (secType === 'grid') {
                        html += `<div class="square-card" onclick="renderAlbumView(${alb.id},'${safeT}','${safeImg}')">
                            <div class="square-img-box">
                                <img src="${safeImg}" onerror="this.src='https://placehold.co/175/2a3540/FFF?text=Album'">
                                <i class="bi bi-play-circle-fill play-overlay" style="font-size:36px;"></i>
                            </div>
                            <h6>${alb.title}</h6>
                            <p>${albArtists}</p>
                        </div>`;
                    } else {
                        html += `<div class="playlist-card" onclick="renderAlbumView(${alb.id},'${safeT}','${safeImg}')">
                            <div class="playlist-img-box">
                                <img src="${safeImg}" onerror="this.src='https://placehold.co/220x160/2a3540/FFF?text=Album'">
                                <i class="bi bi-play-circle-fill play-overlay"></i>
                                <div class="play-overlay-sm"><i class="bi bi-play-fill"></i></div>
                            </div>
                            <h6>${alb.title}</h6>
                            <p>${albArtists}</p>
                        </div>`;
                    }
                });
            } else {
                html += `<div class="text-secondary" style="font-size:13px;padding:4px 0;">Chưa có album nào.</div>`;
            }
            html += `</div>`;
        });

        mainContent.innerHTML = html;
    }

    // ===== DÀNH CHO BẠN =====
    function renderForYouView(fromHistory) {
        setActiveNav('nav-foryou');
        if (!fromHistory) pushNav(() => renderForYouView(true));
        window.scrollTo(0, 0);

        const moods = [
            {label:'Vui vẻ - Năng lượng',sub:'Nhạc sôi động nhất hôm nay',bg:'linear-gradient(135deg,#e06030,#d04090)',icon:'bi-lightning-charge-fill'},
            {label:'Chill & Thư giãn',sub:'Âm nhạc nhẹ nhàng, dễ chịu',bg:'linear-gradient(135deg,#1060a0,#10a080)',icon:'bi-cloud-sun-fill'},
            {label:'Buồn & Tâm tư',sub:'Khi cần một bài hát đồng cảm',bg:'linear-gradient(135deg,#404080,#204060)',icon:'bi-moon-stars-fill'},
            {label:'Tập trung - Lo-fi',sub:'Học bài, làm việc hiệu quả',bg:'linear-gradient(135deg,#304040,#203050)',icon:'bi-headphones'},
            {label:'Luyện tập - Sport',sub:'Nhạc pump up cho buổi gym',bg:'linear-gradient(135deg,#802020,#a05030)',icon:'bi-trophy-fill'},
            {label:'Tình yêu - Romantic',sub:'Những bản nhạc ngọt ngào nhất',bg:'linear-gradient(135deg,#901040,#602060)',icon:'bi-heart-fill'}
        ];

        let html = `
            <div class="for-you-hero mt-4">
                <h2>🎵 Dành Cho Bạn</h2>
                <p>Khám phá nhạc được chọn lọc riêng dựa theo sở thích của bạn</p>
            </div>
            <div class="section-header" style="margin-top:8px;">
                <h3>Chọn Tâm Trạng</h3>
            </div>
            <div class="mood-grid mb-4">`;

        moods.forEach(m => {
            html += `<div class="mood-card" style="background:${m.bg};" onclick="renderMoodSongs('${m.label.replace(/'/g,"\\'")}')">
                <h5>${m.label}</h5>
                <p>${m.sub}</p>
                <i class="bi ${m.icon}"></i>
            </div>`;
        });
        html += `</div>`;

        html += `<div class="section-header"><h3>Phát Nhiều Nhất Hôm Nay</h3><a onclick="renderRecentView('songs')">Xem tất cả</a></div>`;
        if (allSongsData.length > 0) {
            html += buildSongTable(allSongsData.slice(0, 8), 'Bài hát nổi bật', true);
        }

        html += `<div class="section-header mt-4"><h3>Vì Bạn Yêu Thích</h3><a>Thêm</a></div><div class="scrolling-wrapper mb-4">`;
        const recs = allSongsData.length > 0 ? allSongsData.slice(0, 8) : [];
        recs.forEach(song => {
            html += `<div class="square-card" onclick="playSongFromList(${song.id}, allSongsData)">
                <div class="square-img-box">
                    <img src="${song.image_url}" onerror="this.src='https://placehold.co/175/2a3540/FFF?text=Song'">
                    <i class="bi bi-play-circle-fill play-overlay" style="font-size:36px;"></i>
                </div>
                <h6>${song.title}</h6>
                <p>${song.artist}</p>
            </div>`;
        });
        html += `</div>`;

        mainContent.innerHTML = html;
    }

    function renderMoodSongs(mood) {
        pushNav(() => renderMoodSongs(mood));
        window.scrollTo(0, 0);
        let html = `
            <div class="mt-4" style="font-size:13px;color:var(--text-sub);">
                <span onclick="renderForYouView()" style="cursor:pointer;">Dành cho bạn</span>
                <i class="bi bi-chevron-right mx-1" style="font-size:11px;"></i>
                <span style="color:white;font-weight:600;">${mood}</span>
            </div>
            <h1 style="font-size:32px;font-weight:800;margin:12px 0 24px;">${mood}</h1>`;
        html += buildSongTable(allSongsData, 'Danh sách phát', true);
        mainContent.innerHTML = html;
    }

    // ===== CỦA TUI =====
    function renderMyView(fromHistory) {
        setActiveNav('nav-my');
        if (!fromHistory) pushNav(() => renderMyView(true));
        window.scrollTo(0, 0);

        const userName = '<?= isset($_SESSION['username']) ? addslashes($_SESSION['username']) : 'Khách' ?>';
        const userId = '<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0' ?>';
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        if (!isLoggedIn) {
            mainContent.innerHTML = `
                <div class="empty-state mt-5">
                    <div class="empty-state-icon"><i class="bi bi-person-circle"></i></div>
                    <h4>Đăng nhập để xem trang của bạn</h4>
                    <p>Khám phá nhạc được cá nhân hóa, lưu bài hát yêu thích và theo dõi nghệ sĩ</p>
                    <button class="btn-cyan" style="width:auto;padding:10px 32px;border-radius:30px;" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="bi bi-person-fill me-2"></i>Đăng nhập ngay
                    </button>
                </div>`;
            return;
        }

        mainContent.innerHTML = `
            <div class="profile-hero">
                <div class="profile-avatar-wrap">
                    <img src="https://placehold.co/100x100/2a3a4a/FFF?text=${userName.charAt(0).toUpperCase()}" alt="Avatar">
                    <div class="edit-btn"><i class="bi bi-pencil"></i></div>
                </div>
                <h2 class="profile-name">${userName}</h2>
                <div class="profile-meta">
                    <span>ID: ${userId.toString().padStart(8,'0')}</span>
                    <span class="sep">•</span>
                    <span class="profile-badge">Miễn phí</span>
                    <span class="sep">•</span>
                    <span><strong class="text-white">0</strong> Đang theo dõi</span>
                    <span class="sep">•</span>
                    <span><strong class="text-white">0</strong> Người theo dõi</span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn-play-all" onclick="if(likedSongs.length>0)renderFavoritesView()"><i class="bi bi-play-fill"></i> Phát</button>
                    <button class="btn-download"><i class="bi bi-share"></i> Chia sẻ</button>
                </div>
            </div>

            <div class="row g-3 mt-1 mb-4">
                <div class="col-md-4">
                    <div class="profile-card" onclick="renderFavoritesView()" style="background:linear-gradient(135deg,rgba(255,70,100,0.15),rgba(255,30,60,0.05));">
                        <div class="profile-card-icon" style="background:linear-gradient(135deg,#ff4466,#cc2244);">
                            <i class="bi bi-heart-fill" style="color:white;"></i>
                        </div>
                        <div>
                            <h5>Yêu Thích</h5>
                            <p>${likedSongs.length} bài hát</p>
                        </div>
                        <i class="bi bi-chevron-right ms-auto" style="color:var(--text-sub);"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="profile-card" onclick="renderRecentView('songs')" style="background:linear-gradient(135deg,rgba(0,100,200,0.15),rgba(0,60,120,0.05));">
                        <div class="profile-card-icon" style="background:linear-gradient(135deg,#0066cc,#003388);">
                            <i class="bi bi-clock-history" style="color:white;"></i>
                        </div>
                        <div>
                            <h5>Nghe gần đây</h5>
                            <p>${recentSongs.length} bài hát</p>
                        </div>
                        <i class="bi bi-chevron-right ms-auto" style="color:var(--text-sub);"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="profile-card" style="background:linear-gradient(135deg,rgba(0,180,100,0.15),rgba(0,100,60,0.05));">
                        <div class="profile-card-icon" style="background:linear-gradient(135deg,#00b464,#006640);">
                            <i class="bi bi-upload" style="color:white;"></i>
                        </div>
                        <div>
                            <h5>Đã tải lên</h5>
                            <p>0 bài hát · 0 video</p>
                        </div>
                        <i class="bi bi-chevron-right ms-auto" style="color:var(--text-sub);"></i>
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h3>Playlist đã tạo (0)</h3>
                <button class="btn-outline-pill" style="font-size:12px;padding:6px 16px;">
                    <i class="bi bi-plus me-1"></i> Tạo playlist
                </button>
            </div>
            <div class="empty-state" style="padding:40px 20px;">
                <div style="font-size:64px;opacity:0.25;"><i class="bi bi-collection"></i></div>
                <h4>Danh sách playlist chưa có</h4>
                <p>Hãy tạo playlist đầu tiên của bạn.</p>
                <button class="btn-outline-pill">Tạo playlist</button>
            </div>`;
    }

    // ===== YÊU THÍCH =====
    function renderFavoritesView(fromHistory) {
        setActiveNav('nav-fav');
        if (!fromHistory) pushNav(() => renderFavoritesView(true));
        window.scrollTo(0, 0);

        const favSongs = allSongsData.filter(s => likedSongs.includes(s.id));

        let html = `
            <div class="d-flex gap-4 mt-5 mb-5 align-items-end">
                <div class="fav-cover" style="background:linear-gradient(135deg,#ff4488,#cc2266);">
                    <i class="bi bi-heart-fill" style="color:white;"></i>
                </div>
                <div class="pb-2">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-sub);margin-bottom:8px;">Playlist · ${favSongs.length} Bài hát</div>
                    <h1 style="font-size:42px;font-weight:800;margin-bottom:14px;">Your Favorite</h1>
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-suit-heart-fill" style="color:#e05c6d;font-size:16px;"></i>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <button class="btn-play-all" onclick="playFavorites()" ${favSongs.length===0?'disabled':''}>
                            <i class="bi bi-play-fill"></i> Phát tất cả
                        </button>
                        <button class="btn-download"><i class="bi bi-download"></i> Tải về</button>
                    </div>
                </div>
            </div>`;

        if (favSongs.length === 0) {
            html += `<div class="empty-state">
                <div style="font-size:72px;opacity:0.2;"><i class="bi bi-heart"></i></div>
                <h4>Không có dữ liệu</h4>
                <p>Chọn bài hát thêm vào playlist</p>
                <button class="btn-outline-pill" onclick="renderHomeView()">Thêm bài hát</button>
            </div>`;
        } else {
            html += buildSongTable(favSongs, 'Bài hát yêu thích', false);
        }

        mainContent.innerHTML = html;
    }

    function playFavorites() {
        const favSongs = allSongsData.filter(s => likedSongs.includes(s.id));
        if (favSongs.length > 0) playSongFromList(favSongs[0].id, favSongs);
    }

    // ===== NGHE GẦN ĐÂY =====
    function renderRecentView(activeTab, fromHistory) {
        setActiveNav('nav-recent');
        if (!fromHistory) pushNav(() => renderRecentView(activeTab, true));
        window.scrollTo(0, 0);

        const tabs = [
            {key:'songs',label:'Bài hát'},
            {key:'playlist',label:'Playlist'},
            {key:'album',label:'Album'},
            {key:'artist',label:'Nghệ sĩ'},
            {key:'radio',label:'Radios'},
            {key:'video',label:'Video'}
        ];

        let html = `
            <h1 style="font-size:32px;font-weight:800;margin:32px 0 0;">Recently played</h1>
            <div class="rp-tabs">`;
        tabs.forEach(t => {
            html += `<div class="rp-tab ${t.key===activeTab?'active':''}" onclick="renderRecentView('${t.key}')">${t.label}</div>`;
        });
        html += `</div>`;

        if (activeTab === 'songs') {
            const recentList = recentSongs.map(id => allSongsData.find(s => s.id == id)).filter(Boolean);
            if (recentList.length === 0) {
                html += `<div class="empty-state">
                    <div style="font-size:72px;opacity:0.2;"><i class="bi bi-clock-history"></i></div>
                    <h4 style="font-size:18px;font-weight:700;">Chưa có bài hát nào</h4>
                    <p>Bắt đầu nghe nhạc để xem lịch sử tại đây</p>
                    <button class="btn-outline-pill" onclick="renderHomeView()">Khám phá nhạc</button>
                </div>`;
            } else {
                html += `<div class="d-flex align-items-center gap-3 mb-4">
                    <button class="btn-play-all" onclick="playSongFromList(${recentList[0].id}, recentList)">
                        <i class="bi bi-play-fill" style="font-size:18px;"></i>
                    </button>
                    <span style="font-size:15px;font-weight:700;">Bài hát đã phát (${recentList.length})</span>
                </div>`;
                html += buildRecentSongTable(recentList);
            }
        } else if (activeTab === 'album') {
            if (allAlbumsData.length === 0) {
                html += `<div class="empty-state"><div style="font-size:64px;opacity:0.2;"><i class="bi bi-disc"></i></div><h4>Chưa có album nào</h4><p>Khám phá và nghe album để xem tại đây</p></div>`;
            } else {
                html += `<div class="row row-cols-5 g-3 mt-1">`;
                allAlbumsData.slice(0,10).forEach(alb => {
                    const safeTitle = alb.title.replace(/'/g,"\\'");
                    html += `<div class="col"><div class="square-card" onclick="renderAlbumView(${alb.id},'${safeTitle}','${alb.image_url}')">
                        <div class="square-img-box"><img src="${alb.image_url}" onerror="this.src='https://placehold.co/175'"><i class="bi bi-play-circle-fill play-overlay" style="font-size:36px;"></i></div>
                        <h6>${alb.title}</h6><p>Album</p>
                    </div></div>`;
                });
                html += `</div>`;
            }
        } else if (activeTab === 'artist') {
            if (allArtistsData.length === 0) {
                html += `<div class="empty-state"><div style="font-size:64px;opacity:0.2;"><i class="bi bi-people"></i></div><h4>Chưa theo dõi nghệ sĩ nào</h4></div>`;
            } else {
                html += `<div class="d-flex flex-wrap gap-4 mt-3">`;
                allArtistsData.forEach(art => {
                    html += `<div style="text-align:center;cursor:pointer;width:120px;">
                        <img src="${art.image_url}" style="width:90px;height:90px;border-radius:50%;object-fit:cover;margin-bottom:8px;" onerror="this.src='https://placehold.co/90'">
                        <p style="font-size:12px;font-weight:600;margin:0;">${art.name}</p>
                        <p style="font-size:11px;color:var(--text-sub);margin:0;">Nghệ sĩ</p>
                    </div>`;
                });
                html += `</div>`;
            }
        } else {
            html += `<div class="empty-state"><div style="font-size:64px;opacity:0.2;"><i class="bi bi-inbox"></i></div><h4>Chưa có dữ liệu</h4></div>`;
        }

        mainContent.innerHTML = html;
    }

    // ===== SONG TABLE HELPERS =====
    const labelIcons = {
        'SONY MUSIC':          {cls:'sony',  icon:'bi-circle-fill', color:'#e03030'},
        'WARNER RECORDED MUSIC':{cls:'warner',icon:'bi-star-fill',   color:'#3070c0'},
        'NCT MUSIC DISTRIBUTION':{cls:'nct', icon:'bi-soundwave',   color:'#00d4d4'},
        'SKYMUSIC':             {cls:'sky',   icon:'bi-cloud-fill',  color:'#1a8080'},
        'Su Su':                {cls:'susu',  icon:'bi-music-note',  color:'#606060'},
        'INGROOVES MUSIC GROUP':{cls:'warner',icon:'bi-vinyl-fill',  color:'#3070c0'},
        'UNIVERSAL MUSIC GROUP':{cls:'sony',  icon:'bi-disc-fill',   color:'#8030e0'}
    };
    const labelKeys = Object.keys(labelIcons);

    function getLabelForSong(song, idx) {
        return labelKeys[idx % labelKeys.length];
    }

    function buildSongTable(songs, title, showPublisher) {
        let html = `<table class="song-table"><thead><tr>
            <th style="width:4%;text-align:center;">#</th>
            <th style="width:${showPublisher?'40%':'50%'};">Tiêu đề</th>
            ${showPublisher ? '<th style="width:22%;">Người đăng</th>' : ''}
            <th style="width:${showPublisher?'22%':'30%'};">Nghệ sĩ</th>
            <th style="width:5%;text-align:center;"><i class="bi bi-clock"></i></th>
            <th style="width:5%;"></th>
        </tr></thead><tbody>`;
        songs.forEach((song, index) => {
            const labelName = getLabelForSong(song, index);
            const li = labelIcons[labelName] || labelIcons[labelKeys[0]];
            const isLiked = likedSongs.includes(song.id);
            html += `<tr onclick="playSongFromList(${song.id}, allSongsData)">
                <td style="text-align:center;color:var(--text-sub);">${index+1}</td>
                <td><div class="d-flex align-items-center gap-3">
                    <img src="${song.image_url}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;" onerror="this.src='https://placehold.co/40'">
                    <div>
                        <div style="font-size:14px;font-weight:600;color:${currentPlayingId===song.id?'var(--accent-color)':'white'};" id="title-${song.id}">${song.title}</div>
                        ${currentPlayingId===song.id ? '<div style="font-size:11px;color:var(--accent-color);display:flex;align-items:center;gap:4px;"><i class="bi bi-volume-up-fill"></i> Đang phát</div>' : ''}
                    </div>
                </div></td>
                ${showPublisher ? `<td><div class="publisher-cell">
                    <i class="bi ${li.icon}" style="color:${li.color};font-size:16px;"></i>
                    <span style="font-size:12px;color:var(--text-sub);">${labelName}</span>
                </div></td>` : ''}
                <td style="font-size:12px;color:var(--text-sub);">${song.artist}</td>
                <td style="text-align:center;font-size:12px;color:var(--text-sub);">${formatDuration(song.duration)}</td>
                <td><button class="heart-btn ${isLiked?'liked':''}" onclick="event.stopPropagation();toggleLike(${song.id},this)"><i class="bi bi-suit-heart${isLiked?'-fill':''}"></i></button></td>
            </tr>`;
        });
        html += `</tbody></table>`;
        return html;
    }

    function buildRecentSongTable(songs) {
        let html = `<table class="song-table"><thead><tr>
            <th style="width:4%;text-align:center;">#</th>
            <th style="width:40%;">Tiêu đề</th>
            <th style="width:24%;">Người đăng</th>
            <th style="width:22%;">Nghệ sĩ</th>
            <th style="width:6%;text-align:right;">Thời lượng</th>
            <th style="width:4%;"></th>
        </tr></thead><tbody>`;
        songs.forEach((song, index) => {
            const labelName = getLabelForSong(song, index);
            const li = labelIcons[labelName] || labelIcons[labelKeys[0]];
            const isLiked = likedSongs.includes(song.id);
            html += `<tr onclick="playSongFromList(${song.id}, songs)">
                <td style="text-align:center;color:var(--text-sub);">${index+1}</td>
                <td><div class="d-flex align-items-center gap-3">
                    <img src="${song.image_url}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;" onerror="this.src='https://placehold.co/40'">
                    <span style="font-size:14px;font-weight:600;color:${currentPlayingId===song.id?'var(--accent-color)':'var(--accent-color)'};">${song.title}</span>
                </div></td>
                <td><div class="publisher-cell">
                    <i class="bi ${li.icon}" style="color:${li.color};font-size:16px;"></i>
                    <span style="font-size:12px;color:var(--text-sub);">${labelName}</span>
                </div></td>
                <td style="font-size:12px;color:var(--text-sub);">${song.artist}</td>
                <td style="text-align:right;font-size:12px;color:var(--text-sub);">${formatDuration(song.duration)}</td>
                <td><button class="heart-btn ${isLiked?'liked':''}" onclick="event.stopPropagation();toggleLike(${song.id},this)"><i class="bi bi-suit-heart${isLiked?'-fill':''}"></i></button></td>
            </tr>`;
        });
        html += `</tbody></table>`;
        return html;
    }

    // ===== TOGGLE LIKE =====
    function toggleLike(songId, btn) {
        const idx = likedSongs.indexOf(songId);
        if (idx === -1) {
            likedSongs.push(songId);
            btn.classList.add('liked');
            btn.innerHTML = '<i class="bi bi-suit-heart-fill"></i>';
        } else {
            likedSongs.splice(idx, 1);
            btn.classList.remove('liked');
            btn.innerHTML = '<i class="bi bi-suit-heart"></i>';
        }
        localStorage.setItem('nct_liked', JSON.stringify(likedSongs));
        const playerHeart = document.getElementById('player-heart');
        if (playerHeart && currentPlayingId === songId) {
            playerHeart.classList.toggle('liked', likedSongs.includes(songId));
            playerHeart.innerHTML = likedSongs.includes(songId) ? '<i class="bi bi-suit-heart-fill"></i>' : '<i class="bi bi-suit-heart"></i>';
        }
    }

    function togglePlayerHeart() {
        if (!currentPlayingId) return;
        const btn = document.getElementById('player-heart');
        toggleLike(currentPlayingId, btn);
    }

    // ===== ADMIN: SỬA / XÓA BÀI HÁT TỪ BXH =====
    function openEditSongModal_bxh(id, title, artist, composer, catId, albId, lyrics, mood, status) {
        const params = new URLSearchParams({
            edit_song_id: id, title, artist, composer,
            category_id: catId, album_id: albId,
            lyrics, mood, status
        });
        window.open('admin.php?' + params.toString(), '_blank');
    }

    function deleteSong_bxh(songId, songTitle) {
        if (!confirm('Xóa bài hát "' + songTitle + '"? Hành động này không thể hoàn tác.')) return;
        const fd = new FormData();
        fd.append('action_song', 'delete');
        fd.append('song_id', songId);
        fetch('admin.php', { method: 'POST', body: fd })
            .then(() => {
                allSongsData = allSongsData.filter(s => s.id != songId);
                renderHomeView();
                const toast = document.createElement('div');
                toast.style.cssText = 'position:fixed;bottom:100px;right:24px;background:#22c55e;color:#000;padding:10px 20px;border-radius:8px;font-weight:700;z-index:9999;font-size:13px;';
                toast.textContent = '✓ Đã xóa: ' + songTitle;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            })
            .catch(() => alert('Lỗi khi xóa bài hát!'));
    }

    // ===== TẤT CẢ CHỦ ĐỀ =====
    function renderAllCategoriesView(fromHistory) {
        setActiveNav('nav-home');
        if (!fromHistory) pushNav(() => renderAllCategoriesView(true));
        window.scrollTo(0, 0);
        const topicColors = [
            'linear-gradient(135deg,#1a3a6a 0%,#2a5a9a 100%)',
            'linear-gradient(135deg,#1a5a4a 0%,#2a8a6a 100%)',
            'linear-gradient(135deg,#5a3a1a 0%,#9a6a2a 100%)',
            'linear-gradient(135deg,#4a1a3a 0%,#7a2a5a 100%)',
            'linear-gradient(135deg,#1a2a5a 0%,#2a3a8a 100%)',
            'linear-gradient(135deg,#3a1a1a 0%,#7a2a2a 100%)',
            'linear-gradient(135deg,#1a3a2a 0%,#2a6a4a 100%)',
            'linear-gradient(135deg,#2a1a5a 0%,#4a2a8a 100%)',
            'linear-gradient(135deg,#5a4a1a 0%,#8a7a2a 100%)',
            'linear-gradient(135deg,#1a4a4a 0%,#2a7a7a 100%)',
        ];
        let html = `<h2 class="section-title mt-4">Tất Cả Chủ Đề</h2><div class="topic-grid mb-4">`;
        allCategoriesData.forEach((cat, idx) => {
            const bg = topicColors[idx % topicColors.length];
            const safeN = cat.name.replace(/'/g, "\\'");
            const imgSrc = cat.image_url || '';
            html += `<div class="topic-card" style="background:${bg};"
                        onclick="renderCategoryView(${cat.id},'${safeN}')">
                        <h3>${cat.name}</h3>
                        ${imgSrc ? `<img src="${imgSrc}" onerror="this.style.display='none'">` : ''}
                     </div>`;
        });
        html += `</div>`;
        mainContent.innerHTML = html;
    }

    // ===== CATEGORY VIEW =====
    function renderCategoryView(catId, categoryName, fromHistory) {
        if (!fromHistory) pushNav(() => renderCategoryView(catId, categoryName, true));
        window.scrollTo(0, 0);
        let filteredAlbums = allAlbumsData.filter(a => a.category_id == catId);
        let filteredSongs = allSongsData.filter(s => s.category_id == catId);
        let html = `
            <div class="mt-4" style="font-size:13px;color:var(--text-sub);">
                <span onclick="renderHomeView()" style="cursor:pointer;">Khám phá</span>
                <i class="bi bi-chevron-right mx-1" style="font-size:11px;"></i>
                <span style="color:white;font-weight:600;">${categoryName}</span>
            </div>
            <h1 style="font-size:36px;font-weight:800;margin:12px 0 24px;">${categoryName}</h1>`;

        if (filteredAlbums.length > 0) {
            html += `<div class="section-header"><h3>Album</h3></div>
            <div class="row row-cols-5 g-4 mb-5">`;
            filteredAlbums.forEach(alb => {
                const safeTitle = alb.title.replace(/'/g,"\\'" );
                html += `<div class="col"><div class="square-card" onclick="renderAlbumView(${alb.id},'${safeTitle}','${alb.image_url}')">
                    <div class="square-img-box"><img src="${alb.image_url}" onerror="this.src='https://placehold.co/200'"><i class="bi bi-play-circle-fill play-overlay" style="font-size:36px;"></i></div>
                    <h6>${alb.title}</h6><p>Playlist • NCT</p>
                </div></div>`;
            });
            html += `</div>`;
        }

        if (filteredSongs.length > 0) {
            html += `<div class="section-header"><h3>Bài hát (${filteredSongs.length})</h3></div>`;
            html += buildSongTable(filteredSongs, categoryName, false);
        } else if (filteredAlbums.length === 0) {
            html += `<div style="text-align:center;padding:60px 0;color:var(--text-sub);">
                <i class="bi bi-music-note-beamed" style="font-size:64px;opacity:0.2;display:block;margin-bottom:16px;"></i>
                <h4 style="font-size:18px;">Chưa có bài hát nào trong chủ đề này</h4>
                <p>Admin cần thêm bài hát cho chủ đề <strong style="color:white;">${categoryName}</strong></p>
            </div>`;
        }

        mainContent.innerHTML = html;
    }

    // ===== ALBUM VIEW =====
    function renderAlbumView(albId, albumTitle, coverImg, fromHistory) {
        if (!fromHistory) pushNav(() => renderAlbumView(albId, albumTitle, coverImg, true));
        window.scrollTo(0, 0);
        let albumSongs = allSongsData.filter(s => s.album_id == albId);
        let html = `
            <div class="d-flex gap-4 mt-5 mb-5 align-items-end">
                <img src="${coverImg}" style="width:220px;height:220px;border-radius:12px;object-fit:cover;box-shadow:0 10px 30px rgba(0,0,0,0.5);" onerror="this.src='https://placehold.co/220'">
                <div class="pb-2">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-sub);margin-bottom:8px;">Playlist • ${albumSongs.length} Bài hát</div>
                    <h1 style="font-size:38px;font-weight:800;margin-bottom:12px;">${albumTitle}</h1>
                    <div class="d-flex gap-3 align-items-center">
                        <button class="btn-play-all" onclick="if(${albumSongs.length}>0)playSongFromList(${albumSongs[0]?.id||0},albumSongs)">
                            <i class="bi bi-play-fill"></i> Phát tất cả
                        </button>
                        <button class="btn-download"><i class="bi bi-download"></i> Tải về</button>
                    </div>
                </div>
            </div>
            ${buildSongTable(albumSongs, albumTitle, false)}`;
        mainContent.innerHTML = html;
    }

    // ===== LRC PARSER =====
    function parseLRC(lrcString) {
        if (!lrcString) return [];
        const lines = lrcString.split('\n');
        const result = [];
        const timeRegex = /\[(\d{2}):(\d{2}(?:\.\d{2,3})?)\]/;
        lines.forEach(line => {
            const match = timeRegex.exec(line);
            if (match) {
                const time = parseInt(match[1], 10) * 60 + parseFloat(match[2]);
                const text = line.replace(timeRegex, '').trim();
                if (text) result.push({ time, text });
            } else if (line.trim()) {
                result.push({ time: -1, text: line.trim() });
            }
        });
        return result;
    }
    function seekToLyric(time) { if (time > -1) { audio.currentTime = time; if (!isPlaying) togglePlay(); } }

    // ===== SONG DETAIL VIEW =====
    function renderSongDetailView(id, title, artist, audioUrl, imageUrl, fromHistory) {
        if (!fromHistory) pushNav(() => renderSongDetailView(id, title, artist, audioUrl, imageUrl, true));
        window.scrollTo(0, 0);
        prepareToPlay(id, title, artist, audioUrl, imageUrl);
        const songObj = allSongsData.find(s => s.id == id);

        let lyricsHtml = '';
        if (songObj && songObj.lyrics) {
            const rawLyrics = songObj.lyrics;
            const lines = rawLyrics.split('\n');
            lines.forEach(line => {
                const cleanLine = line.replace(/\[\d{2}:\d{2}(?:\.\d{2,3})?\]/g, '').trim();
                if (/^\[(?:ti|ar|al|by|offset|length|re|ve):.*\]$/.test(cleanLine)) return;
                if (cleanLine === '') {
                    lyricsHtml += `<div class="lyric-line lyric-blank"></div>`;
                } else {
                    lyricsHtml += `<div class="lyric-line">${cleanLine}</div>`;
                }
            });
        } else {
            lyricsHtml = `<div style="color:var(--text-sub);font-size:14px;padding:20px 0;text-align:center;">
                <i class="bi bi-music-note-beamed" style="font-size:36px;opacity:0.25;display:block;margin-bottom:12px;"></i>
                Chưa có lời bài hát cho bản nhạc này.
            </div>`;
        }

        const isLiked = likedSongs.includes(id);
        const viewCount = songObj ? (parseInt(songObj.views)||0).toLocaleString('vi-VN') : '0';
        const safeImg = imageUrl || 'https://placehold.co/240';

        let html = `
        <div class="song-detail-hero">
            <div class="song-detail-bg" id="detail-bg" style="background-image:url('${safeImg}');"></div>
            <div class="song-detail-content">
                <div class="song-cover-wrap playing" id="detail-cover-wrap">
                    <img src="${safeImg}" onerror="this.src='https://placehold.co/240'" alt="${title}">
                    <div class="wave-bars">
                        <div class="wave-bar"></div>
                        <div class="wave-bar"></div>
                        <div class="wave-bar"></div>
                        <div class="wave-bar"></div>
                        <div class="wave-bar"></div>
                    </div>
                </div>
                <div class="song-detail-meta">
                    <div class="song-detail-label">Bài hát</div>
                    <h1 class="song-detail-title">${title}</h1>
                    <div class="song-detail-artist">
                        <img src="${safeImg}" onerror="this.src='https://placehold.co/28'" alt="${artist}">
                        <span>${artist}</span>
                    </div>
                    <div class="song-detail-stats">
                        <div class="stat-item" id="stat-views-${id}"><i class="bi bi-eye"></i> ${viewCount} lượt nghe</div>
                        <div class="stat-item"><i class="bi bi-heart"></i> ${songObj && songObj.likes ? parseInt(songObj.likes).toLocaleString('vi-VN') : '—'}</div>
                        <div class="stat-item"><i class="bi bi-share"></i> ${songObj && songObj.shares ? parseInt(songObj.shares).toLocaleString('vi-VN') : '—'}</div>
                    </div>
                    <div class="song-detail-actions">
                        <button class="btn-play-detail" onclick="togglePlay()">
                            <i class="bi bi-pause-fill" id="detail-play-icon"></i> Đang phát
                        </button>
                        <button class="btn-download" style="border-radius:30px;padding:10px 22px;">
                            <i class="bi bi-download"></i> Tải về
                        </button>
                        <button class="btn-action-round ${isLiked?'liked':''}" id="detail-heart" onclick="toggleLike(${id},this)">
                            <i class="bi bi-suit-heart${isLiked?'-fill':''}"></i>
                        </button>
                        <button class="btn-action-round"><i class="bi bi-three-dots"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding: 32px 0 0;">
            <div class="row mb-5 g-4">
                <div class="col-md-8">
                    <div class="lyrics-panel">
                        <div class="lyrics-panel-header">
                            <img src="${safeImg}" onerror="this.src='https://placehold.co/24'" alt="">
                            <span>Lời bài hát</span>
                        </div>
                        <div id="lyrics-scroll-box">${lyricsHtml}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="artist-panel">
                        <img src="${safeImg}" class="artist-panel-avatar" onerror="this.src='https://placehold.co/120'" alt="${artist}">
                        <h5>${artist}</h5>
                        <p class="followers">${(() => {
                            const artData = allArtistsData.find(a => a.name === artist);
                            return (artData && artData.followers != null && artData.followers !== '')
                                ? parseInt(artData.followers).toLocaleString('vi-VN') + ' người theo dõi'
                                : '';
                        })()}</p>
                        <button class="btn-follow-outline" style="width:100%;padding:9px 0;font-size:13px;">Theo dõi</button>
                    </div>
                    <div style="margin-top:16px;background:rgba(15,22,28,0.7);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:20px;">
                        <h6 style="font-size:13px;font-weight:700;color:var(--text-sub);margin-bottom:14px;text-transform:uppercase;letter-spacing:0.8px;">Hàng đầu của ${artist}</h6>
                        ${allSongsData.filter(s=>s.artist===artist&&s.id!=id).slice(0,4).map((s,i)=>`
                        <div onclick="playSongFromList(${s.id}, allSongsData)" style="display:flex;align-items:center;gap:10px;padding:7px 0;cursor:pointer;border-radius:8px;transition:0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background=''">
                            <span style="font-size:12px;color:var(--text-sub);width:16px;text-align:center;">${i+1}</span>
                            <img src="${s.image_url||'https://placehold.co/36'}" style="width:36px;height:36px;border-radius:6px;object-fit:cover;" onerror="this.src='https://placehold.co/36'">
                            <div style="min-width:0;flex:1;">
                                <div style="font-size:13px;font-weight:600;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${s.title}</div>
                                <div style="font-size:11px;color:var(--text-sub);">${s.artist}</div>
                            </div>
                        </div>`).join('')}
                    </div>
                </div>
            </div>
        </div>`;
        mainContent.innerHTML = html;

        updateDetailCoverState();
    }

    function updateDetailCoverState() {
        const wrap = document.getElementById('detail-cover-wrap');
        if (!wrap) return;
        if (isPlaying) wrap.classList.add('playing');
        else wrap.classList.remove('playing');
    }

    // ===== AUDIO ENGINE =====
    const audio = new Audio();
    let isPlaying = false;
    const playBtn = document.getElementById('play-btn');
    const progressBar = document.getElementById('progress-bar');
    const volumeBar = document.getElementById('volume-bar');
    const currentTimeEl = document.getElementById('current-time');
    const totalTimeEl = document.getElementById('total-time');
    const playerThumb = document.getElementById('player-thumb');
    const playerTitle = document.getElementById('player-title');
    const playerArtist = document.getElementById('player-artist');

    function playSongFromList(songId, queue) {
        const song = allSongsData.find(s => s.id == songId);
        if (!song) return;
        currentQueue = Array.isArray(queue) ? queue : allSongsData;
        currentQueueIndex = currentQueue.findIndex(s => s.id == songId);
        renderSongDetailView(song.id, song.title, song.artist, song.audio_url, song.image_url);
    }

    function prepareToPlay(id, title, artist, audioUrl, imageUrl) {
        currentPlayingId = id;
        playerTitle.textContent = title;
        playerArtist.textContent = artist;
        playerThumb.src = imageUrl || 'https://placehold.co/48';

        // Add to recent
        recentSongs = recentSongs.filter(rid => rid != id);
        recentSongs.unshift(id);
        if (recentSongs.length > 50) recentSongs = recentSongs.slice(0, 50);
        localStorage.setItem('nct_recent', JSON.stringify(recentSongs));

        // Update player heart
        const playerHeart = document.getElementById('player-heart');
        if (playerHeart) {
            const liked = likedSongs.includes(id);
            playerHeart.classList.toggle('liked', liked);
            playerHeart.innerHTML = liked ? '<i class="bi bi-suit-heart-fill"></i>' : '<i class="bi bi-suit-heart"></i>';
        }

        if (audio.src !== audioUrl) { audio.src = audioUrl; }
        audio.play().catch(() => {});
        isPlaying = true;
        updatePlayIcons(true);

        incrementSongViews(id);
    }

    function incrementSongViews(songId) {
        const formData = new FormData();
        formData.append('song_id', songId);
        fetch('api_update_views.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    const song = allSongsData.find(s => s.id == songId);
                    if (song) song.views = data.views;
                    const viewEl = document.getElementById('stat-views-' + songId);
                    if (viewEl) viewEl.textContent = parseInt(data.views).toLocaleString('vi-VN') + ' lượt nghe';
                }
            })
            .catch(() => {});
    }

    function togglePlay() {
        if (!audio.src) return;
        if (isPlaying) { audio.pause(); isPlaying = false; }
        else { audio.play(); isPlaying = true; }
        updatePlayIcons(isPlaying);
    }

    function playNext() {
        if (currentQueue.length === 0) return;
        currentQueueIndex = (currentQueueIndex + 1) % currentQueue.length;
        const next = currentQueue[currentQueueIndex];
        if (next) prepareToPlay(next.id, next.title, next.artist, next.audio_url, next.image_url);
    }

    function playPrev() {
        if (currentQueue.length === 0) return;
        if (audio.currentTime > 3) { audio.currentTime = 0; return; }
        currentQueueIndex = (currentQueueIndex - 1 + currentQueue.length) % currentQueue.length;
        const prev = currentQueue[currentQueueIndex];
        if (prev) prepareToPlay(prev.id, prev.title, prev.artist, prev.audio_url, prev.image_url);
    }

    function updatePlayIcons(playing) {
        const detailIcon = document.getElementById('detail-play-icon');
        if (playing) {
            playBtn.classList.replace('bi-play-circle-fill', 'bi-pause-circle-fill');
            playerThumb.classList.remove('spin-paused');
            if (detailIcon) detailIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
        } else {
            playBtn.classList.replace('bi-pause-circle-fill', 'bi-play-circle-fill');
            playerThumb.classList.add('spin-paused');
            if (detailIcon) detailIcon.classList.replace('bi-pause-fill', 'bi-play-fill');
        }
    }

    audio.addEventListener('timeupdate', () => {
        const current = audio.currentTime;
        const duration = audio.duration;
        if (duration) {
            progressBar.max = duration;
            progressBar.value = current;
            currentTimeEl.textContent = formatTime(current);
            totalTimeEl.textContent = formatTime(duration);
        }
        const lyricLines = document.querySelectorAll('.lyric-line');
        if (lyricLines.length > 0) {
            let activeIndex = -1;
            for (let i = 0; i < lyricLines.length; i++) {
                const t = parseFloat(lyricLines[i].dataset.time);
                if (t !== -1 && current >= t) activeIndex = i;
                else if (t !== -1 && current < t) break;
            }
            if (activeIndex !== -1) {
                const activeEl = document.getElementById(`lyric-${activeIndex}`);
                if (activeEl && !activeEl.classList.contains('active-lyric')) {
                    document.querySelectorAll('.active-lyric').forEach(el => el.classList.remove('active-lyric'));
                    document.querySelectorAll('.prev-lyric').forEach(el => el.classList.remove('prev-lyric'));
                    for (let p = Math.max(0, activeIndex - 2); p < activeIndex; p++) {
                        const prevEl = document.getElementById(`lyric-${p}`);
                        if (prevEl) prevEl.classList.add('prev-lyric');
                    }
                    activeEl.classList.add('active-lyric');
                    const container = document.getElementById('lyrics-scroll-box');
                    if (container) container.scrollTo({ top: activeEl.offsetTop - container.clientHeight / 2 + activeEl.clientHeight / 2, behavior: 'smooth' });
                }
            }
        }
    });

    progressBar.addEventListener('input', () => { audio.currentTime = progressBar.value; });
    volumeBar.addEventListener('input', () => { audio.volume = volumeBar.value / 100; });
    audio.addEventListener('ended', () => { playNext(); });

    function formatTime(s) {
        if (isNaN(s)) return '00:00';
        const m = Math.floor(s/60), sec = Math.floor(s%60);
        return `${m<10?'0':''}${m}:${sec<10?'0':''}${sec}`;
    }

    function formatDuration(val) {
        if (!val) return '--:--';
        if (typeof val === 'string' && val.includes(':')) return val;
        const secs = parseInt(val, 10);
        if (isNaN(secs) || secs <= 0) return '--:--';
        const m = Math.floor(secs / 60), s = secs % 60;
        return `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
    }
    </script>
</body>
</html>