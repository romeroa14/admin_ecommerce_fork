<?php

$dir = '/var/www/html/admin_ecommerce_fork/app/Filament/Resources';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), 'Form.php')) {
        $files[] = $file->getPathname();
    }
}

foreach ($files as $file) {
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $newLines = [];
    $changed = false;

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        
        // Match a ::make(...) call that ends with a comma
        if (preg_match('/^(\s*.*::make\([^)]+\)),\s*$/', $line, $matches)) {
            // Check if the NEXT line starts with ->label
            if ($i + 1 < count($lines) && preg_match('/^\s*->label\(/', $lines[$i + 1])) {
                // Yes! Remove the comma from THIS line
                $line = $matches[1];
                $changed = true;
                
                // Now we need to append the comma to wherever the chain ends
                // Let's find the end of the chain
                $j = 1;
                while ($i + $j < count($lines) && preg_match('/^\s*->/', $lines[$i + $j])) {
                    $j++;
                }
                // The last line of the chain is $i + $j - 1
                $lines[$i + $j - 1] .= ',';
            }
        }
        
        $newLines[] = $line;
    }

    if ($changed) {
        file_put_contents($file, implode("\n", $newLines));
        echo "Fixed commas in: $file\n";
    }
}
echo "Done fixing.\n";
