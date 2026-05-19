@props(['preview' => false, 'event' => null, 'imageUrl' => null])

@php
    $isPreview = filter_var($preview, FILTER_VALIDATE_BOOLEAN);

    $dateText = $event?->date
        ? \Carbon\Carbon::parse($event->date)->translatedFormat('d \d\e F Y')
        : '30 septiembre 2028';

    $fontFam = $event?->typography ?? 'Playfair Display, serif';
    $imgUrlFinal = $imageUrl ?? asset('images/boda-ejemplo-brocha.png');
@endphp

<x-papilia.layout :preview="$isPreview" :fontFamily="$fontFam" bgStyle="background-image: url('{{ asset('images/fondosD/fondoD.png') }}');">
    <div
        class="
        relative min-h-screen w-full overflow-x-hidden flex flex-col -mt-10
        {{ $isPreview ? 'max-w-none px-0' : 'max-w-[430px] md:max-w-[768px] lg:max-w-[1024px] mx-auto' }}
    ">
        <div class="w-full relative flex justify-end z-10 pt-0">

            <img @if ($isPreview) :src="imageUrl || '{{ $imgUrlFinal }}'"
                @else
                    src="{{ $imgUrlFinal }}" @endif
                class="
                    {{ $isPreview ? 'w-[92%] sm:w-[85%] md:w-[72%]' : 'w-[63%] sm:w-[53%] md:w-[62%] lg:w-[55%] xl:w-[48%]' }}
                    max-w-none h-auto object-contain drop-shadow-md pointer-events-none pr-0
                ">
        </div>

        <div
            class="relative z-20 flex-1 flex flex-col items-center justify-center w-full
                {{ $isPreview
                    ? 'px-3 sm:px-4 md:px-6 pt-3 pb-6'
                    : 'px-5 sm:px-6 md:px-10 lg:px-14 xl:px-20 pt-4 md:pt-8 lg:pt-10 pb-10 md:pb-14' }}">
            @if ($isPreview)
                <template x-if="monogramPreview">
                    <img :src="monogramPreview" class="mb-4 max-h-28 object-contain">
                </template>
                <h1 x-show="!monogramPreview" x-text="displayTitle || 'J & M'"
                    :style="`color:#b4976d;font-family:${typography || '{{ $fontFam }}'};font-size:clamp(1.8rem,5vw,3.5rem);line-height:1;`"
                    class="text-center font-serif w-full break-words font-bold"></h1>
            @else
                @if ($event?->monogram)
                    <img src="{{ route('file.show', ['id_evento' => $event->id_hex, 'filename' => $event->monogram]) }}"
                        class="mb-4 max-h-28 object-contain">
                @else
                    <h1 style="
                            color:#b4976d;
                            font-family:{{ $fontFam }};
                            font-size:clamp(2.2rem,7vw,5.5rem);
                            line-height:1;
                        "
                        class="text-center font-serif w-full break-words font-bold">
                        {{ $event->name ?? 'J & M' }}
                    </h1>
                @endif

            @endif

            <p @if ($isPreview) x-text="displayDate || '{{ $dateText }}'" @endif
                class="text-center mt-3 md:mt-4 lg:mt-5"
                style="
                    color: #b4976d;
                    font-size: clamp(0.95rem, 2vw, 1.5rem);
                    font-family: 'Poppins', sans-serif;
                    font-weight: normal;
                ">
                @unless ($isPreview)
                    {{ $dateText }}
                @endunless
            </p>

            <div class="text-center mt-6 md:mt-8 mb-6 md:mb-8"
                style="color: #a8792b; font-size: clamp(1rem, 2.5vw, 1.6rem); font-family: 'Poppins', sans-serif;">
                <strong>Vive la Experiencia PAPILIA</strong> con mariposas y la canción del evento
            </div>

            <div
                class="w-full
                    {{ $isPreview ? 'max-w-full px-2' : 'max-w-[340px] sm:max-w-[380px] md:max-w-[460px] lg:max-w-[520px]' }}
                    space-y-4 md:space-y-5
                    {{ $isPreview ? 'pointer-events-none' : '' }}">
                <x-papilia.button icon="video-camera" bgColor="#a8792b" textColor="#ffffff"
                    href="{{ $isPreview ? '#' : route('events.camera', $event->id_hex ?? bin2hex($event->id ?? '')) }}"
                    hoverColor="#a8792b">
                    <span class="font-bold">Toma foto y video</span><br>
                    con mariposas
                </x-papilia.button>

                <x-papilia.button
                    icon="musical-note"
                    bgColor="#a8792b"
                    textColor="#ffffff"
                    hoverColor="#a8792b"
                    href="{{ $isPreview ? '#' : route('events.music', $event->id_hex ?? bin2hex($event->id ?? '')) }}"
                >
                    <span class="font-bold">Escucha su canción</span>
                </x-papilia.button>

                <x-papilia.button icon="share"
                    href="{{ $isPreview ? '#' : route('events.share.create', $event->id_hex ?? bin2hex($event->id ?? '')) }}"
                    bgColor="#a8792b" textColor="#ffffff" hoverColor="#a8792b">
                    <span class="font-bold">Compartir</span>
                </x-papilia.button>
            </div>

            <a href="https://papilia.net/papilia2021/" target="_blank"
                class="relative z-20 text-center mt-10 md:mt-14 lg:mt-16 block"
                style="color: #4a4a4a; font-size: clamp(0.9rem, 1.8vw, 1.2rem); font-family: 'Poppins', sans-serif;">
                papilia.net
            </a>
        </div>
    </div>
</x-papilia.layout>
