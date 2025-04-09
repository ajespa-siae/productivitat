<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NotificacionMail;
use App\Models\Empleado;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'empleado_id',
        'tipo',
        'mensaje',
        'url',
        'leida',
        'fecha_lectura'
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_lectura' => 'datetime'
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public static function crearNotificacion($empleadoId, $tipo, $mensaje, $url = null)
    {
        Log::info('Creando notificación:', [
            'empleado_id' => $empleadoId,
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'url' => $url
        ]);

        $notificacion = self::create([
            'empleado_id' => $empleadoId,
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'url' => $url,
            'leida' => false
        ]);

        // Enviar correo si el empleado tiene un usuario asociado
        $empleado = Empleado::find($empleadoId);
        Log::info('Empleado encontrado:', [
            'empleado_id' => $empleadoId,
            'tiene_empleado' => $empleado ? 'si' : 'no',
            'tiene_user' => ($empleado && $empleado->user) ? 'si' : 'no',
            'email' => $empleado && $empleado->user ? $empleado->user->email : 'no disponible'
        ]);

        if ($empleado && $empleado->user) {
            try {
                Mail::to($empleado->user->email)->send(new NotificacionMail($notificacion));
                Log::info('Correo enviado correctamente');
            } catch (\Exception $e) {
                Log::error('Error al enviar correo:', [
                    'error' => $e->getMessage(),
                    'email' => $empleado->user->email
                ]);
            }
        }

        return $notificacion;
    }

    public function marcarComoLeida()
    {
        $this->update([
            'leida' => true,
            'fecha_lectura' => now()
        ]);
    }
}
