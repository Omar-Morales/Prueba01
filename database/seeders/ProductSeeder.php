<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        $users = User::role(['Administrador', 'Mantenedor'])->get();


        $products = [
            ['name' => 'Laptop HP', 'price' => 750.00, 'quantity' => 10],
            ['name' => 'Teclado Mecánico', 'price' => 50.00, 'quantity' => 20],
            ['name' => 'Monitor Samsung', 'price' => 200.00, 'quantity' => 15],
            ['name' => 'Procesador Intel i7', 'price' => 300.00, 'quantity' => 25],
            ['name' => 'Router TP-Link', 'price' => 80.00, 'quantity' => 18],
            ['name' => 'Audífonos Gaming', 'price' => 35.00, 'quantity' => 30],
            ['name' => 'Mouse Gamer', 'price' => 25.00, 'quantity' => 40],
            ['name' => 'Memoria RAM 16GB', 'price' => 90.00, 'quantity' => 50],
            ['name' => 'Disco Duro 1TB', 'price' => 60.00, 'quantity' => 35],
            ['name' => 'Cámara Web', 'price' => 40.00, 'quantity' => 22],
            ['name' => 'Laptop Dell', 'price' => 900.00, 'quantity' => 12],
            ['name' => 'Switch Gigabit', 'price' => 70.00, 'quantity' => 15],
            ['name' => 'Tarjeta Gráfica RTX 3060', 'price' => 450.00, 'quantity' => 10],
            ['name' => 'Fuente de Poder 650W', 'price' => 60.00, 'quantity' => 25],
            ['name' => 'Placa Madre MSI', 'price' => 120.00, 'quantity' => 20],
            ['name' => 'SSD 512GB', 'price' => 70.00, 'quantity' => 30],
            ['name' => 'Case Gaming', 'price' => 80.00, 'quantity' => 12],
            ['name' => 'Kit de Herramientas', 'price' => 25.00, 'quantity' => 40],
            ['name' => 'Monitor LG', 'price' => 300.00, 'quantity' => 8],
            ['name' => 'Cable HDMI', 'price' => 15.00, 'quantity' => 100],
        ];

    foreach($products as $index => $product){
        // Generar sku: PROD-00001, PROD-00002, etc.
        $sku = 'PROD-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT);

        Product::create([
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $product['quantity'],
            'category_id' => $categories->random()->id,
            'user_id' => $users->random()->id,
            'status' => 'available',
            'sku' => $sku,
        ]);
    }
    }
}
