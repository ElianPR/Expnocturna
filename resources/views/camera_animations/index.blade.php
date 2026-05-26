<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-neutral-800 dark:text-neutral-200">Animaciones de Cámara</h1>

        @if(auth()->user()?->can_manage_animations)
            <flux:button href="{{ route('camera-animations.create') }}"
                class="rounded-lg bg-black px-4 py-2 text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200"
                icon="plus">
                Agregar animación
            </flux:button>
        @endif
    </div>

    @if(!auth()->user()?->can_manage_animations)
        <div class="flex flex-col items-center justify-center p-12 mt-4 text-center bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:icon.lock-closed class="size-12 text-neutral-400 mb-4" />
            <h2 class="text-xl font-medium text-neutral-900 dark:text-neutral-100">Acceso Denegado</h2>
            <p class="text-neutral-500 mt-2">No tienes permiso para acceder a las animaciones.</p>
        </div>
    @else
        <div class="w-full overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full text-sm whitespace-nowrap">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left">Título</th>
                        <th class="px-4 py-3 text-left">Archivo .MP4 (Pantalla Verde)</th>
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
                                @if($animation->mp4_file)
                                    <a href="{{ route('camera-animations.stream', [$animation->id]) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">Ver archivo</a>
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
                            <td colspan="3" class="px-4 py-6 text-center text-neutral-500">
                                No hay animaciones registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        @endif

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