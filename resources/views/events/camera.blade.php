<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Cámara - {{ $event->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Optimizaciones de rendimiento */
        * {
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        video {
            transform: translateZ(0);
            will-change: transform;
            backface-visibility: hidden;
        }

        button {
            touch-action: manipulation;
        }

        /* Animaciones optimizadas */
        .scale-transition {
            transition: transform 0.1s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        .scale-transition:active {
            transform: scale(0.92);
        }

        /* Blur optimizado */
        .backdrop-blur-optimized {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 30px;
            color: white;
            font-size: 14px;
            z-index: 100;
            animation: fadeInOut 2s ease;
            pointer-events: none;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }

            15% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            85% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
        }
    </style>
</head>

<body class="bg-black text-white flex flex-col h-screen overflow-hidden">

    <!-- HEADER -->
    <div class="relative flex items-center px-4 py-3 bg-black/40 backdrop-blur-optimized z-10">

        <div class="absolute left-4">
            <a href="{{ route('events.show', $event->id_hex) }}">
                <flux:button variant="subtle" icon="arrow-left">
                    Volver
                </flux:button>
            </a>
        </div>

        <span class="mx-auto text-sm font-medium tracking-wide truncate max-w-[60%]">
            {{ $event->name }}
        </span>

    </div>

    <!-- VIEWPORT -->
    <div class="flex-1 relative bg-black overflow-hidden">
        <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>

        <div id="recBadge"
            class="hidden absolute top-4 right-4 flex items-center gap-2 bg-red-600 px-3 py-1.5 rounded-full text-xs font-bold shadow-lg animate-pulse z-10">
            <span class="w-2 h-2 bg-white rounded-full"></span>
            GRABANDO
        </div>

        <!-- Indicador de calidad -->
        <div id="qualityBadge"
            class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-optimized px-2 py-1 rounded text-[10px] font-mono z-10">
            <span id="qualityText">HD</span>
        </div>
    </div>

    <!-- CONTROLES -->
    <div class="py-5 flex flex-col items-center gap-5 bg-black/80 backdrop-blur-optimized border-t border-white/10">

        <!-- MODO -->
        <div class="flex gap-10 text-sm tracking-wide">
            <button onclick="setMode('photo')" id="btnPhoto"
                class="font-semibold transition-colors duration-150 text-yellow-400">
                Foto
            </button>
            <button onclick="setMode('video')" id="btnVideo" class="text-white/50 transition-colors duration-150">
                Video
            </button>
        </div>

        <!-- CONTROLES PRINCIPALES -->
        <div class="flex items-center justify-center gap-16 w-full px-6">

            <!-- Espacio para balance -->
            <div class="w-12"></div>

            <!-- BOTÓN CAPTURA -->
            <button id="captureBtn"
                class="scale-transition w-20 h-20 rounded-full border-4 border-white flex items-center justify-center shadow-xl">
                <div id="innerBtn" class="w-14 h-14 bg-white rounded-full transition-all duration-150"></div>
            </button>

            <!-- SWITCH CÁMARA -->
            <button onclick="switchCamera()" id="switchCameraBtn"
                class="scale-transition w-12 h-12 rounded-full bg-white/15 backdrop-blur-optimized flex items-center justify-center border border-white/30 shadow-lg">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h7a5 5 0 015 5v1" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 7l2 2-2 2" />

                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17H10a5 5 0 01-5-5v-1" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17l-2-2 2-2" />
                </svg>

            </button>
        </div>
    </div>

    <!-- PREVIEW MODAL -->
    <div id="previewModal"
        class="hidden fixed inset-0 bg-black/95 z-50 flex flex-col items-center justify-center px-4 animate-in fade-in duration-200">

        <div class="relative w-full max-w-2xl flex-1 flex items-center justify-center">
            <img id="previewImage" class="max-w-full max-h-[70vh] rounded-xl shadow-2xl hidden object-contain" />
            <video id="previewVideo" controls playsinline
                class="max-w-full max-h-[70vh] rounded-xl shadow-2xl hidden"></video>
        </div>

        <div class="grid grid-cols-3 gap-3 w-full max-w-md mt-6 mb-8">
            <flux:button icon="arrow-down-tray" variant="primary" onclick="downloadMedia()" class="scale-transition">
                Guardar
            </flux:button>
            <flux:button icon="share" variant="primary" onclick="shareMedia()" class="scale-transition">
                Compartir
            </flux:button>
            <flux:button icon="arrow-uturn-left" variant="subtle" onclick="closePreview()" class="scale-transition">
                Retomar
            </flux:button>
        </div>
    </div>

    <script>
        // Optimizaciones de rendimiento
        let stream = null;
        let mediaRecorder = null;
        let recordedChunks = [];
        let currentMode = 'photo';
        let facingMode = 'environment';
        let currentBlob = null;
        let currentResolution = {
            width: 0,
            height: 0
        };
        let animationFrameId = null;

        const video = document.getElementById('video');
        const qualityBadge = document.getElementById('qualityText');

        // Función para mostrar notificaciones toast
        function showToast(message, isError = false) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.textContent = message;
            toast.style.background = isError ? 'rgba(220, 38, 38, 0.9)' : 'rgba(0,0,0,0.8)';
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 2000);
        }

        async function startCamera() {
            try {
                // Detener streams anteriores
                if (stream) {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }
                    stream.getTracks().forEach(track => {
                        track.stop();
                        track.enabled = false;
                    });
                    stream = null;
                }

                // Configuración para máxima calidad con AUDIO
                const constraints = {
                    video: {
                        facingMode: {
                            exact: facingMode
                        },
                        width: {
                            ideal: 1920,
                            min: 1280
                        },
                        height: {
                            ideal: 1080,
                            min: 720
                        },
                        frameRate: {
                            ideal: 30
                        }
                    },
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        sampleRate: 44100
                    }
                };

                stream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = stream;

                // El video debe estar MUTED para evitar feedback, pero el audio se graba igual
                video.muted = true;

                // Esperar a que el video esté listo
                await new Promise((resolve) => {
                    video.onloadedmetadata = () => {
                        video.play();
                        resolve();
                    };
                });

                // Obtener resolución real
                const track = stream.getVideoTracks()[0];
                const settings = track.getSettings();
                currentResolution = {
                    width: settings.width || video.videoWidth,
                    height: settings.height || video.videoHeight
                };

                updateQualityBadge();

                // Verificar si hay audio disponible
                const audioTracks = stream.getAudioTracks();
                if (audioTracks.length > 0) {
                    console.log('Audio disponible:', audioTracks[0].label);
                } else {
                    console.warn('No hay audio disponible');
                }

            } catch (err) {
                console.error("Error cámara:", err);
                showToast('Error al acceder a la cámara/micrófono', true);

                // Fallback solo video
                try {
                    const fallbackConstraints = {
                        video: {
                            facingMode: {
                                exact: facingMode
                            }
                        },
                        audio: false
                    };
                    stream = await navigator.mediaDevices.getUserMedia(fallbackConstraints);
                    video.srcObject = stream;
                    await video.play();
                    showToast('Sin acceso al micrófono', true);
                } catch (fallbackErr) {
                    console.error("Error fallback:", fallbackErr);
                }
            }
        }

        function updateQualityBadge() {
            const totalPixels = currentResolution.width * currentResolution.height;
            let quality = '';

            if (totalPixels >= 3840 * 2160) quality = '4K UHD';
            else if (totalPixels >= 1920 * 1080) quality = 'Full HD';
            else if (totalPixels >= 1280 * 720) quality = 'HD';
            else quality = 'SD';

            if (qualityBadge) qualityBadge.textContent = quality;
        }

        async function switchCamera() {
            if (window.navigator && window.navigator.vibrate) {
                window.navigator.vibrate(50);
            }

            facingMode = facingMode === 'environment' ? 'user' : 'environment';
            await startCamera();
        }

        function setMode(mode) {
            currentMode = mode;

            const btnPhoto = document.getElementById('btnPhoto');
            const btnVideo = document.getElementById('btnVideo');
            const inner = document.getElementById('innerBtn');

            if (mode === 'photo') {
                btnPhoto.classList.remove('text-white/50');
                btnPhoto.classList.add('text-yellow-400');
                btnVideo.classList.remove('text-yellow-400');
                btnVideo.classList.add('text-white/50');
                inner.classList.remove('bg-red-600');
                inner.classList.add('bg-white');
            } else {
                btnVideo.classList.remove('text-white/50');
                btnVideo.classList.add('text-yellow-400');
                btnPhoto.classList.remove('text-yellow-400');
                btnPhoto.classList.add('text-white/50');
                inner.classList.remove('bg-white');
                inner.classList.add('bg-red-600');
            }
        }

        function takePhoto() {
            if (!video.videoWidth || !video.videoHeight) {
                showToast('La cámara no está lista', true);
                return;
            }

            if (window.navigator && window.navigator.vibrate) {
                window.navigator.vibrate(100);
            }

            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d', {
                alpha: false,
                desynchronized: true
            });

            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(blob => {
                currentBlob = blob;
                showPreview(URL.createObjectURL(blob), 'image');
            }, 'image/jpeg', 0.95);
        }

        function recordVideo() {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                document.getElementById('recBadge').classList.add('hidden');
                showToast('Grabación detenida');
                return;
            }

            if (!stream) {
                showToast('No hay stream disponible', true);
                return;
            }

            recordedChunks = [];

            // Verificar codecs soportados para iOS
            let mimeType = '';
            const videoCodecs = [
                'video/mp4;codecs=h264,aac',
                'video/mp4;codecs=avc1,mp4a',
                'video/mp4',
                'video/webm'
            ];

            for (const type of videoCodecs) {
                if (MediaRecorder.isTypeSupported(type)) {
                    mimeType = type;
                    break;
                }
            }

            console.log('Usando MIME type:', mimeType || 'default');

            const options = mimeType ? {
                mimeType: mimeType
            } : {};

            try {
                mediaRecorder = new MediaRecorder(stream, options);
            } catch (e) {
                console.warn('Error creando MediaRecorder:', e);
                mediaRecorder = new MediaRecorder(stream);
            }

            mediaRecorder.ondataavailable = (e) => {
                if (e.data.size > 0) {
                    recordedChunks.push(e.data);
                }
            };

            mediaRecorder.onstop = () => {
                console.log('Grabación detenida, chunks:', recordedChunks.length);

                let mime = mediaRecorder.mimeType || 'video/mp4';
                const blob = new Blob(recordedChunks, {
                    type: mime
                });
                currentBlob = blob;
                showPreview(URL.createObjectURL(blob), 'video');
            };

            mediaRecorder.onerror = (e) => {
                console.error('Error en MediaRecorder:', e);
                showToast('Error durante la grabación', true);
            };

            // Grabar en intervalos de 1 segundo para mejor compatibilidad
            mediaRecorder.start(1000);
            document.getElementById('recBadge').classList.remove('hidden');
            showToast('Grabando video con audio...');
        }

        function showPreview(src, type) {
            const modal = document.getElementById('previewModal');
            const img = document.getElementById('previewImage');
            const vid = document.getElementById('previewVideo');

            modal.classList.remove('hidden');
            img.classList.add('hidden');
            vid.classList.add('hidden');

            if (type === 'image') {
                img.src = src;
                img.classList.remove('hidden');
            } else {
                vid.src = src;
                vid.load();
                vid.classList.remove('hidden');

                // En iOS, asegurar que el video se puede ver
                vid.addEventListener('loadedmetadata', () => {
                    vid.play().catch(e => console.log('Auto-play preview error:', e));
                });
            }
        }

        function closePreview() {
            const modal = document.getElementById('previewModal');
            const vid = document.getElementById('previewVideo');

            modal.classList.add('hidden');

            if (vid) {
                vid.pause();
                vid.src = '';
                vid.load();
            }

            const img = document.getElementById('previewImage');
            if (img) {
                img.src = '';
            }

            if (currentBlob) {
                URL.revokeObjectURL(currentBlob);
                currentBlob = null;
            }
        }

        function downloadMedia() {
            if (!currentBlob) return;

            if (currentMode === 'photo') {
                // Para fotos, usar descarga directa
                const a = document.createElement('a');
                a.href = URL.createObjectURL(currentBlob);
                a.download = `foto_${Date.now()}.jpg`;
                a.click();
                setTimeout(() => URL.revokeObjectURL(a.href), 100);
                showToast('Foto guardada');
            } else {
                // Para videos en iOS, usar el método share o mostrar instrucciones
                if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
                    showToast('Usa el botón Compartir para guardar en Fotos');
                } else {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(currentBlob);
                    a.download = `video_${Date.now()}.mp4`;
                    a.click();
                    setTimeout(() => URL.revokeObjectURL(a.href), 100);
                    showToast('Video guardado');
                }
            }
        }

        async function shareMedia() {
            if (!currentBlob) return;

            const filename = currentMode === 'photo' ? `foto_${Date.now()}.jpg` : `video_${Date.now()}.mp4`;
            const fileType = currentMode === 'photo' ? 'image/jpeg' : 'video/mp4';

            // Crear archivo
            const file = new File([currentBlob], filename, {
                type: fileType
            });

            // Intentar compartir con el sistema
            if (navigator.share) {
                try {
                    await navigator.share({
                        files: [file],
                        title: 'Captura PAPILIA',
                        text: 'Capturado con PAPILIA 🦋'
                    });
                    showToast('Compartido correctamente');
                } catch (e) {
                    if (e.name !== 'AbortError') {
                        console.error('Error al compartir:', e);
                        // Fallback para iOS - descarga directa
                        fallbackSaveToPhotos();
                    }
                }
            } else {
                // Fallback para navegadores sin share API
                fallbackSaveToPhotos();
            }
        }

        function fallbackSaveToPhotos() {
            if (!currentBlob) return;

            if (currentMode === 'photo') {
                // Descarga normal para fotos
                const a = document.createElement('a');
                a.href = URL.createObjectURL(currentBlob);
                a.download = `foto_${Date.now()}.jpg`;
                a.click();
                setTimeout(() => URL.revokeObjectURL(a.href), 100);
                showToast('Foto guardada');
            } else {
                // Para iOS, crear un enlace temporal
                const blobUrl = URL.createObjectURL(currentBlob);

                if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
                    // Mostrar instrucciones para iOS
                    showToast('Mantén presionado el video y selecciona "Guardar en Fotos"', false);

                    // Abrir el video en una nueva pestaña para facilitar el guardado
                    const previewVideo = document.getElementById('previewVideo');
                    if (previewVideo && previewVideo.src) {
                        // Ya está en preview, el usuario puede mantener presionado
                    } else {
                        window.open(blobUrl, '_blank');
                    }
                } else {
                    const a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = `video_${Date.now()}.mp4`;
                    a.click();
                    setTimeout(() => URL.revokeObjectURL(blobUrl), 100);
                    showToast('Video guardado');
                }
            }
        }

        // ===== Evento del botón de captura =====
        document.getElementById('captureBtn').addEventListener('click', () => {
            if (currentMode === 'photo') {
                takePhoto();
            } else {
                recordVideo();
            }
        });

        // Limpiar recursos al salir
        window.addEventListener('beforeunload', () => {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
            }
        });

        // Iniciar cámara
        startCamera();

        // Prevenir scroll en iOS
        document.body.addEventListener('touchmove', (e) => {
            if (e.target === video || video.contains(e.target)) {
                e.preventDefault();
            }
        }, {
            passive: false
        });
    </script>
</body>

</html>
