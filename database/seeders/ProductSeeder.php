<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Producto A',
            'price' => 35000,
            'description' => 'Descripción del producto A',
            'image' => 'camisa.jpg',
            'status' => 'A',
        ]);

        Product::create([
            'name' => 'Producto B',
            'price' => 110000,
            'description' => 'Descripción del producto B',
            'image' => 'zapatos.jpg',
            'status' => 'A',
        ]);
    }
}
