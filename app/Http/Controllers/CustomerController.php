<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct()
    {
        //🔹 Solo el ADMINISTRADOR y MANTENEDOR pueden acceder a este controlador
        $this->middleware(['auth', 'permission:administrar.clientes.index'])->only('index', 'getData', 'show');
        $this->middleware(['auth', 'permission:administrar.clientes.create'])->only('create', 'store');
        $this->middleware(['auth', 'permission:administrar.clientes.edit'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:administrar.clientes.delete'])->only('destroy');
    }

    public function index()
    {
        return view('customer.index');
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruc' => 'required|unique:customers|max:11',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            //'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('customers', 'public');
            $data['photo'] = $photoPath;
        }

        Customer::create($data);

        return response()->json(['message' => 'Cliente creado correctamente.']);
    }


    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->photo && file_exists(storage_path('app/public/' . $customer->photo))) {
        $customer->photo_url = asset('storage/' . $customer->photo);
        } else {
            $customer->photo_url = asset('assets/images/customers.png'); // Ruta a tu placeholder
        }
        return response()->json($customer);
    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ruc' => 'required|max:11|unique:customers,ruc,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            //'status' => 'required|in:active,inactive',
        ]);

        $customer = Customer::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('photo')) {
            if ($customer->photo) {
                Storage::disk('public')->delete($customer->photo);
            }
            $photoPath = $request->file('photo')->store('customers', 'public');
            $data['photo'] = $photoPath;
        }

        $customer->update($data);

        return response()->json(['message' => 'Cliente actualizado correctamente.']);
    }


    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = 'inactive';
        $customer->save();
        //$customer->delete();

        return response()->json(['message' => 'Cliente eliminado correctamente.']);
    }

        public function getData(Request $request)
    {
        $customers = Customer::select(['id', 'photo', 'ruc', 'name', 'email', 'phone', 'status'])->where('status', 'active');


        if ($request->ajax()) {
        return DataTables::of($customers)
        ->addColumn('photo', function ($customer) {
                $photo = $customer->photo;

                if ($photo && file_exists(storage_path('app/public/' . $photo))) {
                    $url = asset('storage/' . $photo);
                } else {
                    $url = asset('assets/images/customers.png');
                }

                return '<img src="' . $url . '" class="custom-thumbnail" width="30" alt="Foto de ' . e($customer->name) . '">';
            })
        ->addColumn('acciones', function ($customer) {
            $acciones = '';

            if (Auth::user()->can('administrar.clientes.edit')) {
            $acciones .= '
                    <button type="button"
                            class="btn btn-outline-warning btn-sm btn-icon waves-effect waves-light edit-btn"
                            data-id="' . $customer->id . '"
                            title="Editar">
                        <i class="ri-edit-2-line"></i>
                    </button>';
            }
            if (Auth::user()->can('administrar.clientes.delete')) {
            $acciones .= '
                    <button type="button"
                            class="btn btn-outline-danger btn-sm btn-icon waves-effect waves-light delete-btn"
                            data-id="' . $customer->id . '"
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

    public function select(Request $request)
    {
        $query = Customer::query();

        // Incluir cliente inactivo si es edición
        if ($request->has('include_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('status', 'active')
                ->orWhere('id', $request->include_id);
            });
        } else {
            $query->where('status', 'active');
        }

        $clientes = $query->orderBy('id')->get()->map(function ($cliente) {
            return [
                'id' => $cliente->id,
                'text' => $cliente->status === 'active'
                    ? $cliente->name
                    : $cliente->name . ' (inactivo)',
            ];
        });

        return response()->json($clientes);
    }
}
