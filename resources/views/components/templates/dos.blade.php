@props(['preview' => false, 'event' => null, 'imageUrl' => null])

@php
    $isPreview = filter_var($preview, FILTER_VALIDATE_BOOLEAN);

    $dateText = $event?->date
        ? \Carbon\Carbon::parse($event->date)->translatedFormat('d \d\e F \d\e Y')
        : 'FECHA POR DEFINIR';

    $fontFam = $event?->typography ?? 'Cinzel';

    $imgUrlFinal = $imageUrl ?? asset('img/boda-ejemplo.jpg');
@endphp

<x-papilia.layout :preview="$isPreview" :fontFamily="$fontFam" bgStyle="background-color: #ffffff;"
    class="bg-white relative min-h-screen">

    <!-- Hojas -->
    <img src="{{ asset('images/hojas-arriba.png') }}"
        class="absolute top-0 left-0 w-28 opacity-80 pointer-events-none z-0 -mt-2 -ml-2">

    <img src="{{ asset('images/hojas-abajo.png') }}"
        class="absolute bottom-0 right-0 w-24 opacity-80 pointer-events-none z-0">

    <div class="relative z-10 w-full px-6 pt-10" style="padding-left: 95px;">

        @if ($isPreview)

            <template x-if="monogramPreview">
                <img :src="monogramPreview" class="ml-auto mb-4 max-h-32 object-contain">
            </template>

            <h1 x-show="!monogramPreview" x-text="displayTitle || 'Juan & María'"
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
                class="font-normal"></h1>
        @else
            @if ($event?->monogram)
                <img src="{{ route('file.show', ['id_evento' => $event->id_hex, 'filename' => $event->monogram]) }}"
                    class="ml-auto mb-4 max-h-32 object-contain">
            @else
                <h1 style="
                        font-family: {{ $fontFam }};
                        color: #828189;
                        font-size: {{ mb_strlen($event->name ?? '') > 10 ? 'clamp(1.4rem, 5vw, 2.4rem)' : 'clamp(1.8rem, 7vw, 3rem)' }};
                        line-height: 0.95;
                        word-break: break-word;
                        text-align: right;
                        width: 100%;
                        display: block;
                    "
                    class="font-normal">
                    {{ $event->name ?? 'Juan & María' }}
                </h1>
            @endif

        @endif

        <p @if ($isPreview) x-text="displayDate || 'FECHA POR DEFINIR'" @endif
            class="uppercase font-bold tracking-[0.2em] text-right mt-4"
            style="color: #9ba8b0; font-size: clamp(0.8rem, 2vw, 1rem);">
            @unless ($isPreview)
                {{ strtoupper($dateText) }}
            @endunless
        </p>
    </div>

    <div class="relative z-20 px-6 mt-8">
        <img @if ($isPreview) :src="imageUrl || '{{ $imgUrlFinal }}'"
            @else
                src="{{ $imgUrlFinal }}" @endif
            class="w-full h-auto object-cover shadow-md">
    </div>

    <div class="relative z-20 text-center px-8 mt-6"
        style="color: #9ca8b1; font-size: clamp(0.9rem, 2.8vw, 1.2rem); line-height: 1.5;">
        Vive la Experiencia PAPILIA con mariposas y la canción del evento
    </div>

    <div class="relative z-20 px-6 mt-8 space-y-4 {{ $isPreview ? 'pointer-events-none' : '' }}">
        <x-papilia.button icon="video-camera" bgColor="#e4e5ed" textColor="#738598"
            href="{{ $isPreview ? '#' : route('events.camera', $event->id_hex ?? bin2hex($event->id)) }}"
            hoverColor="#d5d7e0">
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

        <x-papilia.button icon="share"
            href="{{ $isPreview ? '#' : route('events.share.create', $event->id_hex ?? bin2hex($event->id)) }}"
            bgColor="#e4e5ed" textColor="#738598" hoverColor="#d5d7e0">
            Compartir
        </x-papilia.button>
    </div>

    <a href="https://papilia.net/papilia2021/" target="_blank"
        class="relative z-20 text-center mt-10 md:mt-14 lg:mt-16 italic block"
        style="color: #4a4a4a; font-size: clamp(0.9rem, 1.8vw, 1.2rem); font-family: 'Playfair Display', serif;">
        papilia.net
    </a>

</x-papilia.layout>
