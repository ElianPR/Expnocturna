<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name ?? 'Evento' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f6f3ee] text-neutral-900">
    <div class="mx-auto flex min-h-screen w-full max-w-md items-start justify-center px-4 py-5">
        <section class="w-full overflow-hidden rounded-[2rem] bg-[#f3efe8] shadow-sm ring-1 ring-black/5">

            <div class="px-5 pb-8 pt-7">
                <div class="space-y-3 text-center">
                    <flux:heading level="1"
                        class="text-[2.2rem] font-medium leading-none tracking-tight text-neutral-900">
                        {{ $event->name ?? 'Evento sin nombre' }}
                    </flux:heading>

                    <flux:text class="text-sm font-medium uppercase tracking-[0.18em] text-neutral-500">
                        {{ optional($event->date)->translatedFormat('d \d\e F \d\e Y') }}
                    </flux:text>
                </div>

                <div class="mt-6 flex justify-center">
                    <img src="{{ asset('images/portada.png') }}" alt="Portada del evento"
                        class="h-auto max-h-[320px] w-auto max-w-full object-contain">
                </div>

                <div class="mt-6 space-y-4">
                    <div class="rounded-[1.75rem] bg-[#d9ecfb] px-4 py-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/70 text-xl">
                                🦋
                            </div>

                            <div class="min-w-0">
                                <flux:heading level="2" class="text-base font-semibold text-neutral-900">
                                    Vive la experiencia PAPILIA
                                </flux:heading>

                                <flux:text class="mt-1 text-sm leading-5 text-neutral-700">
                                    Toma imágenes y videos con mariposas
                                </flux:text>

                                <flux:button :href="route('events.camera', $event->id_hex)" icon="camera"
                                    class="w-full mt-3 rounded-full bg-purple-600 hover:bg-purple-700 text-white">
                                    Abrir cámara
                                </flux:button>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] bg-[#d9ecfb] px-4 py-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/70 text-xl">
                                🎵
                            </div>

                            <div class="min-w-0">
                                <flux:heading level="2" class="text-base font-semibold text-neutral-900">
                                    Escucha su canción
                                </flux:heading>

                                <flux:text class="mt-1 text-sm leading-5 text-neutral-700">
                                    Próximamente disponible
                                </flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <flux:button variant="primary"
                        class="w-full justify-center rounded-full py-3 text-sm font-medium text-white bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:ring-cyan-300"
                        :href="route('events.share.create', $event->id_hex)" icon="share">
                        Compartir
                    </flux:button>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
