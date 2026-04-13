@props(['preview' => false, 'event' => null, 'imageUrl' => null])

@php
    $isPreview = filter_var($preview, FILTER_VALIDATE_BOOLEAN);

    $titleAttr = $isPreview ? 'x-text="displayTitle"' : '';
    $titleText = $isPreview ? '' : mb_strtoupper($event?->name ?? $event?->monogram ?? 'JUAN Y MARÍA');

    $dateAttr = $isPreview ? 'x-text="displayDate"' : '';
    $dateText = $isPreview ? '' : ($event?->date ? \Carbon\Carbon::parse($event->date)->translatedFormat('d \d\e F \d\e Y') : 'FECHA POR DEFINIR');

    $fontFam = $event?->typography ?? 'Cinzel';
    $fontAttr = $isPreview ? ':style="\'font-family: \' + typography + \';\'"' : 'style="font-family: ' . $fontFam . ';"';

    // Imagen: Prioriza la URL real, si no hay, usa el placeholder de preview
    $imgUrlFinal = $imageUrl ?? asset('img/boda-ejemplo.jpg');
    $imgAttr = $isPreview ? ':src="imageUrl"' : 'src="' . $imgUrlFinal . '"'; 
@endphp

<x-papilia.layout :preview="$isPreview" :fontFamily="$fontFam">
    
    <div class="text-center text-[10px] sm:text-[11px] font-bold tracking-widest text-[#1b311e] px-6 mb-5 uppercase leading-relaxed">
        Vive la experiencia Papilia con <br> mariposas y la canción
    </div>

    <div class="w-full mb-8 relative z-10">
        <img {!! $imgAttr !!} alt="Foto del Evento" class="w-full h-auto object-contain block">
    </div>

    <div class="text-center mb-2 w-full px-8">
        <h1 {!! $titleAttr !!} {!! $fontAttr !!} class="text-4xl text-[#013524] mb-3 break-words leading-none">
            {{ $titleText }}
        </h1>
        <p {!! $dateAttr !!} class="text-[11px] tracking-widest text-[#566b59] mt-3 uppercase font-bold">
            {{ $dateText }}
        </p>
    </div>

    <div class="px-8 w-full select-none pointer-events-none my-4">
        <x-papilia.divider />
    </div>

    <div class="w-full px-6 mt-3 {{ $isPreview ? 'pointer-events-none' : '' }}">
        
        <x-papilia.button 
            icon="video-camera"
            href="{{ $isPreview ? '#' : route('events.camera', $event->id_hex ?? bin2hex($event->id)) }}"
        >
            Toma foto y video <br> con mariposas
        </x-papilia.button>

        <x-papilia.button icon="musical-note"
            href="{{ $isPreview ? '#' : route('events.music', $event->id_hex ?? bin2hex($event->id ?? '')) }}"
        >
            Escucha su canción
        </x-papilia.button>

        <x-papilia.button 
            icon="share" 
            href="{{ $isPreview ? '#' : route('events.share.create', $event->id_hex ?? bin2hex($event->id)) }}">
            Compartir
        </x-papilia.button>

    </div>

    <div class="mt-auto pt-8 pb-4 text-center text-[11px] font-bold text-[#1b311e] tracking-[0.2em] uppercase leading-relaxed">
        papilia.net
    </div>

</x-papilia.layout>