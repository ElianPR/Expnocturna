<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-neutral-800 dark:text-neutral-200">Papelera de Reciclaje</h1>
    </div>

    @if (session('success'))
        @endif

    <div class="w-full overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="min-w-full text-sm whitespace-nowrap">
            <thead class="bg-neutral-100 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
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
                        <td class="px-4 py-3 text-neutral-500">
                            {{ $event->deleted_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 {{ $colorDias }}">
                            {{ $diasRestantes }} días
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <form action="{{ route('events.restore', $event->id_hex) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <flux:button type="submit" size="sm" variant="primary" icon="arrow-path">
                                        Restaurar
                                    </flux:button>
                                </form>

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
                        <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                            La papelera está vacía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
    </script>
</x-layouts.app>