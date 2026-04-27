<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $event->name ?: 'Evento' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: #22A5E8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Montserrat, sans-serif;
        }

        #loader {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .45s ease;
        }

        #loader img {
            height: 100%;
            max-width: 480px;
            width: auto;
            object-fit: cover;
            display: block;
        }

        #mainContent {
            display: none;
        }

        #termsModal {
            position: fixed;
            inset: 0;
            background: rgba(10, 20, 35, .58);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        }

        .modal-box {
            background: white;
            max-width: 520px;
            width: 100%;
            border-radius: 28px;
            padding: 38px 34px;
            box-shadow:
                0 20px 70px rgba(0, 0, 0, .18);
            text-align: center;
        }

        .logo-box {
            width: 90px;
            height: 90px;
            margin: auto;
            margin-bottom: 20px;
            background: #f5fbff;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d9eefb;
        }

        .logo-box img {
            width: 58px;
        }

        .modal-box h2 {
            margin: 0 0 14px;
            font-size: 30px;
            color: #111827;
        }

        .modal-box p {
            line-height: 1.7;
            color: #4b5563;
            margin-bottom: 28px;
        }

        .notice {
            background: #f0f9ff;
            border: 1px solid #d8effb;
            padding: 16px;
            border-radius: 16px;
            font-size: 14px;
            margin-bottom: 28px;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        button {
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: .2s;
        }

        .accept {
            background: var(--brand);
            color: white;
            padding: 15px;
            font-weight: 600;
            border-radius: 14px;
        }

        .accept:hover {
            transform: translateY(-1px);
        }

        .read {
            background: white;
            border: 1px solid #d6d6d6;
            padding: 14px;
            border-radius: 14px;
            font-weight: 500;
        }

        .decline {
            background: #f4f4f5;
            padding: 14px;
            border-radius: 14px;
            color: #444;
        }

        @media (max-width:600px) {
            .modal-box {
                padding: 28px 24px;
            }

            .modal-box h2 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

    <div id="loader">
        <img src="{{ asset('videos/simulacion.gif') }}">
    </div>

    <div id="termsModal">
        <div class="modal-box">

            <div class="logo-box">
                <img src="{{ asset('images/logoC.png') }}">
            </div>

            <h2>Antes de continuar</h2>

            <p>
                Para usar Experiencias Papilia es necesario aceptar
                los Términos y Condiciones y el Aviso de Privacidad.
            </p>

            <div class="notice">
                Al continuar autorizas el uso de la plataforma
                conforme a las políticas aplicables del evento.
            </div>

            <div class="actions">
                <button class="read" onclick="window.open('{{ route('terms') }}','_blank')">
                    Consultar términos y condiciones
                </button>

                <button class="accept" onclick="acceptTerms()">
                    Aceptar y continuar
                </button>

                <button class="decline" onclick="declineTerms()">
                    No aceptar
                </button>
            </div>
        </div>
    </div>

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

        const storageKey = "papilia_terms_event_{{ $event->id }}";


        function showModal() {
            modal.style.display = "flex";
        }

        function showContent() {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
                let accepted = localStorage.getItem(storageKey);
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

        function declineTerms() {
            window.location.href =
                "https://papilia.net/papilia2021/";
        }

        setTimeout(
            showContent,
            7000
        );
    </script>

</body>

</html>
