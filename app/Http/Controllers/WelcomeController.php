<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'mainImage'])->where('status', 'available')->get();
        $categories = Category::all();
        $categoriescount = Category::withCount('products')->get();

        $topClientes = Venta::with('customer')->whereNotNull('customer_id')->groupBy('customer_id')->selectRaw('customer_id, COUNT(*) as total_compras, SUM(total_price) as total_gastado')->orderByDesc('total_compras')->take(6)->get();
        $topProveedores = DB::table(DB::raw('
            (
                SELECT
                    s.id as supplier_id,
                    s.name as supplier_name,
                    COALESCE(s.photo, \'customers.png\') as supplier_photo,
                    p.id as product_id,
                    p.name as product_name,
                    SUM(dc.quantity) as total_cantidad,
                    ROW_NUMBER() OVER (PARTITION BY s.id ORDER BY SUM(dc.quantity) DESC) as rn
                FROM suppliers s
                JOIN compras c ON c.supplier_id = s.id
                JOIN detalle_compras dc ON dc.purchase_id = c.id
                JOIN products p ON p.id = dc.product_id
                GROUP BY s.id, s.name, s.photo, p.id, p.name
            ) ranked
        '))
        ->where('ranked.rn', 1)
        ->leftJoin('product_images as pi', function($join) {
            $join->on('pi.product_id', '=', 'ranked.product_id');
        })
        ->select([
            'ranked.supplier_id',
            'ranked.supplier_name',
            'ranked.supplier_photo',
            'ranked.product_name',
            DB::raw('MIN(pi.image_path) as product_image'), // La imagen con menor id
            'ranked.total_cantidad',
        ])
        ->groupBy(
            'ranked.supplier_id',
            'ranked.supplier_name',
            'ranked.supplier_photo',
            'ranked.product_name',
            'ranked.total_cantidad'
        )
        ->orderByDesc('ranked.total_cantidad')
        ->limit(3)
        ->get();

        return view('welcome', compact('products', 'categories','topClientes','topProveedores','categoriescount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
