<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'images']);

        // Filter by stock availability
        if ($request->has('stock')) {
            if ($request->stock === 'available') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'stock' => $request->stock,
                'category' => $request->category,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'sort' => $sortBy,
            ],
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
