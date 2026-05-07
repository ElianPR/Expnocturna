<x-papilia.layout :preview="false" fontFamily="Montserrat"
    bgStyle="background-image: url('{{ asset('images/papel.jpg') }}'); background-size: cover; background-position: center;">

    <div class="flex flex-col items-center justify-center min-h-[80vh] px-4 sm:px-6 py-10 text-center">
        
        <h2 class="text-lg sm:text-xl font-bold text-[#426b00] mb-10 max-w-[90%] sm:max-w-lg mx-auto leading-snug">
            Esta galería ya no esta disponible para nuevas capturas.
        </h2>

        <div class="w-full mx-auto mb-8 relative z-10 flex justify-center">
            <img src="{{ asset('images/mariposa-capullo.png') }}" alt="Mariposa Capullo" class="w-80 sm:w-[28rem] md:w-[32rem] h-auto object-contain drop-shadow-sm">
        </div>

        <h2 class="text-3xl sm:text-4xl font-extrabold text-[#426b00] mb-2 max-w-[280px] sm:max-w-sm mx-auto leading-tight">
            Esta galería ha<br>cumplido su ciclo
        </h2>

        <div class="w-full px-2 sm:px-6 mx-auto mb-10 overflow-hidden flex justify-center">
            <x-papilia.divider height="w-[120%] min-w-[300px] h-auto max-w-none" />
        </div>

        <a href="https://papilia.net" target="_blank"
            class="relative z-20 text-center font-medium text-[#426b00] hover:underline transition-all text-base sm:text-lg">
            papilia.net
        </a>

    </div>

</x-papilia.layout>
