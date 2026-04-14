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
        //  Butterfly system
        // ═══════════════════════════════════════════
        const COLORS = [
            ['#E8A87C', '#D4956A', '#8B5E3C', '#FFF3E0'],
            ['#7EC8E3', '#5BB8D4', '#2C5F6E', '#E8F7FF'],
            ['#B5E7A0', '#93D47A', '#3D6B2E', '#F0FFF0'],
            ['#F7C6D7', '#F0A0BF', '#8B3A5A', '#FFF0F5'],
            ['#FFE066', '#F5C800', '#7A5C00', '#FFFDE0'],
            ['#C9A0DC', '#B07CC8', '#5C3472', '#F5EEFF'],
            ['#FF8C66', '#F07050', '#7A2810', '#FFE8E0'],
            ['#80E0C0', '#55CCA8', '#1A6B50', '#EAFFF7'],
        ];

        class Butterfly {
            constructor(cw, ch) {
                this.cw = cw;
                this.ch = ch;
                this.colors = COLORS[Math.floor(Math.random() * COLORS.length)];
                this.reset(true);
            }
            reset(init = false) {
                this.x = init ? Math.random() * this.cw : -60;
                this.y = init ? Math.random() * this.ch : Math.random() * this.ch;
                this.size = 14 + Math.random() * 20;
                this.vx = (0.5 + Math.random() * 1.1) * (0.6 + Math.random() * 0.8);
                this.vy = (Math.random() - 0.5) * 0.7;
                this.wf = 0.012 + Math.random() * 0.018;
                this.wo = Math.random() * Math.PI * 2;
                this.fs = 0.08 + Math.random() * 0.12;
                this.fo = Math.random() * Math.PI * 2;
                this.age = 0;
                this.op = init ? Math.random() : 0;
                this.dep = 0.4 + Math.random() * 0.6;
            }
            update(t) {
                this.age++;
                this.x += this.vx;
                this.vy += Math.sin(this.age * this.wf + this.wo) * 0.04;
                this.vy *= 0.96;
                this.y += this.vy;
                if (this.op < 1) this.op = Math.min(1, this.op + 0.02);
                if (this.x > this.cw + 80) this.reset(false);
                if (this.y < -80 || this.y > this.ch + 80) this.vy = (this.ch * 0.5 - this.y) * 0.008;
            }
            draw(ctx, t) {
                const sx = Math.abs(Math.sin(t * this.fs + this.fo));
                const s = this.size * this.dep;
                ctx.save();
                ctx.globalAlpha = this.op * this.dep * 0.92;
                ctx.translate(this.x, this.y);
                ctx.save();
                ctx.globalAlpha = this.op * this.dep * 0.11;
                ctx.translate(s * 0.14, s * 0.24);
                this._wings(ctx, s, sx, true);
                ctx.restore();
                this._wings(ctx, s, sx, false);
                this._body(ctx, s);
                ctx.restore();
            }
            _wings(ctx, s, sx, sh) {
                const [u, l, , p] = this.colors;
                ctx.save();
                ctx.scale(sx, 1);
                const shapes = [
                    [0, 0, s * .8, -s, s * 1.4, -s * .3, s, s * .3, s * .5, s * .6, s * .1, s * .2, 0, 0, u],
                    [0, 0, -s * .8, -s, -s * 1.4, -s * .3, -s, s * .3, -s * .5, s * .6, -s * .1, s * .2, 0, 0, u],
                    [0, s * .1, s * .6, s * .2, s, s * .9, s * .6, s * 1.3, s * .2, s * 1.5, 0, s, 0, s * .1, l],
                    [0, s * .1, -s * .6, s * .2, -s, s * .9, -s * .6, s * 1.3, -s * .2, s * 1.5, 0, s, 0, s * .1,
                        l
                    ],
                ];
                shapes.forEach(([mx, my, c1x, c1y, c2x, c2y, c3x, c3y, c4x, c4y, c5x, c5y, ex, ey, col]) => {
                    ctx.beginPath();
                    ctx.moveTo(mx, my);
                    ctx.bezierCurveTo(c1x, c1y, c2x, c2y, c3x, c3y);
                    ctx.bezierCurveTo(c4x, c4y, c5x, c5y, ex, ey);
                    ctx.fillStyle = sh ? '#000' : col;
                    ctx.fill();
                });
                if (!sh) {
                    ctx.globalAlpha = 0.5;
                    [-1, 1].forEach(ox => {
                        [
                            [ox * s * .85, -s * .35, s * .12],
                            [ox * s * .55, s * .65, s * .1]
                        ].forEach(([ax, ay, r]) => {
                            ctx.beginPath();
                            ctx.arc(ax, ay, r, 0, Math.PI * 2);
                            ctx.fillStyle = p;
                            ctx.fill();
                        });
                    });
                }
                ctx.restore();
            }
            _body(ctx, s) {
                const [, , b] = this.colors;
                ctx.beginPath();
                ctx.ellipse(0, s * .2, s * .1, s * .6, 0, 0, Math.PI * 2);
                ctx.fillStyle = b;
                ctx.fill();
                ctx.strokeStyle = b;
                ctx.lineWidth = 1;
                [
                    [s * .4, -s * .9, s * .5, -s * 1.1],
                    [-s * .4, -s * .9, -s * .5, -s * 1.1]
                ].forEach(([cpx, cpy, ex, ey]) => {
                    ctx.beginPath();
                    ctx.moveTo(0, -s * .2);
                    ctx.quadraticCurveTo(cpx, cpy, ex, ey);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.arc(ex, ey, s * .06, 0, Math.PI * 2);
                    ctx.fillStyle = b;
                    ctx.fill();
                });
            }
        }

        // ═══════════════════════════════════════════
        //  App state
        // ═══════════════════════════════════════════
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const vidEl = document.createElement('video');
        vidEl.autoplay = true;
        vidEl.playsInline = true;
        vidEl.muted = true;

        let butterflies = [],
            animId, tick = 0;
        let facingMode = 'environment';
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

            // ─── KEY FIX ───────────────────────────────────────────────────────────
            // Camera sensor ALWAYS reports landscape dimensions (e.g. 1920×1080)
            // regardless of how the phone is held. We must use the SCREEN orientation,
            // not vidEl.videoWidth/videoHeight, to decide portrait vs landscape aspect.
            const screenIsLandscape = window.innerWidth > window.innerHeight;

            // Use standard phone aspect ratios driven by screen orientation:
            //   Portrait  → 9:16  (tall)
            //   Landscape → 16:9  (wide)
            const aspect = screenIsLandscape ? (16 / 9) : (9 / 16);
            // ───────────────────────────────────────────────────────────────────────

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
            canvas.width = w * dpr;
            canvas.height = h * dpr;
            canvas.style.width = w + 'px';
            canvas.style.height = h + 'px';

            if (butterflies.length) {
                butterflies.forEach(b => {
                    b.cw = canvas.width;
                    b.ch = canvas.height;
                });
            }
        }

        // ═══════════════════════════════════════════
        //  Camera
        // ═══════════════════════════════════════════
        function initButterflies() {
            butterflies = [];
            for (let i = 0; i < 18; i++) butterflies.push(new Butterfly(canvas.width, canvas.height));
        }

        async function startCamera() {
            if (isRecording) stopRecording();
            if (camStream) camStream.getTracks().forEach(t => t.stop());
            try {
                const screenIsLandscape = window.innerWidth > window.innerHeight;

                // ─── MÁXIMA RESOLUCIÓN DEL DISPOSITIVO ───────────────────────────────
                // Usar valores extremadamente altos como "ideal" hace que el navegador
                // negocie la máxima resolución que el hardware soporta (4K, 12MP, etc.)
                // sin forzar un valor exact que podría fallar en algunos dispositivos.
                const videoConstraints = screenIsLandscape ? {
                    facingMode,
                    width: {
                        ideal: 99999
                    },
                    height: {
                        ideal: 99999
                    }
                } : {
                    facingMode,
                    width: {
                        ideal: 99999
                    },
                    height: {
                        ideal: 99999
                    }
                };
                // ─────────────────────────────────────────────────────────────────────

                camStream = await navigator.mediaDevices.getUserMedia({
                    video: videoConstraints,
                    audio: true
                });

                // ─── APLICAR RESOLUCIÓN MÁXIMA REAL DEL SENSOR ───────────────────────
                // Después de obtener el stream, leer las capacidades reales del track
                // y aplicar el máximo exacto que el hardware reporta.
                const track = camStream.getVideoTracks()[0];
                if (track.getCapabilities) {
                    try {
                        const caps = track.getCapabilities();
                        if (caps.width?.max && caps.height?.max) {
                            await track.applyConstraints({
                                width: {
                                    ideal: caps.width.max
                                },
                                height: {
                                    ideal: caps.height.max
                                }
                            });
                        }
                    } catch (e) {
                        /* dispositivo no soporta applyConstraints — continuar */
                    }
                }
                // ─────────────────────────────────────────────────────────────────────

                const vidOnly = new MediaStream(camStream.getVideoTracks());
                vidEl.srcObject = vidOnly;
                await vidEl.play();

                // Esperar a que el video reporte sus dimensiones reales
                await new Promise(r => setTimeout(r, 150));
                sizeViewport();
                initButterflies();
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
                ctx.save();
                if (facingMode === 'user') {
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                }
                ctx.drawImage(vidEl, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);
                ctx.restore();
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

            butterflies.forEach(b => {
                b.cw = canvas.width;
                b.ch = canvas.height;
                b.update(tick);
                b.draw(ctx, tick);
            });
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
                    videoBitsPerSecond: 16_000_000
                } : {});
            } catch (e) {
                mediaRecorder = new MediaRecorder(cs);
            }
            mediaRecorder.ondataavailable = e => {
                if (e.data?.size > 0) recordedChunks.push(e.data);
            };
            mediaRecorder.onstop = finalizeVideo;
            mediaRecorder.start(100);
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
                    // Orientation actually changed — restart camera with correct dimensions
                    lastOrientation = nowOrientation;
                    if (camStream) startCamera(); // re-request with new width/height ideal
                } else {
                    // Same orientation, just a resize — re-fit the viewport
                    sizeViewport();
                    initButterflies();
                }
            }, 150);
        });

        // ═══════════════════════════════════════════
        //  Auto-start
        // ═══════════════════════════════════════════
        (async () => {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                if (devices.some(d => d.kind === 'videoinput')) startCamera();
            } catch {}
        })();
    </script>
</body>

</html>
