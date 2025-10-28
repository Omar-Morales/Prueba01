<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        // 🔹 Solo el ADMINISTRADOR y MANTENEDOR pueden acceder a este controlador
        $this->middleware(['auth', 'permission:administrar.usuarios.index'])->only('index', 'getData', 'show');
        $this->middleware(['auth', 'permission:administrar.usuarios.create'])->only('create', 'store');
        $this->middleware(['auth', 'permission:administrar.usuarios.edit'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:administrar.usuarios.delete'])->only('destroy');
    }

    public function index()
    {
        return view('user.index');
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:9',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = $request->except('password', 'photo');
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users', 'public');
        }

        User::create($data);

        // Asignar rol con Spatie
        $role = Role::find($request->role_id);
        if ($role) {
            $user->assignRole($role->name);
        }

        return response()->json(['message' => 'Usuario creado correctamente.']);
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        $user->photo_url = $user->photo && Storage::disk('public')->exists($user->photo)
            ? asset('storage/' . $user->photo)
            : asset('assets/images/users.jpg');

        $user->role_id = $user->roles->first()?->id; // Para llenar el select
        $user->role_name = $user->roles->first()?->name;
        return response()->json($user);
    }

    public function edit(string $id)
    {
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:9',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $data = $request->except('password', 'photo');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user->update($data);

        // Actualizar rol
        $role = Role::find($request->role_id);
        if ($role) {
            $user->syncRoles($role->name);
        }

        return response()->json(['message' => 'Usuario actualizado correctamente.']);
    }


    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->status = 'inactive';
        $user->save();
        /*
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->delete();*/

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }

            public function getData(Request $request)
    {
        $users = User::with('roles')->where('status', 'active');

            if ($request->ajax()) {
                return DataTables::of($users)
                    ->addColumn('photo', function ($user) {
                    $url = $user->photo && Storage::disk('public')->exists($user->photo)
                    ? asset('storage/' . $user->photo)
                    : asset('assets/images/users.jpg');

                    return '<img src="' . $url . '" class="custom-thumbnail" width="30" alt="Foto de ' . e($user->name) . '">';
                    })
                    ->addColumn('role', function ($user) {
                        return $user->roles->first()->name ?? 'Sin rol';
                    })
                    ->addColumn('acciones', function ($user) {
                    $acciones = '';

                    if (Auth::user()->can('administrar.usuarios.edit')) {
                        $acciones .= '
                        <button type="button"
                                class="btn btn-outline-warning btn-sm btn-icon waves-effect waves-light edit-btn"
                                data-id="' . $user->id . '"
                                title="Editar">
                            <i class="ri-edit-2-line"></i>
                        </button>';
                    }

                    if (Auth::user()->can('administrar.usuarios.delete')) {
                    $acciones .= '
                        <button type="button"
                                class="btn btn-outline-danger btn-sm btn-icon waves-effect waves-light delete-btn"
                                data-id="' . $user->id . '"
                                title="Eliminar">
                            <i class="ri-delete-bin-5-line"></i>
                        </button>';
                    }

                    return $acciones ?: '<span class="text-muted">Sin acciones</span>';
                    })
                    ->rawColumns(['photo', 'acciones'])
                    ->make(true);
            }

            return response()->json(['error' => 'Acceso no permitido'], 403);
    }
}
