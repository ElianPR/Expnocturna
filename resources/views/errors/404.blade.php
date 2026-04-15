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
            max-width: 500px;
            padding: 20px;
        }

        .logo {
            width: 130px;
            margin-bottom: 30px;
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
            font-size: 70px;
            margin: 0;
            font-weight: 600;
            color: #1e88c7;
        }

        p {
            margin-top: 10px;
            font-size: 18px;
            color: #555;
        }

        .link {
            margin-top: 35px;
            display: block;
            font-size: 14px;
            color: #1e88c7;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .link:hover {
            opacity: 0.7;
        }
    </style>
</head>

<body>

    <div class="container">
        <img src="{{ asset('images/logoC.png') }}" alt="Papilia Logo" class="logo">

        <h1>404</h1>

        <p>
            No encontramos esta página
        </p>

        <a href="https://papilia.net/papilia2021/" target="_blank" class="link">
            papilia.net
        </a>

    </div>

</body>

</html>
