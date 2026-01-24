<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display all categories
     */
    public function index()
    {
        $categories = Category::active()
            ->ordered()
            ->withCount('products')
            ->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Display products from a specific category
     * Reuses Products/Index.vue with category filter pre-applied
     */
    public function show(Request $request, Category $category)
    {
        // Start query with products from this category
        $query = Product::active()
            ->where('category_id', $category->id)
            ->with(['category', 'images']);

        // Filter by stock availability
        if ($request->has('stock')) {
            if ($request->stock === 'available') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
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

        // Reuse Products/Index.vue but with category context
        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'stock' => $request->stock,
                'category' => $category->id, // Pre-filter by this category
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'sort' => $sortBy,
            ],
            // Pass category info for breadcrumbs and header
            'currentCategory' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'icon' => $category->icon,
            ],
        ]);
    }
}
