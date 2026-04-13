<!DOCTYPE html>
<html lang="es" class="antialiased" 
      x-data="{ darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches }" 
      x-init="window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => darkMode = e.matches)" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestra Canción - {{ $event->name ?? $event->monogram ?? 'Evento' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

<body class="min-h-screen flex items-center justify-center bg-neutral-100 dark:bg-neutral-950 text-neutral-800 dark:text-neutral-200 p-4 transition-colors duration-300">

    <div class="w-full max-w-sm bg-white dark:bg-neutral-900 rounded-[2.5rem] shadow-2xl overflow-hidden border border-neutral-200/50 dark:border-neutral-800 p-8 flex flex-col items-center relative">

        <a href="javascript:history.back()" class="absolute top-6 left-6 flex items-center justify-center size-10 rounded-full bg-neutral-100 dark:bg-neutral-800 text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-all">
            <flux:icon.arrow-left class="size-5" stroke-width="2.5" />
        </a>

        <div class="w-36 h-36 rounded-full bg-neutral-50 dark:bg-neutral-800 shadow-inner flex items-center justify-center mt-10 mb-8 border-8 border-neutral-100 dark:border-neutral-800 relative">
            
            <div class="absolute inset-0 rounded-full border border-neutral-200 dark:border-neutral-700 m-4 pointer-events-none"></div>
            
            <flux:icon.musical-note class="size-12 text-neutral-300 dark:text-neutral-600" />
        </div>

        <h2 class="text-2xl font-bold tracking-tight text-center mb-1">Nuestra Canción</h2>
        <p class="text-sm font-medium text-neutral-400 dark:text-neutral-500 text-center mb-8 uppercase tracking-widest">
            {{ $event->name ?? $event->monogram ?? 'Evento' }}
        </p>

        <div class="w-full">
            @php
                // Aquí recibes la URL de la canción desde tu controlador
                // Puede ser algo como $songUrl = asset('storage/' . $event->song_path);
                $songUrl = $songUrl ?? null; // Variable de ejemplo
            @endphp

            @if($songUrl)
                <audio controls class="w-full outline-none" controlsList="nodownload">
                    <source src="{{ $songUrl }}" type="audio/mpeg">
                    <source src="{{ $songUrl }}" type="audio/wav">
                    Tu navegador no soporta el reproductor de audio.
                </audio>
            @else
                <div class="text-center py-4 px-6 bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl border border-neutral-200 dark:border-neutral-800">
                    <flux:icon.speaker-x-mark class="size-6 text-neutral-400 mx-auto mb-2" />
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">La canción aún no está disponible.</p>
                </div>
            @endif
        </div>

    </div>

    @fluxScripts
</body>
</html>