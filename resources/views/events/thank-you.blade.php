<!DOCTYPE html>
<html lang="es" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gracias por ser parte de la magia</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">

</head>

<body class="min-h-screen flex items-center justify-center bg-[#d7eaf8] text-[#092d51] p-6" style="font-family: 'Montserrat', sans-serif;">

    <div class="w-full max-w-md text-center flex flex-col items-center relative z-10">
        
        <!-- Logo Top -->
        <img src="{{ asset('images/logo.svg') }}" alt="Granja Papilia" class="h-24 w-auto mb-8 drop-shadow-sm">

        <!-- Title -->
        <h1 class="text-3xl sm:text-4xl font-extrabold text-[#092d51] mb-8 leading-tight">
            ¡Gracias por ser parte<br>de la magia!
        </h1>
        
        <!-- Center Image (Candado) -->
        <div class="relative w-full flex justify-center mb-10">
            <img src="{{ asset('images/candado.png') }}" alt="Candado Mariposa" class="w-72 sm:w-80 h-auto max-w-full drop-shadow-xl object-contain">
        </div>

        <!-- Subtitle -->
        <p class="text-lg sm:text-xl font-bold text-[#092d51] mb-10 leading-snug">
            Por el momento este espacio aún no está disponible.
        </p>

        <!-- Footer -->
        <div class="mt-4 text-center font-medium text-[#092d51]">
            <a href="https://papilia.net" target="_blank" class="hover:underline transition-all">papilia.net</a>
        </div>

    </div>

    @fluxScripts
</body>
</html>