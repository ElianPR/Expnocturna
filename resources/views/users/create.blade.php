<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl max-w-2xl mx-auto">
        
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Crear Usuario</h1>
            <flux:button href="{{ route('users.index') }}" variant="subtle" icon="arrow-left">
                Volver
            </flux:button>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <form action="{{ route('users.store') }}" method="POST" class="flex flex-col gap-6">
                @csrf

                <flux:input 
                    name="name" 
                    label="Nombre" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus 
                />

                <flux:input 
                    name="email" 
                    type="email" 
                    label="Correo electrónico" 
                    value="{{ old('email') }}" 
                    required 
                />

                <flux:input 
                    name="password" 
                    type="password" 
                    label="Contraseña" 
                    required 
                    viewable
                />

                <flux:input 
                    name="password_confirmation" 
                    type="password" 
                    label="Confirmar Contraseña" 
                    required 
                    viewable
                />

                <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                    <h3 class="font-medium text-lg">Permisos</h3>
                    
                    <flux:checkbox 
                        name="can_create_users" 
                        label="Crear y administrar usuarios" 
                        description="Permite acceder a esta sección para agregar, editar o eliminar a otros usuarios." 
                        :checked="old('can_create_users')"
                    />

                    <flux:checkbox 
                        name="can_manage_events" 
                        label="Gestionar eventos" 
                        description="Permite crear, actualizar y eliminar eventos." 
                        :checked="old('can_manage_events')"
                    />

                    <flux:checkbox 
                        name="can_access_trash" 
                        label="Acceder a la papelera" 
                        description="Permite ver, restaurar y eliminar permanentemente eventos de la papelera." 
                        :checked="old('can_access_trash')"
                    />
                </div>

                <div class="flex justify-end pt-4">
                    <flux:button type="submit" variant="primary">
                        Guardar Usuario
                    </flux:button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>
