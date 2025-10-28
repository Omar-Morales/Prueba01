<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Compra;
use App\Models\Venta;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use App\Exports\InventoryExportId;
use Barryvdh\DomPDF\Facade\Pdf;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct()
    {
        // 🔹 Solo el ADMINISTRADOR y Almacen pueden acceder a este controlador
        $this->middleware(['auth', 'permission:administrar.inventarios.index'])->only('index', 'show');
        $this->middleware(['auth', 'permission:administrar.inventarios.export'])->only('exportInventory', 'exportInventoryId', 'exportPdf');

    }

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Inventory::with('user')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            })
            ->whereNotNull('reference_id')
            ->get()
            ->groupBy(fn ($item) => $item->type . '_' . $item->reference_id)
            ->map(function ($items) {
                $first = $items->first();

                $referenceId = $first->reference_id;
                $type = $first->type;
                $translatedType = match ($type) {
                'sale' => 'Venta',
                'purchase' => 'Compra',
                'adjustment_purchase' => 'Ajuste de Compra (Anulada)',
                'adjustment_sale' => 'Ajuste de Venta (Anulada)',
                default => ucfirst($type),
                };

                // Generar botones directamente (no como función anónima)
                if (in_array($type, ['adjustment_purchase', 'adjustment_sale'])) {
                    $acciones = '<span class="badge bg-danger p-2">Anulada</span>';
                } else {
                    $botones  = '';
                    $botones  .= '<button class="btn btn-sm btn-info btn-detalle" data-id="' . $referenceId . '" data-type="' . $type . '">
                                    Detalle
                                </button>';
                    if (Auth::user()->can('administrar.inventarios.export')) {
                    $botones  .= '<button class="btn btn-sm btn-success btn-export-excel" data-id="' . $referenceId . '" data-type="' . $type . '">Excel</button>';
                    $botones  .= '<button class="btn btn-sm btn-danger btn-export-pdf" data-id="' . $referenceId . '" data-type="' . $type . '">PDF</button>';
                    }

                     // Si no hay botones para exportar, mostrar texto "Sin acciones"
                    if (empty(trim(strip_tags($botones )))) {
                        $acciones = '<span class="text-muted">Sin acciones</span>';
                    } else {
                        $acciones = '<div class="btn-group">' . $botones . '</div>';
                    }
                }
                return [
                    'reference_id_original' => $referenceId,
                    'total_quantity' => $items->count('quantity'),
                    'type' => $translatedType,
                    'reason' => $first->reason ?? '-',
                    'user' => $first->user->name ?? 'Sistema',
                    'created_at' => $first->created_at->format('d/m/Y H:i'),
                    'acciones' => $acciones,
                ];
        })->values()
            ->map(function ($item, $index) {
            $item['reference_id'] = $index + 1;
            return $item;
        });

        if ($request->ajax()) {
            return DataTables::of($query)->rawColumns(['acciones'])->make(true);
        }
        return view('inventories.index');
    }
    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show($reference_id, Request $request)
    {
        $inventario = Inventory::where('reference_id', $reference_id)->where('type', $request->query('type'))->first();

        if (!$inventario) {
            return response()->json(['error' => 'Movimiento no encontrado'], 404);
        }

        if ($inventario->type === 'purchase') {
            $compra = Compra::with('detalles.producto', 'supplier', 'user')->find($reference_id);
            if (!$compra) return response()->json(['error' => 'Compra no encontrada'], 404);

            $total = 0;
            $detalles = $compra->detalles->map(function ($detalle) use (&$total) {
                $subtotal = $detalle->quantity * $detalle->unit_cost;
                $total += $subtotal;
                return [
                    'producto' => $detalle->producto->name ?? '—',
                    'quantity' => $detalle->quantity,
                    'unit_price' => $detalle->unit_cost,
                    'subtotal' => $subtotal,
                ];
            });

            return response()->json([
                'tipo' => 'purchase',
                'codigo' => $compra->codigo ?? '—',
                'fecha' => $compra->created_at,
                'supplier' => $compra->supplier->name ?? '—',
                'user' => $compra->user->name ?? '—',
                'detalles' => $detalles,
                'total' => $total,
            ]);
        }

        if ($inventario->type === 'sale') {
            $venta = Venta::with('detalles.producto', 'customer', 'user')->find($reference_id);
            if (!$venta) return response()->json(['error' => 'Venta no encontrada'], 404);

            $total = 0;
            $detalles = $venta->detalles->map(function ($detalle) use (&$total) {
                $subtotal = $detalle->quantity * $detalle->unit_price;
                $total += $subtotal;
                return [
                    'producto' => $detalle->producto->name ?? '—',
                    'quantity' => $detalle->quantity,
                    'unit_price' => $detalle->unit_price,
                    'subtotal' => $subtotal,
                ];
            });

            return response()->json([
                'tipo' => 'sale',
                'codigo' => $venta->codigo ?? '—',
                'fecha' => $venta->created_at,
                'customer' => $venta->customer->name ?? '—',
                'user' => $venta->user->name ?? '—',
                'detalles' => $detalles,
                'total' => $total,
            ]);
        }

        return response()->json(['error' => 'Tipo no válido para mostrar'], 400);
    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, string $id)
    {
    }


    public function destroy(string $id)
    {
    }

    public function exportInventory(Request $request)
    {
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $inventories = Inventory::with(['producto', 'user'])
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        })
        ->whereNotNull('reference_id')
        ->get()
        ->groupBy(fn ($item) => $item->type . '_' . $item->reference_id)
        ->map(function ($items) {
            $first = $items->first();

            $translatedType = match ($first->type) {
                'sale' => 'Venta',
                'purchase' => 'Compra',
                'adjustment_purchase' => 'Ajuste de Compra (Anulada)',
                'adjustment_sale' => 'Ajuste de Venta (Anulada)',
                default => ucfirst($first->type),
            };

            return [
                'reference_id' => $first->reference_id,
                'type' => $translatedType,
                'user' => $first->user->name ?? 'Sistema',
                'created_at' => $first->created_at->format('d/m/Y H:i'),
                'reason' => $first->reason ?? '-',
                'productos' => $items->map(function ($i) {
                    return $i->producto->name . ' (' . $i->quantity . ')';
                })->implode(', '),
                'total_products' => $items->count(), // ✅ ¡Este cuenta productos diferentes!
            ];
        })->values();

       return Excel::download(new InventoryExport($inventories), 'inventario.xlsx');
    }

        public function exportInventoryId($reference_id, Request $request)
    {
    $inventario = Inventory::where('reference_id', $reference_id)
        ->where('type', $request->input('type'))
        ->first();

    if (!$inventario) {
        return back()->with('error', 'No se encontró inventario con ese ID');
    }

    $type = $inventario->type;
    $translatedType = match ($type) {
        'sale' => 'Venta',
        'purchase' => 'Compra',
        'adjustment_purchase' => 'Ajuste de Compra (Anulada)',
        'adjustment_sale' => 'Ajuste de Venta (Anulada)',
        default => ucfirst($type),
    };

    if ($type === 'purchase') {
        $compra = Compra::with('detalles.producto', 'user')->find($reference_id);
        if (!$compra) return back()->with('error', 'Compra no encontrada');

        $total = 0;
        $productos = $compra->detalles->map(function ($detalle) use (&$total) {
        $subtotal = $detalle->quantity * $detalle->unit_cost;
        $total += $subtotal;
            return [
                'Producto' => $detalle->producto->name ?? '—',
                'Cantidad' => $detalle->quantity,
                'Precio unitario' => $detalle->unit_cost,
                'Subtotal' => $subtotal,
            ];
        });

        $data = [
            'reference_id' => $compra->codigo ?? '—',
            'type' => $translatedType,
            'user' => $compra->user->name ?? 'Sistema',
            'created_at' => $compra->created_at->format('d/m/Y H:i'),
            'reason' => $inventario->reason ?? '-',
            'productos' => $productos,
            'total' => $total,
        ];

        return Excel::download(new InventoryExportId($data), "compra_ref_{$reference_id}.xlsx");
    }

    if ($type === 'sale') {
        $venta = Venta::with('detalles.producto', 'user')->find($reference_id);
        if (!$venta) return back()->with('error', 'Venta no encontrada');

        $total = 0;
        $productos = $venta->detalles->map(function ($detalle) use (&$total) {
        $subtotal = $detalle->quantity * $detalle->unit_price;
        $total += $subtotal;
            return [
                'Producto' => $detalle->producto->name ?? '—',
                'Cantidad' => $detalle->quantity,
                'Precio unitario' => $detalle->unit_price,
                'Subtotal' => $subtotal,
            ];
        });

        $data = [
            'reference_id' => $venta->codigo ?? '—',
            'type' => $translatedType,
            'user' => $venta->user->name ?? 'Sistema',
            'created_at' => $venta->created_at->format('d/m/Y H:i'),
            'reason' => $inventario->reason ?? '-',
            'productos' => $productos,
            'total' => $total,
        ];

        return Excel::download(new InventoryExportId($data), "venta_ref_{$reference_id}.xlsx");
    }

    return back()->with('error', 'Tipo de inventario no válido');
    }

    public function exportPdf($reference_id, Request $request)
    {
    $inventario = Inventory::where('reference_id', $reference_id)
        ->where('type', $request->input('type'))
        ->first();

    if (!$inventario) {
        return back()->with('error', 'No se encontró inventario con ese ID');
    }

    $type = $inventario->type;
    $translatedType = match ($type) {
        'sale' => 'Venta',
        'purchase' => 'Compra',
        'adjustment_purchase' => 'Ajuste de Compra (Anulada)',
        'adjustment_sale' => 'Ajuste de Venta (Anulada)',
        default => ucfirst($type),
    };

    if ($type === 'purchase') {
        $compra = Compra::with('detalles.producto', 'user')->find($reference_id);
        if (!$compra) return back()->with('error', 'Compra no encontrada');

        $total = 0;
        $productos = $compra->detalles->map(function ($detalle) use (&$total) {
        $subtotal = $detalle->quantity * $detalle->unit_cost;
        $total += $subtotal;
            return [
                'Producto' => $detalle->producto->name ?? '—',
                'Cantidad' => $detalle->quantity,
                'Precio unitario' => $detalle->unit_cost,
                'Subtotal' => $subtotal,
            ];
        });
        $qrBase64 = $this->generarQrBase64($compra->codigo);

        $data = [
            'reference_id' => $compra->codigo ?? '—',
            'type' => $translatedType,
            'user' => $compra->user->name ?? 'Sistema',
            'created_at' => $compra->created_at->format('d/m/Y H:i'),
            'reason' => $inventario->reason ?? '-',
            'productos' => $productos,
            'qr_code' => $qrBase64,
            'total' => $total
        ];

        $pdf = PDF::loadView('pdf.inventario-id-pdf', compact('data'));
        return $pdf->download("compra_ref_{$reference_id}.pdf");
    }

    if ($type === 'sale') {
        $venta = Venta::with('detalles.producto', 'user')->find($reference_id);
        if (!$venta) return back()->with('error', 'Venta no encontrada');

        $total = 0;
        $productos = $venta->detalles->map(function ($detalle) use (&$total) {
        $subtotal = $detalle->quantity * $detalle->unit_price;
        $total += $subtotal;
            return [
                'Producto' => $detalle->producto->name ?? '—',
                'Cantidad' => $detalle->quantity,
                'Precio unitario' => $detalle->unit_price,
                'Subtotal' => $subtotal,
            ];
        });
        $qrBase64 = $this->generarQrBase64($venta->codigo);
        $data = [
            'reference_id' => $venta->codigo ?? '—',
            'type' => $translatedType,
            'user' => $venta->user->name ?? 'Sistema',
            'created_at' => $venta->created_at->format('d/m/Y H:i'),
            'reason' => $inventario->reason ?? '-',
            'productos' => $productos,
            'qr_code' => $qrBase64,
            'total' => $total
        ];

        $pdf = PDF::loadView('pdf.inventario-id-pdf', compact('data'));
        return $pdf->download("venta_ref_{$reference_id}.pdf");
    }

    return back()->with('error', 'Tipo de inventario no válido');
    }

    private function generarQrBase64(string $codigo)
    {
        $qrCode = new EndroidQrCode(
            data: $codigo,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 100,
            margin: 5
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        return 'data:image/png;base64,' . base64_encode($result->getString());
    }
}
