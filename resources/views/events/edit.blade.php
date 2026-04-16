<x-layouts.app>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Great+Vibes&family=Montserrat:ital,wght@0,400;0,500;1,500&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">

    <style>
        .borde-rasgado {
            mask-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1000 100" preserveAspectRatio="none"><path d="M0,50 L50,80 L100,40 L150,90 L200,30 L250,70 L300,20 L350,80 L400,10 L450,70 L500,30 L550,90 L600,40 L650,80 L700,20 L750,90 L800,30 L850,70 L900,10 L950,80 L1000,50 V100 H0 Z"/></svg>'),
                linear-gradient(black, black),
                url('data:image/svg+xml;utf8,<svg viewBox="0 0 1000 100" preserveAspectRatio="none"><path d="M0,50 L50,20 L100,60 L150,10 L200,70 L250,30 L300,80 L350,20 L400,90 L450,30 L500,70 L550,10 L600,60 L650,20 L700,80 L750,10 L800,70 L850,30 L900,90 L950,20 L1000,50 V0 H0 Z"/></svg>');
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

    <div x-data="previewData()" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start w-full">

        {{-- FORM --}}
        <div class="lg:col-span-7 overflow-hidden rounded-xl border bg-white p-6 dark:bg-neutral-800">

            @if (session('success'))
                <div class="mb-4 text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('events.update', $event->id_hex) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <flux:field>
                    <flux:label>Nombre del Evento *</flux:label>
                    <flux:input type="text" name="name" x-model="name" value="{{ old('name', $event->name) }}"
                        maxlength="80" />
                </flux:field>

                {{-- MONOGRAMA --}}
                <flux:field>
                    <flux:label>Monograma</flux:label>

                    @if ($event->monogram)
                        <img src="{{ route('file.show', [$event->id_hex, $event->monogram]) }}"
                            class="w-24 mb-2 rounded">
                    @endif

                    <flux:input type="file" name="monogram" @change="monogramFile = $event.target.files[0]" />
                </flux:field>

                {{-- TYPO + TEMPLATE --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Tipografía</flux:label>
                        <flux:select name="typography" x-model="typography">
                            <flux:select.option value="Arial">Arial</flux:select.option>
                            <flux:select.option value="Times New Roman">Times New Roman</flux:select.option>
                            <flux:select.option value="'Cinzel', serif">Cinzel</flux:select.option>
                            <flux:select.option value="'Great Vibes', cursive">Great Vibes</flux:select.option>
                            <flux:select.option value="'Playfair Display', serif">Playfair</flux:select.option>
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>Plantilla</flux:label>
                        <flux:select name="template" x-model="template">
                            <flux:select.option value="1">Papilia</flux:select.option>
                            <flux:select.option value="2">Acuarela</flux:select.option>
                            <flux:select.option value="3">Elegante</flux:select.option>
                            <flux:select.option value="0">Base</flux:select.option>
                        </flux:select>
                    </flux:field>
                </div>

                {{-- FECHA --}}
                <flux:field>
                    <flux:label>Fecha del evento</flux:label>
                    <flux:input type="date" name="date" x-model="date" required />
                </flux:field>

                <flux:separator variant="subtle" />

                {{-- FOTO --}}
                <flux:field>
                    <flux:label>Foto principal</flux:label>

                    @if ($photo)
                        <img src="{{ route('file.show', [$event->id_hex, $photo->url]) }}"
                            class="w-full max-w-xs mb-2 rounded">
                    @endif

                    <flux:input type="file" name="main_image" @change="updateImage" />
                </flux:field>

                {{-- MEDIA --}}
                <div x-data="{ mediaType: '{{ $event->song ? (Str::endsWith($event->song, ['mp4','mov','webm']) ? 'video' : 'audio') : '' }}' }">
                    <flux:field>
                        <flux:label>Canción o Video</flux:label>

                        @if ($event->song)
                            <p class="text-xs text-neutral-500 mb-2">Actual: {{ $event->song }}</p>
                        @endif

                        <flux:input type="file" name="song" accept="audio/*,video/*"
                            @change="
                                const file = $event.target.files[0];
                                if (!file) {
                                    mediaType = '';
                                    return;
                                }

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
                    </flux:field>

                    <div x-show="mediaType === 'audio'" x-cloak>
                        <br>
                        <flux:field>
                            <flux:label>Portada de la canción</flux:label>

                            @if ($event->song_cover)
                                <img src="{{ route('events.stream-cover', $event->id_hex) }}" class="w-24 mb-2 rounded">
                            @endif

                            <flux:input type="file" name="song_cover" />
                        </flux:field>
                    </div>
                </div>

                {{-- WATERMARK --}}
                <flux:field>
                    <flux:label>Imagen marca de Agua</flux:label>

                    @if ($event->watermark)
                        <img src="{{ route('file.show', [$event->id_hex, $event->watermark]) }}" class="w-24 mb-2">
                    @endif

                    <flux:input type="file" name="watermark" />
                </flux:field>

                <div class="flex justify-end pt-4">
                    <flux:button type="submit">Actualizar Evento</flux:button>
                </div>

            </form>
        </div>

        {{-- PREVIEW --}}
        <div class="lg:col-span-5 xl:col-span-5 lg:sticky lg:top-6 flex flex-col items-center">

            <span class="text-sm font-semibold text-neutral-500 uppercase mb-4">
                Vista Previa en Vivo
            </span>

            <div
                class="w-full max-w-[320px] h-[650px] bg-black rounded-[40px] p-2 shadow-2xl border-[6px] border-neutral-800 relative">

                <div class="absolute top-0 inset-x-0 h-6 bg-black rounded-b-xl w-32 mx-auto"></div>

                <div class="w-full h-full bg-neutral-50 rounded-[32px] overflow-y-auto no-scrollbar">

                    <div x-show="template == '1'" class="h-full" x-cloak>
                        <x-templates.papilia :preview="true" />
                    </div>

                    <div x-show="template == '2'" class="h-full" x-cloak>
                        <x-templates.dos :preview="true" />
                    </div>

                    <div x-show="template == '3'" class="h-full" x-cloak>
                        <x-templates.tres :preview="true" />
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
                typography: {!! json_encode(old('typography', $event->typography ?? "'Cinzel', serif")) !!},
                template: '{{ old('template', $event->template ?? '1') }}',

                imageUrl: '{{ $photo ? route('file.show', [$event->id_hex, $photo->url]) : '' }}',

                monogramFile: null,

                get displayTitle() {
                    return this.name || 'EVENTO';
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
    </script>
</x-layouts.app>
