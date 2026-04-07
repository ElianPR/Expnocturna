@props(['icon' => null, 'href' => null])

@php
    // Si pasamos un link, será una etiqueta <a>, si no, será un <button>
    $tag = $href ? 'a' : 'button';
    $typeAtributo = $href ? '' : 'type="button"';
@endphp

<{{ $tag }} {!! $href ? 'href="'.$href.'"' : '' !!} {!! $typeAtributo !!} {{ $attributes->merge(['class' => 'block relative w-full bg-[#a8c37d] hover:bg-[#9ebc70] text-[#0f2e14] rounded-2xl py-4 flex items-center justify-center shadow-sm transition-transform active:scale-95 cursor-pointer outline-none mb-3.5 no-underline']) }}>
    
    @if($icon)
        <div class="absolute left-5 text-[#0f2e14] flex items-center">
            <flux:icon name="{{ $icon }}" variant="outline" class="size-6" stroke-width="1.5" />
        </div>
    @endif

    <div class="text-center font-serif italic text-[16px] sm:text-[18px] font-medium leading-snug px-14">
        {{ $slot }}
    </div>

</{{ $tag }}>