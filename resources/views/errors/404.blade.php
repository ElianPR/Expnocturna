<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #ffffff;
            color: #2c2c2c;
        }

        .container {
            max-width: 520px;
            padding: 20px;
        }

        .logo {
            width: 70%;
            height: auto;
            margin-bottom: 30px;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));
            animation: float 3.5s ease-in-out infinite, fadeIn 1.2s ease forwards;
            opacity: 0;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        h1 {
            font-size: clamp(50px, 8vw, 70px);
            margin: 0;
            font-weight: 600;
            color: #1e88c7;
        }

        p {
            margin-top: 14px;
            font-size: clamp(18px, 2.8vw, 20px);
            color: #092D51;
            font-weight: 500;
            line-height: 1.5;
        }

        .link {
            margin-top: 35px;
            display: block;
            font-size: clamp(13px, 2.5vw, 14px);
            color: #1e88c7;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .link:hover {
            opacity: 0.7;
        }
    </style>
</head>

@php
    $options = [
        [
            'img' => 'images/4041.png',
            'text' => '¡Oh, no! La página que buscas ha volado a otro lugar.',
        ],
        [
            'img' => 'images/4042.png',
            'text' => 'Metamorfosis fallida: Esta página no pudo transformarse',
        ],
        [
            'img' => 'images/4043.png',
            'text' => 'Parece que te has desviado del camino.',
        ],
    ];

    $random = $options[array_rand($options)];
@endphp

<body>

    <div class="container">
        <img src="{{ asset($random['img']) }}" alt="404 Imagen" class="logo">

        <p>
            {{ $random['text'] }}
        </p>

        <a href="https://papilia.net/papilia2021/" target="_blank" class="link">
            papilia.net
        </a>
    </div>

</body>

</html>
