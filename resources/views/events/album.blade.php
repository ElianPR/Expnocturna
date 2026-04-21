<!DOCTYPE html>
<html lang="es" class="antialiased" x-data="{ darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches }" x-init="window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => darkMode = e.matches)" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Álbum - {{ $event->name ?? $event->monogram }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Great+Vibes&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        @media (min-width: 768px) {
            body {
                background-image: none !important;
                background-color: #f8fafc;
            }
        }

        @media (max-width: 767px) {
            body {
                background-image: var(--bg-mobile);
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            }
        }
    </style>

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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('albumGallery', () => ({
                // Cargamos las URLs de forma segura con PHP
                allUrls: @json(collect($media)->pluck('url')),
                selected: [],
                isDownloading: false,
                pressTimer: null,
                justLongPressed: false,

                get allSelected() {
                    return this.allUrls.length > 0 && this.selected.length === this.allUrls.length;
                },

                toggleSelectAll() {
                    if (this.allSelected) {
                        this.selected = [];
                    } else {
                        this.selected = [...this.allUrls];
                    }
                },

                toggleSelection(url) {
                    if (this.selected.includes(url)) {
                        this.selected = this.selected.filter(i => i !== url);
                    } else {
                        this.selected.push(url);
                    }
                },

                startPress(url) {
                    this.justLongPressed = false;
                    if (this.selected.length === 0) {
                        this.pressTimer = setTimeout(() => {
                            this.justLongPressed = true;
                            this.toggleSelection(url);
                            if (window.navigator && window.navigator.vibrate) window.navigator
                                .vibrate(50);
                        }, 500);
                    }
                },

                cancelPress() {
                    clearTimeout(this.pressTimer);
                },

                handleLinkClick(event, url) {
                    if (this.justLongPressed) {
                        event.preventDefault();
                        this.justLongPressed = false;
                        return;
                    }

                    if (this.selected.length > 0) {
                        event.preventDefault();
                        this.toggleSelection(url);
                    }
                },

                async downloadSelected() {
                    if (this.selected.length === 0) return;
                    this.isDownloading = true;

                    for (const url of this.selected) {
                        try {
                            // INTENTO 1: Por Blob (Ideal para fotos y videos ligeros)
                            const response = await fetch(url);
                            if (!response.ok) throw new Error('Fallo al obtener el archivo');
                            const blob = await response.blob();
                            const blobUrl = window.URL.createObjectURL(blob);

                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = blobUrl;

                            // Asegurar extensión del archivo
                            let filename = url.split('/').pop().split('?')[0] || 'recuerdo';
                            if (!filename.includes('.')) {
                                filename += url.includes('.mp4') ? '.mp4' : '.jpg';
                            }
                            a.download = filename;

                            document.body.appendChild(a);
                            a.click();

                            setTimeout(() => {
                                window.URL.revokeObjectURL(blobUrl);
                                document.body.removeChild(a);
                            }, 1000);

                            await new Promise(r => setTimeout(r, 600)); // Pausa entre descargas
                        } catch (error) {
                            console.warn('Fallback de descarga activado para:', url);
                            // INTENTO 2: Fallback Nativo (Para videos pesados que crashean la RAM o bloqueos de CORS)
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = '';
                            a.target = '_blank';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);

                            await new Promise(r => setTimeout(r, 1000));
                        }
                    }
                    this.isDownloading = false;
                    this.selected = [];
                }
            }));
        });
    </script>
</head>

@php
    $templates = [
        1 => [
            'bg' => asset('images/fondoAV.jpg'),
            'textName' => '#000000',
            'button' => '#4A720D',
        ],
        2 => [
            'bg' => asset('images/fondoAA.jpg'),
            'textName' => '#828189',
            'button' => '#092D51',
        ],
        3 => [
            'bg' => asset('images/fondoAD.jpg'),
            'textName' => '#B4976E',
            'button' => '#A8792B',
        ],
    ];

    $theme = $templates[$event->template] ?? [
        'bg' => null,
        'textName' => null,
        'button' => '#000000',
    ];
@endphp

<body
    class="min-h-screen pb-24 text-neutral-900 bg-neutral-50 dark:bg-neutral-950 dark:text-neutral-100 transition-colors duration-300"
    x-data="albumGallery" @if ($theme['bg']) style="--bg-mobile: url('{{ $theme['bg'] }}');" @endif>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">

        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-5xl font-bold tracking-tight mb-2"
                style="{{ $theme['textName'] ? 'font-family: ' . ($event->typography ?? "'Cinzel', serif") . '; color: ' . $theme['textName'] . ';' : '' }}">
                {{ $event->name ?? ($event->monogram ?? 'Nuestro Evento') }}
            </h1>
            <p class="text-neutral-600 text-lg">
                Galería de recuerdos
            </p>
            @if ($event->date)
                <p class="text-sm text-neutral-400 dark:text-neutral-500 mt-2">
                    {{ $event->date->format('d/m/Y') }}
                </p>
            @endif
        </div>

        @if (count($media) > 0)
            <div class="flex justify-end mb-6">
                <flux:button variant="subtle" size="sm" @click="toggleSelectAll()">
                    <span x-text="allSelected ? 'Deseleccionar todo' : 'Seleccionar todo'">Seleccionar todo</span>
                </flux:button>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse ($media as $item)
                <div class="group relative aspect-square overflow-hidden bg-neutral-200 dark:bg-neutral-800 shadow-sm rounded-xl transition-all duration-300 border border-neutral-200/50 dark:border-neutral-700"
                    :class="selected.includes('{{ $item['url'] }}') ? 'ring-4 ring-blue-500 scale-95 shadow-lg' :
                        'hover:shadow-md'"
                    @touchstart="startPress('{{ $item['url'] }}')" @touchend="cancelPress()" @touchmove="cancelPress()"
                    @mousedown="startPress('{{ $item['url'] }}')" @mouseup="cancelPress()" @mouseleave="cancelPress()">

                    <button type="button" @click.stop.prevent="toggleSelection('{{ $item['url'] }}')"
                        class="absolute top-3 right-3 z-20 flex items-center justify-center size-8 rounded-full border-2 shadow-sm transition-all cursor-pointer outline-none"
                        :class="selected.includes('{{ $item['url'] }}') ?
                            'bg-blue-500 border-blue-500 text-white' :
                            'bg-black/30 border-white text-transparent hover:bg-black/50 hover:text-white'">

                        <button type="button" @click.stop.prevent="toggleSelection('{{ $item['url'] }}')"
                            @mousedown.stop @touchstart.stop
                            class="absolute top-3 right-3 z-30 flex items-center justify-center size-8 rounded-full border-2 shadow-sm transition-all cursor-pointer outline-none"
                            :class="selected.includes('{{ $item['url'] }}') ?
                                'bg-blue-500 border-blue-500 text-white' :
                                'bg-black/30 border-white text-transparent hover:bg-black/50 hover:text-white'">
                            <flux:icon.check class="size-5" stroke-width="3" />
                        </button>

                        @if ($item['is_video'])
                            <video class="h-full w-full object-cover relative z-20" preload="metadata" controls
                                playsinline webkit-playsinline @click.stop @mousedown.stop @touchstart.stop>
                                <source src="{{ $item['url'] }}" type="video/mp4">
                                Tu navegador no soporta video.
                            </video>
                        @else
                            <a href="{{ $item['url'] }}" target="_blank"
                                @click="handleLinkClick($event, '{{ $item['url'] }}')"
                                class="block h-full w-full select-none relative z-10"
                                style="-webkit-touch-callout: none;">
                                <img src="{{ $item['url'] }}" loading="lazy" alt="Recuerdo"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 select-none pointer-events-none">
                            </a>
                        @endif
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                    <flux:icon.photo class="size-20 text-neutral-300 dark:text-neutral-700 mb-6" />
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-200">Aún no hay recuerdos
                    </h3>
                </div>
            @endforelse
        </div>
    </main>

    <div x-show="selected.length > 0" x-transition.translate.y.100% class="fixed bottom-0 left-0 w-full z-50 p-4 sm:p-6"
        x-cloak>

        <div
            class="max-w-3xl mx-auto bg-white dark:bg-neutral-800 shadow-[0_0_40px_rgba(0,0,0,0.15)] dark:shadow-[0_0_40px_rgba(0,0,0,0.5)] rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 border border-neutral-200 dark:border-neutral-700">

            <div class="flex items-center gap-4 w-full justify-between sm:justify-start sm:w-auto">
                <flux:button variant="subtle" icon="x-mark" @click="selected = []" class="!rounded-full" />
                <span class="font-bold text-lg"><span x-text="selected.length"></span> seleccionados</span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <flux:button variant="subtle" @click="toggleSelectAll()" class="hidden sm:flex">
                    <span x-text="allSelected ? 'Deseleccionar todo' : 'Seleccionar todo'"></span>
                </flux:button>

                <flux:button icon="arrow-down-tray" @click="downloadSelected()" x-bind:disabled="isDownloading"
                    class="flex-1 sm:flex-none !text-white hover:opacity-90 transition-all duration-300"
                    style="background-color: {{ $theme['button'] }} !important;">
                    <span x-text="isDownloading ? 'Descargando...' : 'Descargar'"></span>
                </flux:button>
            </div>
        </div>
    </div>

    @fluxScripts
</body>

</html>
