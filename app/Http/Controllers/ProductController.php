<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        // 🔹 Solo el ADMINISTRADOR y MANTENEDOR pueden acceder a este controlador
        $this->middleware(['auth', 'permission:administrar.productos.index'])->only('index', 'getData', 'show');
        $this->middleware(['auth', 'permission:administrar.productos.create'])->only('create', 'store', 'uploadTemp', 'uploadImages');
        $this->middleware(['auth', 'permission:administrar.productos.edit'])->only('edit', 'update', 'deleteImage');
        $this->middleware(['auth', 'permission:administrar.productos.delete'])->only('destroy');
    }

    public function index()
    {
        //$categories = Category::all();
        return view('product.index'/*, compact('categories')*/);
    }

    public function create()
    {
    }


    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
        'category_id' => 'required|exists:categories,id',
        //'status' => 'required|in:available,sold,archived',
        'fechvencimiento' => 'nullable|date',
        'temp_images' => 'nullable|array',
        'temp_images.*' => 'string',
    ]);

    // 🛑 Validar que la categoría no esté inactiva o archivada
    $categoria = Category::find($request->category_id);
    if ($categoria && $categoria->status === 'inactive') {
        return response()->json(['message' => "La categoría '{$categoria->name}' está inactiva y no puede ser usada."], 422);
    }

    // Generar SKU automáticamente
    $latest = Product::orderBy('id', 'desc')->first();
    $nextId = $latest ? $latest->id + 1 : 1;
    $sku = 'PROD-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

    // Crear producto
    $data = $request->only(['name', 'description', 'price', 'quantity', 'category_id', /*'status',*/ 'fechvencimiento']);
    $data['user_id'] = auth()->id();
    $data['sku'] = $sku;

    $product = Product::create($data);

    // Guardar imágenes temporales subidas por Dropzone
    if ($request->filled('temp_images')) {
        foreach ($request->input('temp_images') as $tempPath) {
            $filename = basename($tempPath);
            $newPath = "products/{$product->id}/{$filename}";

            if (\Storage::disk('public')->exists($tempPath)) {
                \Storage::disk('public')->move($tempPath, $newPath);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $newPath,
                ]);
            }
        }
    }

    return response()->json(['message' => 'Producto creado correctamente.', 'id' => $product->id]);
    }


    public function show($id)
    {
        /*$product = Product::with('images')->findOrFail($id);
        $product->images_urls = $product->images->map(function($img){
            return asset('storage/' . $img->image_path);
        });
        return response()->json($product);*/

    $product = Product::with('images')->findOrFail($id);
    $categoriesQuery = Category::query();
    // Incluir la categoría del producto incluso si está inactiva
    $categoriesQuery->where(function ($q) use ($product) {
        $q->where('status', 'active')
          ->orWhere('id', $product->category_id);
    });
    $categories = $categoriesQuery->orderBy('id')->get()->map(function ($cat) {
        return [
            'id' => $cat->id,
            'text' => $cat->status === 'active'
                      ? $cat->name
                      : $cat->name . ' (inactiva)',
        ];
    });
    //$product->images_urls = $product->images->map(fn($img) => asset('storage/' . $img->image_path));

    return response()->json([
        'product' => $product,
        'categories' => $categories,
    ]);
    }

    public function edit(string $id)
    {
    }


    public function update(Request $request, string $id)
    {
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
        'category_id' => 'required|exists:categories,id',
        //'status' => 'required|in:available,sold,archived',
        'fechvencimiento' => 'nullable|date',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'images_to_delete' => 'nullable|string',
    ]);

    $product = Product::findOrFail($id);

    $data = $request->only(['name', 'description', 'price', 'quantity', 'category_id', /*'status',*/ 'fechvencimiento']);
    $product->update($data);

    if ($request->filled('images_to_delete')) {
        $imagesToDelete = explode(',', $request->input('images_to_delete'));

        foreach ($product->images as $image) {
            $imageUrl = asset('storage/' . $image->image_path);
            if (in_array($imageUrl, $imagesToDelete)) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
        }
    }

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
            ]);
        }
    }

    return response()->json(['message' => 'Producto actualizado correctamente.']);
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Eliminar imágenes relacionadas
        /*foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $product->delete();*/
        $product->update(['status' => 'archived']);

        return response()->json(['message' => 'Producto eliminado correctamente.']);
    }

    public function getData(Request $request)
    {
    $products = Product::with('category', 'images')->select('products.*')->whereIn('status', ['available', 'sold', 'archived']);

    return DataTables::of($products)
            ->addColumn('estado', function ($product) {
                return match($product->status) {
                    'available' => '<span class="badge bg-success p-2">Disponible</span>',
                    'sold' => '<span class="badge bg-warning text-dark p-2">Vendido</span>',
                    'archived' => '<span class="badge bg-danger p-2">Archivado</span>',
                    default => '<span class="badge bg-secondary p-2">Desconocido</span>',
                };
            })
            ->addColumn('fechvencimiento_estado', function ($product) {
            if (!$product->fechvencimiento) {
                return '<span class="badge bg-secondary p-2">Sin fecha</span>';
            }

            $today = \Carbon\Carbon::today();
            $expirationDate = \Carbon\Carbon::parse($product->fechvencimiento);
            $daysDifference = $today->diffInDays($expirationDate, false);

            if ($daysDifference < 0) {
                return '<span class="badge bg-danger text-white p-2">' . $expirationDate->format('d/m/Y') . ' - Vencido</span>';
            } elseif ($daysDifference <= 30) {
                return '<span class="badge bg-warning text-dark p-2">' . $expirationDate->format('d/m/Y') . ' - Por vencer</span>';
            } else {
                return '<span class="badge bg-success text-white p-2">' . $expirationDate->format('d/m/Y') . ' - Vigente</span>';
            }
        })
        ->addColumn('image', function ($product) {
            $img = $product->images->first();
            $photoPath = $img ? $img->image_path : null;

            if ($photoPath && file_exists(storage_path('app/public/' . $photoPath))) {
                $url = asset('storage/' . $photoPath);
            } else {
                $url = asset('assets/images/product.png');
            }

            return '<img src="' . $url . '" class="img-thumbnail" width="30" alt="Imagen de producto">';
        })
        ->addColumn('category_name', fn($product) => $product->category->name ?? 'Sin Categoría')

        ->addColumn('barcode', function ($product) {
            $generator = new BarcodeGeneratorPNG();
            $barcode = base64_encode($generator->getBarcode($product->sku, $generator::TYPE_CODE_128));
            return '<img src="data:image/png;base64,' . $barcode . '" style="height:30px; width:auto; max-width:150px;">';
        })

        ->addColumn('qrcode', function ($product) {
            $qrCode = new EndroidQrCode(
                data: $product->sku,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: 100,
                margin: 5
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrBase64 = base64_encode($result->getString());
            return '<img src="data:image/png;base64,' . $qrBase64 . '" style="height:40px;">';
        })

        ->addColumn('acciones', function ($product) {
            if ($product->status === 'archived') {
            return '';
            }

            $acciones = '';

            if (Auth::user()->can('administrar.productos.edit')) {
            $acciones .= '
                <button type="button"
                    class="btn btn-outline-warning btn-sm btn-icon waves-effect waves-light edit-btn"
                    data-id="' . $product->id . '"
                    title="Editar">
                    <i class="ri-edit-2-line"></i>
                </button>';
            }
            if (Auth::user()->can('administrar.productos.delete')) {
            $acciones .= '
                <button type="button"
                    class="btn btn-outline-danger btn-sm btn-icon waves-effect waves-light delete-btn"
                    data-id="' . $product->id . '"
                    title="Eliminar">
                    <i class="ri-delete-bin-5-line"></i>
                </button>';
            }

            return $acciones ?: '<span class="text-muted">Sin acciones</span>';
        })
        ->rawColumns(['estado','image', 'barcode', 'qrcode', 'acciones', 'fechvencimiento_estado'])
        ->make(true);
    }

public function uploadTemp(Request $request)
{
    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('temp-images', 'public');

        return response()->json([
            'path' => $path,
            'id' => uniqid() // o lo que necesite Dropzone
        ]);
    }

    return response()->json(['error' => 'No se subió ninguna imagen'], 400);
}



public function uploadImages(Request $request, Product $product)
{
    $request->validate([
        'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $image = $request->file('file'); // Nota: 'file' no 'images'
    $path = $image->store('products', 'public');

    $productImage = ProductImage::create([
        'product_id' => $product->id,
        'image_path' => $path,
    ]);

    return response()->json([
        'id' => $productImage->id,
        'url' => asset('storage/' . $path)
    ]);
}


public function deleteImage(Request $request, Product $product)
{
    $request->validate([
        'id' => 'required|integer|exists:product_images,id'
    ]);

    $image = ProductImage::where('product_id', $product->id)
                         ->where('id', $request->id)
                         ->firstOrFail();

    \Storage::disk('public')->delete($image->image_path);
    $image->delete();

    return response()->json(['message' => 'Imagen eliminada']);
}



public function list(Request $request)
{
    /*return response()->json(
        Product::select('id', 'name')->orderBy('name')->get()
    );*/

    $query = Product::query();

    if ($request->has('include_id')) {
        $ids = $request->input('include_id'); // Puede ser array o uno solo
        $query->where(function ($q) use ($ids) {
            $q->where('status', 'available')
              ->orWhereIn('id', (array) $ids);
        });
    } else {
        $query->where('status', 'available');
    }

    $productos = $query->orderBy('name')->get()->map(function ($producto) {
        return [
            'id' => $producto->id,
            'name' => $producto->status === 'available'
                        ? $producto->name
                        : $producto->name . ' (' . $producto->status . ')',
        ];
    });

    return response()->json($productos);
}

}
