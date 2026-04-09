<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name ? $event->name : 'Evento' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Great+Vibes&family=Montserrat:ital,wght@0,400;0,500;1,500&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <style>
        body { margin: 0; padding: 0; }
        #loader {
            position: fixed; inset: 0; background: #000; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.4s ease;
        }
        #loader img { height: 100%; width: auto; max-width: 480px; object-fit: cover; display: block; }
    </style>
</head>
<body>

    <div id="loader">
        <img src="{{ asset('videos/simulacion.gif') }}" alt="">
    </div>

    <div id="mainContent" style="display:none;">
        @if($event->template == 1)
            <x-templates.papilia :event="$event" :imageUrl="$imageUrl" />
        @elseif($event->template == 2)
            <x-templates.dos :event="$event" :imageUrl="$imageUrl" />
        @elseif($event->template == 3)
            <x-templates.tres :event="$event" :imageUrl="$imageUrl" />
        @else
            <div style="text-align: center; padding: 50px; font-family: sans-serif;">
                <h2>Esta plantilla está en construcción</h2>
            </div>
        @endif
    </div>

    <script>
        const loader = document.getElementById('loader');
        const content = document.getElementById('mainContent');
        function showContent() {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
                content.style.display = 'block';
            }, 400);
        }
        setTimeout(showContent, 7000);
    </script>
</body>
</html>