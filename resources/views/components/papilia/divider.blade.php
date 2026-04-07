@props([
    'src' => asset('images/separador-hojas.png'), 
    'height' => 'h-4 sm:h-5' 
])

<div class="flex justify-center items-center w-full my-6 select-none pointer-events-none">
    <img src="{{ $src }}" alt="Separador" class="{{ $height }} object-contain max-w-full opacity-90">
</div>