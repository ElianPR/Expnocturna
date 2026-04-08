<x-layouts.app>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Great+Vibes&family=Montserrat:ital,wght@0,400;0,500;1,500&display=swap" rel="stylesheet">

    <style>
        .borde-rasgado {
            mask-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1000 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,50 L50,80 L100,40 L150,90 L200,30 L250,70 L300,20 L350,80 L400,10 L450,70 L500,30 L550,90 L600,40 L650,80 L700,20 L750,90 L800,30 L850,70 L900,10 L950,80 L1000,50 V100 H0 Z" fill="black"/></svg>'), linear-gradient(black, black), url('data:image/svg+xml;utf8,<svg viewBox="0 0 1000 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,50 L50,20 L100,60 L150,10 L200,70 L250,30 L300,80 L350,20 L400,90 L450,30 L500,70 L550,10 L600,60 L650,20 L700,80 L750,10 L800,70 L850,30 L900,90 L950,20 L1000,50 V0 H0 Z" fill="black"/></svg>');
            mask-position: top, center, bottom;
            mask-size: 100% 20px, 100% calc(100% - 40px), 100% 20px;
            mask-repeat: no-repeat;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Crear un nuevo evento</h1>
        <flux:button href="{{ route('dashboard') }}" variant="subtle" icon="arrow-left">
            Volver a eventos
        </flux:button>
    </div>

    <div x-data="previewData()" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start relative w-full">
        
        <div class="lg:col-span-7 xl:col-span-7 overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/50 dark:bg-green-900/20">
                    <div class="flex items-center gap-3">
                        <flux:icon.check-circle class="text-green-600 dark:text-green-400" />
                        <span class="font-medium text-green-800 dark:text-green-300">{{ session('success') }}</span>
                    </div>

                    @if(session('url_evento'))
                        <div class="mt-4 border-t border-green-200/50 pt-4 dark:border-green-800/50">
                            <p class="mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300">Enlaces del evento:</p>
                            <ul class="space-y-3 text-sm">
                                <li x-data="{ copied: false }" class="flex items-center gap-3">
                                    <span class="font-medium text-neutral-600 dark:text-neutral-400 w-32">Página del evento:</span>
                                    <a href="{{ session('url_evento') }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400 truncate w-48 md:w-auto">
                                        {{ session('url_evento') }}
                                    </a>
                                    <button type="button" @click="forzarCopiado('{{ session('url_evento') }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200">
                                        <flux:icon.document-duplicate x-show="!copied" class="size-4" />
                                        <flux:icon.check x-show="copied" class="size-4 text-green-500" x-cloak />
                                    </button>
                                </li>
                                <li x-data="{ copied: false }" class="flex items-center gap-3">
                                    <span class="font-medium text-neutral-600 dark:text-neutral-400 w-32">Galería / Álbum:</span>
                                    <a href="{{ session('url_album') }}" target="_blank" class="text-purple-600 hover:underline dark:text-purple-400 truncate w-48 md:w-auto">
                                        {{ session('url_album') }}
                                    </a>
                                    <button type="button" @click="forzarCopiado('{{ session('url_album') }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200">
                                        <flux:icon.document-duplicate x-show="!copied" class="size-4" />
                                        <flux:icon.check x-show="copied" class="size-4 text-green-500" x-cloak />
                                    </button>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <flux:field>
                    <flux:label>Nombre del Evento (Opcional si usas monograma)</flux:label>
                    <flux:input type="text" name="name" x-model="name" placeholder="Ej. Boda de Ana y Juan" maxlength="80" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Monograma</flux:label>
                    <flux:input type="text" name="monogram" x-model="monogram" placeholder="Ej. A & J" maxlength="40" />
                    <flux:error name="monogram" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Tipografía</flux:label>
                        <flux:select name="typography" x-model="typography">
                            <flux:select.option value="Arial">Arial</flux:select.option>
                            <flux:select.option value="Times New Roman">Times New Roman</flux:select.option>
                            <flux:select.option value="'Cinzel', serif">Cinzel (Elegante)</flux:select.option>
                            <flux:select.option value="'Great Vibes', cursive">Great Vibes (Cursiva)</flux:select.option>
                        </flux:select>
                        <flux:error name="typography" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Plantilla</flux:label>
                        <flux:select name="template" x-model="template">
                            <flux:select.option value="1">Papilia (Bordes rasgados)</flux:select.option>
                            <flux:select.option value="2">Acuarela (Hojas y Pincelada)</flux:select.option>
                            <flux:select.option value="0">Plantilla Base</flux:select.option>
                        </flux:select>
                        <flux:error name="template" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Fecha del evento</flux:label>
                    <flux:input type="date" name="date" x-model="date" required />
                    <flux:error name="date" />
                </flux:field>

                <flux:separator variant="subtle" />

                <flux:field>
                    <flux:label>Foto Principal (PNG, JPG, WebP)</flux:label>
                    <flux:input type="file" name="main_image" accept="image/jpeg,image/png,image/webp" @change="updateImage" />
                    <flux:error name="main_image" />
                </flux:field>

                <flux:field>
                    <flux:label>Canción de fondo (MP3, WAV)</flux:label>
                    <flux:input type="file" name="song" accept="audio/mp3,audio/wav" />
                    <flux:error name="song" />
                </flux:field>

                <flux:field>
                    <flux:label>Imagen para Marca de Agua (PNG, JPG)</flux:label>
                    <flux:input type="file" name="watermark" accept="image/jpeg,image/png" />
                    <flux:error name="watermark" />
                </flux:field>

                <div class="flex justify-end pt-4">
                    <flux:button type="submit" variant="primary">Guardar Evento</flux:button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-5 xl:col-span-5 lg:sticky lg:top-6 flex flex-col items-center justify-start w-full">
            
            <span class="text-sm font-semibold text-neutral-500 uppercase tracking-widest mb-4">Vista Previa en Vivo</span>
            
            <div class="w-full max-w-[320px] h-[650px] bg-black rounded-[40px] p-2 shadow-2xl relative border-[6px] border-neutral-800 overflow-hidden mx-auto">
                <div class="absolute top-0 inset-x-0 h-6 bg-black rounded-b-xl w-32 mx-auto z-50"></div>

                <div class="w-full h-full bg-neutral-50 rounded-[32px] overflow-y-auto overflow-x-hidden no-scrollbar relative" style="font-family: 'Montserrat', sans-serif;">
                    
                    <div x-show="template == '1'" class="h-full w-full" x-cloak>
                        <x-templates.papilia :preview="true" />
                    </div>
                    <div x-show="template == '2'" class="h-full w-full" x-cloak>
                        <x-templates.dos :preview="true" />
                    </div>

                    <div x-show="template == '0'" class="relative z-10 px-6 py-16 flex flex-col items-center justify-center min-h-full" x-cloak>
                        <h2 class="text-lg font-bold text-neutral-800 mb-2">Plantilla Base</h2>
                        <p class="text-sm text-center text-neutral-500">En desarrollo.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        window.previewData = function() {
            return {
                // Usamos json_encode, es el método más a prueba de balas en Laravel
                name: {!! json_encode(old('name', '')) !!},
                monogram: {!! json_encode(old('monogram', '')) !!},
                date: {!! json_encode(old('date', '')) !!},
                typography: "'Cinzel', serif",
                
                // Para el template, como es solo un número (0 o 1), esto es más que suficiente:
                template: '{{ old("template", "1") }}',
                
                imageUrl: 'https://images.unsplash.com/photo-1519741497674-611481863552?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                
                get displayTitle() {
                    let text = this.name ? this.name : (this.monogram ? this.monogram : 'JUAN Y MARÍA');
                    return text.toUpperCase();
                },
                
                get displayDate() {
                    if (!this.date) return 'FECHA POR DEFINIR';
                    // Agregamos T12:00:00 para evitar desajustes por zona horaria
                    const d = new Date(this.date + 'T12:00:00');
                    return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }).toUpperCase();
                },

                updateImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imageUrl = window.URL.createObjectURL(file);
                    }
                }
            };
        }

        // Script de copiado
        function forzarCopiado(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
                return;
            }
            let textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.top = "-999999px";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try { document.execCommand('copy'); } catch (err) { console.error('Error', err); }
            document.body.removeChild(textArea);
        }
    </script>
</x-layouts.app>