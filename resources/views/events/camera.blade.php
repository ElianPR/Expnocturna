<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>{{ $event->name }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&family=Jost:wght@200;300&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cream: #fdf6ec;
            --gold: #c9a96e;
            --deep: #1a1008;
            --soft: #e8d5b7;
            --red: #e04040;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: var(--deep);
            color: var(--cream);
            font-family: 'Jost', sans-serif;
            font-weight: 200;
            touch-action: none;
        }

        /* grain overlay */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 999;
            opacity: 0.35;
        }

        /* ═══════════════════════════════════════════
     PORTRAIT LAYOUT  (default)
  ═══════════════════════════════════════════ */
        .app {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100dvw;
            height: 100dvh;
        }

        /* header */
        .header {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 18px;
            gap: 8px;
            opacity: 0;
            animation: fadeIn 0.8s ease 0.2s forwards;
        }

        .title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            font-weight: 300;
            letter-spacing: 0.35em;
            color: var(--gold);
            text-transform: uppercase;
            text-align: center;
        }

        .subtitle {
            font-size: 0.56rem;
            letter-spacing: 0.45em;
            color: var(--soft);
            opacity: 0.6;
            display: block;
            text-align: center;
            margin-top: 2px;
        }

        /* mode toggle */
        .mode-toggle {
            display: flex;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(201, 169, 110, 0.2);
            border-radius: 100px;
            padding: 3px;
            gap: 2px;
            flex-shrink: 0;
            opacity: 0;
            animation: fadeIn 0.8s ease 0.4s forwards;
        }

        .mode-btn {
            padding: 5px 16px;
            border-radius: 100px;
            border: none;
            background: transparent;
            color: rgba(253, 246, 236, 0.45);
            font-family: 'Jost', sans-serif;
            font-size: 0.6rem;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            cursor: pointer;
            outline: none;
            transition: background 0.2s, color 0.2s;
            -webkit-tap-highlight-color: transparent;
        }

        .mode-btn.active {
            background: rgba(201, 169, 110, 0.25);
            color: var(--cream);
        }

        /* viewport wrapper */
        .vp-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 0;
            padding: 6px 12px;
        }

        /* the actual rounded camera window */
        .viewport {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 0 0 1px rgba(201, 169, 110, 0.18), 0 24px 64px rgba(0, 0, 0, 0.75);
            /* size set by JS */
        }

        #canvas {
            display: block;
            border-radius: 24px;
            will-change: transform;
            transform: translateZ(0); /* Aceleración por hardware */
        }

        /* corner brackets */
        .corner {
            position: absolute;
            width: 20px;
            height: 20px;
            z-index: 5;
            opacity: 0.5;
        }

        .corner svg {
            width: 100%;
            height: 100%;
        }

        .corner.tl {
            top: 10px;
            left: 10px;
        }

        .corner.tr {
            top: 10px;
            right: 10px;
            transform: scaleX(-1);
        }

        .corner.bl {
            bottom: 10px;
            left: 10px;
            transform: scaleY(-1);
        }

        .corner.br {
            bottom: 10px;
            right: 10px;
            transform: scale(-1);
        }

        /* REC badge */
        .rec-badge {
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 100px;
            padding: 4px 12px 4px 8px;
            z-index: 15;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .rec-badge.show {
            opacity: 1;
        }

        .rec-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--red);
            animation: blink 1s ease-in-out infinite;
        }

        .rec-label,
        #recTimer {
            font-family: 'Jost', sans-serif;
            font-size: 0.62rem;
            color: #fff;
        }

        .rec-label {
            letter-spacing: 0.2em;
        }

        #recTimer {
            letter-spacing: 0.05em;
            min-width: 32px;
            opacity: 0.8;
        }

        /* video overlay */
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            pointer-events: none;
            opacity: 0; /* Se oculta del DOM porque ahora se pinta en el canvas */
        }

        /* flash */
        .flash {
            position: absolute;
            inset: 0;
            border-radius: 24px;
            background: white;
            opacity: 0;
            pointer-events: none;
            z-index: 20;
            transition: opacity 0.05s;
        }

        .flash.active {
            opacity: 1;
        }

        /* controls bar — PORTRAIT: horizontal row below viewport */
        .controls {
            flex-shrink: 0;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 28px;
            padding: 10px 0 20px;
            opacity: 0;
            animation: fadeIn 0.8s ease 0.6s forwards;
        }

        /* shutter */
        .btn-shutter {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            border: none;
            background: none;
            cursor: pointer;
            position: relative;
            outline: none;
            flex-shrink: 0;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-shutter::before {
            content: '';
            position: absolute;
            inset: 4px;
            border-radius: 50%;
            background: var(--cream);
            transition: transform 0.15s, opacity 0.15s, background 0.25s, border-radius 0.25s, inset 0.25s;
        }

        .btn-shutter::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid rgba(201, 169, 110, 0.6);
            transition: border-color 0.25s;
        }

        .btn-shutter:active::before {
            transform: scale(0.88);
            opacity: 0.8;
        }

        .btn-shutter.video-mode::before {
            background: var(--red);
        }

        .btn-shutter.video-mode::after {
            border-color: rgba(224, 64, 64, 0.55);
        }

        .btn-shutter.recording::before {
            border-radius: 6px;
            background: var(--red);
            inset: 20px;
        }

        .btn-shutter.recording::after {
            border-color: rgba(224, 64, 64, 0.7);
        }

        /* side icon buttons */
        .btn-side {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid rgba(201, 169, 110, 0.3);
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            color: var(--cream);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            outline: none;
            flex-shrink: 0;
            -webkit-tap-highlight-color: transparent;
            transition: background 0.2s;
        }

        .btn-side:active {
            background: rgba(255, 255, 255, 0.16);
        }

        .btn-side svg {
            width: 18px;
            height: 18px;
        }

        /* ═══════════════════════════════════════════
     LANDSCAPE LAYOUT
  ═══════════════════════════════════════════ */
        @media (orientation: landscape) {
            .app {
                flex-direction: row;
                align-items: stretch;
            }

            /* left sidebar: title + mode toggle */
            .header {
                flex-direction: column;
                justify-content: center;
                padding: 0 0 0 14px;
                width: 90px;
                flex-shrink: 0;
            }

            .title {
                font-size: 0.7rem;
                letter-spacing: 0.25em;
                writing-mode: vertical-rl;
                transform: rotate(180deg);
            }

            .subtitle {
                display: none;
            }

            /* viewport fills centre */
            .vp-wrap {
                padding: 8px 6px;
            }

            /* right sidebar: controls stack vertically */
            .controls {
                flex-direction: column;
                justify-content: center;
                padding: 0 16px 0 8px;
                gap: 20px;
                width: 80px;
                flex-shrink: 0;
            }

            /* mode toggle moves inside header for landscape */
            .mode-toggle {
                flex-direction: column;
                padding: 3px;
                gap: 2px;
                margin-top: 10px;
            }

            .mode-btn {
                padding: 7px 8px;
                font-size: 0.5rem;
                letter-spacing: 0.15em;
            }
        }

        /* ═══════════════════════════════════════════
     START SCREEN
  ═══════════════════════════════════════════ */
        .start-screen {
            position: absolute;
            inset: 0;
            background: var(--deep);
            border-radius: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            z-index: 50;
            transition: opacity 0.4s;
        }

        .start-screen.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .start-icon {
            width: 50px;
            height: 50px;
            opacity: 0.7;
        }

        .start-text {
            font-family: 'Cormorant Garamond', serif;
            font-size: 0.9rem;
            letter-spacing: 0.28em;
            color: var(--gold);
            text-transform: uppercase;
        }

        .btn-start {
            padding: 10px 28px;
            border-radius: 100px;
            border: 1px solid rgba(201, 169, 110, 0.45);
            background: transparent;
            color: var(--cream);
            font-family: 'Jost', sans-serif;
            font-size: 0.68rem;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            cursor: pointer;
            outline: none;
            transition: background 0.2s, border-color 0.2s;
        }

        .btn-start:hover {
            background: rgba(201, 169, 110, 0.1);
            border-color: var(--gold);
        }

        /* ═══════════════════════════════════════════
     PREVIEW OVERLAY
  ═══════════════════════════════════════════ */
        .preview-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 6, 2, 0.94);
            z-index: 300;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .preview-overlay.show {
            opacity: 1;
            pointer-events: all;
        }

        .preview-label {
            font-family: 'Cormorant Garamond', serif;
            font-size: 0.82rem;
            letter-spacing: 0.38em;
            color: var(--gold);
            text-transform: uppercase;
        }

        .preview-media {
            max-width: min(88vw, 88vh * 0.75);
            max-height: min(68vh, 68vw * 1.333);
            border-radius: 18px;
            object-fit: contain;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
            background: #000;
        }

        .preview-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-action {
            padding: 9px 20px;
            border-radius: 100px;
            border: 1px solid;
            font-family: 'Jost', sans-serif;
            font-weight: 300;
            font-size: 0.68rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            cursor: pointer;
            outline: none;
            transition: background 0.2s, color 0.2s;
            -webkit-tap-highlight-color: transparent;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-save {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--deep);
        }

        .btn-save:active {
            opacity: 0.8;
        }

        .btn-share {
            background: transparent;
            border-color: var(--gold);
            color: var(--gold);
        }

        .btn-share:active {
            background: rgba(201, 169, 110, 0.12);
        }

        .btn-share svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }

        .btn-retake {
            background: transparent;
            border-color: rgba(201, 169, 110, 0.35);
            color: var(--cream);
        }

        .btn-retake:active {
            background: rgba(201, 169, 110, 0.08);
        }

        /* ═══════════════════════════════════════════
     TOAST
  ═══════════════════════════════════════════ */
        .toast {
            position: fixed;
            bottom: 36px;
            left: 50%;
            transform: translateX(-50%) translateY(16px);
            background: rgba(26, 16, 8, 0.95);
            border: 1px solid rgba(201, 169, 110, 0.3);
            color: var(--cream);
            font-size: 0.68rem;
            letter-spacing: 0.12em;
            padding: 9px 20px;
            border-radius: 100px;
            z-index: 500;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s, transform 0.25s;
            white-space: nowrap;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.25
            }
        }
    </style>
</head>

<body>

    <div class="app" id="app">

        <!-- ── Header (title + mode toggle) ── -->
        <div class="header">
            <div class="title">
                Butterfly Lens
                <span class="subtitle">Cámara AR · Foto &amp; Video</span>
            </div>
            <div class="mode-toggle" id="modeToggle">
                <button class="mode-btn active" id="btnModePhoto" onclick="setMode('photo')">📷 Foto</button>
                <button class="mode-btn" id="btnModeVideo" onclick="setMode('video')">🎬 Video</button>
            </div>
        </div>

        <!-- ── Camera viewport ── -->
        <div class="vp-wrap">
            <div class="viewport" id="viewport">

                <div class="start-screen" id="startScreen">
                    <svg class="start-icon" viewBox="0 0 80 80" fill="none">
                        <ellipse cx="33" cy="36" rx="13" ry="18" fill="#c9a96e"
                            opacity="0.7" transform="rotate(-25 33 36)" />
                        <ellipse cx="47" cy="36" rx="13" ry="18" fill="#c9a96e"
                            opacity="0.7" transform="rotate(25 47 36)" />
                        <ellipse cx="30" cy="44" rx="10" ry="13" fill="#c9a96e"
                            opacity="0.5" transform="rotate(20 30 44)" />
                        <ellipse cx="50" cy="44" rx="10" ry="13" fill="#c9a96e"
                            opacity="0.5" transform="rotate(-20 50 44)" />
                        <line x1="40" y1="22" x2="40" y2="58" stroke="#fdf6ec"
                            stroke-width="1.5" stroke-linecap="round" />
                        <path d="M34 20 Q40 16 46 20" stroke="#fdf6ec" stroke-width="1" fill="none"
                            stroke-linecap="round" />
                    </svg>
                    <div class="start-text">Abrir Cámara</div>
                    <button class="btn-start" id="btnStart">Permitir Cámara</button>
                </div>

                <canvas id="canvas"></canvas>

                <video id="overlayVid" class="video-overlay" autoplay loop muted playsinline crossorigin="anonymous">
                    <source src="{{ asset('videos/T 3 B Arriba.mp4') }}" type="video/mp4">
                </video>

                <div class="rec-badge" id="recBadge">
                    <div class="rec-dot"></div>
                    <span class="rec-label">REC</span>
                    <span id="recTimer">0:00</span>
                </div>

                <div class="corner tl"><svg viewBox="0 0 22 22" fill="none">
                        <path d="M1 11V1H11" stroke="#c9a96e" stroke-width="1.2" stroke-linecap="round" />
                    </svg></div>
                <div class="corner tr"><svg viewBox="0 0 22 22" fill="none">
                        <path d="M1 11V1H11" stroke="#c9a96e" stroke-width="1.2" stroke-linecap="round" />
                    </svg></div>
                <div class="corner bl"><svg viewBox="0 0 22 22" fill="none">
                        <path d="M1 11V1H11" stroke="#c9a96e" stroke-width="1.2" stroke-linecap="round" />
                    </svg></div>
                <div class="corner br"><svg viewBox="0 0 22 22" fill="none">
                        <path d="M1 11V1H11" stroke="#c9a96e" stroke-width="1.2" stroke-linecap="round" />
                    </svg></div>

                <div class="flash" id="flash"></div>
            </div>
        </div>

        <!-- ── Controls (flip + shutter) ── -->
        <div class="controls" id="controls">
            <button class="btn-side" id="btnFlip" title="Voltear cámara">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 4v6h6M23 20v-6h-6" />
                    <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15" />
                </svg>
            </button>
            <button class="btn-shutter" id="btnShutter"></button>
            <div style="width:44px;height:44px;flex-shrink:0"></div>
        </div>

    </div><!-- /.app -->

    <!-- Preview overlay -->
    <div class="preview-overlay" id="previewOverlay">
        <div class="preview-label" id="previewLabel">Tu foto</div>
        <img id="previewImg" class="preview-media" src="" alt="" style="display:none">
        <video id="previewVid" class="preview-media" controls playsinline loop style="display:none"></video>
        <div class="preview-actions">
            <button class="btn-action btn-save" id="btnSave">Guardar en celular</button>
            <button class="btn-action btn-share" id="btnShare">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3" />
                    <circle cx="6" cy="12" r="3" />
                    <circle cx="18" cy="19" r="3" />
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
                </svg>
                Compartir en el álbum del evento
            </button>
            <button class="btn-action btn-retake" id="btnClose">Retomar</button>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"></div>

    <script>
        const EVENT_ID = "{{ $event->id_hex }}";
        const UPLOAD_URL = "{{ route('events.share.store', $event->id_hex) }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
    </script>

    <script>
        // ═══════════════════════════════════════════
        //  App state
        // ═══════════════════════════════════════════
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d', { alpha: false }); // Eliminado desynchronized: true para evitar parpadeos/tearing en Android
        const vidEl = document.createElement('video');
        vidEl.autoplay = true;
        vidEl.playsInline = true;
        vidEl.muted = true;
        const overlayVid = document.getElementById('overlayVid');

        let animId, tick = 0;
        let facingMode = 'environment';
        
        // Zoom state
        let currentZoom = 1;
        const MIN_ZOOM = 1;
        const MAX_ZOOM = 4;
        let initialPinchDistance = null;
        let initialZoom = 1;
        let camStream = null;
        let mode = 'photo';
        let isRecording = false;
        let mediaRecorder = null;
        let recordedChunks = [];
        let timerInterval = null;
        let recStartTime = 0;
        let currentShareBlob = null;
        let currentShareExt = 'jpg';

        // ═══════════════════════════════════════════
        //  Viewport sizing — adapts to orientation
        // ═══════════════════════════════════════════
        function sizeViewport() {
            const vpWrap = document.querySelector('.vp-wrap');
            const viewport = document.getElementById('viewport');

            const screenIsLandscape = window.innerWidth > window.innerHeight;

            // Usar la relación de aspecto EXACTA que nos entrega el sensor para evitar TODO recorte
            let aspect = screenIsLandscape ? (4 / 3) : (3 / 4);
            if (vidEl && vidEl.videoWidth && vidEl.videoHeight) {
                const vidAspect = vidEl.videoWidth / vidEl.videoHeight;
                // A veces el sensor reporta ancho > alto incluso en retrato. Lo ajustamos a la orientación de la pantalla.
                if (screenIsLandscape && vidAspect > 1) aspect = vidAspect;
                if (!screenIsLandscape && vidAspect < 1) aspect = vidAspect;
                if (screenIsLandscape && vidAspect < 1) aspect = 1 / vidAspect;
                if (!screenIsLandscape && vidAspect > 1) aspect = 1 / vidAspect;
            }

            const wrapW = vpWrap.clientWidth;
            const wrapH = vpWrap.clientHeight;
            let w, h;

            if (screenIsLandscape) {
                // Fill the available height; clamp width to available width
                h = wrapH;
                w = Math.min(h * aspect, wrapW);
                h = w / aspect;
            } else {
                // Fill the available width; clamp height to available height
                w = wrapW;
                h = Math.min(w / aspect, wrapH);
                w = h * aspect;
            }

            w = Math.floor(w);
            h = Math.floor(h);
            viewport.style.width = w + 'px';
            viewport.style.height = h + 'px';

            const dpr = window.devicePixelRatio || 1;
            const safeDpr = Math.min(dpr, 1.5); // Limitar para evitar resoluciones extremas
            
            // Asegurar que el ancho y alto sean pares (obligatorio para muchos codificadores Android)
            let cw = Math.floor(w * safeDpr);
            let ch = Math.floor(h * safeDpr);
            if (cw % 2 !== 0) cw -= 1;
            if (ch % 2 !== 0) ch -= 1;

            canvas.width = cw;
            canvas.height = ch;
            canvas.style.width = w + 'px';
            canvas.style.height = h + 'px';

        }

        // ═══════════════════════════════════════════
        //  Camera
        // ═══════════════════════════════════════════
        async function startCamera() {
            if (isRecording) stopRecording();
            if (camStream) camStream.getTracks().forEach(t => t.stop());
            try {
                const screenIsLandscape = window.innerWidth > window.innerHeight;

                // ─── RESOLUCIÓN ÓPTIMA (4:3) ───────────────────────────────
                // Pedimos exactamente 1280x960 (4:3 estandar). Es un formato 
                // soportado nativamente por hardware en casi todos los celulares.
                // Esto fuerza a Android a darnos la vista MÁS AMPLIA del sensor (sin 
                // recortes de zoom) manteniendo un rendimiento súper fluido.
                const videoConstraints = screenIsLandscape ? {
                    facingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 960 }
                } : {
                    facingMode,
                    width: { ideal: 960 },
                    height: { ideal: 1280 }
                };
                // ─────────────────────────────────────────────────────────────

                camStream = await navigator.mediaDevices.getUserMedia({
                    video: videoConstraints,
                    audio: true
                });
                
                // Ya no forzamos `caps.width.max` porque necesitamos
                // mantener la carga del CPU/GPU baja para evitar el lag de giro.

                const vidOnly = new MediaStream(camStream.getVideoTracks());
                vidEl.srcObject = vidOnly;
                await vidEl.play();

                // Esperar a que el video reporte sus dimensiones reales
                await new Promise(r => setTimeout(r, 150));
                sizeViewport();
                document.getElementById('startScreen').classList.add('hidden');
                if (!animId) loop();
            } catch (e) {
                alert('Acceso a la cámara denegado. Por favor permite el permiso y recarga la página.');
            }
        }

        // ═══════════════════════════════════════════
        //  Render loop
        // ═══════════════════════════════════════════
        function loop() {
            animId = requestAnimationFrame(loop);
            tick++;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            if (vidEl.readyState >= 2) {
                const vr = vidEl.videoWidth / vidEl.videoHeight;
                const cr = canvas.width / canvas.height;
                let sw, sh, sx, sy;
                if (vr > cr) {
                    sh = vidEl.videoHeight;
                    sw = sh * cr;
                    sy = 0;
                    sx = (vidEl.videoWidth - sw) / 2;
                } else {
                    sw = vidEl.videoWidth;
                    sh = sw / cr;
                    sx = 0;
                    sy = (vidEl.videoHeight - sh) / 2;
                }
                
                // Aplicar el zoom digital reduciendo el área fuente y centrándola
                const zoomedSw = sw / currentZoom;
                const zoomedSh = sh / currentZoom;
                const zoomedSx = sx + (sw - zoomedSw) / 2;
                const zoomedSy = sy + (sh - zoomedSh) / 2;

                ctx.save();
                if (facingMode === 'user') {
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                }
                ctx.drawImage(vidEl, zoomedSx, zoomedSy, zoomedSw, zoomedSh, 0, 0, canvas.width, canvas.height);
                ctx.restore();
            }

            if (overlayVid && overlayVid.readyState >= 2) {
                const vr = overlayVid.videoWidth / overlayVid.videoHeight;
                const cr = canvas.width / canvas.height;
                let sw, sh, sx, sy;
                
                // Usamos una lógica tipo "contain" para el overlay para asegurarnos
                // de que la animación completa (el corazón) sea visible en el nuevo aspect ratio 3:4
                if (vr > cr) {
                    sw = canvas.width;
                    sh = sw / vr;
                    sx = 0;
                    sy = (canvas.height - sh) / 2;
                } else {
                    sh = canvas.height;
                    sw = sh * vr;
                    sy = 0;
                    sx = (canvas.width - sw) / 2;
                }
                
                // Chroma key processing para eliminar el fondo verde
                if (!window.offscreenCanvas) {
                    window.offscreenCanvas = document.createElement('canvas');
                    window.offscreenCtx = window.offscreenCanvas.getContext('2d', { willReadFrequently: true });
                }
                
                const procW = Math.floor(sw);
                const procH = Math.floor(sh);
                
                if (window.offscreenCanvas.width !== procW || window.offscreenCanvas.height !== procH) {
                    window.offscreenCanvas.width = procW;
                    window.offscreenCanvas.height = procH;
                }
                
                window.offscreenCtx.drawImage(overlayVid, 0, 0, overlayVid.videoWidth, overlayVid.videoHeight, 0, 0, procW, procH);
                
                try {
                    let frame = window.offscreenCtx.getImageData(0, 0, procW, procH);
                    let l = frame.data.length / 4;
                    
                    for (let i = 0; i < l; i++) {
                        let r = frame.data[i * 4 + 0];
                        let g = frame.data[i * 4 + 1];
                        let b = frame.data[i * 4 + 2];
                        
                        // Detectar fondo verde (rango ajustable según el video)
                        if (g > 100 && g > r * 1.3 && g > b * 1.3) {
                            frame.data[i * 4 + 3] = 0; // Transparent
                        } else if (g > 80 && g > r * 1.1 && g > b * 1.1) {
                            // Suavizado de bordes (anti-aliasing)
                            let dif = g - Math.max(r, b);
                            frame.data[i * 4 + 3] = Math.max(0, 255 - dif * 4);
                            frame.data[i * 4 + 1] = Math.min(g, Math.max(r, b)); // Reducir componente verde en el borde
                        }
                    }
                    
                    window.offscreenCtx.putImageData(frame, 0, 0);
                    ctx.drawImage(window.offscreenCanvas, 0, 0, procW, procH, sx, sy, sw, sh);
                } catch (e) {
                    // Fallback en caso de error de CORS o similar
                    ctx.drawImage(overlayVid, 0, 0, overlayVid.videoWidth, overlayVid.videoHeight, sx, sy, sw, sh);
                }
            }

            // vignette
            const vig = ctx.createRadialGradient(
                canvas.width / 2, canvas.height / 2, canvas.height * .22,
                canvas.width / 2, canvas.height / 2, canvas.height * .7
            );
            vig.addColorStop(0, 'rgba(0,0,0,0)');
            vig.addColorStop(1, 'rgba(0,0,0,0.36)');
            ctx.fillStyle = vig;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        // ═══════════════════════════════════════════
        //  Mode toggle
        // ═══════════════════════════════════════════
        function setMode(m) {
            if (isRecording) return;
            mode = m;
            document.getElementById('btnModePhoto').classList.toggle('active', m === 'photo');
            document.getElementById('btnModeVideo').classList.toggle('active', m === 'video');
            const s = document.getElementById('btnShutter');
            s.classList.toggle('video-mode', m === 'video');
            s.classList.remove('recording');
        }

        // ═══════════════════════════════════════════
        //  Photo capture
        // ═══════════════════════════════════════════
        function capturePhoto() {
            const fl = document.getElementById('flash');
            fl.classList.add('active');
            setTimeout(() => fl.classList.remove('active'), 160);

            const out = document.createElement('canvas');
            out.width = canvas.width;
            out.height = canvas.height;
            out.getContext('2d').drawImage(canvas, 0, 0);
            const dataUrl = out.toDataURL('image/jpeg', 0.95);

            out.toBlob(blob => {
                currentShareBlob = blob;
                currentShareExt = 'jpg';
            }, 'image/jpeg', 0.95);

            const img = document.getElementById('previewImg');
            const vid = document.getElementById('previewVid');
            img.src = dataUrl;
            img.style.display = 'block';
            vid.style.display = 'none';
            vid.src = '';
            document.getElementById('previewLabel').textContent = 'Tu foto';

            document.getElementById('btnSave').onclick = () => {
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = `butterfly-foto-${Date.now()}.jpg`;
                a.click();
            };
            document.getElementById('previewOverlay').classList.add('show');
        }

        // ═══════════════════════════════════════════
        //  Video recording
        // ═══════════════════════════════════════════
        function bestMime() {
            return [
                'video/mp4;codecs=h264,aac',
                'video/mp4',
                'video/webm;codecs=vp9,opus',
                'video/webm;codecs=vp8,opus',
                'video/webm'
            ].find(t => MediaRecorder.isTypeSupported(t)) || '';
        }

        function startRecording() {
            if (!camStream) return;
            recordedChunks = [];
            const cs = canvas.captureStream(30);
            camStream.getAudioTracks().forEach(t => cs.addTrack(t));
            const mime = bestMime();
            try {
                mediaRecorder = new MediaRecorder(cs, mime ? {
                    mimeType: mime,
                    videoBitsPerSecond: 2500000 // 2.5 Mbps para estabilidad
                } : {});
            } catch (e) {
                mediaRecorder = new MediaRecorder(cs);
            }
            
            mediaRecorder.onerror = e => {
                showToast("Error del codificador de video. " + (e.error ? e.error.message : ''));
                if (isRecording) stopRecording();
            };

            mediaRecorder.ondataavailable = e => {
                if (e.data?.size > 0) recordedChunks.push(e.data);
            };
            mediaRecorder.onstop = finalizeVideo;
            mediaRecorder.start(1000); // Chunks de 1 segundo para evitar sobrecarga
            isRecording = true;
            document.getElementById('btnShutter').classList.add('recording');
            document.getElementById('recBadge').classList.add('show');
            recStartTime = Date.now();
            updateTimer();
            timerInterval = setInterval(updateTimer, 500);
        }

        function stopRecording() {
            if (!mediaRecorder || !isRecording) return;
            isRecording = false;
            mediaRecorder.stop();
            clearInterval(timerInterval);
            document.getElementById('btnShutter').classList.remove('recording');
            document.getElementById('recBadge').classList.remove('show');
            document.getElementById('recTimer').textContent = '0:00';
        }

        function finalizeVideo() {
            if (isRecording) {
                isRecording = false;
                clearInterval(timerInterval);
                document.getElementById('btnShutter').classList.remove('recording');
                document.getElementById('recBadge').classList.remove('show');
                document.getElementById('recTimer').textContent = '0:00';
            }

            const mime = mediaRecorder.mimeType || 'video/webm';
            const blob = new Blob(recordedChunks, {
                type: mime
            });
            const url = URL.createObjectURL(blob);
            const ext = mime.includes('mp4') ? 'mp4' : 'webm';
            currentShareBlob = blob;
            currentShareExt = ext;

            const img = document.getElementById('previewImg');
            const vid = document.getElementById('previewVid');
            img.style.display = 'none';
            vid.style.display = 'block';
            vid.src = url;
            vid.play();
            document.getElementById('previewLabel').textContent = 'Tu video';

            document.getElementById('btnSave').onclick = () => {
                const a = document.createElement('a');
                a.href = url;
                a.download = `butterfly-video-${Date.now()}.${ext}`;
                a.click();
            };
            document.getElementById('previewOverlay').classList.add('show');
        }

        function updateTimer() {
            const s = Math.floor((Date.now() - recStartTime) / 1000);
            document.getElementById('recTimer').textContent =
                `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`;
        }

        // ═══════════════════════════════════════════
        //  Share
        // ═══════════════════════════════════════════
        let toastTimer;

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.classList.add('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
        }

        async function shareMedia() {
            if (!currentShareBlob) return;

            showToast('Subiendo al álbum...');

            const formData = new FormData();
            const filename = `butterfly-lens-${Date.now()}.${currentShareExt}`;
            formData.append('files[]', currentShareBlob, filename);
            formData.append('_token', CSRF_TOKEN);

            try {
                const response = await fetch(UPLOAD_URL, {
                    method: 'POST',
                    body: formData,
                });

                if (response.ok) {
                    showToast('¡Guardado en el álbum del evento!');
                    closePreview();
                } else {
                    showToast('Error al subir. Intenta de nuevo.');
                }
            } catch (e) {
                showToast('Error de conexión. Intenta de nuevo.');
            }
        }

        // ═══════════════════════════════════════════
        //  Event bindings
        // ═══════════════════════════════════════════
        document.getElementById('btnStart').addEventListener('click', startCamera);

        // Pinch to zoom logic
        const vpEl = document.getElementById('viewport');
        vpEl.addEventListener('touchstart', e => {
            if (e.touches.length === 2) {
                initialPinchDistance = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY
                );
                initialZoom = currentZoom;
            }
        }, { passive: true });

        vpEl.addEventListener('touchmove', e => {
            if (e.touches.length === 2 && initialPinchDistance) {
                const currentDistance = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY
                );
                const distanceRatio = currentDistance / initialPinchDistance;
                currentZoom = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, initialZoom * distanceRatio));
            }
        }, { passive: true });

        vpEl.addEventListener('touchend', e => {
            if (e.touches.length < 2) {
                initialPinchDistance = null;
            }
        });

        document.getElementById('btnShutter').addEventListener('click', () => {
            if (mode === 'photo') capturePhoto();
            else if (!isRecording) startRecording();
            else stopRecording();
        });

        document.getElementById('btnFlip').addEventListener('click', async () => {
            if (isRecording) return;
            facingMode = facingMode === 'environment' ? 'user' : 'environment';
            await startCamera();
        });

        document.getElementById('btnShare').addEventListener('click', shareMedia);

        document.getElementById('btnClose').addEventListener('click', () => {
            closePreview();
        });

        function closePreview() {
            document.getElementById('previewOverlay').classList.remove('show');
            const v = document.getElementById('previewVid');
            v.pause();
            v.removeAttribute('src');
            v.load(); // <- esto fuerza a WebKit a soltar el audio completamente
            URL.revokeObjectURL(v.src); // liberar memoria del blob
        }

        // ═══════════════════════════════════════════
        //  Orientation / resize handling
        // ═══════════════════════════════════════════
        let resizeDebounce;
        let lastOrientation = window.innerWidth > window.innerHeight ? 'landscape' : 'portrait';

        window.addEventListener('resize', () => {
            clearTimeout(resizeDebounce);
            resizeDebounce = setTimeout(() => {
                const nowLandscape = window.innerWidth > window.innerHeight;
                const nowOrientation = nowLandscape ? 'landscape' : 'portrait';

                if (nowOrientation !== lastOrientation) {
                    lastOrientation = nowOrientation;
                    if (camStream) startCamera();
                } else {
                    // Same orientation, just a resize — re-fit the viewport
                    sizeViewport();
                }
            }, 150);
        });

        document.getElementById('btnStart')
            .addEventListener('click', startCamera);
    </script>
</body>

</html>
