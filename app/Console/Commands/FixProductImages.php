<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class FixProductImages extends Command
{
    protected $signature = 'products:fix-images';
    protected $description = 'Fix broken product image paths in the database';

    public function handle(): void
    {
        // Get real files from storage
        $realFiles = Storage::disk('public')->files('products');
        $realFiles = array_values(array_filter($realFiles, fn($f) => 
            !str_contains($f, 'placeholder') && 
            !str_contains($f, 'real-image') &&
            !str_contains($f, 'test-image')
        ));

        if (empty($realFiles)) {
            $this->error('No real image files found in storage/app/public/products/');
            return;
        }

        $this->info('Real files found: ' . count($realFiles));
        foreach ($realFiles as $f) {
            $this->line('  - ' . $f);
        }

        // Find broken images (seeder paths that don't exist)
        $allImages = ProductImage::all();
        $fixed = 0;

        foreach ($allImages as $i => $img) {
            $fullPath = storage_path('app/public/' . $img->image);
            
            if (!file_exists($fullPath)) {
                $newPath = $realFiles[$fixed % count($realFiles)];
                $img->update(['image' => $newPath]);
                $this->info("Fixed: {$img->image} => {$newPath}");
                $fixed++;
            } else {
                $this->line("OK: {$img->image}");
            }
        }

        $this->info("Fixed {$fixed} broken image paths.");
    }
}
