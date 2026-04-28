<!DOCTYPE html>
<html lang="es" class="antialiased" x-data="{ darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches }" x-init="window.matchMedia('(prefers-color-scheme: dark)')
    .addEventListener('change', e => darkMode = e.matches)" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Papelera - {{ $event->name ?? $event->monogram }}</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        function updateTheme() {
            if (window.matchMedia &&
                window.matchMedia(
                    '(prefers-color-scheme: dark)'
                ).matches) {
                document.documentElement
                    .classList.add('dark');
            } else {
                document.documentElement
                    .classList.remove('dark');
            }
        }
        updateTheme();

        window.matchMedia(
            '(prefers-color-scheme: dark)'
        ).addEventListener(
            'change',
            updateTheme
        );
    </script>


    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('trashGallery', () => ({

                allUrls: @json(collect($media)->pluck('url')),

                selected: [],
                pressTimer: null,
                justLongPressed: false,
                processing: false,


                get allSelected() {
                    return this.allUrls.length > 0 &&
                        this.selected.length === this.allUrls.length;
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
                        this.selected =
                            this.selected.filter(
                                i => i !== url
                            );
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

                            if (
                                window.navigator &&
                                window.navigator.vibrate
                            ) {
                                window.navigator.vibrate(50);
                            }

                        }, 500);

                    }

                },

                cancelPress() {
                    clearTimeout(this.pressTimer);
                },

                handleLinkClick(e, url) {

                    if (this.justLongPressed) {
                        e.preventDefault();
                        this.justLongPressed = false;
                        return;
                    }

                    if (this.selected.length > 0) {
                        e.preventDefault();
                        this.toggleSelection(url);
                    }

                },

                async restoreSelected() {

                    if (!this.selected.length) {
                        return;
                    }

                    const result = await Swal.fire({
                        title: '¿Restaurar archivos?',
                        text: 'Los archivos volverán al álbum',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280'
                    });

                    if (!result.isConfirmed) return;

                    this.processing = true;

                    try {

                        const formData = new FormData();

                        formData.append(
                            '_token',
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content
                        );

                        this.selected.forEach(file => {
                            formData.append(
                                'files[]',
                                file
                            );
                        });

                        const response = await fetch(
                            `{{ route('album.restore', request()->route('id_album')) }}`, {
                                method: 'POST',
                                credentials: 'same-origin',
                                body: formData
                            }
                        );

                        if (!response.ok) {
                            throw new Error();
                        }

                        window.location.reload();

                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron restaurar los archivos'
                        });
                    }

                    this.processing = false;

                },

                async forceDelete() {

                    if (!this.selected.length) {
                        return;
                    }

                    const result = await Swal.fire({
                        title: '¿Eliminar definitivamente?',
                        text: 'Esta acción no se puede deshacer',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280'
                    });

                    if (!result.isConfirmed) return;

                    this.processing = true;

                    try {

                        const formData = new FormData();

                        formData.append(
                            '_token',
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content
                        );

                        formData.append(
                            '_method',
                            'DELETE'
                        );

                        this.selected.forEach(file => {
                            formData.append(
                                'files[]',
                                file
                            );
                        });

                        const response = await fetch(
                            `{{ route('album.force-delete', request()->route('id_album')) }}`, {
                                method: 'POST',
                                credentials: 'same-origin',
                                body: formData
                            }
                        );

                        if (!response.ok) {
                            throw new Error();
                        }

                        window.location.reload();

                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron eliminar los archivos'
                        });
                    }
                    this.processing = false;
                }
            }));

        });
    </script>

</head>


<body
    class="min-h-screen pb-24 text-neutral-900 bg-neutral-50 dark:bg-neutral-950 dark:text-neutral-100 transition-colors duration-300"
    x-data="trashGallery">

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">

        <div class="text-center mb-10">

            <h1 class="text-3xl md:text-5xl font-bold tracking-tight mb-2">
                Papelera
            </h1>

            <p class="text-neutral-500 dark:text-neutral-400 text-lg">
                Fotos y videos eliminados
            </p>

            @if ($event->date)
                <p class="text-sm text-neutral-400 dark:text-neutral-500 mt-2">
                    {{ $event->date->format('d/m/Y') }}
                </p>
            @endif

            <div class="mt-6">
                <a href="{{ route('album.admin', request()->route('id_album')) }}">
                    <flux:button variant="subtle" icon="arrow-left">
                        Volver al álbum
                    </flux:button>
                </a>
            </div>

        </div>


        @if (count($media) > 0)
            <div class="flex justify-end mb-6 gap-3">

                <flux:button variant="subtle" size="sm" @click="toggleSelectAll()">

                    <span
                        x-text="
                        allSelected
                        ? 'Deseleccionar todo'
                        : 'Seleccionar todo'
                        ">
                    </span>

                </flux:button>

            </div>
        @endif



        <div
            class="
            grid
            grid-cols-1
            sm:grid-cols-2
            md:grid-cols-3
            lg:grid-cols-4
            gap-4
            md:gap-6
            ">

            @forelse($media as $item)
                <div class="
                        group
                        relative
                        aspect-square
                        overflow-hidden
                        bg-neutral-200
                        dark:bg-neutral-800
                        shadow-sm
                        rounded-xl
                        transition-all
                        duration-300
                        border
                        border-neutral-200/50
                        dark:border-neutral-700
                        "
                    :class="selected.includes('{{ $item['url'] }}') ?
                        'ring-4 ring-amber-500 scale-95 shadow-lg' :
                        'hover:shadow-md'"
                    @touchstart="startPress('{{ $item['url'] }}')" @touchend="cancelPress()" @touchmove="cancelPress()"
                    @mousedown="startPress('{{ $item['url'] }}')" @mouseup="cancelPress()" @mouseleave="cancelPress()">

                    <button type="button"
                        @click.stop.prevent="
                            toggleSelection(
                            '{{ $item['url'] }}'
                            )
                        "
                        @mousedown.stop @touchstart.stop
                        class="
                            absolute
                            top-3
                            right-3
                            z-30
                            flex
                            items-center
                            justify-center
                            size-8
                            rounded-full
                            border-2
                            shadow-sm
                            transition-all
                            cursor-pointer
                            outline-none
                        "
                        :class="selected.includes(
                                '{{ $item['url'] }}'
                            ) ?
                            'bg-amber-500 border-amber-500 text-white' :
                            'bg-black/30 border-white text-transparent hover:bg-black/50 hover:text-white'">

                        <flux:icon.check class="size-5" stroke-width="3" />

                    </button>


                    @if ($item['is_video'])
                        <video class="h-full w-full object-cover" preload="metadata" controls playsinline @click.stop>

                            <source src="{{ $item['url'] }}" type="video/mp4">

                        </video>
                    @else
                        <a href="{{ $item['url'] }}" target="_blank"
                            @click="
                                handleLinkClick(
                                $event,
                                '{{ $item['url'] }}'
                                )
                                "
                            class="
                                block
                                h-full
                                w-full
                                select-none
                                "
                            style="-webkit-touch-callout:none;">

                            <img src="{{ $item['url'] }}" loading="lazy" alt="Archivo en papelera"
                                class="
                                    h-full
                                    w-full
                                    object-cover
                                    transition-transform
                                    duration-500
                                    group-hover:scale-105
                                    select-none
                                    pointer-events-none
                                    ">

                        </a>
                    @endif

                </div>

            @empty

                <div
                    class="
                    col-span-full
                    flex
                    flex-col
                    items-center
                    justify-center
                    py-20
                    text-center
                    ">

                    <flux:icon.trash
                        class="
                        size-20
                        text-neutral-300
                        dark:text-neutral-700
                        mb-6
                        " />

                    <h3
                        class="
                        text-xl
                        font-semibold
                        text-neutral-900
                        dark:text-neutral-200
                        ">
                        La papelera está vacía
                    </h3>

                    <p class="text-neutral-500 mt-2">
                        No hay elementos eliminados.
                    </p>

                </div>
            @endforelse

        </div>

    </main>



    <div x-show="selected.length>0" x-transition.translate.y.100%
        class="
        fixed
        bottom-0
        left-0
        w-full
        z-50
        p-4
        sm:p-6
        "
        x-cloak>

        <div
            class="
                max-w-3xl
                mx-auto
                bg-white
                dark:bg-neutral-800
                shadow-[0_0_40px_rgba(0,0,0,0.15)]
                dark:shadow-[0_0_40px_rgba(0,0,0,0.5)]
                rounded-2xl
                p-4
                flex
                flex-col
                sm:flex-row
                items-center
                justify-between
                gap-4
                border
                border-neutral-200
                dark:border-neutral-700
                ">

            <div
                class="
                flex
                items-center
                gap-4
                w-full
                justify-between
                sm:justify-start
                sm:w-auto
                ">

                <flux:button variant="subtle" icon="x-mark" @click="selected=[]" class="!rounded-full" />

                <span class="font-bold text-lg">
                    <span x-text="selected.length"></span>
                    seleccionados
                </span>

            </div>


            <div
                class="
                flex
                items-center
                gap-3
                w-full
                sm:w-auto
                ">

                <flux:button variant="subtle" @click="toggleSelectAll()" class="hidden sm:flex">

                    <span
                        x-text="
                        allSelected
                        ? 'Deseleccionar'
                        : 'Seleccionar todo'
                        ">
                    </span>

                </flux:button>


                <flux:button variant="primary" icon="arrow-uturn-left" @click="restoreSelected()"
                    x-bind:disabled="processing" class="flex-1 sm:flex-none">

                    Restaurar

                </flux:button>


                <flux:button variant="danger" icon="trash" @click="forceDelete()" x-bind:disabled="processing"
                    class="flex-1 sm:flex-none">

                    Eliminar definitivo

                </flux:button>

            </div>

        </div>

    </div>
    @fluxScripts

</body>

</html>
