<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Eventos</h1>

            <button
                class="rounded-lg bg-black px-4 py-2 text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                Agregar evento
            </button>
        </div>

        <!-- Tabla -->
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Plantilla</th>
                        <th class="px-4 py-3 text-left">Usuario</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($events as $event)
                        <tr class="border-t border-neutral-200 dark:border-neutral-700">
                            <td class="px-4 py-3">
                                {{ $event->name ?? '—' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $event->date }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $event->template }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $event->id_user }}
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
</x-layouts.app>