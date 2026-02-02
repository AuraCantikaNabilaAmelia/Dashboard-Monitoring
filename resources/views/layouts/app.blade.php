<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'BPKP Dashboard') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ['selector', '[data-theme="dark"]'],
        }
        
        // Forced dark default logic
        const savedTheme = localStorage.getItem('theme');
        const themeToApply = savedTheme || 'dark';
        document.documentElement.setAttribute('data-theme', themeToApply);
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        :root {
            --bpkp-navy: #1e293b;
            --bpkp-blue: #3b82f6;
            --bpkp-dark-glass: rgba(15, 23, 42, 0.8);
            --bpkp-text-light: #f1f5f9;
            --bpkp-text-muted: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        html {
            font-size: 14px;
        }

        @media (min-width: 768px) {
            html {
                font-size: 15px;
            }
        }

        @media (min-width: 1280px) {
            html {
                font-size: 16px;
            }
        }

        /* Custom Scrollbar Premium */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.2);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.5);
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.4);
        }

        /* Firefox Support */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.2) transparent;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .webkit-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617;
            color: var(--bpkp-text-light);
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .background-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: moveBlob 20s infinite alternate;
        }

        .blob-1 {
            width: 40vw;
            height: 40vw;
            background: #4338ca;
            top: -10%;
            left: -10%;
            animation-duration: 25s;
        }

        .blob-2 {
            width: 35vw;
            height: 35vw;
            background: #2563eb;
            bottom: -10%;
            right: -10%;
            animation-duration: 30s;
            animation-delay: -5s;
        }

        @keyframes moveBlob {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(50px, 50px) scale(1.05); }
        }

        .glass {
            background: var(--bpkp-dark-glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
        }

        .theme-toggle-wrapper .toggle-switch {
            position: relative;
            display: inline-block;
            width: 80px;
            height: 40px;
            transform: scale(0.8);
            transition: transform 0.2s;
        }

        .theme-toggle-wrapper .toggle-switch:hover {
            transform: scale(0.9);
        }

        .theme-toggle-wrapper .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .theme-toggle-wrapper .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(145deg, #f1c40f, #f39c12);
            transition: 0.4s;
            border-radius: 34px;
            box-shadow: 0 0 15px rgba(241, 196, 15, 0.5);
        }

        .theme-toggle-wrapper .slider:before {
            position: absolute;
            content: "☀️";
            height: 32px;
            width: 32px;
            left: 4px;
            bottom: 4px;
            background: white;
            transition: 0.4s;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .theme-toggle-wrapper input:checked + .slider {
            background: linear-gradient(145deg, #2c3e50, #34495e);
            box-shadow: 0 0 15px rgba(44, 62, 80, 0.5);
        }

        .theme-toggle-wrapper input:checked + .slider:before {
            transform: translateX(40px);
            content: "🌙";
        }

        .theme-toggle-wrapper .clouds {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .theme-toggle-wrapper .cloud {
            position: absolute;
            width: 20px;
            height: 20px;
            fill: rgba(255, 255, 255, 0.8);
            transition: all 0.4s ease;
            filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.1));
        }

        .theme-toggle-wrapper .cloud1 {
            top: 10px;
            left: 10px;
            animation: floatCloud1 8s infinite linear;
        }

        .theme-toggle-wrapper .cloud2 {
            top: 15px;
            left: 40px;
            transform: scale(0.8);
            animation: floatCloud2 12s infinite linear;
        }

        @keyframes floatCloud1 {
            0% { transform: translateX(-20px); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { transform: translateX(80px); opacity: 0; }
        }

        @keyframes floatCloud2 {
            0% { transform: translateX(-20px) scale(0.8); opacity: 0; }
            20% { opacity: 0.7; }
            80% { opacity: 0.7; }
            100% { transform: translateX(80px) scale(0.8); opacity: 0; }
        }

        .theme-toggle-wrapper input:checked + .slider .cloud {
            opacity: 0;
            transform: translateY(-20px);
        }

        [data-theme="light"] {
            --bpkp-dark-glass: rgba(255, 255, 255, 0.98);
            --bpkp-text-light: #0f172a;
            --bpkp-text-muted: #475569;
            --glass-border: rgba(0, 0, 0, 0.15);
        }
        
        [data-theme="light"] body {
            background-color: #f8fafc;
        }

        [data-theme="light"] .glass {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #0f172a;
        }

        [data-theme="light"] .sidebar-link {
            color: #64748b;
        }
        
        [data-theme="light"] .sidebar-link:hover:not(.active) {
            background: rgba(0, 0, 0, 0.02);
        }

        [data-theme="light"] .blob {
            opacity: 0.1;
        }

        [data-theme="light"] .text-slate-500,
        [data-theme="light"] .text-slate-400,
        [data-theme="light"] .text-slate-300 {
            color: #1e293b !important;
            font-weight: 500;
        }

        [data-theme="light"] .glass {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
        }

        [data-theme="light"] ::placeholder {
            color: #64748b !important;
            opacity: 1;
        }

        .sidebar-link.active {
            background: rgba(59, 130, 246, 0.2);
            border-left: 4px solid var(--bpkp-blue);
            color: var(--bpkp-blue);
        }

        [data-theme="dark"] .sidebar-link.active {
            color: white;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            min-width: 240px;
            z-index: 9999;
            margin-top: 12px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 8px;
        }

        [data-theme="dark"] .dropdown-content {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        .dropdown.active .dropdown-content {
            display: block;
        }

        @keyframes dropdownFade {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        main {
            position: relative;
        }

        aside {
            z-index: 50;
        }

        .modal-root {
            z-index: 100;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div x-data="{ sidebarOpen: false }" class="flex relative h-screen overflow-hidden">
        {{-- Overlay for mobile --}}
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-sm lg:hidden" 
             style="display: none;">
        </div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:sticky top-0 left-0 z-[70] w-64 h-full glass transform transition-transform duration-300 ease-in-out flex flex-col p-6 space-y-8">
            <div class="flex flex-col items-center space-y-4 px-2 mb-4 relative">
                <button @click="sidebarOpen = false" class="lg:hidden absolute -top-2 -right-2 p-2 text-slate-500 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>

                <img src="{{ asset('logo-bpkp.webp') }}" alt="Logo" class="w-24 h-auto drop-shadow-2xl">
                <div class="text-center">
                    <span class="font-bold text-xl tracking-tight block">BPKP Jabar</span>
                    <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Monitoring System</span>
                </div>
            </div>
            
            <nav class="flex-1 space-y-2 overflow-y-auto pr-2 custom-scrollbar">
                <a href="/dashboard" class="sidebar-link {{ Request::is('dashboard') && !Request::is('dashboard/*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Overview</span>
                </a>
                <a href="/dashboard/st" class="sidebar-link {{ Request::is('dashboard/st*') || Request::is('dashboard/detail*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span>Surat Tugas</span>
                </a>
                <a href="/dashboard/lhp" class="sidebar-link {{ Request::is('dashboard/lhp*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                    <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                    <span>LHP</span>
                </a>
                <div class="pt-4 pb-2 px-4">
                    <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Monitoring</span>
                </div>
                <a href="/dashboard/daily" class="sidebar-link {{ Request::is('dashboard/daily*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <span>Harian</span>
                </a>
                <a href="/dashboard/monthly" class="sidebar-link {{ Request::is('dashboard/monthly*') || Request::is('dashboard/employee*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Bulanan</span>
                </a>
                <a href="/dashboard/bidwas" class="sidebar-link {{ Request::is('dashboard/bidwas*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Bidang</span>
                </a>
                <a href="/dashboard/tindaklanjut" class="sidebar-link {{ Request::is('dashboard/tindaklanjut*') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                    <i data-lucide="mail-check" class="w-5 h-5"></i>
                    <span>Tindak Lanjut</span>
                </a>
            </nav>

            <div class="pt-6 border-t border-white/10 text-center">
                 <p class="text-[10px] text-slate-500 uppercase font-bold">&copy; {{ date('Y') }} BPKP JABAR</p>
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
                            <div class="text-right hidden sm:block">
                                <p class="text-xs md:text-sm font-bold">{{ Auth::user()->name }}</p>
                                <p class="text-[9px] md:text-[10px] text-slate-500 uppercase tracking-widest font-bold">{{ Auth::user()->jabatan ?? Auth::user()->role }}</p>
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

        // Sync toggle state with current theme
        const currentTheme = root.getAttribute('data-theme') || 'dark';
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
