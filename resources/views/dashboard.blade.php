<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Eventos</h1>

            <flux:button href="{{ route('events.create') }}"
                class="rounded-lg bg-black px-4 py-2 text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200"
                icon="plus">
                Agregar evento
            </flux:button>
        </div>

        <div class="w-full overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full text-sm whitespace-nowrap">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-center">Estado de portada</th>
                        <th class="px-4 py-3 text-center">Estado de Álbum</th>
                        <th class="px-4 py-3 text-left">Enlaces</th>
                        <th class="px-4 py-3 text-left">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($events as $event)
                        <tr class="border-t border-neutral-200 dark:border-neutral-700">
                            <td class="px-4 py-3">
                                <a href="{{ route('events.qr', $event->id_hex) }}"
                                    class="font-semibold text-neutral-800 hover:text-blue-600 hover:underline dark:text-neutral-200 dark:hover:text-blue-400 transition-colors">
                                    {{ $event->name ?? ($event->monogram ?? '—') }}
                                </a>
                            </td>

                            <td class="px-4 py-3">
                                {{ $event->date ? $event->date->format('d/m/Y') : '—' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div x-data="{
                                    active: {{ $event->is_active ? 'true' : 'false' }},
                                    loading: false
                                }" class="flex flex-col items-center justify-center gap-1">

                                    <button type="button"
                                        @click="
                                                loading = true;
                                                fetch('{{ route('events.toggle-status', $event->id_hex) }}', {
                                                    method: 'PATCH',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json',
                                                        'Content-Type': 'application/json'
                                                    }
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    if(data.success) active = data.is_active;
                                                })
                                                .finally(() => loading = false)
                                            "
                                        :class="active ? 'bg-green-500' : 'bg-neutral-300 dark:bg-neutral-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        :disabled="loading">
                                        <span class="sr-only">Cambiar estado</span>
                                        <span :class="active ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                    </button>

                                    <span x-text="active ? 'Activo' : 'Desactivado'"
                                        :class="active ? 'text-green-600 dark:text-green-400' :
                                            'text-neutral-500 dark:text-neutral-400'"
                                        class="text-[11px] font-medium uppercase tracking-wider">
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div x-data="{
                                    active: {{ $event->album_active ? 'true' : 'false' }},
                                    loading: false
                                }" class="flex flex-col items-center justify-center gap-1">

                                    <button type="button"
                                        @click="
                                            loading = true;
                                            fetch('{{ route('events.toggle-album', $event->id_hex) }}', {
                                                method: 'PATCH',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                            .then(res => res.json())
                                            .then(data => {
                                                if(data.success) active = data.album_active;
                                            })
                                            .finally(() => loading = false)
                                        "
                                        :class="active ? 'bg-green-500' : 'bg-neutral-300 dark:bg-neutral-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                        :disabled="loading">
                                        <span class="sr-only">Cambiar estado álbum</span>
                                        <span :class="active ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                        </span>
                                    </button>

                                    <span x-text="active ? 'Activo' : 'Desactivado'"
                                        :class="active ? 'text-green-600 dark:text-green-400' :
                                            'text-neutral-500 dark:text-neutral-400'"
                                        class="text-[11px] font-medium uppercase tracking-wider">
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-2">

                                    <div x-data="{ copied: false }" class="flex items-center gap-2">
                                        <div class="w-16">
                                            <a href="{{ url('/event/' . $event->id_hex) }}" target="_blank"
                                                class="text-blue-600 hover:underline dark:text-blue-400">
                                                Evento
                                            </a>
                                        </div>

                                        <button
                                            @click="forzarCopiado('{{ url('/event/' . $event->id_hex) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 focus:outline-none"
                                            title="Copiar enlace">
                                            <flux:icon.document-duplicate x-show="!copied" class="size-4" />
                                            <flux:icon.check x-show="copied" class="size-4 text-green-500" x-cloak />
                                        </button>

                                        <div class="w-16">
                                            <span :class="copied ? 'opacity-100' : 'opacity-0'"
                                                class="text-xs font-medium text-green-500 transition-opacity duration-300">
                                                ¡Copiado!
                                            </span>
                                        </div>
                                    </div>

                                    <div x-data="{ copied: false }" class="flex items-center gap-2">
                                        <div class="w-16">
                                            <a href="{{ url('/album/' . bin2hex($event->album)) }}" target="_blank"
                                                class="text-purple-600 hover:underline dark:text-purple-400">
                                                Álbum
                                            </a>
                                        </div>

                                        <button
                                            @click="forzarCopiado('{{ url('/album/' . bin2hex($event->album)) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 focus:outline-none"
                                            title="Copiar enlace">
                                            <flux:icon.document-duplicate x-show="!copied" class="size-4" />
                                            <flux:icon.check x-show="copied" class="size-4 text-green-500" x-cloak />
                                        </button>

                                        <div class="w-16">
                                            <span :class="copied ? 'opacity-100' : 'opacity-0'"
                                                class="text-xs font-medium text-green-500 transition-opacity duration-300">
                                                ¡Copiado!
                                            </span>
                                        </div>
                                    </div>

                                    <div x-data="{ copied: false }" class="flex items-center gap-2">

                                        <div class="w-16">
                                            <a href="{{ url('/album/' . bin2hex($event->album) . '/admin') }}"
                                                target="_blank" class="text-red-600 hover:underline dark:text-red-400">
                                                Admin
                                            </a>
                                        </div>

                                        <button
                                            @click="forzarCopiado('{{ url('/album/' . bin2hex($event->album) . '/admin') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 focus:outline-none"
                                            title="Copiar enlace">

                                            <flux:icon.document-duplicate x-show="!copied" class="size-4" />
                                            <flux:icon.check x-show="copied" class="size-4 text-green-500" x-cloak />
                                        </button>

                                        <div class="w-16">
                                            <span :class="copied ? 'opacity-100' : 'opacity-0'"
                                                class="text-xs font-medium text-green-500 transition-opacity duration-300">
                                                ¡Copiado!
                                            </span>
                                        </div>

                                    </div>

                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <flux:button href="#" size="sm" variant="subtle" icon="pencil-square">
                                        Editar
                                    </flux:button>
                                    <flux:button 
                                        type="button"
                                        size="sm" 
                                        variant="danger" 
                                        icon="trash"
                                        onclick="confirmDelete('{{ route('events.destroy', $event->id_hex) }}')"
                                    >
                                        Eliminar
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-neutral-500">
                                No hay eventos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: '¿Estás completamente seguro?',
                text: "Se eliminará el evento, la música, la portada y todas las fotos de la galería. Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Rojo de peligro
                cancelButtonColor: '#6b7280',  // Gris neutro
                confirmButtonText: 'Sí, eliminar todo',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crea un formulario fantasma y lo envía con el método DELETE de Laravel
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
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
            try {
                document.execCommand('copy');
            } catch (err) {
                console.error('Error', err);
            }
            document.body.removeChild(textArea);
        }
    </script>
</x-layouts.app>
