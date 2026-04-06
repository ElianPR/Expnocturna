<!DOCTYPE html>
<html lang="es" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Álbum - {{ $event->name ?? $event->monogram }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 min-h-screen text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
    
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
        
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-5xl font-bold tracking-tight mb-4">
                {{ $event->name ?? $event->monogram ?? 'Nuestro Evento' }}
            </h1>
            <p class="text-neutral-500 dark:text-neutral-400 text-lg">
                Galería de recuerdos
            </p>
            @if($event->date)
                <p class="text-sm text-neutral-400 dark:text-neutral-500 mt-2">
                    {{ $event->date->format('d/m/Y') }}
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse ($media as $item)
                <div class="group relative aspect-square overflow-hidden rounded-xl bg-neutral-200 dark:bg-neutral-800 shadow-sm transition-all hover:shadow-md border border-neutral-200/50 dark:border-neutral-800">
                    
                    @if($item['is_video'])
                        <video controls class="h-full w-full object-cover" preload="metadata">
                            <source src="{{ $item['url'] }}" type="video/mp4">
                            Tu navegador no soporta la reproducción de video.
                        </video>
                    @else
                        <a href="{{ $item['url'] }}" target="_blank" class="block h-full w-full">
                            <img src="{{ $item['url'] }}" loading="lazy" alt="Recuerdo del evento" 
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </a>
                    @endif

                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                    <svg class="size-16 text-neutral-300 dark:text-neutral-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-200">Aún no hay recuerdos</h3>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Las fotos y videos subidos aparecerán aquí.</p>
                </div>
            @endforelse
        </div>

    </main>

</body>
</html>