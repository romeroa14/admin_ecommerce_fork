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

        // Add product_id and user_id if logged in
        $validated['product_id'] = $product->id;
        $validated['user_id'] = auth()->id();

        // Set status based on configuration (auto-approve or pending)
        $validated['status'] = config('reviews.auto_approve', false) ? 'approved' : 'pending';

        if ($validated['status'] === 'approved') {
            $validated['approved_at'] = now();
        }

        $review = Review::create($validated);

        return back()->with('success', 'Gracias por tu reseña! ' .
            ($review->status === 'pending' ? 'Será revisada antes de publicarse.' : 'Tu reseña ha sido publicada.'));
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
