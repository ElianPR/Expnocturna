@props(['icon' => null, 'href' => null, 'bgColor' => '#a8c37d', 'textColor' => '#0f2e14', 'hoverColor' => '#9ebc70'])

@php
    $tag = $href ? 'a' : 'button';
    $typeAtributo = $href ? '' : 'type="button"';
@endphp

<{{ $tag }} {!! $href ? 'href="'.$href.'"' : '' !!} {!! $typeAtributo !!} 
    style="background-color: {{ $bgColor }}; color: {{ $textColor }};"
    onmouseover="this.style.backgroundColor='{{ $hoverColor }}'"
    onmouseout="this.style.backgroundColor='{{ $bgColor }}'"
    {{ $attributes->merge(['class' => "block relative w-full rounded-xl py-4 flex items-center justify-center shadow-sm transition-transform active:scale-95 cursor-pointer outline-none mb-3.5 no-underline"]) }}>
    
    @if($icon)
        <div class="absolute left-5 flex items-center" style="color: {{ $textColor }};">
            <flux:icon name="{{ $icon }}" variant="outline" class="size-6" stroke-width="1.5" />
        </div>
    @endif

    <div class="text-center font-serif italic text-[16px] sm:text-[18px] font-medium leading-snug px-14">
        {{ $slot }}
    </div>

</{{ $tag }}>