<!DOCTYPE html>
<html lang="es" class="antialiased" x-data="{ darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches }" x-init="window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => darkMode = e.matches)" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestra Canción - {{ $event->name ?? ($event->monogram ?? 'Evento') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $backgroundMap = [
        1 => asset('images/fondoVM.jpg'),
        2 => asset('images/fondoAM.jpg'),
        3 => asset('images/fondoDM.jpg'),
    ];

    $backgroundImage = $backgroundMap[$event->template] ?? null;

    $colorMap = [
        1 => '#4A720D',
        2 => '#092D9A',
        3 => '#DEAD2B',
    ];

    $buttonColor = $colorMap[$event->template] ?? '#000000';
@endphp

<body class="min-h-screen flex flex-col items-center justify-center text-neutral-800 dark:text-neutral-200 p-4"
    @if ($backgroundImage) style="background-image: url('{{ $backgroundImage }}'); 
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
    <div class="w-full max-w-sm md:max-w-md">

        <div
            class="rounded-[2.5rem] p-6 md:p-8 flex flex-col items-center shadow-2xl
            bg-white/0 dark:bg-neutral-900/0 backdrop-blur-sm border border-white/10">

            @php
                $isVideo = preg_match('/\.(mp4|mov|webm)$/i', $event->song);
                $songUrl = $event->song ? route('events.stream-song', bin2hex($event->id)) : null;
                $coverUrl = $event->song_cover ? route('events.stream-cover', bin2hex($event->id)) : null;
            @endphp

            @if ($isVideo)
                <div class="w-full mb-6 rounded-2xl overflow-hidden shadow-lg bg-black/80 backdrop-blur-sm">
                    <video controls playsinline class="w-full h-auto object-cover" controlsList="nodownload">
                        <source src="{{ $songUrl }}">
                    </video>
                </div>

                <h2 class="text-xl md:text-2xl font-bold text-center mb-2 text-black">
                    Nuestro Video
                </h2>
            @else
                <div
                    class="w-40 h-40 md:w-44 md:h-44 rounded-full shadow-lg flex items-center justify-center mb-6
                    border-8 border-white/40 dark:border-neutral-800/60
                    relative overflow-hidden bg-white/40 dark:bg-neutral-800/40 backdrop-blur-sm
                    {{ $coverUrl ? 'animate-[spin_20s_linear_infinite]' : '' }}">

                    <div class="absolute inset-0 rounded-full border border-white/30 m-4 z-20"></div>

                    @if ($coverUrl)
                        <img src="{{ $coverUrl }}" class="w-full h-full object-cover z-10">
                        <div class="absolute w-8 h-8 bg-white/70 dark:bg-neutral-800/70 rounded-full z-30"></div>
                    @else
                        <flux:icon.musical-note class="size-12 text-neutral-400 z-10" />
                    @endif
                </div>

                <h2 class="text-xl md:text-2xl font-bold text-center mb-2 text-black">
                    Nuestra Canción
                </h2>
            @endif

            <p class="text-xs md:text-sm text-center uppercase tracking-widest mb-6 opacity-70 text-black">
                {{ $event->name ?? ($event->monogram ?? 'Evento') }}
            </p>

            <div class="w-full">
                @if ($songUrl && !$isVideo)
                    <audio controls class="w-full" controlsList="nodownload">
                        <source src="{{ $songUrl }}">
                    </audio>
                @elseif(!$songUrl)
                    <div class="text-center py-4 px-6 rounded-2xl bg-white/40 dark:bg-neutral-800/40 backdrop-blur-sm">
                        <flux:icon.speaker-x-mark class="size-6 mx-auto mb-2 opacity-60" />
                        <p class="text-sm opacity-70">Contenido no disponible.</p>
                    </div>
                @endif
            </div>

            <div class="mb-4 mt-3">
                <flux:button href="javascript:history.back()" icon="arrow-left-end-on-rectangle"
                    class="w-full justify-center transition-all duration-300 hover:opacity-90"
                    style="background-color: {{ $buttonColor }}; color: white;">
                    Regresar
                </flux:button>
            </div>

        </div>
    </div>

    <a href="https://papilia.net/papilia2021/" target="_blank"
        class="mt-6 text-center italic block text-sm opacity-70 hover:opacity-100 transition text-black">
        papilia.net
    </a>

    @fluxScripts
</body>

</html>
