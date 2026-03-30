<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir archivos - {{ $event->name ?? 'Evento' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f6f3ee] text-neutral-900">

    <div id="loader" class="fixed inset-0 bg-black/40 flex items-center justify-center hidden z-50">
        <div class="bg-white px-6 py-5 rounded-2xl shadow text-center">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-green-500 border-t-transparent mx-auto">
            </div>
            <p class="mt-3 text-sm text-neutral-700">Subiendo archivos...</p>
        </div>
    </div>

    <div class="mx-auto flex min-h-screen w-full max-w-md items-start justify-center px-4 py-5">
        <section class="w-full overflow-hidden rounded-[2rem] bg-[#f3efe8] shadow-sm ring-1 ring-black/5">
            <div class="px-5 pb-8 pt-7">
                <div class="space-y-2 text-center">
                    <flux:heading level="1" class="text-2xl font-medium">
                        Compartir recuerdos
                    </flux:heading>

                    <flux:text class="text-sm text-neutral-600">
                        {{ $event->name ?? 'Evento sin nombre' }}
                    </flux:text>

                    <flux:text class="text-sm text-neutral-500">
                        Sube fotos y videos de este evento
                    </flux:text>
                </div>

                <div id="formMessage"></div>

                <form id="uploadForm" action="{{ route('events.share.store', $event->id_hex) }}" method="POST"
                    enctype="multipart/form-data" class="mt-6 space-y-5">

                    @csrf

                    <div class="rounded-[1.75rem] bg-[#d9ecfb] px-4 py-4">
                        <div class="space-y-2">
                            <flux:heading level="2" class="text-base font-semibold">
                                Selecciona archivos
                            </flux:heading>

                            <flux:text class="text-sm leading-5 text-neutral-700">
                                Puedes subir fotos o videos de hasta 25 MB por archivo.
                            </flux:text>

                            <input type="file" id="fileInput" multiple accept="image/*,video/*"
                                class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm">

                            <div id="previewContainer" class="mt-4 grid grid-cols-2 gap-3"></div>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] bg-white/70 px-4 py-4">
                        <flux:text class="text-sm text-neutral-600">
                            Los archivos se guardarán en una carpeta privada del evento.
                        </flux:text>
                    </div>

                    <flux:button id="submitBtn" variant="primary" icon="arrow-up-tray" type="submit"
                        class="w-full text-white bg-green-500 hover:bg-green-600">
                        Subir archivos
                    </flux:button>

                    <a href="{{ route('events.show', $event->id_hex) }}"
                        class="inline-flex w-full items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-3 text-sm font-medium text-neutral-800 transition hover:bg-neutral-50">
                        Volver
                    </a>

                </form>
            </div>
        </section>
    </div>

    <script>
        const input = document.getElementById('fileInput');
        const previewContainer = document.getElementById('previewContainer');
        const form = document.getElementById('uploadForm');
        const loader = document.getElementById('loader');
        const messageBox = document.getElementById('formMessage');
        const submitBtn = document.getElementById('submitBtn');

        let filesQueue = [];

        input.addEventListener('change', (e) => {
            const newFiles = Array.from(e.target.files);

            newFiles.forEach(file => filesQueue.push(file));

            renderPreview();
            input.value = '';
        });

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
                    renderPreview();
                };

                wrapper.appendChild(removeBtn);

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.className = "w-full h-32 object-cover rounded";
                    img.src = URL.createObjectURL(file);
                    wrapper.appendChild(img);
                } else if (file.type.startsWith('video/')) {
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
        <div class="mt-4 rounded-2xl bg-${color}-100 px-4 py-3 text-sm text-${color}-800">
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

            filesQueue.forEach(file => {
                formData.append('files[]', file);
            });

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

                    if (!res.ok) {
                        throw new Error('Error servidor');
                    }

                    return res.text();
                })
                .then(() => {
                    showMessage('success', 'Archivos subidos correctamente.');
                    filesQueue = [];
                    renderPreview();
                })
                .catch(() => {
                    showMessage('error', 'Error al subir los archivos.');
                });
        });
    </script>

</body>

</html>
