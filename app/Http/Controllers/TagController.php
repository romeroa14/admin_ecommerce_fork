<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagController extends Controller
{
    public function show(Tag $tag)
    {
        $products = $tag->products()->active()->with('images')->paginate(12);

        return Inertia::render('Tags/Show', [
            'tag' => $tag,
            'products' => $products,
        ]);
    }
}
