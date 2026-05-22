<x-papilia.layout :preview="false" fontFamily="Poppins" bgStyle="background-image: url('{{ asset('images/fondosV/fondoV.png') }}');" class="bg-cover bg-center bg-no-repeat min-h-screen flex flex-col justify-between">

    <div class="relative z-10 w-full flex flex-col items-center justify-center min-h-[80vh] px-6 py-12 text-center flex-1">

        <h1 class="text-3xl sm:text-4xl font-extrabold text-[#436c00] mb-6 max-w-[90%] sm:max-w-lg mx-auto leading-tight" style="font-family: 'Poppins', sans-serif;">
            Las mariposas<br>ya no habitan en<br>este QR.
        </h1>

        <div class="w-full mx-auto mb-8 relative z-10 flex justify-center">
            <img src="{{ asset('images/fondosV/marV.png') }}" alt="Mariposa" class="w-72 sm:w-[24rem] md:w-[28rem] h-auto object-contain">
        </div>

        <p class="text-lg sm:text-xl text-[#436c00] font-bold max-w-[90%] sm:max-w-md mx-auto leading-snug" style="font-family: 'Poppins', sans-serif;">
            Confiamos en que su<br>experiencia fue única.<br>Gracias por elegirnos para<br>capturar su historia.
        </p>

    </div>

    <div class="w-full pb-8 pt-4">
        <a href="https://papilia.net" target="_blank"
            class="relative z-20 text-center font-medium text-[#436c00] block hover:underline transition-all text-base sm:text-lg" style="font-family: 'Poppins', sans-serif;">
            papilia.net
        </a>
    </div>

</x-papilia.layout>
