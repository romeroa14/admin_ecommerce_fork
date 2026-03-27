<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubcategoryController extends Controller
{
    /**
     * Display products from a specific subcategory
     */
    public function show(Request $request, Subcategory $subcategory)
    {
        $query = Product::active()
            ->where('subcategory_id', $subcategory->id)
            ->with(['category', 'subcategory', 'productImages']);

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
        $categories = Category::active()->get();

        // Load the main category for breadcrumbs and siblings for filters
        $subcategory->load('category.subcategories');

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'stock' => $request->stock,
                'category_id' => $subcategory->category_id,
                'subcategory_id' => $subcategory->id,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'sort' => $sortBy,
            ],
            'currentCategory' => [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'slug' => $subcategory->slug,
                'description' => $subcategory->description,
                'parent' => $subcategory->category, // Support breadcrumbs: Home > Category > Subcategory
                'subcategories' => $subcategory->category->subcategories, // Support siblings in filters
                'is_subcategory' => true,
            ],
        ]);
    }
}
