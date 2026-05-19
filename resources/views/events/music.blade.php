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
                        color: {{ $event->template == 1 ? '#595959' : $buttonColor }};
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
                <div class="mb-8">
                    <div class="relative w-56 h-56 rounded-3xl overflow-hidden shadow-lg"
                        style="background-color: {{ $buttonColor }}">

                        @if ($coverUrl)
                            <img src="{{ $coverUrl }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    preserveAspectRatio="xMidYMid meet" version="1.0" viewBox="454.0 133.9 459.0 516.7"
                                    class="w-36 h-36" fill="white">
                                    <g id="__id1_suvaoyauuc">
                                        <path
                                            d="m494.95 649.51c-29.448-6.6616-46.115-32.183-39.459-60.421 5.7887-24.558 26.696-45.165 55.459-54.661 7.7192-2.5486 9.832-2.8085 23-2.8294 14.342-0.0227 18.049 0.61833 28.25 4.8847 1.6607 0.69457 1.75-5.0796 1.75-113.16v-113.9l25.75-7.2808c14.162-4.0044 50.5-14.213 80.75-22.685 30.25-8.4723 77.926-21.927 105.95-29.899s51.06-14.383 51.197-14.245 0.32085 64.586 0.40764 143.22c0.14453 130.94 0.0166 143.51-1.5208 149.4-6.8117 26.087-32.304 48.753-62.031 55.155-3.3 0.71064-10.5 1.2922-16 1.2923-16.788 3.9e-4 -29.307-4.5604-40.06-14.594-21.498-20.061-19.857-52.489 3.9245-77.53 11.06-11.647 24.933-20.057 40.344-24.46 6.3043-1.8012 10.273-2.219 20.791-2.1891 12.61 0.0358 17.144 0.86299 26.75 4.8806 1.6574 0.69321 1.75-3.161 1.75-72.875 0-42.627-0.37655-73.607-0.89466-73.607-0.49207 0-3.7546 0.87269-7.25 1.9393-3.4954 1.0666-30.655 8.7438-60.355 17.06s-73.924 20.719-98.276 27.561c-24.352 6.8417-44.477 12.44-44.723 12.44-0.24583 0-0.57148 46.912-0.72366 104.25l-0.27669 104.25-2.2048 5.7753c-5.7451 15.049-15.73 27.939-28.947 37.37-7.3657 5.2561-20.559 11.57-28.848 13.805-8.6461 2.3317-26.456 2.8781-34.5 1.0585zm383.6-375.74c-3.6503-13.204-8.2339-17.751-22.224-22.045l-7.8803-2.4186 4.5-0.76891c7.3878-1.2624 16.381-5.8552 19.518-9.9678 1.5509-2.0333 4.0114-7.0731 5.468-11.2l2.6482-7.5027 1.6311 5.8184c3.5475 12.655 9.9875 18.695 23.622 22.158l7.1879 1.8254-5.5084 1.2327c-3.0296 0.678-8.167 2.5612-11.416 4.185-6.9848 3.4903-10.476 8.1353-13.666 18.183l-2.1481 6.7656zm-89.604-47.815c0-5.1896-4.4456-10.174-11.443-12.83l-4.6654-1.7709 4.232-1.268c7.4367-2.2281 9.1094-4.0605 12.398-13.581 0.55778-1.6149 0.95923-1.1053 2.0849 2.6465 1.6818 5.6057 6.7894 10.462 12.039 11.447l3.688 0.69188-5.1228 1.911c-5.8976 2.2-9.8475 6.3519-10.782 11.333-0.70425 3.754-2.4285 4.7628-2.4285 1.4209zm47.334-18.036c-4.3334-20.438-14.298-30.18-35.142-34.356l-5.6105-1.1239s5.6864-0.80614 8.1361-2.1776c12.784-7.1573 26.396-11.732 31.883-30.257 1.222-4.125 2.4291-6.825 2.6825-6s1.2402 4.2 2.1928 7.5c3.4269 11.87 9.4055 19.848 18.259 24.365 3.5108 1.7911 10.215 3.9218 17.765 5.6456 2.6935 0.61503 2.2338 0.8797-4.5 2.5906-10.43 2.65-19.242 7.1952-23.425 12.083-3.4707 4.0548-7.283 12.706-9.7964 22.23l-1.2967 4.914z"
                                            style="fill: rgb(255, 255, 255);" />
                                    </g>
                                </svg>
                            </div>
                        @endif

                    </div>
                </div>
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
