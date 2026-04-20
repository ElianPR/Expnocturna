<!DOCTYPE html>
<html lang="es" class="antialiased" x-data="{ darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches }" x-init="window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => darkMode = e.matches)" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir archivos - {{ $event->name ?? 'Evento' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Great+Vibes&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">
</head>

@php
    $templates = [
        1 => [
            'bg' => asset('images/fondoCV.png'),
            'button' => '#4A720D',
            'card' => '#CCD9B7',
            'text' => '#426B00',
            'textName' => '#000000',
        ],
        2 => [
            'bg' => asset('images/fondoCA.png'),
            'button' => '#092D51',
            'card' => '#BFC5DE',
            'text' => '#092D51',
            'textName' => '#828189',
        ],
        3 => [
            'bg' => asset('images/fondoCD.png'),
            'button' => '#A8792B',
            'card' => '#F5E9DB',
            'text' => '#8F6827',
            'textName' => '#B4976E',
        ],
    ];

    $theme = $templates[$event->template] ?? [
        'bg' => null,
        'button' => '#000000',
        'card' => '#ffffff',
        'text' => '#000000',
    ];
@endphp

<body class="min-h-screen text-neutral-800 p-4"
    @if ($theme['bg']) style="background-image: url('{{ $theme['bg'] }}');
               background-position: center;
               background-repeat: no-repeat;
               background-attachment: fixed;" @endif>

    <style>
        @media (min-width: 768px) {
            body {
                background-size: contain !important;
            }
        }

        @media (max-width: 767px) {
            body {
                background-size: cover !important;
            }
        }
    </style>

    <!-- Loader -->
    <div id="loader" class="fixed inset-0 bg-black/40 flex items-center justify-center hidden z-50">
        <div class="bg-white px-6 py-5 rounded-2xl shadow text-center">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-t-transparent mx-auto"
                style="border-color: {{ $theme['button'] }}; border-top-color: transparent;"></div>
            <p class="mt-3 text-sm text-neutral-700">Subiendo archivos...</p>
        </div>
    </div>

    <div class="mx-auto flex min-h-screen w-full max-w-sm flex-col items-center pt-6 pb-10">

        <!-- Header -->
        <div class="w-full text-center mb-2">
            <flux:heading level="1" class="text-xl font-bold italic" style="color: {{ $theme['text'] }};">
                Compartir recuerdos
            </flux:heading>

            <flux:text class="text-sm" style="color: {{ $theme['text'] }};">
                Sube fotos o videos de este evento.
            </flux:text>
        </div>

        <div class="w-full text-center my-6">
            <h2 class="text-5xl md:text-6xl tracking-normal"
                style="font-family: {{ $event->typography ?? "'Cinzel', serif" }};
                       color: {{ $theme['textName'] }};">

                {{ $event->name ?? ($event->monogram ?? 'Evento') }}
            </h2>

            @if (!empty($event->date))
                <p class="text-sm uppercase tracking-widest mt-2" style="color: {{ $theme['text'] }}; opacity: 0.6;">
                    {{ \Carbon\Carbon::parse($event->date)->translatedFormat('j \d\e F \d\e Y') }}
                </p>
            @endif
        </div>

        <div id="formMessage" class="w-full"></div>

        <!-- Form -->
        <form id="uploadForm" action="{{ route('events.share.store', $event->id_hex) }}" method="POST"
            enctype="multipart/form-data" class="w-full space-y-4 mt-2">

            @csrf

            <!-- Card -->
            <div class="rounded-2xl px-5 py-5 space-y-3 shadow-sm" style="background-color: {{ $theme['card'] }};">

                <flux:heading level="3" class="text-base font-semibold text-neutral-800">
                    Selecciona archivos
                </flux:heading>

                <flux:text class="text-sm leading-5 text-neutral-600">
                    Puedes subir fotos o videos de hasta 25 MB por archivo.
                </flux:text>

                <label
                    class="flex items-center justify-center w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-500 cursor-pointer hover:bg-neutral-50 transition">
                    <input type="file" id="fileInput" multiple accept="image/*,video/*" class="sr-only">
                    <span id="fileLabel">Elegir archivos. Sin archivos seleccionados</span>
                </label>

                <div id="previewContainer" class="grid grid-cols-2 gap-3"></div>
            </div>

            <div class="rounded-2xl bg-white/40 px-5 py-3 shadow-sm">
                <flux:text class="text-xs italic text-black/60 text-center">
                    Los archivos se guardan en una carpeta privada del evento.
                </flux:text>
            </div>

            <flux:button id="submitBtn" type="submit" icon="arrow-up-tray"
                class="w-full justify-center !text-white transition-all duration-300 hover:opacity-90"
                style="background-color: {{ $theme['button'] }};">
                Subir archivos
            </flux:button>

            <flux:button onclick="handleBack('{{ route('events.show', $event->id_hex) }}')"
                icon="arrow-left-end-on-rectangle"
                class="w-full justify-center !text-white transition-all duration-300 hover:opacity-90"
                style="background-color: {{ $theme['button'] }};">
                Regresar
            </flux:button>

        </form>

        <a href="https://papilia.net/papilia2021/" target="_blank"
            class="mt-8 text-center italic block text-sm text-black/60 hover:text-black/90 transition">
            papilia.net
        </a>

    </div>

    <script>
        function handleBack(fallbackUrl) {
            if (document.referrer && document.referrer !== window.location.href) {
                history.back();
            } else {
                window.location.href = fallbackUrl;
            }
        }
    </script>

    <script>
        const input = document.getElementById('fileInput');
        const fileLabel = document.getElementById('fileLabel');
        const previewContainer = document.getElementById('previewContainer');
        const form = document.getElementById('uploadForm');
        const loader = document.getElementById('loader');
        const messageBox = document.getElementById('formMessage');
        const submitBtn = document.getElementById('submitBtn');

        let filesQueue = [];

        input.addEventListener('change', (e) => {
            const newFiles = Array.from(e.target.files);
            newFiles.forEach(file => filesQueue.push(file));
            updateLabel();
            renderPreview();
            input.value = '';
        });

        function updateLabel() {
            fileLabel.textContent = filesQueue.length > 0 ?
                `${filesQueue.length} archivo${filesQueue.length > 1 ? 's' : ''} seleccionado${filesQueue.length > 1 ? 's' : ''}` :
                'Elegir archivos. Sin archivos seleccionados';
        }

        function renderPreview() {
            previewContainer.innerHTML = '';

            filesQueue.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = "relative bg-white rounded-xl p-2 shadow overflow-hidden";

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '✕';
                removeBtn.type = 'button';
                removeBtn.className =
                    "absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 text-xs z-10";

                removeBtn.onclick = (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    filesQueue.splice(index, 1);
                    updateLabel();
                    renderPreview();
                };

                wrapper.appendChild(removeBtn);

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.className = "w-full h-32 object-cover rounded";
                    img.src = URL.createObjectURL(file);
                    wrapper.appendChild(img);
                } else {
                    const video = document.createElement('video');
                    video.className = "w-full h-32 object-cover rounded";
                    video.src = URL.createObjectURL(file);
                    video.controls = true;
                    wrapper.appendChild(video);
                }

                previewContainer.appendChild(wrapper);
            });
        }

        function showMessage(type, text) {
            const color = type === 'success' ? 'green' : 'red';
            messageBox.innerHTML = `
                <div class="mb-2 rounded-2xl bg-${color}-100 px-4 py-3 text-sm text-${color}-800">
                    ${text}
                </div>
            `;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (filesQueue.length === 0) {
                showMessage('error', 'Debes seleccionar al menos un archivo.');
                return;
            }

            loader.classList.remove('hidden');
            submitBtn.disabled = true;

            const formData = new FormData();
            filesQueue.forEach(file => formData.append('files[]', file));

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(async res => {
                    loader.classList.add('hidden');
                    submitBtn.disabled = false;
                    if (!res.ok) throw new Error('Error servidor');
                    return res.text();
                })
                .then(() => {
                    showMessage('success', 'Archivos subidos correctamente.');
                    filesQueue = [];
                    updateLabel();
                    renderPreview();
                })
                .catch(() => {
                    showMessage('error', 'Error al subir los archivos.');
                });
        });
    </script>

    @fluxScripts
</body>

</html>
