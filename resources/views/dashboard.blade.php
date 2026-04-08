<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Eventos</h1>

            <flux:button href="{{ route('events.create') }}"
                class="rounded-lg bg-black px-4 py-2 text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200" icon="plus">
                Agregar evento
            </flux:button>
        </div>

        <div class="w-full overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full text-sm whitespace-nowrap">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Enlaces</th>
                        <th class="px-4 py-3 text-left">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($events as $event)
                        <tr class="border-t border-neutral-200 dark:border-neutral-700">
                            <td class="px-4 py-3">
                                <a href="{{ route('events.qr', $event->id_hex) }}" class="font-semibold text-neutral-800 hover:text-blue-600 hover:underline dark:text-neutral-200 dark:hover:text-blue-400 transition-colors">
                                    {{ $event->name ?? $event->monogram ?? '—' }}
                                </a>
                            </td>

                            <td class="px-4 py-3">
                                {{ $event->date ? $event->date->format('d/m/Y') : '—' }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-2">
                                    
                                    <div x-data="{ copied: false }" class="flex items-center gap-2">
                                        <div class="w-16">
                                            <a href="{{ url('/event/' . $event->id_hex) }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">
                                                Evento
                                            </a>
                                        </div>
                                        
                                        <button @click="forzarCopiado('{{ url('/event/' . $event->id_hex) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 focus:outline-none" title="Copiar enlace">
                                            <flux:icon.document-duplicate x-show="!copied" class="size-4" />
                                            <flux:icon.check x-show="copied" class="size-4 text-green-500" x-cloak />
                                        </button>
                                        
                                        <div class="w-16">
                                            <span :class="copied ? 'opacity-100' : 'opacity-0'" class="text-xs font-medium text-green-500 transition-opacity duration-300">
                                                ¡Copiado!
                                            </span>
                                        </div>
                                    </div>

                                    <div x-data="{ copied: false }" class="flex items-center gap-2">
                                        <div class="w-16">
                                            <a href="{{ url('/album/' . bin2hex($event->album)) }}" target="_blank" class="text-purple-600 hover:underline dark:text-purple-400">
                                                Álbum
                                            </a>
                                        </div>

                                        <button @click="forzarCopiado('{{ url('/album/' . bin2hex($event->album)) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 focus:outline-none" title="Copiar enlace">
                                            <flux:icon.document-duplicate x-show="!copied" class="size-4" />
                                            <flux:icon.check x-show="copied" class="size-4 text-green-500" x-cloak />
                                        </button>

                                        <div class="w-16">
                                            <span :class="copied ? 'opacity-100' : 'opacity-0'" class="text-xs font-medium text-green-500 transition-opacity duration-300">
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
                                    <flux:button href="#" size="sm" variant="danger" icon="trash">
                                        Eliminar
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                                No hay eventos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
            try { document.execCommand('copy'); } catch (err) { console.error('Error', err); }
            document.body.removeChild(textArea);
        }
    </script>
</x-layouts.app>