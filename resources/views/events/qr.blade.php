<x-layouts.app>
    <div class="max-w-4xl mx-auto py-2 space-y-8 text-center relative">

        <div class="flex items-center justify-between">
            <flux:heading size="xl">
                QR del evento
            </flux:heading>

            <flux:button href="{{ route('dashboard') }}" variant="subtle" icon="arrow-left">
                Volver a eventos
            </flux:button>
        </div>
        <flux:text>
            Comparte estos códigos para acceder rápidamente al evento y al álbum
        </flux:text>

        <div class="grid md:grid-cols-2 gap-8 justify-center">

            <!-- QR EVENTO -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow space-y-4 flex flex-col items-center">
                <canvas id="canvasEvento" class="rounded-xl w-full max-w-[260px] h-auto"></canvas>

                <flux:button icon="arrow-down-tray" variant="filled"
                    onclick="downloadCanvas('canvasEvento','evento.png')" class="w-full">
                    Descargar QR Evento
                </flux:button>
            </div>

            <!-- QR ALBUM -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow space-y-4 flex flex-col items-center">
                <canvas id="canvasAlbum" class="rounded-xl w-full max-w-[260px] h-auto"></canvas>

                <flux:button icon="arrow-down-tray" variant="filled" onclick="downloadCanvas('canvasAlbum','album.png')"
                    class="w-full">
                    Descargar QR Álbum
                </flux:button>
            </div>

        </div>

    </div>

    <div class="hidden">
        <div id="qrEvento">
            {!! QrCode::format('svg')->size(300)->margin(1)->errorCorrection('H')->generate($url_evento) !!}
        </div>
        <div id="qrAlbum">
            {!! QrCode::format('svg')->size(300)->margin(1)->errorCorrection('H')->generate($url_album) !!}
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            renderQR(
                'qrEvento',
                'canvasEvento',
                '"Un momento mágico está por comenzar…"',
                'Escanea y graba el primer baile con mariposas'
            );
            renderQR(
                'qrAlbum',
                'canvasAlbum',
                'Escanea para ver los recuerdos del evento',
                'Revive cada momento especial capturado'
            );
        });

        async function renderQR(sourceId, canvasId, titleText, subtitleText) {

            const svg = document.querySelector(`#${sourceId} svg`);
            const svgData = new XMLSerializer().serializeToString(svg);

            const canvas = document.getElementById(canvasId);
            const ctx = canvas.getContext("2d");

            const size = 700;
            const topPad = 160; // espacio real que ocupa el título
            const bottomPad = 140; // espacio real que ocupa el subtítulo
            canvas.width = size;
            canvas.height = size + topPad + bottomPad;

            const bg = await loadImage("{{ asset('images/papel.jpg') }}");
            const qrImg = await loadImage("data:image/svg+xml;base64," + btoa(svgData));
            const logo = await loadImage("{{ asset('images/logoP.png') }}");

            // fondo papel
            ctx.drawImage(bg, 0, 0, canvas.width, canvas.height);

            // tipografía 
            const fontFamily = "Georgia, serif";
            const fontSize = 34;
            const lineHeight = 44;

            // --- TÍTULO arriba ---
            ctx.save();
            ctx.fillStyle = "#3b2f1e";
            ctx.font = `${fontSize}px ${fontFamily}`;
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            // centro vertical del bloque de título: mitad del topPad
            wrapText(ctx, titleText, size / 2, topPad / 2, size - 100, lineHeight);
            ctx.restore();

            // --- QR (sin caja blanca extra, el SVG ya trae fondo blanco) ---
            const qrBoxSize = 500;
            const qrX = (size - qrBoxSize) / 2;
            const qrY = topPad + (size - qrBoxSize) / 2;

            ctx.drawImage(qrImg, qrX + 40, qrY + 40, qrBoxSize - 80, qrBoxSize - 80);

            // --- LOGO centrado sobre el QR ---
            const logoSize = 100;
            const logoX = size / 2 - logoSize / 2;
            const logoY = qrY + qrBoxSize / 2 - logoSize / 2;

            ctx.fillStyle = "#fff";
            roundRect(ctx, logoX - 10, logoY - 10, logoSize + 20, logoSize + 20, 12, true);
            ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);

            // --- SUBTÍTULO abajo ---
            ctx.save();
            ctx.fillStyle = "#3b2f1e";
            ctx.font = `${fontSize}px ${fontFamily}`;
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            // inicio del área de subtítulo: topPad + size, centro: + bottomPad/2
            wrapText(ctx, subtitleText, size / 2, topPad + size + bottomPad / 2, size - 100, lineHeight);
            ctx.restore();
        }

        function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
            const words = text.split(' ');
            let line = '';
            const lines = [];

            for (const word of words) {
                const test = line ? line + ' ' + word : word;
                if (ctx.measureText(test).width > maxWidth && line) {
                    lines.push(line);
                    line = word;
                } else {
                    line = test;
                }
            }
            lines.push(line);

            const startY = y - ((lines.length - 1) * lineHeight) / 2;
            lines.forEach((l, i) => ctx.fillText(l, x, startY + i * lineHeight));
        }

        function downloadCanvas(canvasId, filename) {
            const canvas = document.getElementById(canvasId);
            const a = document.createElement("a");
            a.download = filename;
            a.href = canvas.toDataURL("image/png");
            a.click();
        }

        function loadImage(src) {
            return new Promise((resolve) => {
                const img = new Image();
                img.crossOrigin = "anonymous";
                img.onload = () => resolve(img);
                img.src = src;
            });
        }

        function roundRect(ctx, x, y, width, height, radius, fill) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
            if (fill) ctx.fill();
        }
    </script>

</x-layouts.app>
