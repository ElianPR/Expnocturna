<x-papilia.layout 
    title="{{ $event->name }} - Papilia" 
    fontFamily="{{ $event->typography ?? 'Cinzel' }}">
    
    <div class="text-center text-[11px] sm:text-xs font-semibold tracking-widest text-[#1b311e] px-4 mb-6 uppercase leading-relaxed">
        Vive la experiencia Papilia con <br> 
        mariposas y la canción del evento
    </div>

    <div class="w-full h-64 sm:h-72 mb-8 borde-rasgado bg-neutral-200 shadow-inner">
        <img src="{{ $event->main_image_url }}" 
             alt="Foto principal del evento" class="w-full h-full object-cover">
    </div>

    <div class="text-center mb-2">
        <h1 class="font-nombres text-4xl sm:text-5xl text-[#012a1c] mb-2 tracking-wide" 
            style="text-shadow: 2px 2px 0px #cda941;">
            {{ mb_strtoupper($event->name) }}
        </h1>
        <p class="text-sm tracking-widest text-[#445847] mt-3 uppercase font-medium">
            @if($event->date)
                {{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y') }}
            @else
                Fecha por definir
            @endif
        </p>
    </div>

    <x-papilia.divider />

    <div class="flex flex-col w-full px-2 mt-2">
        
        <x-papilia.button icon="video-camera">
            Toma foto y video <br> con mariposas
        </x-papilia.button>

        <x-papilia.button icon="musical-note">
            Escucha su canción
        </x-papilia.button>

        <x-papilia.button icon="arrow-uturn-right">
            Compartir
        </x-papilia.button>
        
    </div>

    <div class="mt-auto pt-8 pb-4 text-center text-sm font-medium text-[#1b311e] tracking-wide">
        papilia.net
    </div>

</x-papilia.layout>