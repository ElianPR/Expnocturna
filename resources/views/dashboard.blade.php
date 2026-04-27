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
                        <th class="px-4 py-3 text-left">Fechas</th>
                        <th class="px-4 py-3 text-center">Estado de portada</th>
                        <th class="px-4 py-3 text-center">Estado de Álbum</th>
                        <th class="px-4 py-3 text-left">Enlaces</th>
                        <th class="px-4 py-3 text-left">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($events as $event)
                        @php
                            $today = \Carbon\Carbon::now()->startOfDay();
                            
                            // Lógica para EVENTO
                            $eventoAviso = null;
                            $eventoColor = '';
                            
                            if ($event->date) {
                                $diffInicio = $today->diffInDays($event->date, false);
                                
                                if (!$event->is_active) {
                                    if ($event->cover_expiration && $today->diffInDays($event->cover_expiration, false) < 0) {
                                        $eventoAviso = 'Finalizado';
                                        $eventoColor = 'bg-neutral-100 text-neutral-600 border-neutral-300 dark:bg-neutral-800 dark:text-neutral-400 dark:border-neutral-700';
                                    } elseif ($diffInicio <= 0) {
                                        $eventoAviso = 'Requiere Activar';
                                        $eventoColor = 'bg-red-100 text-red-700 border-red-300 dark:bg-red-900/50 dark:text-red-300 dark:border-red-800';
                                    } elseif ($diffInicio <= 3) {
                                        $eventoAviso = 'Pronto a Iniciar';
                                        $eventoColor = 'bg-yellow-100 text-yellow-700 border-yellow-300 dark:bg-yellow-900/50 dark:text-yellow-300 dark:border-yellow-800';
                                    } else {
                                        $eventoAviso = 'En Espera';
                                        $eventoColor = 'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-800';
                                    }
                                } else {
                                    if ($event->cover_expiration) {
                                        $diffExp = $today->diffInDays($event->cover_expiration, false);
                                        if ($diffExp < 0) {
                                            $eventoAviso = 'Expirado, Desactivar';
                                            $eventoColor = 'bg-red-100 text-red-700 border-red-300 dark:bg-red-900/50 dark:text-red-300 dark:border-red-800';
                                        } elseif ($diffExp <= 3) {
                                            $eventoAviso = 'Próximo a Expirar';
                                            $eventoColor = 'bg-orange-100 text-orange-700 border-orange-300 dark:bg-orange-900/50 dark:text-orange-300 dark:border-orange-800';
                                        }
                                    }
                                }
                            }

                            // Lógica para ALBUM
                            $albumAviso = null;
                            $albumColor = '';

                            if ($event->album_availability) {
                                $diffAlb = $today->diffInDays($event->album_availability, false);
                                if (!$event->album_active) {
                                    if ($event->album_expiration && $today->diffInDays($event->album_expiration, false) < 0) {
                                        $albumAviso = 'Finalizado';
                                        $albumColor = 'bg-neutral-100 text-neutral-600 border-neutral-300 dark:bg-neutral-800 dark:text-neutral-400 dark:border-neutral-700';
                                    } elseif ($diffAlb <= 0) {
                                        $albumAviso = 'Requiere Activar';
                                        $albumColor = 'bg-red-100 text-red-700 border-red-300 dark:bg-red-900/50 dark:text-red-300 dark:border-red-800';
                                    } elseif ($diffAlb <= 3) {
                                        $albumAviso = 'Pronto a Iniciar';
                                        $albumColor = 'bg-yellow-100 text-yellow-700 border-yellow-300 dark:bg-yellow-900/50 dark:text-yellow-300 dark:border-yellow-800';
                                    } else {
                                        $albumAviso = 'En Espera';
                                        $albumColor = 'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-800';
                                    }
                                } else {
                                    if ($event->album_expiration) {
                                        $diffAlbExp = $today->diffInDays($event->album_expiration, false);
                                        if ($diffAlbExp < 0) {
                                            $albumAviso = 'Expirado, Desactivar';
                                            $albumColor = 'bg-red-100 text-red-700 border-red-300 dark:bg-red-900/50 dark:text-red-300 dark:border-red-800';
                                        } elseif ($diffAlbExp <= 3) {
                                            $albumAviso = 'Próximo a Expirar';
                                            $albumColor = 'bg-orange-100 text-orange-700 border-orange-300 dark:bg-orange-900/50 dark:text-orange-300 dark:border-orange-800';
                                        }
                                    }
                                }
                            } elseif ($event->album_active && $event->album_expiration) {
                                $diffAlbExp = $today->diffInDays($event->album_expiration, false);
                                if ($diffAlbExp < 0) {
                                    $albumAviso = 'Expirado, Desactivar';
                                    $albumColor = 'bg-red-100 text-red-700 border-red-300 dark:bg-red-900/50 dark:text-red-300 dark:border-red-800';
                                } elseif ($diffAlbExp <= 3) {
                                    $albumAviso = 'Próximo a Expirar';
                                    $albumColor = 'bg-orange-100 text-orange-700 border-orange-300 dark:bg-orange-900/50 dark:text-orange-300 dark:border-orange-800';
                                }
                            } elseif (!$event->album_active && $event->album_expiration) {
                                if ($today->diffInDays($event->album_expiration, false) < 0) {
                                    $albumAviso = 'Finalizado';
                                    $albumColor = 'bg-neutral-100 text-neutral-600 border-neutral-300 dark:bg-neutral-800 dark:text-neutral-400 dark:border-neutral-700';
                                }
                            }
                        @endphp
                        <tr class="border-t border-neutral-200 dark:border-neutral-700">
                            <td class="px-4 py-3">
                                <a href="{{ route('events.qr', $event->id_hex) }}"
                                    class="font-semibold text-neutral-800 hover:text-blue-600 hover:underline dark:text-neutral-200 dark:hover:text-blue-400 transition-colors">
                                    {{ $event->name ?? ($event->monogram ?? '—') }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="flex flex-col gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Evento</span>
                                            @if($eventoAviso)
                                                <span class="text-[9px] font-bold px-2 py-0.5 rounded border {{ $eventoColor }}">{{ $eventoAviso }}</span>
                                            @endif
                                        </div>
                                        <span class="text-neutral-700 dark:text-neutral-300 whitespace-nowrap">
                                            {{ $event->date ? $event->date->format('d/m/Y') : '—' }} 
                                            @if($event->cover_expiration)
                                                - {{ $event->cover_expiration->format('d/m/Y') }}
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Álbum</span>
                                            @if($albumAviso)
                                                <span class="text-[9px] font-bold px-2 py-0.5 rounded border {{ $albumColor }}">{{ $albumAviso }}</span>
                                            @endif
                                        </div>
                                        <span class="text-neutral-700 dark:text-neutral-300 whitespace-nowrap">
                                            {{ $event->album_availability ? $event->album_availability->format('d/m/Y') : '—' }} 
                                            @if($event->album_expiration)
                                                - {{ $event->album_expiration->format('d/m/Y') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
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

                                        <span x-show="!loading" :class="active ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                        </span>

                                        <div x-show="loading" class="absolute inset-0 flex items-center justify-center"
                                            x-cloak>
                                            <svg class="animate-spin h-4 w-4 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z">
                                                </path>
                                            </svg>
                                        </div>

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

                                        <span x-show="!loading" :class="active ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                        </span>

                                        <div x-show="loading" class="absolute inset-0 flex items-center justify-center"
                                            x-cloak>
                                            <svg class="animate-spin h-4 w-4 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z">
                                                </path>
                                            </svg>
                                        </div>

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
                                                target="_blank"
                                                class="text-red-600 hover:underline dark:text-red-400">
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
                                    <flux:button href="{{ route('events.edit', $event->id_hex) }}" size="sm"
                                        variant="subtle" icon="pencil-square">
                                        Editar
                                    </flux:button>
                                    <flux:button type="button" size="sm" variant="danger" icon="trash"
                                        onclick="confirmDelete('{{ route('events.destroy', $event->id_hex) }}')">
                                        Mover a la papelera
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
                text: "El evento se movera a la papelera y sera eliminado permanentemente despuede de 60 dias.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Rojo de peligro
                cancelButtonColor: '#6b7280', // Gris neutro
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
