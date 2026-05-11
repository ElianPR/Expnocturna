<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Usuarios</h1>

            @if(auth()->user()->can_create_users)
                <flux:button href="{{ route('users.create') }}"
                    class="rounded-lg bg-black px-4 py-2 text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200"
                    icon="plus">
                    Agregar usuario
                </flux:button>
            @endif
        </div>

        @if(!auth()->user()->can_create_users)
            <div class="flex flex-col items-center justify-center p-12 mt-4 text-center bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
                <flux:icon.lock-closed class="size-12 text-neutral-400 mb-4" />
                <h2 class="text-xl font-medium text-neutral-900 dark:text-neutral-100">Acceso Denegado</h2>
                <p class="text-neutral-500 mt-2">No tienes permiso para administrar usuarios.</p>
            </div>
        @else

        <div class="w-full overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full text-sm whitespace-nowrap">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Correo</th>
                        <th class="px-4 py-3 text-left">Permisos</th>
                        <th class="px-4 py-3 text-left">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-t border-neutral-200 dark:border-neutral-700">
                            <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="ml-2 text-[10px] font-bold uppercase tracking-wider text-green-600 bg-green-100 px-2 py-0.5 rounded-full dark:bg-green-900/30 dark:text-green-400">Tú</span>
                                @endif
                            </td>
                            
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                {{ $user->email }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex gap-2 flex-wrap">
                                    @if($user->can_create_users)
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-900/50 dark:text-purple-300 dark:border-purple-800">Usuarios</span>
                                    @endif
                                    @if($user->can_manage_events)
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-800">Eventos</span>
                                    @endif
                                    @if($user->can_access_trash)
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border bg-red-100 text-red-700 border-red-300 dark:bg-red-900/50 dark:text-red-300 dark:border-red-800">Papelera</span>
                                    @endif
                                    @if(!$user->can_create_users && !$user->can_manage_events && !$user->can_access_trash)
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Sin permisos adicionales</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <flux:button href="{{ route('users.edit', $user->id) }}" size="sm" variant="subtle" icon="pencil-square">
                                        Editar
                                    </flux:button>
                                    @if($user->id !== auth()->id())
                                        <flux:button type="button" size="sm" variant="danger" icon="trash" onclick="confirmUserDelete('{{ route('users.destroy', $user->id) }}')">
                                            Eliminar
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                                No hay usuarios registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmUserDelete(url) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
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
    </script>
</x-layouts.app>
