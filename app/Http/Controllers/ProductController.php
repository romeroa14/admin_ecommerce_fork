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
        $query = Product::active()->with(['category', 'subcategory', 'productImages']);

        // Filter by text search (LIKE)
        if ($request->has('search') && $request->search) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('name', 'ilike', $searchTerm);
        }

        // Filter by stock availability
        if ($request->has('stock')) {
            if ($request->stock === 'available') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        } elseif ($request->has('category') && $request->category) {
            // Support legacy query param
            $query->where('category_id', $request->category);
        }

        // Filter by subcategory
        if ($request->has('subcategory_id') && $request->subcategory_id) {
            $query->where('subcategory_id', $request->subcategory_id);
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
                'category_id' => $request->category_id ?: $request->category,
                'subcategory_id' => $request->subcategory_id,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'sort' => $sortBy,
                'search' => $request->search,
            ],
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'subcategory', 'productImages', 'variants.variantGroup', 'tags']);

        // Check stock logic
        $isInStock = $product->in_stock;

        // Get similar products from the same category
        $similarProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'productImages'])
            ->take(6)
            ->get();

        // Get approved reviews with user data
        $reviews = $product->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->get();

        // Calculate review statistics
        $reviewStats = [
            'average' => round($reviews->avg('rating'), 1),
            'total' => $reviews->count(),
            'ratings' => [
                5 => $reviews->where('rating', 5)->count(),
                4 => $reviews->where('rating', 4)->count(),
                3 => $reviews->where('rating', 3)->count(),
                2 => $reviews->where('rating', 2)->count(),
                1 => $reviews->where('rating', 1)->count(),
            ],
        ];

        return Inertia::render('Products/Show', [
            'product' => $product,
            'isInStock' => $isInStock,
            'similarProducts' => $similarProducts,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
        ]);
    }
}
