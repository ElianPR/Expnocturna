<!DOCTYPE html>
<html lang="es" class="antialiased" x-data="{ darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches }" x-init="window.matchMedia('(prefers-color-scheme: dark)')
    .addEventListener('change', e => darkMode = e.matches)" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Nuestra Canción - {{ $event->name ?? ($event->monogram ?? 'Evento') }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cormorant+Garamond:wght@400;700&family=Great+Vibes&family=Montserrat:wght@400;600;700&display=swap"
        rel="stylesheet">
</head>

@php
    $backgroundMap = [
        1 => asset('images/fondosV/fondoV.png'),
        2 => asset('images/fondosA/fondoA.png'),
        3 => asset('images/fondosD/fondoD.png'),
    ];

    $backgroundImage = $backgroundMap[$event->template] ?? null;

    $colorMap = [
        1 => '#436C00',
        2 => '#092D51',
        3 => '#A8792B',
    ];

    $buttonColor = $colorMap[$event->template] ?? '#000000';

    $eventFont = $event->typography ?? "'Playfair Display', serif";

    $isVideo = preg_match('/\.(mp4|mov|webm)$/i', $event->song);

    $songUrl = $event->song ? route('events.stream-song', bin2hex($event->id)) : null;

    $coverUrl = $event->song_cover ? route('events.stream-cover', bin2hex($event->id)) : null;
@endphp

<body class="min-h-screen flex flex-col items-center justify-center text-neutral-800 dark:text-neutral-200 p-4"
    @if ($backgroundImage) style="
            background-image: url('{{ $backgroundImage }}');
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        " @endif>

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

    <div class="w-full max-w-sm md:max-w-md">

        <div class="rounded-[2.5rem] p-6 md:p-8 flex flex-col items-center">
            <h1 class="text-[3.0rem] md:text-[3.4rem] text-center mb-2"
                style="
                        font-family: {!! $eventFont !!};
                        color: {{ $buttonColor }};
                        line-height: 1.1;
                        letter-spacing: 0.02em;
                        font-weight: 600;
                        text-transform: none;
                    ">
                {{ $event->name ?? ($event->monogram ?? 'Evento') }}
            </h1>

            <p class="text-lg mb-6 text-center" style="color: {{ $buttonColor }}">
                Escucha su canción
            </p>
            @if ($isVideo)

                <div class="w-full mb-6 rounded-2xl overflow-hidden shadow-lg bg-black/80 backdrop-blur-sm">
                    <video controls playsinline class="w-full h-auto object-cover" controlsList="nodownload">

                        <source src="{{ $songUrl }}">
                    </video>
                </div>
            @else
                <div class="rounded-3xl p-4 mb-8 border-[5px] bg-white/70" style="border-color: {{ $buttonColor }}">
                    <div
                        class="relative w-52 h-52 rounded-full overflow-hidden
                        {{ $coverUrl ? 'animate-[spin_20s_linear_infinite]' : '' }}">

                        @if ($coverUrl)
                            <img src="{{ $coverUrl }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-14 h-14 rounded-full bg-white border-4 border-black"></div>
                            </div>
                        @else
                            <div class="w-full h-full bg-black flex items-center justify-center">
                                <flux:icon.musical-note class="size-20 text-white" />
                            </div>
                        @endif
                    </div>
                </div>

                <h2 class="text-xl font-bold mb-3 text-center" style="color: {{ $buttonColor }}">
                    Nombre de la canción
                </h2>

                <p class="text-xl leading-relaxed max-w-xs mb-8 text-center text-neutral-700">
                    Un vuelo nuevo comienza con esta melodía.
                </p>

            @endif

            @if ($songUrl && !$isVideo)
                <div class="w-full mt-2 mb-6">
                    <audio controls class="w-full" controlsList="nodownload">
                        <source src="{{ $songUrl }}">
                    </audio>
                </div>
            @elseif(!$songUrl)
                <div class="text-center py-4 px-6 rounded-2xl bg-white/40 dark:bg-neutral-800/40 backdrop-blur-sm">

                    <flux:icon.speaker-x-mark class="size-6 mx-auto mb-2 opacity-60" />

                    <p class="text-sm opacity-70">
                        Contenido no disponible.
                    </p>

                </div>
            @endif

            <div class="mb-4 mt-2 w-full flex justify-center">
                <flux:button onclick="handleBack('{{ route('events.show', $event->id_hex) }}')"
                    icon="arrow-left-end-on-rectangle" class="w-64 justify-center rounded-xl text-lg font-semibold"
                    style="background-color: {{ $buttonColor }}; color: white;">
                    Regresar
                </flux:button>
            </div>
        </div>
    </div>

    <a href="https://papilia.net/papilia2021/" target="_blank"
        class="mt-2 text-center block text-xs opacity-60 text-black">
        papilia.net
    </a>

    <script>
        function handleBack(fallbackUrl) {
            if (document.referrer && document.referrer !== window.location.href) {
                history.back();

            } else {
                window.location.href = fallbackUrl;

            }
        }
    </script>
    @fluxScripts

</body>

</html>
