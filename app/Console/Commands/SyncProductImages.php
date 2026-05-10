<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SyncProductImages extends Command
{
    protected $signature = 'products:sync-images {--dry-run : Show what would download without saving}';
    protected $description = 'Download product images from Google Drive folders';

    private string $apiKey;
    private int $downloaded = 0;
    private int $skipped = 0;
    private int $errors = 0;

    public function __construct()
    {
        parent::__construct();
        $this->apiKey = config('services.google_drive_api_key', '');
    }

    public function handle(): int
    {
        if (empty($this->apiKey)) {
            $this->error('❌ GOOGLE_DRIVE_API_KEY not configured in .env');
            return self::FAILURE;
        }

        $this->info('🖼️  Syncing product images from Google Drive...');

        $products = Product::whereNotNull('photo_link')
            ->where('photo_link', '!=', '')
            ->get();

        $this->info("📦 Found {$products->count()} products with photo links.");
        $this->newLine();

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $this->processProduct($product);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->showSummary();

        return self::SUCCESS;
    }

    private function processProduct(Product $product): void
    {
        $folderId = $this->extractFolderId($product->photo_link);
        if (!$folderId) {
            $this->errors++;
            return;
        }

        // Check if product already has images — skip download but still sync images column
        if (!$this->option('dry-run') && $product->productImages()->count() > 0) {
            $this->syncImagesColumn($product);
            $this->skipped++;
            return;
        }

        // List files in the Drive folder
        $files = $this->listDriveFiles($folderId);
        if (empty($files)) {
            $this->errors++;
            return;
        }

        if ($this->option('dry-run')) {
            $this->skipped++;
            return;
        }

        $isFirst = true;
        foreach ($files as $i => $file) {
            $imageData = $this->downloadFile($file['id']);

            // Skip files larger than 10MB (unusual for product photos)
            if (strlen($imageData) < 1024 || strlen($imageData) > 10 * 1024 * 1024) {
                continue;
            }

            $filename = 'products/' . $product->id . '/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $imageData);

            ProductImage::create([
                'product_id' => $product->id,
                'image'      => $filename,
                'is_primary' => $isFirst,
                'sort_order' => $i,
                'alt_text'   => $product->name,
            ]);

            $isFirst = false;
        }

        // Also populate the images JSON column so frontend picks them up via Priority 1
        $this->syncImagesColumn($product);

        $this->downloaded++;
    }

    private function syncImagesColumn(Product $product): void
    {
        $imagePaths = $product->productImages()->ordered()->pluck('image')->toArray();
        $product->update(['images' => $imagePaths]);
    }

    private function extractFolderId(?string $url): ?string
    {
        if (empty($url)) return null;

        // Match: drive.google.com/drive/folders/{ID}
        if (preg_match('#/folders/([a-zA-Z0-9_-]+)#', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    private function listDriveFiles(string $folderId): array
    {
        $url = 'https://www.googleapis.com/drive/v3/files';
        $query = "'{$folderId}' in parents and mimeType contains 'image/' and trashed = false";

        $response = Http::timeout(15)
            ->get($url, [
                'q'      => $query,
                'key'    => $this->apiKey,
                'fields' => 'files(id,name,mimeType)',
                'pageSize' => 50,
            ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json('files', []);
    }

    private function downloadFile(string $fileId): string
    {
        // Use direct download URL (works for publicly shared files, no API key needed)
        $url = "https://drive.google.com/uc?export=download&id={$fileId}";

        $response = Http::timeout(30)
            ->withOptions(['allow_redirects' => true])
            ->get($url);

        return $response->successful() ? $response->body() : '';
    }

    private function showSummary(): void
    {
        $this->info('═══════════════════════════════════');
        $this->info('🖼️  Image Sync Summary');
        $this->info('═══════════════════════════════════');
        $this->info("  ✅ Downloaded: <fg=green>{$this->downloaded}</>");
        $this->info("  ⏭️  Skipped:    <fg=gray>{$this->skipped}</>");
        $this->info("  ❌ Errors:     <fg=red>{$this->errors}</>");
    }
}
