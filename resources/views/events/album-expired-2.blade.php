<x-papilia.layout :preview="false" fontFamily="Montserrat" bgStyle="background-color: #ffffff;"
    class="bg-white relative min-h-screen">

    <!-- Hojas -->
    <img src="{{ asset('images/hojas-arriba.png') }}"
        class="absolute top-0 left-0 w-32 sm:w-48 opacity-80 pointer-events-none z-0 -mt-2 -ml-2">

    <img src="{{ asset('images/hojas-abajo.png') }}"
        class="absolute bottom-0 right-0 w-32 sm:w-48 opacity-80 pointer-events-none z-0">

    <div class="relative z-10 w-full flex flex-col items-center justify-center min-h-[80vh] px-6 py-12 text-center">

        <h2 class="text-lg sm:text-xl font-bold text-[#092D51] mb-12 max-w-[90%] sm:max-w-lg mx-auto leading-snug">
            Si necesitas recuperar algún recuerdo especial, puedes consultar con nosotros
        </h2>

        <div class="w-full mx-auto mb-10 relative z-10 flex justify-center">
            <img src="{{ asset('images/mariposa-capullo-2.png') }}" alt="Mariposa Capullo" class="w-80 sm:w-[28rem] md:w-[32rem] h-auto object-contain drop-shadow-sm">
        </div>

        <h2 class="text-3xl sm:text-4xl font-extrabold text-[#092D51] mb-12 max-w-[280px] sm:max-w-sm mx-auto leading-tight">
            Esta galería ha<br>cumplido su ciclo
        </h2>

        <a href="https://papilia.net" target="_blank"
            class="relative z-20 text-center font-medium text-[#092D51] hover:underline transition-all text-base sm:text-lg">
            papilia.net
        </a>

    </div>

</x-papilia.layout>
