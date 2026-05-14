<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Animaciones de Cámara</h1>

            <flux:button href="{{ route('camera-animations.create') }}"
                class="rounded-lg bg-black px-4 py-2 text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200"
                icon="plus">
                Agregar animación
            </flux:button>
        </div>

        @if(session('success'))
            <div class="bg-transparent border border-green-500 text-green-600 dark:border-green-400 dark:text-green-400 px-4 py-3 rounded-xl relative" role="alert">
                <div class="flex items-center gap-3">
                    <flux:icon.check-circle class="size-5" />
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="w-full overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full text-sm whitespace-nowrap">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left">Título</th>
                        <th class="px-4 py-3 text-left">Archivo .MOV (iOS)</th>
                        <th class="px-4 py-3 text-left">Archivo .WEBM (Android)</th>
                        <th class="px-4 py-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($animations as $animation)
                        <tr class="border-t border-neutral-200 dark:border-neutral-700">
                            <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $animation->title }}
                            </td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                @if($animation->mov_file)
                                    <a href="{{ route('camera-animations.stream', [$animation->id, 'mov']) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">Ver archivo</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                @if($animation->webm_file)
                                    <a href="{{ route('camera-animations.stream', [$animation->id, 'webm']) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">Ver archivo</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <flux:button href="{{ route('camera-animations.edit', $animation) }}" size="sm" variant="subtle" icon="pencil-square">
                                        Editar
                                    </flux:button>
                                    <flux:button type="button" size="sm" variant="danger" icon="trash" onclick="confirmAnimationDelete('{{ route('camera-animations.destroy', $animation) }}')">
                                        Eliminar
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                                No hay animaciones registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmAnimationDelete(url) {
            Swal.fire({
                title: '¿Eliminar animación?',
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
