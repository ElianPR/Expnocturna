@props(['preview' => false, 'event' => null, 'imageUrl' => null])

@php
    $isPreview = filter_var($preview, FILTER_VALIDATE_BOOLEAN);

    $titleText = $event?->monogram ?? $event?->name ?? 'J & M';

    $dateText = $event?->date
        ? \Carbon\Carbon::parse($event->date)->translatedFormat('d \d\e F Y')
        : '30 septiembre 2028';

    $fontFam = $event?->typography ?? 'Playfair Display, serif';
    $imgUrlFinal = $imageUrl ?? asset('images/boda-ejemplo-brocha.png');
@endphp

<x-papilia.layout
    :preview="$isPreview"
    :fontFamily="$fontFam"
    bgStyle="background-color: #ffffff;"
>
    <div class="
        bg-white relative min-h-screen w-full overflow-x-hidden flex flex-col
        {{ $isPreview
            ? 'max-w-none px-0'
            : 'max-w-[430px] md:max-w-[768px] lg:max-w-[1024px] mx-auto'
        }}
    ">
        <img
            src="{{ asset('images/flores-doradas-abajo.svg') }}"
            alt="Decoración"
            class="absolute bottom-0 right-0
                {{ $isPreview
                    ? 'w-24 sm:w-28 md:w-32 translate-y-10 sm:translate-y-12 md:translate-y-14'
                    : 'w-36 sm:w-44 md:w-56 lg:w-64 xl:w-72 translate-y-16 sm:translate-y-20 md:translate-y-24'
                }}
                opacity-90 pointer-events-none z-0
                translate-x-2"
        >

        <div class="w-full relative flex justify-end z-10 pt-4 md:pt-6 lg:pt-8">
            <img
                src="{{ asset('images/flores-doradas-arriba.png') }}"
                alt="Decoración"
                class="absolute top-0 left-0
                    {{ $isPreview
                        ? 'w-20 sm:w-24 md:w-28'
                        : 'w-32 sm:w-40 md:w-56 lg:w-64 xl:w-72'
                    }}
                    opacity-90 pointer-events-none
                    -mt-4 -ml-4"
            >

            <img
                @if($isPreview)
                    :src="imageUrl || '{{ $imgUrlFinal }}'"
                @else
                    src="{{ $imgUrlFinal }}"
                @endif
                alt="Foto de los Novios"
                class="
                    {{ $isPreview
                        ? 'w-[92%] sm:w-[85%] md:w-[72%]'
                        : 'w-[78%] sm:w-[68%] md:w-[62%] lg:w-[55%] xl:w-[48%]'
                    }}
                    max-w-none
                    h-auto
                    object-contain
                    drop-shadow-md
                    pointer-events-none
                    pr-2 md:pr-4
                "
            >
        </div>

        <div
            class="relative z-20 flex-1 flex flex-col items-center justify-center w-full
                {{ $isPreview
                    ? 'px-3 sm:px-4 md:px-6 pt-3 pb-6'
                    : 'px-5 sm:px-6 md:px-10 lg:px-14 xl:px-20 pt-4 md:pt-8 lg:pt-10 pb-10 md:pb-14'
                }}"
        >
            <h1
                @if($isPreview)
                    x-text="displayTitle || '{{ $titleText }}'"
                @endif
                class="text-center font-serif w-full break-words"
                style="
                    color: #bfa472;
                    font-family: {{ $fontFam }};
                    font-size: clamp(2.2rem, 7vw, 5.5rem);
                    line-height: 1;
                "
            >
                @unless($isPreview)
                    {{ $titleText }}
                @endunless
            </h1>

            <p
                @if($isPreview)
                    x-text="displayDate || '{{ $dateText }}'"
                @endif
                class="text-center mt-3 md:mt-4 lg:mt-5"
                style="
                    color: #646668;
                    font-size: clamp(0.95rem, 2vw, 1.5rem);
                    font-family: sans-serif;
                "
            >
                @unless($isPreview)
                    {{ $dateText }}
                @endunless
            </p>

            <div
                class="text-center font-medium mt-6 md:mt-8 mb-6 md:mb-8"
                style="
                    color: #d1b88a;
                    font-size: clamp(1rem, 2.5vw, 1.6rem);
                "
            >
                Vive la Experiencia PAPILIA
            </div>

            <div
                class="w-full
                    {{ $isPreview
                        ? 'max-w-full px-2'
                        : 'max-w-[340px] sm:max-w-[380px] md:max-w-[460px] lg:max-w-[520px]'
                    }}
                    space-y-4 md:space-y-5
                    {{ $isPreview ? 'pointer-events-none' : '' }}"
            >
                <x-papilia.button
                    icon="video-camera"
                    bgColor="#fdf6eb"
                    textColor="#5c5e60"
                    href="{{ $isPreview ? '#' : route('events.camera', $event->id_hex ?? bin2hex($event->id ?? '')) }}"
                    hoverColor="#f5eadb"
                >
                    <span class="font-bold">Toma foto y video</span>
                    <br>
                    con mariposas
                </x-papilia.button>

                <x-papilia.button
                    icon="musical-note"
                    bgColor="#fdf6eb"
                    textColor="#5c5e60"
                    hoverColor="#f5eadb"
                >
                    <span class="font-bold">Escucha su canción</span>
                </x-papilia.button>

                <x-papilia.button
                    icon="share"
                    href="{{ $isPreview ? '#' : route('events.share.create', $event->id_hex ?? bin2hex($event->id ?? '')) }}"
                    bgColor="#fdf6eb"
                    textColor="#5c5e60"
                    hoverColor="#f5eadb"
                >
                    <span class="font-bold">Compartir</span>
                </x-papilia.button>
            </div>

            <div
                class="relative z-20 text-center mt-10 md:mt-14 lg:mt-16 italic"
                style="
                    color: #4a4a4a;
                    font-size: clamp(0.9rem, 1.8vw, 1.2rem);
                    font-family: 'Playfair Display', serif;
                "
            >
                papilia.net
            </div>
        </div>
    </div>
</x-papilia.layout>