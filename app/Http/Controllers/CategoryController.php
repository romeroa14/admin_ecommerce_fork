<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function show(Category $category)
    {
        $products = $category->products()->active()->with('images')->paginate(12);

        return Inertia::render('Categories/Show', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
