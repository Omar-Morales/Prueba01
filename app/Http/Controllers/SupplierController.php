<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function __construct()
    {
        // 🔹 Solo el ADMINISTRADOR y MANTENEDOR pueden acceder a este controlador
        $this->middleware(['auth', 'permission:administrar.proveedores.index'])->only('index', 'getData', 'show');
        $this->middleware(['auth', 'permission:administrar.proveedores.create'])->only('create', 'store');
        $this->middleware(['auth', 'permission:administrar.proveedores.edit'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:administrar.proveedores.delete'])->only('destroy');
    }

    public function index()
    {
        return view('supplier.index');
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruc' => 'required|unique:suppliers|max:11',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            //'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('suppliers', 'public');
            $data['photo'] = $photoPath;
        }

        Supplier::create($data);

        return response()->json(['message' => 'Proveedor creado correctamente.']);
    }

    public function show(string $id)
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier->photo && file_exists(storage_path('app/public/' . $supplier->photo))) {
        $supplier->photo_url = asset('storage/' . $supplier->photo);
        } else {
            $supplier->photo_url = asset('assets/images/suppliers.png'); // Ruta a tu placeholder
        }
        return response()->json($supplier);
    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'ruc' => 'required|max:11|unique:suppliers,ruc,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $id,
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            //'status' => 'required|in:active,inactive',
        ]);

        $supplier = Supplier::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('photo')) {
            if ($supplier->photo) {
                Storage::disk('public')->delete($supplier->photo);
            }
            $photoPath = $request->file('photo')->store('suppliers', 'public');
            $data['photo'] = $photoPath;
        }

        $supplier->update($data);

        return response()->json(['message' => 'Proveedor actualizado correctamente.']);
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->status = 'inactive';
        $supplier->save();
        //$supplier->delete();

        return response()->json(['message' => 'Proveedor eliminado correctamente.']);
    }

            public function getData(Request $request)
    {
        $suppliers = Supplier::select(['id', 'photo', 'ruc', 'name', 'email', 'phone', 'status'])->where('status', 'active');


        if ($request->ajax()) {
        return DataTables::of($suppliers)
            ->addColumn('photo', function ($supplier) {
                $photo = $supplier->photo;

                if ($photo && file_exists(storage_path('app/public/' . $photo))) {
                    $url = asset('storage/' . $photo);
                } else {
                    $url = asset('assets/images/suppliers.png');
                }

                return '<img src="' . $url . '" class="custom-thumbnail" width="30" alt="Foto de ' . e($supplier->name) . '">';
            })
            ->addColumn('acciones', function ($supplier) {
            $acciones = '';

            if (Auth::user()->can('administrar.proveedores.edit')) {
            $acciones .= '
                    <button type="button"
                            class="btn btn-outline-warning btn-sm btn-icon waves-effect waves-light edit-btn"
                            data-id="' . $supplier->id . '"
                            title="Editar">
                        <i class="ri-edit-2-line"></i>
                    </button>';
            }
            if (Auth::user()->can('administrar.proveedores.delete')) {
            $acciones .= '
                    <button type="button"
                            class="btn btn-outline-danger btn-sm btn-icon waves-effect waves-light delete-btn"
                            data-id="' . $supplier->id . '"
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

    // SupplierController.php
    public function select(Request $request)
    {
        $query = Supplier::query();

        // Incluir proveedor inactivo si es edición
        if ($request->has('include_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('status', 'active')
                ->orWhere('id', $request->include_id);
            });
        } else {
            $query->where('status', 'active');
        }

        $proveedores = $query->orderBy('id')->get()->map(function ($proveedor) {
            return [
                'id' => $proveedor->id,
                'text' => $proveedor->status === 'active'
                        ? $proveedor->name
                        : $proveedor->name . ' (inactivo)',
            ];
        });

        return response()->json($proveedores);
    }


}
