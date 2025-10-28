<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Venta;
use App\Models\VentaLog;
use App\Models\DetalleVenta;
use App\Models\Transaction;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Customer;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function __construct()
    {
        // 🔹 Solo el ADMINISTRADOR y Vendedor pueden acceder a este controlador
        $this->middleware('permission:administrar.ventas.index')->only(['index', 'getData', 'detalle', 'show']);
        $this->middleware('permission:administrar.ventas.create')->only(['store']);
        $this->middleware('permission:administrar.ventas.edit')->only(['update']);
        $this->middleware('permission:administrar.ventas.delete')->only(['destroy']);
    }

    public function index()
    {
        $tiposDocumento = TipoDocumento::where('type', 'venta')->get();

        return view('venta.index', compact('tiposDocumento'));
    }

    public function create()
    {
    }

    protected function actualizarEstadoProducto(Product $producto)
    {
        $producto->refresh();

        if ($producto->quantity <= 0 && $producto->status !== 'sold') {
            $producto->update(['status' => 'sold']);
        } elseif ($producto->quantity > 0 && $producto->status !== 'available') {
            $producto->update(['status' => 'available']);
        }
    }

    public function store(Request $request)
{
    $details = json_decode($request->details, true);

    if (!is_array($details) || empty($details)) {
        return response()->json(['message' => 'Detalles inválidos.'], 422);
    }

    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'tipodocumento_id' => 'required|exists:tipodocumento,id',
        'sale_date' => 'required|date',
        'payment_method' => 'required|in:cash,card,transfer',
        'status' => 'nullable|in:completed,pending',
    ]);

    // Validar cliente activo
    $cliente = Customer::find($request->customer_id);
    if ($cliente && $cliente->status === 'inactive') {
        return response()->json(['message' => "El cliente '{$cliente->name}' está inactivo."], 422);
    }

    // Validar productos
    $idsProductos = collect($details)->pluck('product_id')->unique();
    $productos = Product::whereIn('id', $idsProductos)->get()->keyBy('id');

    foreach ($idsProductos as $idProd) {
        $producto = $productos[$idProd] ?? null;
        if (!$producto) {
            return response()->json(['message' => "El producto con ID $idProd no existe."], 422);
        }
        if ($producto->status === 'archived') {
            return response()->json(['message' => "El producto '{$producto->name}' está archivado."], 422);
        }
    }

    DB::beginTransaction();

    try {
        $total = collect($details)->sum(fn($item) => $item['quantity'] * $item['unit_price']);
        $status = $request->status ?? 'completed';

        $venta = Venta::create([
            'customer_id' => $request->customer_id,
            'tipodocumento_id' => $request->tipodocumento_id,
            'user_id' => auth()->id(),
            'sale_date' => $request->sale_date,
            'payment_method' => $request->payment_method,
            'status' => $status,
            'total_price' => $total,
            'codigo' => null,
        ]);

        $codigo = 'VNT-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT);
        $venta->update(['codigo' => $codigo]);

        foreach ($details as $item) {
            DetalleVenta::create([
                'sale_id' => $venta->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity'] * $item['unit_price'],
            ]);

            // Registra en inventario con descripción distinta según el estado
            Inventory::create([
                'product_id' => $item['product_id'],
                'type' => 'sale',
                'quantity' => -$item['quantity'],
                'reason' => $status === 'completed'
                    ? 'Venta ID: ' . $venta->id
                    : 'Venta pendiente ID: ' . $venta->id,
                'user_id' => auth()->id(),
                'reference_id' => $venta->id,
            ]);


            // Solo reducir el stock si es completado
            if ($status === 'completed') {
            $producto = $productos[$item['product_id']];
                if ($producto->quantity < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para: {$producto->name}");
                }
                $producto->decrement('quantity', $item['quantity']);
                $this->actualizarEstadoProducto($producto);
            }
        }

        // Registrar transacción con descripción apropiada
        Transaction::create([
            'type' => 'sale',
            'amount' => $total,
            'reference_id' => $venta->id,
            'description' => $status === 'completed'
                ? 'Venta ID: ' . $venta->id
                : 'Venta pendiente ID: ' . $venta->id,
            'user_id' => auth()->id(),
        ]);

        // Log
        $this->logVenta($venta->id, 'created', [], [
            'new_data' => $venta->toArray(),
            'new_details' => $details,
        ]);

        DB::commit();

        return response()->json(['message' => 'Venta registrada correctamente.']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}


    public function show($id)
    {
        /*$venta = Venta::with('detalles')->findOrFail($id);

        return response()->json([
            'id' => $venta->id,
            'customer' => $venta->customer_id,
            'tipo_documento' => $venta->tipodocumento_id,
            'fecha' => Carbon::parse($venta->sale_date)->format('Y-m-d'),
            'total' => $venta->total_price,
            'estado' => $venta->status,
            'payment_method' => $venta->payment_method,
            'codigo' => $venta->codigo,
            'detalle' => $venta->detalles
        ]);*/

    $venta = Venta::with('detalles')->findOrFail($id);

    // Traer clientes activos o el cliente actual si está inactivo
    $clientesQuery = Customer::query();
    $clientesQuery->where(function ($q) use ($venta) {
        $q->where('status', 'active')
          ->orWhere('id', $venta->customer_id);
    });

    $clientes = $clientesQuery->orderBy('id')->get()->map(function ($cliente) {
        return [
            'id' => $cliente->id,
            'text' => $cliente->status === 'active'
                ? $cliente->name
                : $cliente->name . ' (inactivo)',
        ];
    });

    return response()->json([
        'venta' => [
            'id' => $venta->id,
            'customer' => $venta->customer_id,
            'tipo_documento' => $venta->tipodocumento_id,
            'fecha' => Carbon::parse($venta->sale_date)->format('Y-m-d'),
            'total' => $venta->total_price,
            'estado' => $venta->status,
            'payment_method' => $venta->payment_method,
            'codigo' => $venta->codigo,
            'detalle' => $venta->detalles
        ],
        'clientes' => $clientes,
    ]);
    }

    public function edit(string $id)
    {
    }
/*
    public function update(Request $request, $id)
    {
        $venta = Venta::findOrFail($id);
        $originalData = $venta->toArray(); // 📝 Backup datos anteriores

        $details = json_decode($request->details, true);

        if (!is_array($details) || empty($details)) {
            return response()->json(['message' => 'Detalles inválidos.'], 422);
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tipodocumento_id' => 'required|exists:tipodocumento,id',
            'sale_date' => 'required|date',
            'codigo' => 'nullable|string|max:50|unique:ventas,codigo,' . $venta->id,
            'payment_method' => 'required|in:cash,card,transfer',
            'status' => 'nullable|in:completed,pending',
            'details' => 'required',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($venta, $request, $details, $originalData) {
            $detallesAnteriores = collect();

            // 🔄 Revertir stock si la venta anterior estaba completada
            if ($venta->status === 'completed') {
                $detallesAnteriores = DetalleVenta::where('sale_id', $venta->id)->get();

                foreach ($detallesAnteriores as $detalle) {
                    $producto = Product::find($detalle->product_id);
                    if ($producto) {
                        $producto->quantity += $detalle->quantity;
                        $producto->save();
                    }
                }
            }

            // 🧹 Limpiar detalles e inventario previos
            DetalleVenta::where('sale_id', $venta->id)->delete();
            Inventory::where('reference_id', $venta->id)->delete();

            // 💲 Calcular nuevo total
            $total = collect($details)->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            // ✏️ Actualizar venta
            $venta->update([
                'customer_id' => $request->customer_id,
                'tipodocumento_id' => $request->tipodocumento_id,
                'sale_date' => $request->sale_date,
                'status' => $request->status ?? 'completed',
                'payment_method' => $request->payment_method,
                'total_price' => $total,
                'codigo' => $request->codigo ?? $venta->codigo,
            ]);

            // 🔎 Verificar integridad de productos
            $idsProductos = collect($details)->pluck('product_id');
            $productos = Product::whereIn('id', $idsProductos)->get()->keyBy('id');

            if ($productos->count() !== $idsProductos->unique()->count()) {
                throw new \Exception('Uno o más productos ya no existen.');
            }

            // 🧾 Guardar nuevos detalles
            foreach ($details as $item) {
                DetalleVenta::create([
                    'sale_id' => $venta->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);

                Inventory::create([
                    'product_id' => $item['product_id'],
                    'type' => 'sale',
                    'quantity' => -$item['quantity'],
                    'reason' => 'Venta ID: ' . $venta->id,
                    'reference_id' => $venta->id,
                    'user_id' => auth()->id(),
                ]);

                if ($venta->status === 'completed') {
                    $producto = $productos[$item['product_id']] ?? null;
                    if ($producto) {
                        $producto->quantity -= $item['quantity'];
                        $producto->save();
                    }
                }
            }

            Transaction::where('reference_id', $venta->id)
                ->where('type', 'sale')
                ->update([
                    'amount' => $total,
                    'description' => 'Venta ID: ' . $venta->id,
                ]);

            // 📋 Registrar log
            $this->logVenta($venta->id, 'updated', [
                'old_data' => $originalData,
                'old_details' => $detallesAnteriores->toArray(),
            ], [
                'new_data' => $venta->getChanges(),
                'new_details' => $details,
            ]);
        });

        return response()->json(['message' => 'Venta actualizada correctamente.']);
    }
*/
    public function update(Request $request, $id)
    {
        $venta = Venta::findOrFail($id);
        $originalData = $venta->toArray();
        $originalStatus = $venta->status;

        $details = json_decode($request->details, true);
        if (!is_array($details) || empty($details)) {
            return response()->json(['message' => 'Detalles inválidos.'], 422);
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tipodocumento_id' => 'required|exists:tipodocumento,id',
            'sale_date' => 'required|date',
            'codigo' => 'nullable|string|max:50|unique:ventas,codigo,' . $venta->id,
            'payment_method' => 'required|in:cash,card,transfer',
            'status' => 'nullable|in:completed,pending',
        ]);

        foreach ($details as $index => $item) {
            if (
                !isset($item['product_id']) || !is_numeric($item['product_id']) ||
                !isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] < 1 ||
                !isset($item['unit_price']) || !is_numeric($item['unit_price']) || $item['unit_price'] < 0
            ) {
                return response()->json([
                    'message' => "Detalle inválido en la posición $index.",
                    'errors' => [
                        "details[$index]" => ['Debe tener un product_id, quantity y unit_price válidos.']
                    ],
                ], 422);
            }
        }

        DB::transaction(function () use ($venta, $request, $details, $originalData, $originalStatus) {
            $nuevoStatus = $request->status ?? 'completed';

            // Obtener detalles e inventario previos
            $detallesAnteriores = DetalleVenta::where('sale_id', $venta->id)->get()->keyBy('product_id');
            $inventarioAnterior = Inventory::where('reference_id', $venta->id)->get()->keyBy('product_id');
            $productos = Product::whereIn('id', collect($details)->pluck('product_id'))->get()->keyBy('id');

            $total = 0;
            $procesados = [];

            // ⚠️ REVERTIR STOCK si el original era "completed" y pasa a "pending"
            if ($originalStatus === 'completed' && $nuevoStatus === 'pending') {
                foreach ($detallesAnteriores as $detalle) {
                    $producto = $productos[$detalle->product_id] ?? Product::find($detalle->product_id);
                    if ($producto) {
                        $producto->quantity += $detalle->quantity;
                        $producto->save();
                        $this->actualizarEstadoProducto($producto);
                    }
                }
            }

            $venta->update([
                'customer_id' => $request->customer_id,
                'tipodocumento_id' => $request->tipodocumento_id,
                'sale_date' => $request->sale_date,
                'status' => $nuevoStatus,
                'payment_method' => $request->payment_method,
                'codigo' => $request->codigo ?? $venta->codigo,
            ]);

            foreach ($details as $item) {
                $productId = $item['product_id'];
                $cantidadNueva = $item['quantity'];
                $precioUnitario = $item['unit_price'];
                $subtotal = $cantidadNueva * $precioUnitario;
                $total += $subtotal;

                $procesados[] = $productId;

                $detalleExistente = $detallesAnteriores->get($productId);
                $producto = $productos->get($productId);

                if ($detalleExistente) {
                    // 🧮 Recalcular diferencia de cantidad si es completed → completed
                    if ($originalStatus === 'completed' && $nuevoStatus === 'completed') {
                        $diferencia = $cantidadNueva - $detalleExistente->quantity;
                        if ($producto && $diferencia !== 0) {
                            if ($producto->quantity - $diferencia < 0) {
                                throw new \Exception("Stock insuficiente para el producto {$producto->name}.");
                            }
                            $producto->quantity -= $diferencia;
                            $producto->save();
                            $this->actualizarEstadoProducto($producto);
                        }
                    }

                    // Si era pending → completed
                    if ($originalStatus === 'pending' && $nuevoStatus === 'completed') {
                        if ($producto->quantity < $cantidadNueva) {
                            throw new \Exception("Stock insuficiente para el producto {$producto->name}.");
                        }
                        $producto->quantity -= $cantidadNueva;
                        $producto->save();
                        $this->actualizarEstadoProducto($producto);
                    }

                    // Actualizar detalle
                    $detalleExistente->update([
                        'quantity' => $cantidadNueva,
                        'unit_price' => $precioUnitario,
                        'subtotal' => $subtotal,
                    ]);
                } else {
                    // ➕ Nuevo producto agregado
                    DetalleVenta::create([
                        'sale_id' => $venta->id,
                        'product_id' => $productId,
                        'quantity' => $cantidadNueva,
                        'unit_price' => $precioUnitario,
                        'subtotal' => $subtotal,
                    ]);

                    // Control de stock nuevo
                    if ($nuevoStatus === 'completed') {
                        if ($producto->quantity < $cantidadNueva) {
                            throw new \Exception("Stock insuficiente para el producto {$producto->name}.");
                        }
                        $producto->quantity -= $cantidadNueva;
                        $producto->save();
                        $this->actualizarEstadoProducto($producto);
                    }
                }

                // Inventario
                $inventarioExistente = $inventarioAnterior->get($productId);
                $inventoryReason = $nuevoStatus === 'pending' ? 'Venta pendiente ID: ' . $venta->id : 'Venta ID: ' . $venta->id;
                if ($inventarioExistente) {
                    $inventarioExistente->update([
                        'quantity' => -$cantidadNueva,
                        'reason' => $inventoryReason,
                    ]);
                } else {
                    Inventory::create([
                        'product_id' => $productId,
                        'type' => 'sale',
                        'quantity' => -$cantidadNueva,
                        'reason' => $inventoryReason,
                        'reference_id' => $venta->id,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            // 🗑 Productos eliminados
            $eliminados = $detallesAnteriores->keys()->diff($procesados);
            foreach ($eliminados as $pid) {
                $detalle = $detallesAnteriores[$pid];
                $producto = $productos[$pid] ?? Product::find($pid);
                $inventario = $inventarioAnterior[$pid] ?? null;

                if ($producto && $originalStatus === 'completed' && $nuevoStatus === 'completed') {
                    $producto->quantity += $detalle->quantity;
                    $producto->save();
                    $this->actualizarEstadoProducto($producto);
                }

                $detalle->delete();
                if ($inventario) $inventario->delete();
            }

            $venta->total_price = $total;
            $venta->save();

            Transaction::where('reference_id', $venta->id)
                ->where('type', 'sale')
                ->update([
                    'amount' => $total,
                    'description' => $nuevoStatus === 'pending' ? 'Venta pendiente ID: ' . $venta->id : 'Venta ID: ' . $venta->id,
                ]);

            $this->logVenta($venta->id, 'updated', [
                'old_data' => $originalData,
                'old_details' => $detallesAnteriores->toArray(),
            ], [
                'new_data' => $venta->getChanges(),
                'new_details' => $details,
            ]);
        });

        return response()->json(['message' => 'Venta actualizada correctamente.']);
    }


    public function destroy($id)
    {
        $venta = Venta::with('detalles.producto')->findOrFail($id);

        if ($venta->status === 'cancelled') {
            return response()->json(['message' => 'Esta venta ya fue anulada.'], 400);
        }

        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;

                if ($producto) {
                    // Solo si la venta fue completada, devolvemos el stock
                    if ($venta->status === 'completed') {
                        $producto->quantity += $detalle->quantity;
                        $producto->save();
                    }

                    // Registrar en inventario, sea pendiente o completada
                    Inventory::create([
                        'product_id' => $detalle->product_id,
                        'type' => 'adjustment_sale',
                        'quantity' => $venta->status === 'completed' ? $detalle->quantity : 0,
                        'reason' => 'Anulación de venta ID: ' . $venta->id . ' (estado: ' . $venta->status . ')',
                        'reference_id' => $venta->id,
                        'user_id' => auth()->id(),
                    ]);

                    // Evaluar estado del producto solo si fue completada
                    if ($venta->status === 'completed') {
                        $this->actualizarEstadoProducto($producto);
                    }
                }
            }

            // Anular venta
            $venta->update(['status' => 'cancelled']);

            // Actualizar descripción y monto en transacción relacionada
            Transaction::where('reference_id', $venta->id)
                ->where('type', 'sale')
                ->update([
                    'description' => 'Venta anulada',
                    'amount' => 0
                ]);

            // Log de anulación
            $this->logVenta($venta->id, 'cancelled', [
                'old_data' => $venta->toArray(),
                'old_details' => $venta->detalles->toArray(),
            ]);
        });

        return response()->json(['message' => 'Venta anulada correctamente.']);
    }


        public function getData()
    {
        $ventas = Venta::with(['customer', 'user', 'tipodocumento'])->select('ventas.*');

        return DataTables::of($ventas)
            ->addColumn('cliente', fn($v) => $v->customer->name ?? '-')
            ->addColumn('tipo_documento', fn($v) => $v->tipodocumento->name ?? '-')
            ->addColumn('usuario', fn($v) => $v->user->name ?? '-')
            ->addColumn('fecha', fn($v) => Carbon::parse($v->sale_date)->format('d/m/Y'))
            ->addColumn('total', fn($v) => 'S/ ' . number_format($v->total_price, 2))
            ->addColumn('estado', function ($v) {
                return match($v->status) {
                    'completed' => '<span class="badge bg-success p-2">Completada</span>',
                    'pending' => '<span class="badge bg-warning text-dark p-2">Pendiente</span>',
                    'cancelled' => '<span class="badge bg-danger p-2">Anulada</span>',
                    default => '<span class="badge bg-secondary p-2">Desconocido</span>',
                };
            })
            ->addColumn('metodopago', fn($v) => $v->payment_method ?? '-')
            ->addColumn('acciones', function ($v) {
                if ($v->status === 'cancelled') return '';
                $acciones = '';

                if (Auth::user()->can('administrar.ventas.edit')) {
                    $acciones .= '
                    <button type="button" class="btn btn-sm btn-outline-warning btn-icon waves-effect waves-light edit-btn"
                    data-id="' . $v->id . '" title="Editar">
                    <i class="ri-edit-2-line"></i>
                    </button>';
                }

                if (Auth::user()->can('administrar.ventas.delete')) {
                    $acciones .= '
                    <button type="button" class="btn btn-sm btn-outline-danger btn-icon waves-effect waves-light delete-btn"
                    data-id="' . $v->id . '" title="Eliminar">
                    <i class="ri-delete-bin-5-line"></i>
                    </button>';
                }

                $acciones .= '
                <button type="button" class="btn btn-sm btn-outline-info btn-icon waves-effect waves-light ver-detalle-btn"
                data-id="' . $v->id . '" title="Ver detalle">
                <i class="ri-eye-line"></i>
                </button>';

                return $acciones ?: '<span class="text-muted">Sin acciones</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }

        public function detalle($id)
    {
        $venta = Venta::with('detalles.producto')->findOrFail($id);
        $detalle = $venta->detalles->map(function ($item) {
            $producto = $item->producto;
            $nombreBase = $producto->name ?? 'Sin nombre';
            if ($producto && $producto->status === 'archived') {
                $nombreBase .= ' (archived)';
            }
            return [
                'product_name' => $nombreBase,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->quantity * $item->unit_price,
            ];
        });
        return response()->json(['detalle' => $detalle]);
    }

    protected function logVenta($ventaId, $accion, $datosAntes = [], $datosDespues = [])
    {
        VentaLog::create([
            'venta_id' => $ventaId,
            'accion' => $accion,
            'datos_antes' => !empty($datosAntes) ? json_encode($datosAntes) : null,
            'datos_despues' => !empty($datosDespues) ? json_encode($datosDespues) : null,
            'ip' => request()->ip(),
            'user_id' => auth()->id(),
        ]);
    }


}
