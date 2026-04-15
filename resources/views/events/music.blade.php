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
</head>

<body class="min-h-screen flex items-center justify-center bg-neutral-100 dark:bg-neutral-950 text-neutral-800 dark:text-neutral-200 p-4 transition-colors duration-300">

    <div class="w-full max-w-sm bg-white dark:bg-neutral-900 rounded-[2.5rem] shadow-2xl overflow-hidden border border-neutral-200/50 dark:border-neutral-800 p-8 flex flex-col items-center relative">

        <a href="javascript:history.back()" class="absolute top-6 left-6 z-50 flex items-center justify-center size-10 rounded-full bg-neutral-100/80 dark:bg-neutral-800/80 backdrop-blur-md text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 transition-all">
            <flux:icon.arrow-left class="size-5" stroke-width="2.5" />
        </a>

        @php
            $isVideo = preg_match('/\.(mp4|mov|webm)$/i', $event->song);
            $songUrl = $event->song ? route('events.stream-song', bin2hex($event->id)) : null;
            $coverUrl = $event->song_cover ? route('events.stream-cover', bin2hex($event->id)) : null;
        @endphp

        @if($isVideo)
            <div class="w-full mt-10 mb-6 rounded-2xl overflow-hidden shadow-lg bg-black">
                <video controls playsinline class="w-full h-auto object-cover" controlsList="nodownload">
                    <source src="{{ $songUrl }}">
                    Tu navegador no soporta videos.
                </video>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-center mb-1">Nuestro Video</h2>
        @else
            <div class="w-40 h-40 rounded-full shadow-lg flex items-center justify-center mt-10 mb-8 border-8 border-neutral-100 dark:border-neutral-800 relative overflow-hidden bg-neutral-50 dark:bg-neutral-800 {{ $coverUrl ? 'animate-[spin_20s_linear_infinite]' : '' }}">
                
                <div class="absolute inset-0 rounded-full border border-neutral-200/50 dark:border-neutral-700/50 m-4 pointer-events-none z-20"></div>
                
                @if($coverUrl)
                    <img src="{{ $coverUrl }}" alt="Portada" class="w-full h-full object-cover z-10">
                    <div class="absolute w-8 h-8 bg-neutral-100 dark:bg-neutral-800 rounded-full z-30 shadow-inner border border-neutral-300 dark:border-neutral-700"></div>
                @else
                    <flux:icon.musical-note class="size-12 text-neutral-300 dark:text-neutral-600 z-10" />
                @endif
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-center mb-1">Nuestra Canción</h2>
        @endif

        <p class="text-sm font-medium text-neutral-400 dark:text-neutral-500 text-center mb-8 uppercase tracking-widest mt-1">
            {{ $event->name ?? $event->monogram ?? 'Evento' }}
        </p>

        <div class="w-full">
            @if($songUrl && !$isVideo)
                <audio controls class="w-full outline-none" controlsList="nodownload">
                    <source src="{{ $songUrl }}">
                    Tu navegador no soporta el reproductor de audio.
                </audio>
            @elseif(!$songUrl)
                <div class="text-center py-4 px-6 bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl border border-neutral-200 dark:border-neutral-800">
                    <flux:icon.speaker-x-mark class="size-6 text-neutral-400 mx-auto mb-2" />
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Contenido no disponible.</p>
                </div>
            @endif
        </div>

    </div>

    @fluxScripts
</body>
</html>