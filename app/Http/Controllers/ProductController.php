<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::active()
            ->with(['category', 'images'])
            ->paginate(12);

        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'variants.variantGroup', 'reviews', 'tags']);

        // Check stock logic
        $isInStock = $product->in_stock;

        // Similar products logic could be added here

        return Inertia::render('Products/Show', [
            'product' => $product,
            'isInStock' => $isInStock,
        ]);
    }
}
