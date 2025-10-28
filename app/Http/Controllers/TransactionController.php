<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Compra;
use App\Models\Venta;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionExport;
use App\Exports\TransactionExportId;
use Barryvdh\DomPDF\Facade\Pdf;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct()
    {
        // 🔹 Solo el ADMINISTRADOR y Analista pueden acceder a este controlador
        $this->middleware(['auth', 'permission:administrar.transacciones.index'])->only('index', 'show');
        $this->middleware(['auth', 'permission:administrar.transacciones.export'])->only('exportTransaction', 'exportTransactionId', 'exportPdf');

    }

    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $query = Transaction::with('user')
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [
                    Carbon::parse($start)->startOfDay(),
                    Carbon::parse($end)->endOfDay()
                ]);
            });

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addColumn('user', fn($t) => $t->user->name ?? '—')
                ->addColumn('created_at', fn($t) => $t->created_at->format('d/m/Y H:i'))
                ->addColumn('type', function ($t) {
                $type = $t->type;
                $translatedType = match ($type) {
                    'sale' => 'Venta',
                    'purchase' => 'Compra',
                    'payment' => 'Pago',
                    'refund' => 'Reembolso',
                    'adjustment' => 'Ajuste',
                    default => ucfirst($type),
                };
                return $translatedType;
                })
                ->addColumn('acciones', function ($t) {
                $botones = '';
                $botones  .= '<button class="btn btn-sm btn-info btn-detalle" data-id="' . $t->reference_id . '" data-type="' . $t->type . '">
                                Detalle
                            </button>';
                if (Auth::user()->can('administrar.transacciones.export')) {
                    $botones  .= '<button class="btn btn-sm btn-success btn-export-excel" data-id="' . $t->reference_id . '" data-type="' . $t->type . '">Excel</button>';
                    $botones  .= '<button class="btn btn-sm btn-danger btn-export-pdf" data-id="' . $t->reference_id . '" data-type="' . $t->type . '">PDF</button>';
                }

                $botones = trim($botones);

                if ($botones === '') {
                    return '<span class="text-muted">Sin acciones</span>';
                }

                return '<div class="btn-group">' . $botones . '</div>';
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }

        return view('transactions.index'); // Blade que ya tienes
    }


    public function create()
    {

    }


    public function store(Request $request)
    {

    }

    public function show($reference_id, Request $request)
    {
        $type = $request->query('type');

        $transaction = Transaction::where('reference_id', $reference_id)
            ->where('type', $type)
            ->with('user')
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transacción no encontrada'], 404);
        }

        // Simulación de detalle según tipo
        if ($type === 'purchase') {
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
                'entidad' => $compra->supplier->name ?? '—',
                'user' => $compra->user->name ?? '—',
                'detalles' => $detalles,
                'total' => $total,
            ]);
        }

        if ($type === 'sale') {
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
                'entidad' => $venta->customer->name ?? '—',
                'user' => $venta->user->name ?? '—',
                'detalles' => $detalles,
                'total' => $total,
            ]);
        }

        // Casos simples como pago, reembolso, ajuste, etc.
        if (in_array($type, ['payment', 'refund', 'adjustment'])) {
            return response()->json([
                'tipo' => $type,
                'codigo' => '—',
                'fecha' => $transaction->created_at,
                'entidad' => '-', // Podrías agregar una entidad si la relacionas
                'user' => $transaction->user->name ?? 'Sistema',
                'detalles' => [
                    [
                        'producto' => '—',
                        'quantity' => '—',
                        'unit_price' => '—',
                        'subtotal' => $transaction->amount,
                    ]
                ],
                'total' => $transaction->amount,
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

    public function exportTransaction(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = Transaction::with('user')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            })
            ->get()
            ->groupBy(fn ($t) => $t->type . '_' . $t->reference_id)
            ->map(function ($items) {
                $first = $items->first();

                $translatedType = match ($first->type) {
                    'sale' => 'Venta',
                    'purchase' => 'Compra',
                    'adjustment_purchase' => 'Ajuste de Compra (Anulada)',
                    'adjustment_sale' => 'Ajuste de Venta (Anulada)',
                    default => ucfirst($first->type),
                };

                $productos = '-';

                if ($first->type === 'sale') {
                    $venta = Venta::with('detalles.producto')->find($first->reference_id);
                    if ($venta) {
                        $productos = $venta->detalles->map(fn ($d) =>
                            $d->producto->name . ' (' . $d->quantity . ')'
                        )->implode(', ');
                    }
                }

                if ($first->type === 'purchase') {
                    $compra = Compra::with('detalles.producto')->find($first->reference_id);
                    if ($compra) {
                        $productos = $compra->detalles->map(fn ($d) =>
                            $d->producto->name . ' (' . $d->quantity . ')'
                        )->implode(', ');
                    }
                }

                return [
                    'reference_id' => $first->reference_id,
                    'type' => $translatedType,
                    'user' => $first->user->name ?? 'Sistema',
                    'created_at' => $first->created_at->format('d/m/Y H:i'),
                    'amount' => $first->amount,
                    'description' => $items->pluck('description')->filter()->implode('; '),
                    'productos' => $productos,
                ];
            })->values();

        return Excel::download(new TransactionExport($transactions), 'transacciones.xlsx');
    }


    public function exportTransactionId($reference_id, Request $request)
    {
        $type = $request->input('type');

        $transaction = Transaction::where('reference_id', $reference_id)
            ->where('type', $type)
            ->with('user')
            ->first();

        if (!$transaction) {
            return back()->with('error', 'No se encontró transacción con ese ID');
        }

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
                'description' => $transaction->description ?? '-',
                'productos' => $productos,
                'total' => $total,
            ];

            return Excel::download(new TransactionExportId($data), "compra_ref_{$reference_id}.xlsx");
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
                'description' => $transaction->description ?? '-',
                'productos' => $productos,
                'total' => $total,
            ];

            return Excel::download(new TransactionExportId($data), "venta_ref_{$reference_id}.xlsx");
        }

        return back()->with('error', 'Tipo de transacción no válido');
    }

    public function exportPdf($reference_id, Request $request)
    {
        $type = $request->input('type');

        $transaction = Transaction::where('reference_id', $reference_id)
            ->where('type', $type)
            ->first();

        if (!$transaction) {
            return back()->with('error', 'No se encontró transacción con ese ID');
        }

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
                'description' => $transaction->description ?? '-',
                'productos' => $productos,
                'qr_code' => $qrBase64,
                'total' => $total
            ];

            $pdf = PDF::loadView('pdf.transaccion-id-pdf', compact('data'));
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
                'description' => $transaction->description ?? '-',
                'productos' => $productos,
                'qr_code' => $qrBase64,
                'total' => $total
            ];

            $pdf = PDF::loadView('pdf.transaccion-id-pdf', compact('data'));
            return $pdf->download("venta_ref_{$reference_id}.pdf");
        }

        return back()->with('error', 'Tipo de transacción no válido');
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
