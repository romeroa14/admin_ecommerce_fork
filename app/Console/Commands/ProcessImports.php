<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessImports extends Command
{
    protected $signature = 'imports:process';
    protected $description = 'Procesa las importaciones pendientes';

    public function handle()
    {
        $this->info('Procesando importaciones...');
        
        // Ejecutar el queue worker para procesar jobs de importación
        Artisan::call('queue:work', [
            '--tries' => 3,
            '--timeout' => 300,
            '--stop-when-empty' => true
        ]);
        
        $this->info('Importaciones procesadas.');
    }
}
