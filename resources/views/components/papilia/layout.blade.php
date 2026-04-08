@props(['preview' => false, 'title' => 'Experiencia Papilia', 'fontFamily' => 'Cinzel', 'bgStyle' => "background-color: #f7f5f0; background-image: url('/images/fondo-papel.jpg');"])

@php
    $isPreview = filter_var($preview, FILTER_VALIDATE_BOOLEAN);
@endphp

@if(!$isPreview)
<!DOCTYPE html>
<html lang="es" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fontFamily) }}:wght@400;600;700&family=Montserrat:ital,wght@0,400;0,500;1,500&display=swap" rel="stylesheet">
    
    <style>
        .font-nombres { font-family: '{{ $fontFamily }}', serif; }
        .font-base { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="font-base text-neutral-800 min-h-screen bg-neutral-900 flex justify-center w-full relative">
@endif

    <div class="{{ $isPreview ? 'w-full min-h-full' : 'w-full max-w-full sm:max-w-md md:max-w-2xl lg:max-w-4xl min-h-screen shadow-2xl' }} relative flex flex-col overflow-x-hidden mx-auto" 
         style="{{ $bgStyle }} background-size: cover; background-position: center;">
        
        <div class="absolute inset-0 bg-white/40 z-0 pointer-events-none"></div>

        <div class="relative z-10 w-full flex flex-col min-h-full py-10">
            {{ $slot }}
        </div>
    </div>

@if(!$isPreview)
    @fluxScripts
</body>
</html>
@endif