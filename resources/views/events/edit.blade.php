<x-layouts.app>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Great+Vibes&family=Montserrat:ital,wght@0,400;0,500;1,500&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">

    <style>
        .borde-rasgado {
            mask-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1000 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,50 L50,80 L100,40 L150,90 L200,30 L250,70 L300,20 L350,80 L400,10 L450,70 L500,30 L550,90 L600,40 L650,80 L700,20 L750,90 L800,30 L850,70 L900,10 L950,80 L1000,50 V100 H0 Z" fill="black"/></svg>'), linear-gradient(black, black), url('data:image/svg+xml;utf8,<svg viewBox="0 0 1000 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,50 L50,20 L100,60 L150,10 L200,70 L250,30 L300,80 L350,20 L400,90 L450,30 L500,70 L550,10 L600,60 L650,20 L700,80 L750,10 L800,70 L850,30 L900,90 L950,20 L1000,50 V0 H0 Z" fill="black"/></svg>');
            mask-position: top, center, bottom;
            mask-size: 100% 20px, 100% calc(100% - 40px), 100% 20px;
            mask-repeat: no-repeat;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Editar evento</h1>
        <flux:button href="{{ route('dashboard') }}" variant="subtle" icon="arrow-left">
            Volver a eventos
        </flux:button>
    </div>

    <div x-data="previewData()" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start relative w-full">

        <div
            class="lg:col-span-7 xl:col-span-7 overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">

            <form action="{{ route('events.update', $event->id_hex) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <flux:field>
                    <flux:label>Nombre del Evento *</flux:label>
                    <flux:input type="text" name="name" x-model="name" placeholder="Ej. Boda de Ana y Juan"
                        maxlength="80" />
                    <flux:error name="name" />
                </flux:field>

                {{-- MONOGRAMA --}}
                <flux:field>
                    <flux:label>Monograma (Imagen)</flux:label>

                    {{-- Archivo actual con opción de quitar --}}
                    @if ($event->monogram)
                        <div x-data="{ remove: false }" class="mb-3">
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-700/50 border border-neutral-200 dark:border-neutral-600">
                                <flux:icon.photo class="size-5 text-neutral-400 shrink-0" />
                                <span class="text-sm text-neutral-700 dark:text-neutral-300 truncate flex-1">
                                    {{ $event->monogram }}
                                </span>
                                <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                    <input type="checkbox" name="remove_monogram" value="1"
                                        x-model="remove"
                                        class="rounded border-neutral-300 text-red-500 focus:ring-red-500">
                                    <span class="text-sm text-red-600 dark:text-red-400 font-medium">Quitar</span>
                                </label>
                            </div>
                            <p x-show="remove" x-transition
                                class="mt-2 text-xs text-red-500 dark:text-red-400">
                                El monograma se eliminará al guardar. Solo se usará el nombre del evento.
                            </p>
                        </div>
                    @endif

                    <flux:input type="file" name="monogram" accept="image/png,image/jpeg,image/webp"
                        @change="monogramFile = $event.target.files[0]" />
                    <flux:error name="monogram" />
                </flux:field>

                {{-- TIPOGRAFÍA + PLANTILLA --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Tipografía</flux:label>
                        <flux:select name="typography" x-model="typography">
                            <flux:select.option value="Arial">Arial</flux:select.option>
                            <flux:select.option value="Times New Roman">Times New Roman</flux:select.option>
                            <flux:select.option value="'Cinzel', serif">Cinzel (Elegante)</flux:select.option>
                            <flux:select.option value="'Great Vibes', cursive">Great Vibes (Cursiva)</flux:select.option>
                            <flux:select.option value="'Playfair Display', serif">Playfair Display (Romántica)</flux:select.option>
                        </flux:select>
                        <flux:error name="typography" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Plantilla</flux:label>
                        <flux:select name="template" x-model="template">
                            <flux:select.option value="1">Papilia (Bordes rasgados)</flux:select.option>
                            <flux:select.option value="2">Acuarela (Hojas y Pincelada)</flux:select.option>
                            <flux:select.option value="3">Elegante (Flores Doradas)</flux:select.option>
                            <flux:select.option value="0">Plantilla Base</flux:select.option>
                        </flux:select>
                        <flux:error name="template" />
                    </flux:field>
                </div>

                {{-- FECHA --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Fecha del evento</flux:label>
                        <flux:input type="date" name="date" x-model="date" required />
                        <flux:error name="date" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Expira portada</flux:label>
                        <flux:input type="date" name="cover_expiration" x-model="cover_expiration" required />
                        <flux:error name="cover_expiration" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Apertura álbum</flux:label>
                        <flux:input type="date" name="album_availability" x-model="album_availability" required />
                        <flux:error name="album_availability" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Expira álbum</flux:label>
                        <flux:input type="date" name="album_expiration" x-model="album_expiration" required />
                        <flux:error name="album_expiration" />
                    </flux:field>
                </div>

                <flux:separator variant="subtle" />

                {{-- FOTO PRINCIPAL --}}
                <flux:field>
                    <flux:label>Foto Principal (PNG, JPG, WebP)</flux:label>

                    @if ($photo)
                        <div class="mb-3 flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-700/50 border border-neutral-200 dark:border-neutral-600">
                            <flux:icon.photo class="size-5 text-neutral-400 shrink-0" />
                            <span class="text-sm text-neutral-700 dark:text-neutral-300 truncate">
                                {{ $photo->url }}
                            </span>
                        </div>
                    @endif

                    <flux:input type="file" name="main_image" accept="image/jpeg,image/png,image/webp"
                        @change="updateImage" />
                    <flux:error name="main_image" />
                </flux:field>

                {{-- CANCIÓN / VIDEO --}}
                <div x-data="{ mediaType: '{{ $event->song ? (Str::endsWith($event->song, ['mp4','mov','webm']) ? 'video' : 'audio') : '' }}' }"
                    class="space-y-6">

                    <flux:field>
                        <flux:label>Canción o Video (MP3, MP4, WAV, MOV)</flux:label>

                        @if ($event->song)
                            <div class="mb-3 flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-700/50 border border-neutral-200 dark:border-neutral-600">
                                <flux:icon.musical-note class="size-5 text-neutral-400 shrink-0" />
                                <span class="text-sm text-neutral-700 dark:text-neutral-300 truncate">
                                    {{ $event->song }}
                                </span>
                            </div>
                        @endif

                        <flux:input type="file" name="song" accept="audio/*,video/*"
                            @change="
                                const file = $event.target.files[0];
                                if (!file) { mediaType = ''; return; }
                                const type = file.type;
                                const ext = file.name.split('.').pop().toLowerCase();
                                if (type.startsWith('audio') || ['mp3','wav','mpeg'].includes(ext)) {
                                    mediaType = 'audio';
                                } else if (type.startsWith('video') || ['mp4','mov','webm'].includes(ext)) {
                                    mediaType = 'video';
                                } else {
                                    mediaType = '';
                                }
                            " />
                        <flux:error name="song" />
                    </flux:field>

                    <div x-show="mediaType === 'audio'" x-transition x-cloak>
                        <flux:field>
                            <flux:label>Portada de la canción (Opcional, PNG/JPG)</flux:label>

                            @if ($event->song_cover)
                                <div class="mb-3 flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-700/50 border border-neutral-200 dark:border-neutral-600">
                                    <flux:icon.photo class="size-5 text-neutral-400 shrink-0" />
                                    <span class="text-sm text-neutral-700 dark:text-neutral-300 truncate">
                                        {{ $event->song_cover }}
                                    </span>
                                </div>
                            @endif

                            <flux:input type="file" name="song_cover" accept="image/jpeg,image/png,image/webp" />
                            <flux:error name="song_cover" />
                            <p class="text-xs text-neutral-500 mt-1">Esta imagen se mostrará como un disco mientras
                                suena la canción.</p>
                        </flux:field>
                    </div>
                </div>

                {{-- MARCA DE AGUA --}}
                <flux:field>
                    <flux:label>Imagen para Marca de Agua (PNG, JPG)</flux:label>

                    @if ($event->watermark)
                        <div class="mb-3 flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-700/50 border border-neutral-200 dark:border-neutral-600">
                            <flux:icon.photo class="size-5 text-neutral-400 shrink-0" />
                            <span class="text-sm text-neutral-700 dark:text-neutral-300 truncate">
                                {{ $event->watermark }}
                            </span>
                        </div>
                    @endif

                    <flux:input type="file" name="watermark" accept="image/jpeg,image/png" />
                    <flux:error name="watermark" />
                </flux:field>

                <div class="flex justify-end pt-4">
                    <flux:button type="submit" variant="primary">Actualizar Evento</flux:button>
                </div>
            </form>
        </div>

        {{-- VISTA PREVIA --}}
        <div class="lg:col-span-5 xl:col-span-5 lg:sticky lg:top-6 flex flex-col items-center justify-start w-full">

            <span class="text-sm font-semibold text-neutral-500 uppercase tracking-widest mb-4">Vista Previa en
                Vivo</span>

            <div
                class="w-full max-w-[320px] h-[650px] bg-black rounded-[40px] p-2 shadow-2xl relative border-[6px] border-neutral-800 overflow-hidden mx-auto">
                <div class="absolute top-0 inset-x-0 h-6 bg-black rounded-b-xl w-32 mx-auto z-50"></div>

                <div class="w-full h-full bg-neutral-50 rounded-[32px] overflow-y-auto overflow-x-hidden no-scrollbar relative"
                    style="font-family: 'Montserrat', sans-serif;">

                    <div x-show="template == '1'" class="h-full w-full" x-cloak>
                        <x-templates.papilia :preview="true" />
                    </div>
                    <div x-show="template == '2'" class="h-full w-full" x-cloak>
                        <x-templates.dos :preview="true" />
                    </div>
                    <div x-show="template == '3'" class="h-full w-full" x-cloak>
                        <x-templates.tres :preview="true" />
                    </div>

                    <div x-show="template == '0'"
                        class="relative z-10 px-6 py-16 flex flex-col items-center justify-center min-h-full" x-cloak>
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
                name: {!! json_encode(old('name', $event->name)) !!},
                date: {!! json_encode(old('date', optional($event->date)->format('Y-m-d'))) !!},
                cover_expiration: {!! json_encode(old('cover_expiration', optional($event->cover_expiration)->format('Y-m-d'))) !!},
                album_expiration: {!! json_encode(old('album_expiration', optional($event->album_expiration)->format('Y-m-d'))) !!},
                album_availability: {!! json_encode(old('album_availability', optional($event->album_availability)->format('Y-m-d'))) !!},
                typography: {!! json_encode(old('typography', $event->typography ?? "'Cinzel', serif")) !!},
                template: '{{ old('template', $event->template ?? '1') }}',
                imageUrl: '{{ $photo ? route('file.show', [$event->id_hex, $photo->url]) : 'https://images.unsplash.com/photo-1519741497674-611481863552?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}',

                monogramFile: null,

                get displayTitle() {
                    return this.name ? this.name : 'JUAN & MARÍA';
                },

                get displayDate() {
                    if (!this.date) return 'FECHA POR DEFINIR';
                    const d = new Date(this.date + 'T12:00:00');
                    if (isNaN(d)) return 'FECHA INVÁLIDA';
                    return d.toLocaleDateString('es-ES', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                },

                updateImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imageUrl = URL.createObjectURL(file);
                    }
                },

                get monogramPreview() {
                    if (!this.monogramFile) return null;
                    return URL.createObjectURL(this.monogramFile);
                }
            };
        }

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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('swal_success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: '{{ session('swal_success') }}',
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#000',
                });
            });
        </script>
    @endif

    @if (session('swal_error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: '¡Algo salió mal!',
                    text: '{{ session('swal_error') }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#000',
                });
            });
        </script>
    @endif
</x-layouts.app>