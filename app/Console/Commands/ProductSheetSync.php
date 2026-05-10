<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ProductSheetSync extends Command
{
    protected $signature = 'products:sync-sheet {--dry-run : Show what would change without saving}';
    protected $description = 'Sync products from Google Sheets "tienda" tab';

    private string $sheetUrl = 'https://docs.google.com/spreadsheets/d/1scM9VhLVIdzppBQ9RuIRV9Pm6b0GSQJ84XzNO3J3Og4/gviz/tq?tqx=out:csv&sheet=tienda';

    private int $created = 0;
    private int $updated = 0;
    private int $skipped = 0;
    private array $warnings = [];

    private ?int $defaultCategoryId = null;

    public function handle(): int
    {
        $this->info('🔄 Syncing products from Google Sheets...');
        $this->info("📊 Sheet: {$this->sheetUrl}");

        $rows = $this->fetchSheet();
        if (empty($rows)) {
            $this->error('❌ No data received from Google Sheets.');
            return self::FAILURE;
        }

        $this->info("📋 Found " . count($rows) . " products in sheet.");
        $this->info("🗄️  Database currently has " . Product::count() . " products.");
        $this->newLine();

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $i => $row) {
            $this->processRow($row, $i);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->showSummary();

        return self::SUCCESS;
    }

    private function fetchSheet(): array
    {
        $csv = @file_get_contents($this->sheetUrl);
        if (!$csv) {
            return [];
        }

        $lines = explode("\n", trim($csv));
        if (count($lines) < 2) {
            return [];
        }

        array_shift($lines); // Remove header row

        $rows = [];
        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (empty($data[2]) || trim($data[2]) === '') continue; // Skip empty product names

            $rows[] = [
                'stock'       => (int) ($data[0] ?? 0),
                'photo_link'  => $data[1] ?? '',
                'name'        => trim($data[2] ?? ''),
                'commission'  => $this->parsePrice($data[3] ?? '0'),
                'price_bcv'   => $this->parsePrice($data[4] ?? '0'),
                'price_bs'    => $data[5] ?? '',
                'price_promo' => $this->parsePrice($data[6] ?? '0'),
                'info'        => $data[7] ?? '',
            ];
        }

        return $rows;
    }

    private function parsePrice(string $price): float
    {
        $price = trim($price, " \t\n\r\0\x0B$\"");
        $price = str_replace(',', '.', $price);
        return (float) $price;
    }

    private function processRow(array $row, int $index): void
    {
        $name = $row['name'];
        $slug = Str::slug($name);
        $price = $row['price_promo'] ?: $row['price_bcv'];
        $comparePrice = $row['price_bcv'] ?: $price;
        $discountPct = $comparePrice > 0 && $comparePrice > $price
            ? round((($comparePrice - $price) / $comparePrice) * 100, 2)
            : 0;
        $isActive = $row['stock'] > 0;
        $description = $row['info'] ?: $name;

        // Find existing product by name (exact match)
        $product = Product::where('name', $name)->first();

        if ($product) {
            // Update
            if ($this->option('dry-run')) {
                $this->skipped++;
                return;
            }

            $product->update([
                'slug'                => $slug,
                'price'               => $price,
                'compare_price'       => $comparePrice,
                'discount_percentage' => $discountPct,
                'stock'               => $row['stock'],
                'status'              => $isActive ? 'active' : 'draft',
                'short_description'   => $description,
                'description'         => $description,
            ]);
            $this->updated++;
        } else {
            // Create
            if ($this->option('dry-run')) {
                $this->skipped++;
                return;
            }

            Product::create([
                'name'                => $name,
                'slug'                => $this->uniqueSlug($slug),
                'sku'                 => $this->generateSku($name),
                'price'               => $price,
                'compare_price'       => $comparePrice,
                'cost'                => $price * 0.6, // approximate
                'discount_percentage' => $discountPct,
                'category_id'         => $this->getDefaultCategoryId(),
                'stock'               => $row['stock'],
                'low_stock_threshold' => 5,
                'track_inventory'     => true,
                'status'              => $isActive ? 'active' : 'draft',
                'is_featured'         => false,
                'is_on_demand'        => false,
                'images'              => json_encode([]),
                'short_description'   => $description,
                'description'         => $description,
                'meta_title'          => $name,
                'meta_description'    => Str::limit($description, 160),
                'meta_keywords'       => json_encode(explode(' ', Str::limit($name, 100))),
            ]);
            $this->created++;
        }
    }

    private function uniqueSlug(string $slug): string
    {
        $original = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter++;
        }
        return $slug;
    }

    private function generateSku(string $name): string
    {
        $prefix = strtoupper(substr(Str::slug($name, ''), 0, 4));
        $random = strtoupper(Str::random(4));
        return "{$prefix}-{$random}";
    }

    private function getDefaultCategoryId(): ?int
    {
        if ($this->defaultCategoryId === null) {
            $category = \App\Models\Category::first();
            $this->defaultCategoryId = $category ? $category->id : null;
        }
        return $this->defaultCategoryId;
    }

    private function showSummary(): void
    {
        $this->info('═══════════════════════════════════');
        $this->info('📊 Sync Summary');
        $this->info('═══════════════════════════════════');
        $this->info("  ✅ Created:  <fg=green>{$this->created}</>");
        $this->info("  🔄 Updated:  <fg=yellow>{$this->updated}</>");
        $this->info("  ⏭️  Skipped:  <fg=gray>{$this->skipped}</>");
        $this->info("  🗄️  DB Total: <fg=cyan>" . Product::count() . '</>');
        $this->newLine();

        if (!empty($this->warnings)) {
            $this->warn('⚠️  Warnings:');
            foreach ($this->warnings as $warning) {
                $this->line("   - {$warning}");
            }
        }
    }
}
