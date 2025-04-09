<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionMail;
use App\Models\Notificacion;

class SendTestEmail extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Envía un correo de prueba para verificar la configuración del correo';

    public function handle()
    {
        $email = $this->argument('email');
        
        // Crear una notificación de prueba
        $notificacion = Notificacion::create([
            'empleado_id' => 1, // ID de empleado ficticio
            'tipo' => 'prueba',
            'mensaje' => 'Esta es una prueba del sistema de correo',
            'url' => null,
            'leida' => false
        ]);

        try {
            Mail::to($email)->send(new NotificacionMail($notificacion));
            $this->info('Correo enviado correctamente a: ' . $email);
        } catch (\Exception $e) {
            $this->error('Error al enviar el correo: ' . $e->getMessage());
        }
    }
}
