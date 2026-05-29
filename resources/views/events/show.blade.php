<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $event->name ?: 'Evento' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cormorant+Garamond:wght@400;700&family=Great+Vibes&family=Montserrat:wght@400;600;700&display=swap"
        rel="stylesheet">

    @php
        $backgroundMap = [
            1 => asset('images/fondosV/fondoV.png'),
            2 => asset('images/fondosA/fondoA.png'),
            3 => asset('images/fondosD/fondoD.png'),
        ];

        $colorMap = [
            1 => '#436C00',
            2 => '#092D51',
            3 => '#A8792B',
        ];

        $backgroundImage = $backgroundMap[$event->template] ?? $backgroundMap[1];

        $themeColor = $colorMap[$event->template] ?? '#436C00';

        $eventFont = $event->typography ?? "'Playfair Display', serif";
    @endphp

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: #000;
        }

        #loader {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 9999;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        #loader img {
            max-width: 100vw;
            max-height: 100vh;

            width: auto;
            height: auto;

            object-fit: contain;
        }

        #mainContent {
            display: none;
        }

        #termsModal {
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(10px);
        }

        .terms-card {
            width: 100%;
            max-width: 390px;
            min-height: 720px;

            position: relative;

            background-image: url('{{ $backgroundImage }}');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;

            border-radius: 0;
            overflow: hidden;

            display: flex;
            flex-direction: column;
            align-items: center;

            padding:
                70px 28px 30px;
        }

        .event-title {
            font-family: {!! $eventFont !!};
            color: {{ $themeColor }};
            font-size: clamp(2.5rem, 7vw, 4.2rem);
            line-height: 1.05;
            letter-spacing: 0.02em;
            font-weight: 550;
            text-align: center;
            text-wrap: balance;
            max-width: 90%;
            margin-inline: auto;
            word-break: break-word;
            margin: 0;
        }

        .event-date {
            margin-top: 10px;
            font-size: 1.1rem;
            color: {{ $themeColor }};
            font-weight: 500;
            text-align: center;
        }

        .terms-box {
            margin-top: 90px;

            width: 100%;
            border: 6px solid {{ $themeColor }};
            border-radius: 18px;

            background: rgba(255, 255, 255, 0.18);

            backdrop-filter: blur(3px);

            padding: 26px 18px 24px;
        }

        .terms-btn {
            width: 100% !important;
            justify-content: center !important;

            border-radius: 12px !important;

            background: {{ $themeColor }} !important;
            color: white !important;

            min-height: 88px !important;

            font-size: 1.05rem !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;

            text-align: center !important;

            box-shadow: none !important;
        }

        .terms-link {
            margin-top: 16px;
            display: block;
            text-align: center;

            font-size: .92rem;
            text-decoration: none;

            color: {{ $themeColor }};
        }

        .terms-link:hover {
            opacity: .8;
        }

        @media (max-width: 480px) {
            .terms-card {
                min-height: 100vh;
                max-width: 100%;
                border-radius: 0;
            }

            .event-title {
                font-size: 3.7rem;
            }

            .terms-box {
                margin-top: 70px;
            }
        }
    </style>
</head>

<body>
    <div id="loader">
        <img src="{{ asset('videos/simulacion.gif') }}" alt="">
    </div>

    <div id="termsModal">

        <div class="terms-card">

            <h1 class="event-title">
                {{ $event->name }}
            </h1>

            <div class="event-date">
                {{ \Carbon\Carbon::parse($event->date)->locale('es')->translatedFormat('j F Y') }}
            </div>

            <div class="terms-box">

                <flux:button class="terms-btn" onclick="acceptTerms()">
                    ACEPTAR <br>
                    Términos y Condiciones <br>
                    CONTINUAR
                </flux:button>

                <a href="{{ route('terms', $event->id_hex) }}" target="_blank" class="terms-link">
                    Consulta Términos y Condiciones.
                </a>

            </div>

            <a href="https://papilia.net/papilia2021/" target="_blank"
                class="relative z-20 text-center mt-10 md:mt-14 lg:mt-16 block"
                style="color: #4a4a4a; font-size: clamp(0.9rem, 1.8vw, 1.2rem); font-family: 'Poppins', sans-serif;">
                papilia.net
            </a>

        </div>

    </div>

    {{-- CONTENIDO --}}
    <div id="mainContent">

        @if ($event->template == 1)
            <x-templates.papilia :event="$event" :imageUrl="$imageUrl" />
        @elseif($event->template == 2)
            <x-templates.dos :event="$event" :imageUrl="$imageUrl" />
        @elseif($event->template == 3)
            <x-templates.tres :event="$event" :imageUrl="$imageUrl" />
        @else
            <div style="text-align:center;padding:50px">
                <h2>Esta plantilla está en construcción</h2>
            </div>
        @endif

    </div>

    <script>
        const loader = document.getElementById('loader');
        const content = document.getElementById('mainContent');
        const modal = document.getElementById('termsModal');

        const storageKey = "papilia_terms_event_{{ $event->id_hex }}";

        function showModal() {
            modal.style.display = "flex";
        }

        function showContent() {

            loader.style.opacity = '0';

            setTimeout(() => {

                loader.style.display = 'none';

                const accepted = localStorage.getItem(storageKey);

                if (accepted === "accepted") {

                    content.style.display = 'block';

                } else {

                    showModal();

                }

            }, 450);
        }

        function acceptTerms() {

            localStorage.setItem(
                storageKey,
                "accepted"
            );

            modal.style.display = 'none';

            content.style.display = 'block';
        }

        setTimeout(
            showContent,
            7000
        );
    </script>

    @fluxScripts

</body>

</html>
