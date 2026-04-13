@props(['preview' => false, 'event' => null, 'imageUrl' => null])

@php
    $isPreview = filter_var($preview, FILTER_VALIDATE_BOOLEAN);

    $titleText = $event?->name ?? $event?->monogram ?? 'Juan & María';

    $dateText = $event?->date
        ? \Carbon\Carbon::parse($event->date)->translatedFormat('d \d\e F \d\e Y')
        : 'FECHA POR DEFINIR';

    $fontFam = $event?->typography ?? 'Cinzel';

    $imgUrlFinal = $imageUrl ?? asset('img/boda-ejemplo.jpg');
@endphp

<x-papilia.layout
    :preview="$isPreview"
    :fontFamily="$fontFam"
    bgStyle="background-color: #ffffff;"
    class="bg-white relative min-h-screen"
>

    <!-- Hojas -->
    <img
        src="{{ asset('images/hojas-arriba.png') }}"
        alt="Decoración superior"
        class="absolute top-0 left-0 w-28 opacity-80 pointer-events-none z-0 -mt-2 -ml-2"
    >

    <img
        src="{{ asset('images/hojas-abajo.png') }}"
        alt="Decoración inferior"
        class="absolute bottom-0 right-0 w-24 opacity-80 pointer-events-none z-0"
    >

    <!-- Encabezado -->
    <div class="relative z-10 w-full px-6 pt-10" style="padding-left: 95px;">

        <h1
    @if($isPreview)
        x-text="displayTitle || 'Juan & María'"
        :style="`
            font-family: ${typography || '{{ $fontFam }}'};
            color: #828189;
            font-size: ${
                (displayTitle || '').length > 10
                    ? 'clamp(1.4rem, 5vw, 2.4rem)'
                    : 'clamp(1.8rem, 7vw, 3rem)'
            };
            line-height: 0.95;
            word-break: break-word;
            text-align: right;
            width: 100%;
            display: block;
        `"
    @else
        style="
            font-family: {{ $fontFam }};
            color: #828189;
            font-size: {{ mb_strlen($titleText) > 10 ? 'clamp(1.4rem, 5vw, 2.4rem)' : 'clamp(1.8rem, 7vw, 3rem)' }};
            line-height: 0.95;
            word-break: break-word;
            text-align: right;
            width: 100%;
            display: block;
        "
    @endif
    class="font-normal"
>
    @unless($isPreview)
        {{ $titleText }}
    @endunless
</h1>

        <p
            @if($isPreview)
                x-text="displayDate || 'FECHA POR DEFINIR'"
            @endif
            class="uppercase font-bold tracking-[0.2em] text-right mt-4"
            style="
                color: #9ba8b0;
                font-size: clamp(0.8rem, 2vw, 1rem);
            "
        >
            @unless($isPreview)
                {{ strtoupper($dateText) }}
            @endunless
        </p>
    </div>

    <!-- Imagen -->
    <div class="relative z-20 px-6 mt-8">
        <img
            @if($isPreview)
                :src="imageUrl || '{{ $imgUrlFinal }}'"
            @else
                src="{{ $imgUrlFinal }}"
            @endif
            alt="Foto del Evento"
            class="w-full h-auto object-cover shadow-md"
        >
    </div>

    <!-- Texto -->
    <div
        class="relative z-20 text-center px-8 mt-6"
        style="
            color: #9ca8b1;
            font-size: clamp(0.9rem, 2.8vw, 1.2rem);
            line-height: 1.5;
        "
    >
        Vive la Experiencia PAPILIA con mariposas y la canción del evento
    </div>

    <!-- Botones -->
    <div class="relative z-20 px-6 mt-8 space-y-4 {{ $isPreview ? 'pointer-events-none' : '' }}">
        <x-papilia.button
            icon="video-camera"
            bgColor="#e4e5ed"
            textColor="#738598"
            href="{{ $isPreview ? '#' : route('events.camera', $event->id_hex ?? bin2hex($event->id)) }}"
            hoverColor="#d5d7e0"
        >
            Toma foto y video <br> con mariposas
        </x-papilia.button>

        <x-papilia.button
            icon="musical-note"
            bgColor="#e4e5ed"
            textColor="#738598"
            href="{{ $isPreview ? '#' : route('events.music', $event->id_hex ?? bin2hex($event->id ?? '')) }}"
            hoverColor="#d5d7e0"
        >
            Escucha su canción
        </x-papilia.button>

        <x-papilia.button
            icon="share"
            href="{{ $isPreview ? '#' : route('events.share.create', $event->id_hex ?? bin2hex($event->id)) }}"
            bgColor="#e4e5ed"
            textColor="#738598"
            hoverColor="#d5d7e0"
        >
            Compartir
        </x-papilia.button>
    </div>

    <!-- Footer -->
    <div
        class="relative z-20 text-center mt-12 pb-6 italic"
        style="
            color: #6f889d;
            font-size: clamp(0.85rem, 2vw, 1rem);
            font-family: serif;
        "
    >
        papilia.net
    </div>

</x-papilia.layout>