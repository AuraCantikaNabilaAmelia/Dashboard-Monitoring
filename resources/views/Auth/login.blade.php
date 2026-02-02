<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BPKP Jawa Barat</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <script>
        // Forced dark default logic
        const savedTheme = localStorage.getItem('theme');
        const themeToApply = savedTheme || 'dark';
        document.documentElement.setAttribute('data-bs-theme', themeToApply);
    </script>
    
    <style>
        :root {
            --bpkp-navy: #1e293b;
            --bpkp-blue: #3b82f6;
            --bpkp-dark-glass: rgba(15, 23, 42, 0.8);
            --bpkp-text-light: #f1f5f9;
            --bpkp-text-muted: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 20px;
            overflow-x: hidden;
            overflow-y: auto;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            position: relative;
            color: #1e293b;
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
            opacity: 0.6;
            animation: moveBlob 20s infinite alternate;
        }

        .blob-1 {
            width: 50vw;
            height: 50vw;
            background: #6366f1;
            top: -10%;
            left: -10%;
            animation-duration: 25s;
        }

        .blob-2 {
            width: 40vw;
            height: 40vw;
            background: #3b82f6;
            bottom: -10%;
            right: -10%;
            animation-duration: 30s;
            animation-delay: -5s;
        }

        .blob-3 {
            width: 30vw;
            height: 30vw;
            background: #d946ef;
            bottom: 20%;
            left: 20%;
            animation-duration: 22s;
            animation-delay: -10s;
            opacity: 0.5;
        }

        @keyframes moveBlob {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(50px, 50px) scale(1.05); }
        }

        [data-bs-theme="dark"] body {
             background-color: #020617;
             color: #f1f5f9;
        }

        [data-bs-theme="dark"] .blob {
            opacity: 0.5;
        }

        [data-bs-theme="dark"] .blob-1 {
            background: #4338ca;
            filter: blur(100px);
        }
        
        [data-bs-theme="dark"] .blob-2 {
            background: #2563eb;
            filter: blur(100px);
        }
        
        [data-bs-theme="dark"] .blob-3 {
            background: #06b6d4;
            filter: blur(100px);
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            width: 900px; 
            max-width: 100%;
            background: var(--bpkp-dark-glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); 
            min-height: 550px; 
            margin: auto;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .login-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.6);
        }

        .login-sidebar {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            border-right: 1px solid var(--glass-border);
            color: var(--bpkp-text-light);
            background: rgba(255, 255, 255, 0.03);
        }

        .brand-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 10% 10%, rgba(59, 130, 246, 0.15), transparent 60%);
            pointer-events: none;
        }

        .brand-logo {
            width: 150px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
        }

        .login-title {
            font-weight: 800;
            font-size: 3rem;
            line-height: 1.1;
            margin-bottom: 20px;
            color: var(--bpkp-text-light);
        }

        .login-subtitle {
            font-size: 0.95rem;
            color: var(--bpkp-text-muted);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .login-form-container {
            flex: 0.9;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: transparent;
        }

        .wave-group {
            position: relative;
            margin-bottom: 35px; 
        }

        .wave-group .input {
            font-size: 16px;
            padding: 10px 10px 10px 5px;
            display: block;
            width: 100%; 
            border: none;
            border-bottom: 1px solid #475569;
            background: transparent;
            color: var(--bpkp-text-light);
            font-weight: 500;
        }

        .wave-group .input:focus {
            outline: none;
            border-bottom-color: var(--bpkp-blue);
        }

        .wave-group .label {
            color: var(--bpkp-text-muted);
            font-size: 16px;
            font-weight: normal;
            position: absolute;
            pointer-events: none;
            left: 5px;
            top: 10px;
            display: flex;
        }

        .wave-group .label-char {
            transition: 0.2s ease all;
            transition-delay: calc(var(--index) * .05s);
        }

        .wave-group .input:focus ~ label .label-char,
        .wave-group .input:valid ~ label .label-char {
            transform: translateY(-25px);
            font-size: 12px;
            color: var(--bpkp-blue); 
            font-weight: 700;
        }

        .wave-group .bar {
            position: relative;
            display: block;
            width: 100%;
        }

        .wave-group .bar:before,.wave-group .bar:after {
            content: '';
            height: 2px;
            width: 0;
            bottom: 1px;
            position: absolute;
            background: var(--bpkp-blue);
            transition: 0.2s ease all;
        }

        .wave-group .bar:before { left: 50%; }
        .wave-group .bar:after { right: 50%; }

        .wave-group .input:focus ~ .bar:before,
        .wave-group .input:focus ~ .bar:after {
            width: 50%;
        }

        .btn-animated {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            height: 56px; 
            padding: 0 20px;
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.3);
            background: transparent;
            border-radius: 16px; 
            font-size: 1.1rem; 
            font-weight: 600; 
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s ease-in;
            z-index: 1;
            margin-top: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-animated::before {
            content: "";
            position: absolute;
            left: 50%;
            transform: translateX(-50%) scaleY(1) scaleX(1.25);
            top: 100%;
            width: 140%;
            height: 180%;
            background-color: rgba(59, 130, 246, 0.2);
            border-radius: 50%;
            display: block;
            transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
            z-index: -1;
        }

        .btn-animated::after {
            content: "";
            position: absolute;
            left: 55%;
            transform: translateX(-50%) scaleY(1) scaleX(1.45);
            top: 180%;
            width: 160%;
            height: 190%;
            background-color: var(--bpkp-blue);
            border-radius: 50%;
            display: block;
            transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
            z-index: -1;
        }

        .btn-animated:hover {
            color: #ffffff;
            border: 1px solid var(--bpkp-blue);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.5);
        }

        .btn-animated:hover::before {
            top: -35%;
            background-color: var(--bpkp-blue);
            transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
        }

        .btn-animated:hover::after {
            top: -45%;
            background-color: var(--bpkp-blue);
            transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
        }

        .btn-animated .icon-container,
        .btn-animated .text {
            position: relative; 
            z-index: 2;
        }
        
        .btn-animated .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-animated svg {
            width: 24px;
            height: 24px;
            fill: #ffffff;
            transition: transform 0.3s ease;
        }

        .btn-animated:hover svg {
            transform: translateX(4px);
        }

        .theme-toggle-wrapper {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: 0.6s ease-in-out;
            font-family: inherit;
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
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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

        .feature-list {
            display: flex;
            gap: 15px;
            color: var(--bpkp-text-muted);
            font-size: 0.85rem;
            margin-top: auto;
        }

        .feature-item { display: flex; align-items: center; gap: 6px; }
        .feature-item i { color: var(--bpkp-blue); }

        .text-dark { color: var(--bpkp-text-light) !important; }
        .text-muted { color: var(--bpkp-text-muted) !important; }

        @media (max-width: 900px) {
            body { padding: 10px; align-items: flex-start; }
            .login-wrapper { flex-direction: column; width: 100%; min-height: auto; margin: 20px 0; }
            .login-sidebar, .login-form-container { padding: 30px; flex: none; }
            .login-title { font-size: 2rem; }
        }

        .custom-checkbox-container {
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .custom-checkbox-container input { display: none; }
        .custom-checkbox-container svg { overflow: visible; }
        .path {
            fill: none;
            stroke: var(--bpkp-text-muted);
            stroke-width: 6;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke-dasharray 0.5s ease, stroke-dashoffset 0.5s ease, stroke 0.3s;
            stroke-dasharray: 241 9999999;
            stroke-dashoffset: 0;
        }
        .custom-checkbox-container input:checked ~ svg .path {
            stroke-dasharray: 70.5096664428711 9999999;
            stroke-dashoffset: -262.2723388671875;
            stroke: var(--bpkp-blue);
        }

        [data-bs-theme="dark"] body {
            background: linear-gradient(-45deg, #020617, #1e1b4b, #172554, #0f172a);
            background-size: 400% 400%;
        }
    </style>
</head>
<body>
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    
    <div class="theme-toggle-wrapper">
        <label class="toggle-switch">
          <input type="checkbox" id="theme-checkbox" />
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeCheckbox = document.getElementById('theme-checkbox');
            const htmlElement = document.documentElement;

            // Sync toggle state with current theme
            const currentTheme = htmlElement.getAttribute('data-bs-theme') || 'dark';
            if(themeCheckbox) themeCheckbox.checked = currentTheme === 'dark';

            themeCheckbox.addEventListener('change', function() {
                const newTheme = this.checked ? 'dark' : 'light';
                htmlElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        });
    </script>

    <div class="login-wrapper" id="loginCard">
        <div class="login-sidebar">
            <div class="brand-glow"></div>
            <div class="login-content">
                <h1 class="login-title">Welcome To<br>Dashboard Monitoring</h1>
                <p class="login-subtitle">BPKP Perwakilan Provinsi Jawa Barat.<br>Terintegrasi, Real-time, Profesional.</p>
                
                <div class="feature-list">
                    <div class="feature-item"><i class="bi bi-check-circle-fill"></i> Secure</div>
                    <div class="feature-item"><i class="bi bi-lightning-charge-fill"></i> Fast</div>
                    <div class="feature-item"><i class="bi bi-graph-up-arrow"></i> Analytics</div>
                </div>
            </div>
        </div>
        
        <div class="login-form-container">
            <div class="brand-logo mx-auto">
                <img src="{{ asset('logo-bpkp.webp') }}" alt="BPKP Logo" style="width: 100%; height: auto;">
            </div>
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Login Akun</h3>
                <p class="text-secondary small">Masuk untuk mengakses dashboard.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-3 mb-4 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="wave-group">
                    <input required type="text" name="nip" class="input" value="{{ old('nip') }}">
                    <span class="bar"></span>
                    <label class="label">
                        <span class="label-char" style="--index: 0">N</span>
                        <span class="label-char" style="--index: 1">I</span>
                        <span class="label-char" style="--index: 2">P</span>
                    </label>
                </div>

                <div class="wave-group">
                    <input required type="password" name="password" class="input">
                    <span class="bar"></span>
                    <label class="label">
                        <span class="label-char" style="--index: 0">P</span>
                        <span class="label-char" style="--index: 1">a</span>
                        <span class="label-char" style="--index: 2">s</span>
                        <span class="label-char" style="--index: 3">s</span>
                        <span class="label-char" style="--index: 4">w</span>
                        <span class="label-char" style="--index: 5">o</span>
                        <span class="label-char" style="--index: 6">r</span>
                        <span class="label-char" style="--index: 7">d</span>
                    </label>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                    <div class="d-flex align-items-center gap-2">
                        <label class="custom-checkbox-container">
                            <input type="checkbox" name="remember" id="remember">
                            <svg viewBox="0 0 64 64" height="1.2em" width="1.2em">
                                <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16" pathLength="575.0541381835938" class="path"></path>
                            </svg>
                        </label>
                        <label class="small text-secondary cursor-pointer" for="remember" style="cursor: pointer;">Remember Me</label>
                    </div>
                </div>

                <button type="submit" class="btn-animated group">
                    <div class="icon-container">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.172 11L10.808 5.63605L12.222 4.22205L20 12L12.222 19.778L10.808 18.364L16.172 13H4V11H16.172Z"></path>
                        </svg>
                    </div>
                    <span class="text">Masuk Sekarang</span>
                </button>

            </form>
            
            <div class="mt-4 text-center text-secondary small opacity-50" style="font-size: 11px;">
                &copy; {{ date('Y') }} BPKP Jawa Barat.
            </div>
        </div>
    </div>
</body>
</html>
