<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl max-w-2xl mx-auto">
        
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Editar Animación</h1>
            <flux:button href="{{ route('camera-animations.index') }}" variant="subtle" icon="arrow-left">
                Volver
            </flux:button>
        </div>

        <div class="bg-blue-50/50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <flux:icon.information-circle class="size-5 text-blue-500 dark:text-blue-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Información sobre los archivos</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Archivo .MOV:</strong> Para iOS y Safari. <br> <span class="text-xs opacity-75">Actualmente: {{ $cameraAnimation->mov_file ? 'Subido' : 'No disponible' }}</span></li>
                            <li><strong>Archivo .WEBM:</strong> Para Android y Chrome/Edge. <br> <span class="text-xs opacity-75">Actualmente: {{ $cameraAnimation->webm_file ? 'Subido' : 'No disponible' }}</span></li>
                        </ul>
                        <p class="mt-2 text-xs">Deja los campos de archivo vacíos si no deseas reemplazarlos.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <form action="{{ route('camera-animations.update', $cameraAnimation) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf
                @method('PUT')

                <flux:input 
                    name="title" 
                    label="Título de la animación" 
                    value="{{ old('title', $cameraAnimation->title) }}" 
                    required 
                />

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-neutral-800 dark:text-neutral-200">Reemplazar Archivo .MOV (iOS)</label>
                    <input type="file" name="mov_file" accept=".mov,.qt,.mp4" class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-neutral-100 file:text-neutral-700 hover:file:bg-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-200 dark:hover:file:bg-neutral-600 dark:text-neutral-400">
                    @error('mov_file')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-neutral-800 dark:text-neutral-200">Reemplazar Archivo .WEBM (Android/Chrome)</label>
                    <input type="file" name="webm_file" accept=".webm" class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-neutral-100 file:text-neutral-700 hover:file:bg-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-200 dark:hover:file:bg-neutral-600 dark:text-neutral-400">
                    @error('webm_file')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-4 border-t border-neutral-200 dark:border-neutral-700">
                    <flux:button type="submit" variant="primary">
                        Actualizar Animación
                    </flux:button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>
