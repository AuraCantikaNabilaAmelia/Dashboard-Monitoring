<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'BPKP Dashboard') }}</title>

    {{-- Terapkan tema & state sidebar SEBELUM render agar tidak ada flicker --}}
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        // Sidebar: set lebar yang benar sebelum Alpine load (hindari loncat)
        if (localStorage.getItem('sidebarExpanded') === 'true') {
            document.documentElement.classList.add('sidebar-expanded');
        }
        // Matikan animasi & serahkan kontrol ke Alpine setelah ia siap
        document.documentElement.classList.add('no-anim');
        const lepasNoAnim = () => requestAnimationFrame(
            () => document.documentElement.classList.remove('no-anim')
        );
        document.addEventListener('alpine:initialized', lepasNoAnim);
        window.addEventListener('load', lepasNoAnim); // fallback
    </script>

    {{-- Tailwind terkompilasi via Vite (bukan CDN) — menghilangkan FOUC --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        /* ── DESIGN TOKENS ─────────────────────────────── */
        :root {
            --bpkp-blue:       #3b82f6;
            --bpkp-blue-soft:  #eff6ff;
            --bpkp-blue-border:#bfdbfe;

            /* Light mode — lavender dashboard */
            --bg:              #edf0f7;
            --surface:         #ffffff;
            --surface-2:       #f5f7fc;
            --border:          rgba(0,0,0,0.06);
            --border-strong:   rgba(0,0,0,0.10);
            --text:            #1a1d2e;
            --text-muted:      #6b7280;
            --text-dim:        #9ca3af;
            --sidebar-bg:      #ffffff;
            --sidebar-border:  rgba(0,0,0,0.06);
            --header-bg:       transparent;
            --shadow-sm:       0 2px 8px rgba(0,0,0,.05);
            --shadow-md:       0 4px 20px rgba(0,0,0,.07);
            --shadow-lg:       0 8px 32px rgba(0,0,0,.10);
        }

        /* Dark mode overrides */
        [data-theme="dark"] {
            --bg:              #020617;
            --surface:         rgba(15, 23, 42, 0.80);
            --surface-2:       rgba(30, 41, 59, 0.60);
            --border:          rgba(255,255,255,.08);
            --border-strong:   rgba(255,255,255,.15);
            --text:            #f1f5f9;
            --text-muted:      #94a3b8;
            --text-dim:        #475569;
            --sidebar-bg:      rgba(15, 23, 42, 0.90);
            --sidebar-border:  rgba(255,255,255,.08);
            --header-bg:       rgba(15, 23, 42, 0.70);
            --shadow-sm:       0 1px 3px rgba(0,0,0,.3);
            --shadow-md:       0 4px 16px rgba(0,0,0,.4);
            --shadow-lg:       0 8px 32px rgba(0,0,0,.5);
        }

        /* ── BASE ──────────────────────────────────────── */
        html { font-size: 14px; }
        @media (min-width: 768px)  { html { font-size: 15px; } }
        @media (min-width: 1280px) { html { font-size: 16px; } }

        * { scrollbar-width: thin; scrollbar-color: var(--border-strong) transparent; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 20px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--bpkp-blue); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            transition: background-color .3s ease, color .3s ease;
        }

        /* ── BACKGROUND BLOBS (dark only) ─────────────── */
        .background-blobs {
            position: fixed; inset: 0;
            overflow: hidden; z-index: 0; pointer-events: none;
        }
        .blob {
            position: absolute; border-radius: 50%;
            filter: blur(80px); opacity: 0;
            animation: moveBlob 20s infinite alternate;
            transition: opacity .5s;
        }
        [data-theme="dark"] .blob { opacity: 0.35; }

        .blob-1 { width: 40vw; height: 40vw; background: #4338ca; top: -10%; left: -10%; animation-duration: 25s; }
        .blob-2 { width: 35vw; height: 35vw; background: #2563eb; bottom: -10%; right: -10%; animation-duration: 30s; animation-delay: -5s; }

        @keyframes moveBlob {
            0%   { transform: translate(0,0) scale(1); }
            33%  { transform: translate(30px,-50px) scale(1.1); }
            66%  { transform: translate(-20px,20px) scale(.9); }
            100% { transform: translate(50px,50px) scale(1.05); }
        }

        /* ── GLASS CARD ────────────────────────────────── */
        .glass {
            background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            transition: background .3s ease, border-color .3s ease, box-shadow .3s ease;
        }

        [data-theme="dark"] .glass {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* ── SIDEBAR ───────────────────────────────────── */
        aside {
            z-index: 50;
            border-radius: 0 !important;
        }

        /* Label visibility handled by Alpine x-show — no CSS override needed */

        .sidebar-link {
            color: #9ca3af;
            font-weight: 500;
            transition: all .2s ease;
            border-radius: 14px !important;
            margin: 2px 0;
        }
        .sidebar-link:hover:not(.active) {
            background: #f3f4f6;
            color: #374151;
        }
        .sidebar-link.active {
            background: #3b82f6;
            color: #ffffff !important;
            font-weight: 700;
            border-left: none !important;
            box-shadow: 0 4px 14px rgba(59,130,246,0.4);
        }
        .sidebar-link.active i { color: #ffffff !important; }

        [data-theme="dark"] .sidebar-link.active {
            background: #3b82f6;
            color: #ffffff !important;
        }

        /* sidebar brand text - hide name/subtitle, keep logo */
        aside .font-bold       { display: none; }
        aside .text-slate-500  { display: none !important; }
        aside .text-center div { display: none; }

        /* footer copyright */
        aside .border-t p { display: none; }
        aside .border-t   { border: none !important; padding: 0 !important; }

        /* ── HEADER / MAIN ─────────────────────────────── */
        main > header {
            background: transparent;
            border-bottom: none;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            margin: -1rem -1rem 2rem -1rem;
            padding: 1rem 1.5rem;
            border-radius: 0;
        }
        @media (min-width: 768px) {
            main > header {
                margin: -2rem -2rem 2rem -2rem;
                padding: 1.25rem 2rem;
            }
        }

        [data-theme="dark"] main > header {
            background: rgba(15,23,42,0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        /* header title color */
        main > header h1 { color: var(--text); font-size: 1.5rem; }
        main > header .text-slate-400 { color: var(--text-muted) !important; }

        /* header glass buttons → clean white pill */
        [data-theme="light"] header .glass {
            background: #ffffff !important;
            border: 1px solid rgba(0,0,0,0.07) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
            border-radius: 14px !important;
        }

        /* avatar pill */
        [data-theme="light"] .bg-blue-500\/20.rounded-full {
            background: #dbeafe !important;
        }

        /* header title + subtitle */
        main > header h1 { color: var(--text); }
        main > header .text-slate-400 { color: var(--text-muted) !important; }

        /* ── TYPOGRAPHY OVERRIDES ──────────────────────── */
        .text-slate-900, .text-slate-800 { color: var(--text) !important; }
        .text-slate-600, .text-slate-500, .text-slate-400 { color: var(--text-muted) !important; }
        .text-slate-300, .text-slate-200 { color: var(--text-dim) !important; }
        [data-theme="dark"] .text-slate-200,
        [data-theme="dark"] .text-slate-300 { color: #cbd5e1 !important; }
        [data-theme="dark"] .text-slate-400,
        [data-theme="dark"] .text-slate-500 { color: #94a3b8 !important; }
        [data-theme="dark"] .text-white { color: #f1f5f9 !important; }

        /* dark: keep bold headings bright */
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3 { color: #f1f5f9; }

        /* ── STAT / INFO CARDS ─────────────────────────── */
        /* Light: remove dark tint backgrounds */
        [data-theme="light"] .bg-white\/5,
        [data-theme="light"] .bg-white\/10 {
            background: var(--surface-2) !important;
        }
        [data-theme="light"] .bg-slate-50,
        [data-theme="light"] .dark\:bg-white\/5 {
            background: var(--surface-2) !important;
        }
        [data-theme="light"] .border-white\/5,
        [data-theme="light"] .border-white\/10 {
            border-color: var(--border) !important;
        }

        /* Coloured stat pill backgrounds — keep translucent */
        [data-theme="light"] .bg-blue-500\/20   { background: #dbeafe !important; }
        [data-theme="light"] .bg-emerald-500\/10 { background: #d1fae5 !important; }
        [data-theme="light"] .bg-purple-500\/20  { background: #ede9fe !important; }
        [data-theme="light"] .bg-amber-500\/10   { background: #fef9c3 !important; }
        [data-theme="light"] .bg-red-500\/10     { background: #fee2e2 !important; }
        [data-theme="light"] .bg-green-500\/20   { background: #d1fae5 !important; }

        /* coloured border accents */
        [data-theme="light"] .border-blue-500\/20  { border-color: #bfdbfe !important; }
        [data-theme="light"] .border-emerald-500\/20{ border-color: #a7f3d0 !important; }
        [data-theme="light"] .border-red-500\/20   { border-color: #fca5a5 !important; }

        /* row hover in light */
        [data-theme="light"] .hover\:bg-slate-100:hover { background: #f1f5f9 !important; }
        [data-theme="light"] .hover\:bg-white\/10:hover { background: #f1f5f9 !important; }

        /* ── CHART BACKGROUNDS ─────────────────────────── */
        [data-theme="light"] .bg-slate-50\/50 { background: #f8fafc !important; }
        [data-theme="light"] .bg-slate-950\/95 { background: rgba(255,255,255,.98) !important; }
        [data-theme="light"] .bg-white\/98     { background: rgba(255,255,255,.98) !important; }
        [data-theme="light"] .bg-white\/90     { background: rgba(255,255,255,.95) !important; }

        /* section divider labels */
        [data-theme="light"] .text-\[10px\].uppercase.text-slate-500 {
            color: #94a3b8 !important;
        }

        /* ── DROPDOWN ──────────────────────────────────── */
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content {
            display: none; position: absolute;
            right: 0; top: 100%;
            min-width: 240px; z-index: 9999; margin-top: 12px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            padding: 8px;
        }
        [data-theme="dark"] .dropdown-content {
            background: #1e293b;
            border-color: rgba(255,255,255,.1);
        }
        .dropdown.active .dropdown-content { display: block; }

        /* ── HEADER GLASS BUTTON ───────────────────────── */
        [data-theme="light"] header .glass {
            background: var(--surface) !important;
            border-color: var(--border) !important;
            box-shadow: var(--shadow-sm) !important;
            color: var(--text-muted);
        }
        [data-theme="light"] header .glass:hover {
            background: var(--surface-2) !important;
        }

        /* ── AVATAR ────────────────────────────────────── */
        [data-theme="light"] .bg-blue-500\/20.rounded-full {
            background: #dbeafe !important;
        }
        [data-theme="light"] .border-blue-500\/30 { border-color: #93c5fd !important; }
        [data-theme="light"] .text-blue-400 { color: #2563eb !important; }

        /* ── THEME TOGGLE ──────────────────────────────── */
        .theme-toggle-wrapper .toggle-switch {
            position: relative; display: inline-block;
            width: 80px; height: 40px;
            transform: scale(.8); transition: transform .2s;
        }
        .theme-toggle-wrapper .toggle-switch:hover { transform: scale(.9); }
        .theme-toggle-wrapper .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .theme-toggle-wrapper .slider {
            position: absolute; cursor: pointer; inset: 0;
            background: linear-gradient(145deg,#f1c40f,#f39c12);
            transition: .4s; border-radius: 34px;
            box-shadow: 0 0 15px rgba(241,196,15,.5);
        }
        .theme-toggle-wrapper .slider:before {
            position: absolute; content: "☀️";
            height: 32px; width: 32px; left: 4px; bottom: 4px;
            background: white; transition: .4s; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; box-shadow: 0 0 10px rgba(0,0,0,.1); z-index: 2;
        }
        .theme-toggle-wrapper input:checked + .slider {
            background: linear-gradient(145deg,#2c3e50,#34495e);
            box-shadow: 0 0 15px rgba(44,62,80,.5);
        }
        .theme-toggle-wrapper input:checked + .slider:before {
            transform: translateX(40px); content: "🌙";
        }
        .theme-toggle-wrapper .clouds { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
        .theme-toggle-wrapper .cloud {
            position: absolute; width: 20px; height: 20px;
            fill: rgba(255,255,255,.8); transition: all .4s;
        }
        .theme-toggle-wrapper .cloud1 { top:10px; left:10px; animation: floatCloud1 8s infinite linear; }
        .theme-toggle-wrapper .cloud2 { top:15px; left:40px; transform:scale(.8); animation: floatCloud2 12s infinite linear; }
        @keyframes floatCloud1 {
            0%   { transform: translateX(-20px); opacity: 0; }
            20%  { opacity: 1; }
            80%  { opacity: 1; }
            100% { transform: translateX(80px); opacity: 0; }
        }
        @keyframes floatCloud2 {
            0%   { transform: translateX(-20px) scale(.8); opacity: 0; }
            20%  { opacity: .7; }
            80%  { opacity: .7; }
            100% { transform: translateX(80px) scale(.8); opacity: 0; }
        }
        .theme-toggle-wrapper input:checked + .slider .cloud { opacity: 0; transform: translateY(-20px); }

        /* ── MISC ──────────────────────────────────────── */
        main { position: relative; }
        .modal-root { z-index: 100; }

        /* modal overlay in light */
        [data-theme="light"] #chartModal,
        [data-theme="light"] #detailsModal {
            background: rgba(248,250,252,.97) !important;
        }
        [data-theme="light"] #chartModal .glass,
        [data-theme="light"] #detailsModal .glass {
            background: #ffffff !important;
            border-color: var(--border) !important;
        }

        /* ── PLACEHOLDER ───────────────────────────────── */
        [data-theme="light"] ::placeholder { color: #94a3b8 !important; opacity: 1; }

        /* ── INPUT / FORM FIELDS ───────────────────────── */
        [data-theme="light"] input,
        [data-theme="light"] select,
        [data-theme="light"] textarea {
            background: #ffffff !important;
            color: var(--text) !important;
            border-color: var(--border) !important;
        }

        /* ── SIDEBAR TOOLTIP ───────────────────────────── */
        .nav-item-wrap { position: relative; }

        .sidebar-tooltip {
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%) translateX(-6px);
            background: #1e293b;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 10px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity .18s ease, transform .18s ease;
            z-index: 9999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        .sidebar-tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1e293b;
        }
        /* Show tooltip on hover ONLY when sidebar is collapsed */
        aside:not(.is-expanded) .nav-item-wrap:hover .sidebar-tooltip {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }
        /* Hide tooltip when expanded */
        aside.is-expanded .sidebar-tooltip { display: none; }

        /* ── Teks di bawah logo (hanya saat sidebar terbuka) ── */
        .kop-text { display: none; text-align: center; margin-top: 8px; }
        aside.is-expanded .kop-text { display: block; }
        .kop-text .kop-line { display: block; font-weight: 700; font-size: 13px; line-height: 1.25; color: var(--text); }
        .kop-text .kop-sub { display: block; margin-top: 3px; font-size: 9px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #94a3b8; }

        /* ── TRANSITIONS ───────────────────────────────── */
        .glass, body, .sidebar-link, main > header {
            transition: background .3s ease, border-color .3s ease,
                        color .3s ease, box-shadow .3s ease;
        }
        aside { transition: width .3s cubic-bezier(.4,0,.2,1) !important; }

        /* ── ANTI-FLICKER (FOUC) sidebar Alpine ──────────── */
        /* Sembunyikan elemen Alpine sampai komponen siap dievaluasi */
        [x-cloak] { display: none !important; }
        /* Set lebar sidebar SEBELUM Alpine load (cocokkan dgn localStorage) */
        html:not(.sidebar-expanded) aside { width: 5rem; }   /* w-20 */
        html.sidebar-expanded aside       { width: 14rem; }  /* w-56 */
        /* Jangan animasikan lebar pada paint pertama */
        html.no-anim aside { transition: none !important; }
        /* Visibilitas label sidebar sebelum Alpine ambil alih (cocok dgn state tersimpan) */
        html.no-anim:not(.sidebar-expanded) [data-sb-show] { display: none; }
        html.no-anim.sidebar-expanded [data-sb-hide]       { display: none; }
        /* Ukuran logo & alignment link sebelum Alpine */
        html.no-anim:not(.sidebar-expanded) [data-sb-logo] { width: 3rem; }   /* w-12 */
        html.no-anim.sidebar-expanded       [data-sb-logo] { width: 5rem; }   /* w-20 */
        html.no-anim:not(.sidebar-expanded) .sidebar-link  { justify-content: center; }
        html.no-anim.sidebar-expanded       .sidebar-link  { gap: 0.75rem; }
    </style>
</head>
<body class="min-h-screen">
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true' }"
         x-init="$watch('sidebarExpanded', v => localStorage.setItem('sidebarExpanded', v))"
         class="flex relative h-screen overflow-hidden">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[90] bg-slate-900/60 backdrop-blur-sm lg:hidden"
             style="display: none;">
        </div>

        <aside :class="[
                   sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                   sidebarExpanded ? 'w-56 is-expanded' : 'w-20'
               ]"
               class="fixed lg:sticky top-0 left-0 z-[95] h-full glass flex flex-col py-5 items-center transition-all duration-300 ease-in-out overflow-hidden">

            {{-- Logo + toggle --}}
            <div class="flex flex-col items-center w-full px-3 pb-5 mb-2 relative" style="border-bottom: 1px solid rgba(0,0,0,0.06);">
                <button @click="sidebarOpen = false" class="lg:hidden absolute top-0 right-1 p-1 text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
                <img src="{{ asset('logo-bpkp.webp') }}" alt="Logo" data-sb-logo
                     :class="sidebarExpanded ? 'w-28' : 'w-16'"
                     class="h-auto drop-shadow-md transition-all duration-300">
                <div class="kop-text">
                    <span class="kop-line">Perwakilan BPKP</span>
                    <span class="kop-line">Provinsi Jawa Barat</span>
                    <span class="kop-sub">Dashboard System</span>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 flex flex-col space-y-1 w-full px-2 mt-4">

                {{-- Nav item helper macro --}}
                @php
                $navItems = [
                    ['href'=>'/dashboard','active'=>Request::is('dashboard')&&!Request::is('dashboard/*'),'icon'=>'layout-dashboard','label'=>'Overview'],
                    ['href'=>'/dashboard/st','active'=>Request::is('dashboard/st*')||Request::is('dashboard/detail*'),'icon'=>'file-text','label'=>'Surat Tugas'],
                    ['href'=>'/dashboard/lhp','active'=>Request::is('dashboard/lhp*'),'icon'=>'clipboard-check','label'=>'LHP'],
                ];
                $monitoringItems = [
                    ['href'=>'/dashboard/daily','active'=>Request::is('dashboard/daily*'),'icon'=>'calendar','label'=>'Harian'],
                    ['href'=>'/dashboard/monthly','active'=>Request::is('dashboard/monthly*')||Request::is('dashboard/employee*'),'icon'=>'bar-chart-3','label'=>'Bulanan'],
                    ['href'=>'/dashboard/bidwas','active'=>Request::is('dashboard/bidwas*'),'icon'=>'users','label'=>'Bidang'],
                    ['href'=>'/dashboard/tindaklanjut','active'=>Request::is('dashboard/tindaklanjut*'),'icon'=>'mail-check','label'=>'Tindak Lanjut'],
                ];
                @endphp

                @foreach($navItems as $item)
                <div class="nav-item-wrap">
                    <a href="{{ $item['href'] }}"
                       class="sidebar-link {{ $item['active'] ? 'active' : '' }} flex items-center px-3 py-2.5 rounded-xl transition-all"
                       :class="sidebarExpanded ? 'space-x-3' : 'justify-center'">
                        <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 flex-shrink-0"></i>
                        <span x-show="sidebarExpanded" data-sb-show
                              x-transition:enter="transition ease-out duration-150"
                              x-transition:enter-start="opacity-0"
                              x-transition:enter-end="opacity-100"
                              class="text-sm font-medium whitespace-nowrap">{{ $item['label'] }}</span>
                    </a>
                    <div class="sidebar-tooltip">{{ $item['label'] }}</div>
                </div>
                @endforeach

                {{-- Monitoring section --}}
                <div class="pt-3 pb-1 px-2" x-show="sidebarExpanded" data-sb-show
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100">
                    <span class="text-[9px] uppercase tracking-widest font-bold text-slate-400">Monitoring</span>
                </div>
                <div x-show="!sidebarExpanded" data-sb-hide class="my-1 mx-2 border-t border-slate-200 dark:border-white/10"></div>

                @foreach($monitoringItems as $item)
                <div class="nav-item-wrap">
                    <a href="{{ $item['href'] }}"
                       class="sidebar-link {{ $item['active'] ? 'active' : '' }} flex items-center px-3 py-2.5 rounded-xl transition-all"
                       :class="sidebarExpanded ? 'space-x-3' : 'justify-center'">
                        <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 flex-shrink-0"></i>
                        <span x-show="sidebarExpanded" data-sb-show
                              x-transition:enter="transition ease-out duration-150"
                              x-transition:enter-start="opacity-0"
                              x-transition:enter-end="opacity-100"
                              class="text-sm font-medium whitespace-nowrap">{{ $item['label'] }}</span>
                    </a>
                    <div class="sidebar-tooltip">{{ $item['label'] }}</div>
                </div>
                @endforeach
            </nav>

            {{-- Toggle expand/collapse button --}}
            <div class="mt-4 w-full px-2">
                <button @click="sidebarExpanded = !sidebarExpanded"
                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-slate-400 hover:text-slate-600 transition-all"
                        style="background: rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.06);">
                    <i :data-lucide="sidebarExpanded ? 'panel-left-close' : 'panel-left-open'" class="w-4 h-4 flex-shrink-0"></i>
                    <span x-show="sidebarExpanded" data-sb-show
                          x-transition:enter="transition ease-out duration-150"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          class="text-xs font-medium whitespace-nowrap">Sembunyikan</span>
                </button>
            </div>

            <div class="mt-3 px-2 w-full text-center" x-show="sidebarExpanded">
                <p class="text-[9px] uppercase font-bold text-slate-400">&copy; {{ date('Y') }} BPKP JABAR</p>
            </div>
        </aside>

        <main class="flex-1 h-screen overflow-y-auto p-4 md:p-8 relative">
            <header class="flex justify-between items-center mb-8 relative z-[60]">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden glass p-2.5 rounded-xl text-slate-400 hover:text-white transition-all">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold">@yield('title', 'Dashboard')</h1>
                        <p class="text-slate-400 text-xs md:text-sm">Welcome back, {{ Auth::user()->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 md:space-x-6">
                    <div class="dropdown" id="accountDropdown">
                        <button class="flex items-center space-x-3 glass px-3 py-1.5 md:px-4 md:py-2 rounded-2xl hover:bg-white/5 transition-all outline-none" onclick="toggleDropdown(event)">
                            @php
                                $bidangUser = null;
                                if (Auth::user()->bidang_id) {
                                    $bidangUser = \Illuminate\Support\Facades\DB::table('r_bidwas')
                                        ->where('id_bidwas', Auth::user()->bidang_id)
                                        ->value('nm_bidwas');
                                } elseif (Auth::user()->nip) {
                                    $bidangUser = \Illuminate\Support\Facades\DB::table('r_pegawai')
                                        ->join('r_bidwas', 'r_pegawai.id_bidwas', '=', 'r_bidwas.id_bidwas')
                                        ->where('r_pegawai.nip', Auth::user()->nip)
                                        ->value('r_bidwas.nm_bidwas');
                                }
                                // Singkat hanya untuk nama yang sangat panjang (Koordinator Pengawasan...)
                                $bidangShort = null;
                                if ($bidangUser) {
                                    if (str_contains($bidangUser, 'Instansi Pemerintah Pusat'))  $bidangShort = 'IPP';
                                    elseif (str_contains($bidangUser, 'Pemerintah Daerah'))        $bidangShort = 'APD';
                                    elseif (str_contains($bidangUser, 'Akuntan Negara'))            $bidangShort = 'Akuntan Negara';
                                    elseif (str_contains($bidangUser, 'Program dan Pelaporan'))     $bidangShort = 'P3A';
                                    elseif (str_contains($bidangUser, 'Investigasi'))               $bidangShort = 'Investigasi';
                                    elseif (str_contains($bidangUser, 'Sub Bagian Kepegawaian'))    $bidangShort = 'Sub Bag. Kepegawaian';
                                    elseif (str_contains($bidangUser, 'Sub Bagian Keuangan'))       $bidangShort = 'Sub Bag. Keuangan';
                                    elseif (str_contains($bidangUser, 'Sub Bagian Umum'))           $bidangShort = 'Sub Bag. Umum';
                                    elseif (str_contains($bidangUser, 'Tata Usaha'))                $bidangShort = 'Bag. Tata Usaha';
                                    else $bidangShort = $bidangUser; // Investigasi, Tata Usaha, Keuangan, Umum, dll → tampil apa adanya
                                }
                            @endphp
                            @php
                                // Label jabatan rapi untuk role tanpa divisi
                                $jabatanLabels = [
                                    'kepala_perwakilan'        => 'Kepala Perwakilan',
                                    'kepala_bagian_umum'       => 'Kepala Bagian Umum',
                                    'subkoor_keuangan'         => 'Subkoordinator Keuangan',
                                    'subkoor_bmn_rt_kearsipan' => 'Subkoordinator BMN, RT & Kearsipan',
                                ];
                                $jabatanRaw   = Auth::user()->jabatan;
                                $labelJabatan = $jabatanLabels[$jabatanRaw] ?? ($jabatanRaw ?: Auth::user()->role);
                            @endphp
                            <div class="text-right hidden sm:block">
                                <p class="text-xs md:text-sm font-bold">{{ Auth::user()->name }}</p>
                                @if($bidangShort)
                                <p class="text-[9px] md:text-[10px] text-blue-400 tracking-wide font-semibold truncate max-w-[150px]" title="{{ $bidangUser }}">{{ $bidangShort }}</p>
                                @else
                                <p class="text-[9px] md:text-[10px] text-blue-400 tracking-wide font-semibold truncate max-w-[150px]" title="{{ $labelJabatan }}">{{ $labelJabatan }}</p>
                                @endif
                            </div>
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-500/20 rounded-full flex items-center justify-center text-blue-400 font-bold border border-blue-500/30 text-xs md:text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <i data-lucide="chevron-down" class="w-3 h-3 md:w-4 md:h-4 text-slate-500 transition-transform duration-200" id="dropdownIcon"></i>
                        </button>

                        <div class="dropdown-content">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-white/10 mb-2">
                                <p class="text-[10px] text-slate-500 uppercase font-black mb-1">Signed in as</p>
                                <p class="text-sm font-bold truncate text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</p>
                            </div>
                            <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-all text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                <span class="font-bold">Profile Setting</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all text-sm text-left font-bold transition-all">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="theme-toggle-wrapper hidden sm:block">
                        <label class="toggle-switch">
                            <input type="checkbox" id="theme-toggle-dashboard">
                            <span class="slider">
                                <div class="clouds">
                                    <svg viewBox="0 0 100 100" class="cloud cloud1">
                                      <path d="M30,45 Q35,25 50,25 Q65,25 70,45 Q80,45 85,50 Q90,55 85,60 Q80,65 75,60 Q65,60 60,65 Q55,70 50,65 Q45,70 40,65 Q35,60 25,60 Q20,65 15,60 Q10,55 15,50 Q20,45 30,45"></path>
                                    </svg>
                                    <svg viewBox="0 0 100 100" class="cloud cloud2">
                                      <path d="M30,45 Q35,25 50,25 Q65,25 70,45 Q80,45 85,50 Q90,55 85,60 Q80,65 75,60 Q65,60 60,65 Q55,70 50,65 Q45,70 40,65 Q35,60 25,60 Q20,65 15,60 Q10,55 15,50 Q20,45 30,45"></path>
                                    </svg>
                                </div>
                            </span>
                        </label>
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    @stack('modals')

    <script>
        lucide.createIcons();

        // Re-create icons after sidebar toggle (for panel icon swap)
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {});
        });
        document.addEventListener('click', (e) => {
            if (e.target.closest('[\\@click*="sidebarExpanded"]')) {
                setTimeout(() => lucide.createIcons(), 320);
            }
        });

        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('accountDropdown');
            const icon = document.getElementById('dropdownIcon');

            document.querySelectorAll('.status-filter-dropdown input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            dropdown.classList.toggle('active');

            if (dropdown.classList.contains('active')) {
                icon.style.transform = 'rotate(180deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('accountDropdown');
            const icon = document.getElementById('dropdownIcon');
            if (dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
                if(icon) icon.style.transform = 'rotate(0deg)';
            }
        });

        const themeToggle = document.getElementById('theme-toggle-dashboard');
        const root = document.documentElement;

        const currentTheme = root.getAttribute('data-theme') || 'light';
        if(themeToggle) themeToggle.checked = currentTheme === 'dark';

        if(themeToggle) {
            themeToggle.addEventListener('change', function() {
                const newTheme = this.checked ? 'dark' : 'light';
                root.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
            });
        }

        document.addEventListener('alpine:init', () => {
            Alpine.store('notify', {
                show: false,
                message: '',
                type: 'success',
                toast(message, type = 'success') {
                    this.message = message;
                    this.type = type;
                    this.show = true;
                    setTimeout(() => { this.show = false; }, 3000);
                }
            });

            Alpine.store('confirm', {
                show: false,
                title: 'Konfirmasi',
                message: 'Apakah Anda yakin?',
                confirmText: 'Ya, Lanjutkan',
                cancelText: 'Batal',
                onConfirm: null,

                ask(message, onConfirm, title = 'Konfirmasi', confirmText = 'Ya, Lanjutkan') {
                    this.title = title;
                    this.message = message;
                    this.onConfirm = onConfirm;
                    this.confirmText = confirmText;
                    this.show = true;
                },

                execute() {
                    if (this.onConfirm) this.onConfirm();
                    this.show = false;
                }
            });
        });
    </script>

    <div x-data x-show="$store.notify.show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         class="fixed bottom-8 right-8 z-[9999]"
         style="display: none;">
        <div :class="{
            'bg-green-500': $store.notify.type === 'success',
            'bg-red-500': $store.notify.type === 'error',
            'bg-blue-500': $store.notify.type === 'info'
        }" class="px-6 py-4 rounded-2xl shadow-2xl text-white font-bold flex items-center gap-3 backdrop-blur-md bg-opacity-90 min-w-[300px]">
            <template x-if="$store.notify.type === 'success'"><i data-lucide="check-circle" class="w-6 h-6"></i></template>
            <template x-if="$store.notify.type === 'error'"><i data-lucide="alert-circle" class="w-6 h-6"></i></template>
            <template x-if="$store.notify.type === 'info'"><i data-lucide="info" class="w-6 h-6"></i></template>
            <span x-text="$store.notify.message"></span>
        </div>
    </div>

    <div x-data x-show="$store.confirm.show"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div @click.away="$store.confirm.show = false"
             class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl max-w-2xl w-full p-12 text-center"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <div class="w-24 h-24 bg-amber-100 dark:bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-8 text-amber-500">
                <i data-lucide="help-circle" class="w-12 h-12"></i>
            </div>

            <h3 class="text-3xl font-black mb-4 dark:text-white text-slate-900" x-text="$store.confirm.title"></h3>
            <p class="text-lg text-slate-500 dark:text-slate-400 mb-10 px-4" x-text="$store.confirm.message"></p>

            <div class="flex gap-6">
                <button @click="$store.confirm.show = false"
                        class="flex-1 px-8 py-5 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-[1.5rem] font-bold text-lg transition-all text-slate-700 dark:text-slate-300"
                        x-text="$store.confirm.cancelText"></button>
                <button @click="$store.confirm.execute()"
                        class="flex-1 px-8 py-5 bg-blue-500 hover:bg-blue-600 text-white rounded-[1.5rem] font-bold text-lg transition-all shadow-xl shadow-blue-500/30"
                        x-text="$store.confirm.confirmText"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                setTimeout(() => {
                    Alpine.store('notify').toast("{{ session('success') }}", 'success');
                    lucide.createIcons();
                }, 500);
            @endif
            @if(session('error'))
                setTimeout(() => {
                    Alpine.store('notify').toast("{{ session('error') }}", 'error');
                    lucide.createIcons();
                }, 500);
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
