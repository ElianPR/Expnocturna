<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Crear un nuevo evento</h1>

            <flux:button href="{{ route('dashboard') }}" variant="subtle" icon="arrow-left">
                Volver a eventos
            </flux:button>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 md:p-8 dark:border-neutral-700 dark:bg-neutral-800">

            @if (session('success'))
                <div
                    class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/50 dark:bg-green-900/20">
                    <div class="flex items-center gap-3">
                        <flux:icon.check-circle class="text-green-600 dark:text-green-400" />
                        <span class="font-medium text-green-800 dark:text-green-300">{{ session('success') }}</span>
                    </div>

                    @if (session('url_evento'))
                        <div class="mt-4 border-t border-green-200/50 pt-4 dark:border-green-800/50">
                            <p class="mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300">Enlaces del
                                evento:</p>
                            <ul class="space-y-3 text-sm">

                                <li x-data="{ copied: false }" class="flex items-center gap-3">
                                    <span class="font-medium text-neutral-600 dark:text-neutral-400 w-32">Página del
                                        evento:</span>
                                    <a href="{{ session('url_evento') }}" target="_blank"
                                        class="text-blue-600 hover:underline dark:text-blue-400 truncate w-48 md:w-auto">
                                        {{ session('url_evento') }}
                                    </a>
                                    <button
                                        @click="forzarCopiado('{{ session('url_evento') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200"
                                        title="Copiar enlace">
                                        <flux:icon.document-duplicate x-show="!copied" class="size-4" />
                                        <flux:icon.check x-show="copied" class="size-4 text-green-500" x-cloak />
                                    </button>
                                </li>

                                <li x-data="{ copied: false }" class="flex items-center gap-3">
                                    <span class="font-medium text-neutral-600 dark:text-neutral-400 w-32">Galería /
                                        Álbum:</span>
                                    <a href="{{ session('url_album') }}" target="_blank"
                                        class="text-purple-600 hover:underline dark:text-purple-400 truncate w-48 md:w-auto">
                                        {{ session('url_album') }}
                                    </a>
                                    <button
                                        @click="forzarCopiado('{{ session('url_album') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200"
                                        title="Copiar enlace">
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
                    <flux:input type="text" name="name" value="{{ old('name') }}"
                        placeholder="Ej. Boda de Ana y Juan" maxlength="80" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Monograma</flux:label>
                    <flux:input type="text" name="monogram" value="{{ old('monogram') }}" placeholder="Ej. A & J"
                        maxlength="40" />
                    <flux:error name="monogram" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Tipografía</flux:label>
                        <flux:select name="typography" placeholder="Elige una tipografía">
                            <flux:select.option value="Arial">Arial</flux:select.option>
                            <flux:select.option value="Times New Roman">Times New Roman</flux:select.option>
                            <flux:select.option value="Cursive">Cursiva Elegante</flux:select.option>
                        </flux:select>
                        <flux:error name="typography" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Plantilla</flux:label>
                        <flux:select name="template">
                            <flux:select.option value="0">Plantilla Base (Pendiente)</flux:select.option>
                        </flux:select>
                        <flux:error name="template" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Fecha del evento</flux:label>
                    <flux:input type="date" name="date" value="{{ old('date') }}" required />
                    <flux:error name="date" />
                </flux:field>

                <flux:separator variant="subtle" />

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
    </div>

    <script>
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
            try {
                document.execCommand('copy');
            } catch (err) {
                console.error('Error', err);
            }
            document.body.removeChild(textArea);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
