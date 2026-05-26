<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>{{ $event->name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Cormorant+Garamond:wght@400;600;700&family=Great+Vibes&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/camera-event.css') }}">
    @php
        $themes = [
            1 => [
                'bg' => asset('images/fondosV/fondoV.png'),
                'primary' => '#305820',
                'icon' => asset('images/fondosV/camaraV.png'),
            ],
            2 => [
                'bg' => asset('images/fondosA/fondoA.png'),
                'primary' => '#092D51',
                'icon' => asset('images/fondosA/camaraA.png'),
            ],
            3 => [
                'bg' => asset('images/fondosD/fondoD.png'),
                'primary' => '#8F6827',
                'icon' => asset('images/fondosD/camaraD.png'),
            ],
        ];

        $shutterColors = [
            1 => ['fill' => '#71ca5b', 'border' => '#245b00'],
            2 => ['fill' => '#6daae3', 'border' => '#092d51'],
            3 => ['fill' => '#dca752', 'border' => '#8f6827'],
        ];

        $template = $event->template ?? 1;
        $theme = $themes[$template];
        $shutter = $shutterColors[$template];
        $eventFont = $event->typography ?? "'Playfair Display', serif";

        $animations = $event->cameraAnimations
            ->map(function ($anim) {
                return route('camera-animations.stream', $anim->id);
            })
            ->toArray();
        if (empty($animations)) {
            $animations = [asset('videos/T 3 B Arriba.mp4')];
        }
    @endphp

    <style>
        .btn-shutter::before {
            background: {{ $shutter['fill'] }} !important;
        }

        .btn-shutter::after {
            border-color: {{ $shutter['border'] }} !important;
        }

        .btn-shutter.video-mode::before {
            background: #e04040 !important;
        }

        .btn-shutter.video-mode::after {
            border-color: rgba(224, 64, 64, 0.55) !important;
        }

        .btn-shutter.recording::before {
            background: #e04040 !important;
            border-radius: 6px !important;
            inset: 20px !important;
        }

        .btn-shutter.recording::after {
            border-color: rgba(224, 64, 64, 0.7) !important;
        }

        .mode-toggle {
            background: {{ $shutter['border'] }}26;
            border-color: {{ $shutter['border'] }}66;
        }

        .mode-btn.active {
            background: {{ $theme['primary'] }};
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    style="
    --gold: {{ $theme['primary'] }};
    --primary: {{ $theme['primary'] }};
    --bg-image: url('{{ $theme['bg'] }}');
    --shutter-fill: {{ $shutter['fill'] }};
    --shutter-border: {{ $shutter['border'] }};
">
    <div class="app" id="app">

        <div class="header">
            <div class="title"
                style="
                    font-family: {!! $eventFont !!};
                    color: {{ $theme['primary'] }};
                    font-size: 1.8rem;
                    line-height: 1.1;
                    letter-spacing: 0.02em;
                    font-weight: 800;
                ">
                {{ $event->name }}
            </div>
            <div class="mode-toggle" id="modeToggle">
                <button class="mode-btn active" id="btnModePhoto" onclick="setMode('photo')">Foto</button>
                <button class="mode-btn" id="btnModeVideo" onclick="setMode('video')">Video</button>
            </div>
        </div>

        <!-- ── Camera viewport ── -->
        <div class="vp-wrap">
            <div class="viewport" id="viewport">
                <div class="start-screen" id="startScreen">
                    <img src="{{ $theme['icon'] }}" alt="Ícono del evento" class="start-icon">
                    <div class="start-text"
                        style="color: {{ $theme['primary'] }}; font-family: 'Jost', sans-serif; font-style: normal; font-weight: 300; text-align: center;">
                        Abre la cámara y deja que las mariposas entren en tu pantalla.
                    </div>

                    <flux:button id="btnStart" variant="primary" icon="camera"
                        class="w-64 justify-center rounded-xl text-lg font-semibold"
                        style="background-color: {{ $theme['primary'] }}; color: white;">
                        Permitir Cámara
                    </flux:button>
                </div>

                <canvas id="canvas"></canvas>

                <video id="overlayVid" class="video-overlay" autoplay muted playsinline crossorigin="anonymous">
                </video>

                <div class="rec-badge" id="recBadge">
                    <div class="rec-dot"></div>
                    <span class="rec-label">REC</span>
                    <span id="recTimer">0:00</span>
                </div>

                <div class="corner tl"><svg viewBox="0 0 22 22" fill="none">
                        <path d="M1 11V1H11" stroke="var(--primary)" stroke-width="1.2" stroke-linecap="round" />
                    </svg></div>
                <div class="corner tr"><svg viewBox="0 0 22 22" fill="none">
                        <path d="M1 11V1H11" stroke="var(--primary)" stroke-width="1.2" stroke-linecap="round" />
                    </svg></div>
                <div class="corner bl"><svg viewBox="0 0 22 22" fill="none">
                        <path d="M1 11V1H11" stroke="var(--primary)" stroke-width="1.2" stroke-linecap="round" />
                    </svg></div>
                <div class="corner br"><svg viewBox="0 0 22 22" fill="none">
                        <path d="M1 11V1H11" stroke="var(--primary)" stroke-width="1.2" stroke-linecap="round" />
                    </svg></div>

                <div class="flash" id="flash"></div>
            </div>
        </div>

        <!-- ── Controls (flip + shutter) ── -->
        <div class="controls" id="controls">
            <button class="btn-side" id="btnFlip" title="Voltear cámara">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round">
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
        <div class="preview-label" id="previewLabel"></div>
        <img id="previewImg" class="preview-media" src="" alt="" style="display:none">
        <video id="previewVid" class="preview-media" controls playsinline loop style="display:none"></video>
        <div class="preview-actions">
            <flux:button id="btnSave" variant="primary" icon="arrow-down-tray"
                class="w-64 justify-center rounded-xl text-lg font-semibold"
                style="background-color: {{ $theme['primary'] }}; color: white;">
                Guardar en celular
            </flux:button>

            <flux:button id="btnShare" variant="primary" icon="share"
                class="w-64 justify-center rounded-xl text-lg font-semibold"
                style="background-color: {{ $theme['primary'] }}; color: white;">
                Compartir en el álbum del evento
            </flux:button>

            <flux:button id="btnClose" variant="primary" icon="arrow-uturn-left"
                class="w-64 justify-center rounded-xl text-lg font-semibold"
                style="background-color: {{ $theme['primary'] }}; color: white;">
                Retomar
            </flux:button>
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
        let cameraReady = false;

        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d', {
            alpha: false
        });
        const watermarkImg = new Image();
        watermarkImg.crossOrigin = "anonymous";
        let hasWatermark = false;
        @if ($event->watermark)
            watermarkImg.src =
                "{{ route('file.show', ['id_evento' => $event->id_hex, 'filename' => $event->watermark]) }}";
            watermarkImg.onload = () => {
                hasWatermark = true;
            };
        @endif
        const vidEl = document.createElement('video');
        vidEl.autoplay = true;
        vidEl.playsInline = true;
        vidEl.muted = true;
        const overlayVid = document.getElementById('overlayVid');
        const animationsList = @json($animations);

        function playRandomAnimation() {
            if (animationsList.length === 0) return;
            const randomIndex = Math.floor(Math.random() * animationsList.length);
            overlayVid.src = animationsList[randomIndex];
            overlayVid.load();
            overlayVid.play().catch(() => {});
        }

        playRandomAnimation();

        overlayVid.addEventListener('ended', () => {
            playRandomAnimation();
        });

        let animId, tick = 0;
        let facingMode = 'environment';

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

        function setControlsEnabled(enabled) {
            cameraReady = enabled;

            document.getElementById('btnShutter').disabled = !enabled;
            document.getElementById('btnFlip').disabled = !enabled;
            document.getElementById('btnModePhoto').disabled = !enabled;
            document.getElementById('btnModeVideo').disabled = !enabled;

            document.querySelectorAll(
                '#btnShutter, #btnFlip, #btnModePhoto, #btnModeVideo'
            ).forEach(btn => {
                btn.style.opacity = enabled ? '1' : '0.4';
                btn.style.pointerEvents = enabled ? 'auto' : 'none';
            });
        }

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
            setControlsEnabled(false);
            if (isRecording) stopRecording();
            if (camStream) camStream.getTracks().forEach(t => t.stop());
            try {
                const videoConstraints = {
                    facingMode: 'environment',
                    width: {
                        ideal: 1440
                    },
                    height: {
                        ideal: 1080
                    }
                };

                camStream = await navigator.mediaDevices.getUserMedia({
                    video: videoConstraints,
                    audio: true
                });

                const vidOnly = new MediaStream(camStream.getVideoTracks());
                vidEl.srcObject = vidOnly;
                await vidEl.play();

                // Esperar a que el video reporte sus dimensiones reales
                await new Promise(r => setTimeout(r, 150));
                sizeViewport();
                document.getElementById('startScreen').classList.add('hidden');
                overlayVid.currentTime = 0;
                overlayVid.play().catch(() => {});
                if (!animId) loop();
                setControlsEnabled(true);
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

                // Lógica tipo "cover" para que el overlay ocupe TODA la pantalla de la cámara
                if (vr > cr) {
                    sh = canvas.height;
                    sw = sh * vr;
                    sy = 0;
                    sx = (canvas.width - sw) / 2;
                } else {
                    sw = canvas.width;
                    sh = sw / vr;
                    sx = 0;
                    sy = (canvas.height - sh) / 2;
                }

                // Chroma key processing para eliminar el fondo verde
                if (!window.offscreenCanvas) {
                    window.offscreenCanvas = document.createElement('canvas');
                    window.offscreenCtx = window.offscreenCanvas.getContext('2d', {
                        willReadFrequently: true
                    });
                }

                const procW = Math.floor(sw);
                const procH = Math.floor(sh);

                if (window.offscreenCanvas.width !== procW || window.offscreenCanvas.height !== procH) {
                    window.offscreenCanvas.width = procW;
                    window.offscreenCanvas.height = procH;
                }

                window.offscreenCtx.drawImage(overlayVid, 0, 0, overlayVid.videoWidth, overlayVid.videoHeight, 0, 0, procW,
                    procH);

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
                canvas.width / 2,
                canvas.height / 2,
                canvas.height * .30,
                canvas.width / 2,
                canvas.height / 2,
                canvas.height * .55
            );

            vig.addColorStop(0, 'rgba(0,0,0,0)');
            vig.addColorStop(1, 'rgba(0,0,0,0.10)');

            ctx.fillStyle = vig;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            if (hasWatermark) {
                const wmWidth = canvas.width * 0.30;
                const wmHeight = (watermarkImg.height / watermarkImg.width) * wmWidth;
                const margin = canvas.width * 0.05;
                ctx.drawImage(
                    watermarkImg,
                    canvas.width - wmWidth - margin,
                    canvas.height - wmHeight - margin,
                    wmWidth,
                    wmHeight
                );
            }
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

            document.getElementById('btnSave').onclick = async () => {

                if (!currentShareBlob) return;

                const file = new File(
                    [currentShareBlob],
                    `butterfly-foto-${Date.now()}.jpg`, {
                        type: 'image/jpeg'
                    }
                );

                if (navigator.canShare && navigator.canShare({
                        files: [file]
                    })) {

                    try {
                        await navigator.share({
                            files: [file],
                            title: 'Foto del evento',
                            text: 'Compartida desde Butterfly Lens'
                        });
                    } catch (e) {
                        console.log('Compartir cancelado');
                    }

                } else {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(file);
                    a.download = file.name;
                    a.click();
                }
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

            document.getElementById('btnSave').onclick = async () => {

                if (!currentShareBlob) return;

                const file = new File(
                    [currentShareBlob],
                    `butterfly-video-${Date.now()}.${ext}`, {
                        type: currentShareBlob.type
                    }
                );

                if (navigator.canShare && navigator.canShare({
                        files: [file]
                    })) {

                    try {
                        await navigator.share({
                            files: [file],
                            title: 'Video del evento',
                            text: 'Compartido desde Butterfly Lens'
                        });
                    } catch (e) {
                        console.log('Compartir cancelado');
                    }

                } else {

                    const a = document.createElement('a');
                    a.href = url;
                    a.download = file.name;
                    a.click();
                }
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
        setControlsEnabled(false);
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
        }, {
            passive: true
        });

        vpEl.addEventListener('touchmove', e => {
            if (e.touches.length === 2 && initialPinchDistance) {
                const currentDistance = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY
                );
                const distanceRatio = currentDistance / initialPinchDistance;
                currentZoom = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, initialZoom * distanceRatio));
            }
        }, {
            passive: true
        });

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

        // Inicializar el tamaño del viewport al cargar la página
        sizeViewport();
    </script>
    @fluxScripts
</body>

</html>
