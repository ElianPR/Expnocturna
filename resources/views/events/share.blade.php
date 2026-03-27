<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir archivos - {{ $event->name ?? 'Evento' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f6f3ee] text-neutral-900">
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

                @if (session('status'))
                    <div class="mt-4 rounded-2xl bg-green-100 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-4 rounded-2xl bg-red-100 px-4 py-3 text-sm text-red-800">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    action="{{ route('events.share.store', $event->id_hex) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="mt-6 space-y-5"
                >
                    @csrf

                    <div class="rounded-[1.75rem] bg-[#d9ecfb] px-4 py-4">
                        <div class="space-y-2">
                            <flux:heading level="2" class="text-base font-semibold">
                                Selecciona archivos
                            </flux:heading>

                            <flux:text class="text-sm leading-5 text-neutral-700">
                                Puedes subir fotos o videos de hasta 25 MB por archivo.
                            </flux:text>

                            <input
                                type="file"
                                name="files[]"
                                multiple
                                accept="image/*,video/*"
                                class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm"
                            >
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] bg-white/70 px-4 py-4">
                        <flux:text class="text-sm text-neutral-600">
                            Los archivos se guardarán en una carpeta privada del evento dentro del proyecto.
                        </flux:text>
                    </div>

                    <flux:button
                        type="submit"
                        variant="primary"
                        icon="arrow-up-tray"
                        class="w-full text-white bg-green-500 hover:bg-green-600"
                    >
                        Subir archivos
                    </flux:button>

                    <a
                        href="{{ route('events.show', $event->id_hex) }}"
                        class="inline-flex w-full items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-3 text-sm font-medium text-neutral-800 transition hover:bg-neutral-50"
                    >
                        Volver
                    </a>
                </form>
            </div>
        </section>
    </div>
</body>
</html>