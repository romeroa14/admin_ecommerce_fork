<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'mail:test {email : Email de destino}';
    protected $description = 'Envía un correo de prueba para verificar la configuración SMTP';

    public function handle()
    {
        $recipient = $this->argument('email');
        $this->info("Enviando correo de prueba a: {$recipient} ...");

        try {
            Mail::raw(
                "✅ Correo de prueba desde EquipoContainer.\n\nLa configuración de email funciona correctamente.\n\nFecha: " . now(),
                function ($m) use ($recipient) {
                    $m->to($recipient)->subject('✅ Prueba de Email - EquipoContainer');
                }
            );
            $this->info('✅ ¡Email enviado exitosamente!');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
