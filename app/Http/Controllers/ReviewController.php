<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'nullable|string|max:5000',
            'image' => 'nullable|image|max:5120', // 5MB
            'youtube_url' => 'nullable|url',
            'reviewer_name' => 'required_without:user_id|string|max:255',
            'reviewer_email' => 'required_without:user_id|email|max:255',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('reviews', 'public');
        }

        // Logic to link or create user
        $userId = auth()->id();
        
        if (!$userId && $request->reviewer_email) {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $request->reviewer_email],
                [
                    'name' => $request->reviewer_name,
                    'type' => 'customer',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                ]
            );
            $userId = $user->id;
        }

        $validated['product_id'] = $product->id;
        $validated['user_id'] = $userId;

        // Force approved status by default so they appear immediately
        $validated['status'] = 'approved';
        $validated['approved_at'] = now();

        $review = \App\Models\Review::create($validated);

        return back()->with('success', '¡Gracias por tu reseña! Tu opinión ha sido publicada correctamente.');
    }

    public function markHelpful(Review $review)
    {
        $review->markAsHelpful();
        return back();
    }

    public function markUnhelpful(Review $review)
    {
        $review->markAsUnhelpful();
        return back();
    }
}
