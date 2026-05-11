<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        // En Laravel 11 / nuevos middlewares en controlador pueden no ser directos,
        // pero podemos simplemente validar en una closure aquí o en los métodos, o usar request.
        // Lo más seguro en Laravel >=11 es usar un middleware de closure pero dentro del routing.
        // Sin embargo para este caso simple, como todas las rutas lo requieren:
    }

    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = collect();
        if (auth()->user()->can_create_users) {
            $users = User::all();
        }
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (!auth()->user()->can_create_users) {
            abort(403, 'No tienes permisos para administrar usuarios.');
        }

        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can_create_users) {
            abort(403, 'No tienes permisos para administrar usuarios.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'can_create_users' => $request->boolean('can_create_users'),
            'can_manage_events' => $request->boolean('can_manage_events'),
            'can_access_trash' => $request->boolean('can_access_trash'),
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        if (!auth()->user()->can_create_users) {
            abort(403, 'No tienes permisos para administrar usuarios.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->can_create_users) {
            abort(403, 'No tienes permisos para administrar usuarios.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ];

        // Validar contraseña solo si se envió un valor
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Only allow changing permissions if the current user is not editing themselves and losing admin rights? 
        // We assume trust, just apply permissions.
        $user->can_create_users = $request->boolean('can_create_users');
        $user->can_manage_events = $request->boolean('can_manage_events');
        $user->can_access_trash = $request->boolean('can_access_trash');

        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->can_create_users) {
            abort(403, 'No tienes permisos para administrar usuarios.');
        }

        // Prevenir que el usuario se elimine a sí mismo
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('swal_error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
