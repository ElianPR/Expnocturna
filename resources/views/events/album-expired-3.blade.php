<x-papilia.layout :preview="false" fontFamily="Montserrat" bgStyle="background-color: #ffffff;"
    class="bg-white relative min-h-screen overflow-x-hidden">

    <!-- Flores movidas al flujo del documento -->

    <div class="relative z-10 w-full flex flex-col items-center justify-center min-h-[80vh] px-6 py-12 text-center">

        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#A8792B] mb-12 max-w-[90%] sm:max-w-xl mx-auto leading-snug">
            Los recuerdos de este evento ya no están disponibles.
        </h2>

        <div class="w-full mx-auto mb-10 relative z-10 flex justify-center">
            <img src="{{ asset('images/mariposa-capullo-3.png') }}" alt="Mariposa Capullo" class="w-80 sm:w-[28rem] md:w-[32rem] h-auto object-contain drop-shadow-sm">
        </div>

        <h2 class="text-xl sm:text-2xl font-bold text-[#A8792B] mb-2 sm:mb-4 max-w-[90%] sm:max-w-md mx-auto leading-tight">
            El tiempo de visualización para este evento ha terminado.
        </h2>

        <div class="w-full relative pointer-events-none flex justify-end translate-x-2 sm:translate-x-4 -mt-12 sm:-mt-18 z-0 mb-2">
            <img src="{{ asset('images/flores-doradas-abajo-2.png') }}" alt="Flores Doradas" class="w-36 sm:w-48 md:w-56 opacity-90 object-contain">
        </div>

        <a href="https://papilia.net" target="_blank"
            class="relative z-20 text-center font-medium text-[#426b00] hover:underline transition-all text-base sm:text-lg mt-2">
            papilia.net
        </a>

    </div>

</x-papilia.layout>
