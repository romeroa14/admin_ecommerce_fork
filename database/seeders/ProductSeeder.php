<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Inventory;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear categorías
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'description' => 'Home improvement and garden supplies'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports equipment and accessories'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Crear marcas
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'description' => 'Technology company'],
            ['name' => 'Nike', 'slug' => 'nike', 'description' => 'Sports brand'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'description' => 'Electronics company'],
            ['name' => 'Adidas', 'slug' => 'adidas', 'description' => 'Sports brand'],
        ];

        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }

        // Crear tags
        $tags = [
            ['name' => 'New', 'slug' => 'new', 'type' => 'general', 'color' => '#10B981'],
            ['name' => 'Sale', 'slug' => 'sale', 'type' => 'promotion', 'color' => '#EF4444'],
            ['name' => 'Featured', 'slug' => 'featured', 'type' => 'featured', 'color' => '#F59E0B'],
            ['name' => 'Limited Edition', 'slug' => 'limited-edition', 'type' => 'general', 'color' => '#8B5CF6'],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }

        // Crear productos de ejemplo
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'sku' => 'IPH15P-128',
                'description' => 'The latest iPhone with advanced features and premium design.',
                'short_description' => 'Latest iPhone with Pro features',
                'price' => 999.99,
                'compare_price' => 1099.99,
                'cost' => 800.00,
                'category_id' => Category::where('slug', 'electronics')->first()->id,
                'brand_id' => Brand::where('slug', 'apple')->first()->id,
                'stock' => 50,
                'low_stock_threshold' => 10,
                'track_inventory' => true,
                'status' => 'active',
                'is_featured' => true,
                'meta_title' => 'iPhone 15 Pro - Latest Apple Smartphone',
                'meta_description' => 'Get the latest iPhone 15 Pro with advanced features and premium design.',
                'meta_keywords' => ['iphone', 'apple', 'smartphone', 'pro'],
            ],
            [
                'name' => 'Nike Air Max 270',
                'slug' => 'nike-air-max-270',
                'sku' => 'NAM270-BLK-10',
                'description' => 'Comfortable running shoes with Max Air cushioning.',
                'short_description' => 'Comfortable running shoes',
                'price' => 150.00,
                'compare_price' => 180.00,
                'cost' => 100.00,
                'category_id' => Category::where('slug', 'clothing')->first()->id,
                'brand_id' => Brand::where('slug', 'nike')->first()->id,
                'stock' => 25,
                'low_stock_threshold' => 5,
                'track_inventory' => true,
                'status' => 'active',
                'is_featured' => false,
                'meta_title' => 'Nike Air Max 270 - Comfortable Running Shoes',
                'meta_description' => 'Comfortable running shoes with Max Air cushioning technology.',
                'meta_keywords' => ['nike', 'shoes', 'running', 'air max'],
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'slug' => 'samsung-galaxy-s24',
                'sku' => 'SGS24-256',
                'description' => 'Latest Samsung smartphone with advanced camera and AI features.',
                'short_description' => 'Latest Samsung smartphone',
                'price' => 799.99,
                'compare_price' => 899.99,
                'cost' => 600.00,
                'category_id' => Category::where('slug', 'electronics')->first()->id,
                'brand_id' => Brand::where('slug', 'samsung')->first()->id,
                'stock' => 30,
                'low_stock_threshold' => 8,
                'track_inventory' => true,
                'status' => 'active',
                'is_featured' => true,
                'meta_title' => 'Samsung Galaxy S24 - Advanced Smartphone',
                'meta_description' => 'Latest Samsung smartphone with advanced camera and AI features.',
                'meta_keywords' => ['samsung', 'galaxy', 'smartphone', 'android'],
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Crear variantes para algunos productos
            if ($product->slug === 'nike-air-max-270') {
                $sizes = ['8', '9', '10', '11', '12'];
                foreach ($sizes as $size) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => "Size {$size}",
                        'sku' => "NAM270-BLK-{$size}",
                        'stock' => rand(5, 15),
                        'attributes' => ['size' => $size, 'color' => 'Black'],
                        'is_default' => $size === '10',
                        'is_active' => true,
                    ]);
                }
            }

            // Crear imágenes de ejemplo
            ProductImage::create([
                'product_id' => $product->id,
                'image' => 'products/' . Str::slug($product->name) . '-1.jpg',
                'alt_text' => $product->name,
                'order' => 1,
                'is_primary' => true,
            ]);

            // Crear inventario
            Inventory::create([
                'product_id' => $product->id,
                'location' => 'Main Warehouse',
                'quantity' => $product->stock,
                'reserved_quantity' => 0,
                'status' => $product->stock > $product->low_stock_threshold ? 'in_stock' : 'low_stock',
                'notes' => 'Initial stock',
            ]);

            // Asignar tags
            $product->tags()->attach(Tag::where('name', 'New')->first()->id);
            if ($product->is_featured) {
                $product->tags()->attach(Tag::where('name', 'Featured')->first()->id);
            }
        }

        $this->command->info('Products seeded successfully!');
    }
}