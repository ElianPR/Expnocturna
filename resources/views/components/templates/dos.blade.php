@props(['preview' => false, 'event' => null, 'imageUrl' => null])

@php
    $isPreview = filter_var($preview, FILTER_VALIDATE_BOOLEAN);

    $dateText = $event?->date
        ? \Carbon\Carbon::parse($event->date)->translatedFormat('d \d\e F \d\e Y')
        : 'FECHA POR DEFINIR';

    $fontFam = $event?->typography ?? 'Cinzel';

    $imgUrlFinal = $imageUrl ?? asset('img/boda-ejemplo.jpg');
@endphp

<x-papilia.layout :preview="$isPreview" :fontFamily="$fontFam" bgStyle="background-image: url('{{ asset('images/fondosA/fondoA.png') }}');">



    <div class="relative z-10 w-full px-6 pt-10" style="padding-left: 95px;">

        @if ($isPreview)

            <template x-if="monogramPreview">
                <img :src="monogramPreview" class="ml-auto mb-4 max-h-32 object-contain">
            </template>

            <h1 x-show="!monogramPreview" x-text="displayTitle || 'Juan & María'"
                :style="`
                                    font-family: ${typography || '{{ $fontFam }}'};
                                    color: #092d51;
                                    font-size: ${
                                        (displayTitle || '').length > 10
                                            ? 'clamp(1.8rem, 6vw, 3rem)'
                                            : 'clamp(2.4rem, 8vw, 4rem)'
                                    };
                                    line-height: 0.95;
                                    word-break: break-word;
                                    text-align: right;
                                    width: 100%;
                                    display: block;
                                `"
                class="font-bold"></h1>
        @else
            @if ($event?->monogram)
                <img src="{{ route('file.show', ['id_evento' => $event->id_hex, 'filename' => $event->monogram]) }}"
                    class="ml-auto mb-4 max-h-32 object-contain">
            @else
                <h1 style="
                        font-family: {{ $fontFam }};
                        color: #092d51;
                        font-size: {{ mb_strlen($event->name ?? '') > 10 ? 'clamp(1.8rem, 6vw, 3rem)' : 'clamp(2.4rem, 8vw, 4rem)' }};
                        line-height: 0.95;
                        word-break: break-word;
                        text-align: right;
                        width: 100%;
                        display: block;
                    "
                    class="font-bold">
                    {{ $event->name ?? 'Juan & María' }}
                </h1>
            @endif

        @endif

        <p @if ($isPreview) x-text="displayDate || 'FECHA POR DEFINIR'" @endif
            class="uppercase font-normal tracking-[0.2em] text-right mt-4"
            style="color: #092d51; font-family: 'Poppins', sans-serif; font-size: clamp(0.8rem, 2vw, 1rem);">
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
        style="color: #092d51; font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2.8vw, 1.2rem); line-height: 1.5;">
        <strong>Vive la Experiencia PAPILIA</strong> con mariposas y la canción del evento
    </div>

    <div class="relative z-20 px-6 mt-8 space-y-4 {{ $isPreview ? 'pointer-events-none' : '' }}">
        <x-papilia.button icon="video-camera" bgColor="#092d51" textColor="#ffffff"
            href="{{ $isPreview ? '#' : route('events.camera', $event->id_hex ?? bin2hex($event->id)) }}"
            hoverColor="#092d51">
            Toma foto y video <br> con mariposas
        </x-papilia.button>

        <x-papilia.button
            icon="musical-note"
            bgColor="#092d51"
            textColor="#ffffff"
            href="{{ $isPreview ? '#' : route('events.music', $event->id_hex ?? bin2hex($event->id ?? '')) }}"
            hoverColor="#092d51"
        >
            Escucha su canción
        </x-papilia.button>

        <x-papilia.button icon="share"
            href="{{ $isPreview ? '#' : route('events.share.create', $event->id_hex ?? bin2hex($event->id)) }}"
            bgColor="#092d51" textColor="#ffffff" hoverColor="#092d51">
            Compartir
        </x-papilia.button>
    </div>

    <a href="https://papilia.net/papilia2021/" target="_blank"
        class="relative z-20 text-center mt-10 md:mt-14 lg:mt-16 italic block"
        style="color: #4a4a4a; font-size: clamp(0.9rem, 1.8vw, 1.2rem); font-family: 'Poppins', sans-serif;">
        papilia.net
    </a>

</x-papilia.layout>
