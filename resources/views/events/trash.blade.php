<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-neutral-800 dark:text-neutral-200">Papelera de Reciclaje</h1>
    </div>

    @if (session('swal_success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('swal_success') }}',
                    confirmButtonColor: '#000',
                });
            });
        </script>
    @endif

    @if (session('swal_error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('swal_error') }}',
                    confirmButtonColor: '#000',
                });
            });
        </script>
    @endif

    @if(!auth()->user()->can_access_trash)
        <div class="flex flex-col items-center justify-center p-12 mt-4 text-center bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:icon.lock-closed class="size-12 text-neutral-400 mb-4" />
            <h2 class="text-xl font-medium text-neutral-900 dark:text-neutral-100">Acceso Denegado</h2>
            <p class="text-neutral-500 mt-2">No tienes permiso para acceder a la papelera.</p>
        </div>
    @else

    <div class="w-full overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="min-w-full text-sm whitespace-nowrap">
            <thead class="bg-neutral-100 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Creador</th>
                    <th class="px-4 py-3 text-left">Eliminado el</th>
                    <th class="px-4 py-3 text-left">Tiempo restante</th>
                    <th class="px-4 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    @php
                        // Obtenemos los días como un número entero absoluto usando (int) o round()
                        $diasEnPapelera = (int) round($event->deleted_at->diffInDays(now()));
                        $diasRestantes = 60 - $diasEnPapelera;
                        
                        // Si por alguna razón el sistema se retrasa en borrarlo, evitamos que salga "-1 días"
                        if ($diasRestantes < 0) {
                            $diasRestantes = 0;
                        }
                        
                        $colorDias = $diasRestantes <= 5 ? 'text-red-500 font-bold' : 'text-orange-500';
                    @endphp
                    <tr class="border-t border-neutral-200 dark:border-neutral-700">
                        <td class="px-4 py-3 font-semibold text-neutral-500">
                            {{ $event->name ?? $event->monogram ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-neutral-500">
                            {{ $event->user->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-neutral-500">
                            {{ $event->deleted_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 {{ $colorDias }}">
                            {{ $diasRestantes }} días
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button 
                                    type="button"
                                    size="sm" 
                                    variant="primary" 
                                    icon="arrow-path"
                                    onclick="confirmRestore('{{ route('events.restore', $event->id_hex) }}')"
                                >
                                    Restaurar
                                </flux:button>

                                <flux:button 
                                    type="button"
                                    size="sm" 
                                    variant="danger" 
                                    icon="trash"
                                    onclick="confirmPermanentDelete('{{ route('events.force-destroy', $event->id_hex) }}')"
                                >
                                    Destruir
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-neutral-500">
                            La papelera está vacía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmPermanentDelete(url) {
            Swal.fire({
                title: '¿Destrucción total?',
                text: "Esto borrará los archivos del servidor para siempre. No hay vuelta atrás.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Destruir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmRestore(url) {
            Swal.fire({
                title: '¿Restaurar evento?',
                text: "El evento volverá a estar activo en tu lista principal.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `@csrf @method('PATCH')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</x-layouts.app>