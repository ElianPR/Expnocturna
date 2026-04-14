<!DOCTYPE html>
<html lang="es" class="antialiased" 
      x-data="{ darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches }" 
      x-init="window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => darkMode = e.matches)" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gracias por acompañarnos</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <script>
        function updateTheme() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        updateTheme();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateTheme);
    </script>
</head>

<body class="min-h-screen flex items-center justify-center bg-neutral-50 dark:bg-neutral-950 text-neutral-800 dark:text-neutral-200 p-6 transition-colors duration-300">

    <div class="w-full max-w-lg text-center flex flex-col items-center">
        
        <flux:icon.heart class="size-12 text-[#bfa472] dark:text-[#d1b88a] mb-6 opacity-80" stroke-width="1.5" />

        <h1 class="text-4xl sm:text-5xl font-serif text-[#bfa472] mb-4" style="font-family: 'Playfair Display', serif;">
            Gracias por acompañarnos
        </h1>
        
        <p class="text-lg text-neutral-500 dark:text-neutral-400 mb-8 font-light" style="font-family: 'Cinzel', serif;">
            {{ $event->name ?? $event->monogram ?? 'Nuestro Evento' }}
        </p>

        <div class="w-16 h-[1px] bg-neutral-300 dark:bg-neutral-700 mb-8"></div>

        

        <div class="mt-16 text-center text-[12px] italic text-neutral-400 dark:text-neutral-600" style="font-family: 'Playfair Display', serif;">
            papilia.net
        </div>

    </div>

    @fluxScripts
</body>
</html>