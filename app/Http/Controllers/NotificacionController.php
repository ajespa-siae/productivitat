<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificacionController extends Controller
{
    public function eliminar(Notificacion $notificacion)
    {
        Log::info('NotificacionController@eliminar - Iniciando', [
            'notificacion_id' => $notificacion->id
        ]);

        $empleado = Empleado::where('nif', Auth::user()->nif)->firstOrFail();
        
        if ($notificacion->empleado_id !== $empleado->id) {
            Log::error('NotificacionController@eliminar - Intento de eliminar notificación de otro empleado', [
                'notificacion_empleado_id' => $notificacion->empleado_id,
                'empleado_id' => $empleado->id
            ]);
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $notificacion->delete();

        Log::info('NotificacionController@eliminar - Notificación eliminada', [
            'notificacion_id' => $notificacion->id
        ]);

        return response()->json(['success' => true]);
    }

    public function index()
    {
        \Log::info('NotificacionController@index - Iniciando');
        $empleado = Empleado::where('nif', Auth::user()->nif)->firstOrFail();
        \Log::info('NotificacionController@index - Empleado encontrado:', [
            'empleado_id' => $empleado->id,
            'empleado_nif' => $empleado->nif
        ]);

        $notificaciones = Notificacion::where('empleado_id', $empleado->id)
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('NotificacionController@index - Notificaciones encontradas:', [
            'count' => $notificaciones->count(),
            'notificaciones' => $notificaciones->toArray()
        ]);

        return response()->json($notificaciones);
    }

    public function noLeidas()
    {
        \Log::info('NotificacionController@noLeidas - Iniciando');
        $empleado = Empleado::where('nif', Auth::user()->nif)->firstOrFail();
        \Log::info('NotificacionController@noLeidas - Empleado encontrado:', [
            'empleado_id' => $empleado->id,
            'empleado_nif' => $empleado->nif
        ]);

        $count = Notificacion::where('empleado_id', $empleado->id)
            ->where('leida', false)
            ->count();

        \Log::info('NotificacionController@noLeidas - Contador:', ['count' => $count]);
        return response()->json(['count' => $count]);
    }

    public function marcarComoLeida(Notificacion $notificacion)
    {
        $empleado = Empleado::where('nif', Auth::user()->nif)->firstOrFail();
        
        if ($notificacion->empleado_id !== $empleado->id) {
            abort(403, 'No tienes permiso para marcar esta notificación como leída.');
        }

        $notificacion->marcarComoLeida();

        return response()->json(['success' => true]);
    }

    public function marcarTodasComoLeidas()
    {
        $empleado = Empleado::where('nif', Auth::user()->nif)->firstOrFail();
        
        Notificacion::where('empleado_id', $empleado->id)
            ->where('leida', false)
            ->update([
                'leida' => true,
                'fecha_lectura' => now()
            ]);

        return response()->json(['success' => true]);
    }
}
