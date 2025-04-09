<?php

namespace App\Http\Controllers;

use App\Models\AlegacionResultado;
use App\Models\ResultadoEvaluacion;
use App\Models\Empleado;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AlegacionResultadoController extends Controller
{
    public function store(Request $request, ResultadoEvaluacion $resultado)
    {
        Log::info('Iniciando creación de alegación:', [
            'resultado_id' => $resultado->id,
            'user_nif' => Auth::user()->nif
        ]);

        $request->validate([
            'texto_alegacion' => 'required|string|min:10'
        ]);

        Log::info('Validación superada');

        // Verificar que el usuario autenticado es el evaluado
        $empleado = Empleado::where('nif', Auth::user()->nif)->firstOrFail();
        if ($resultado->evaluacion->evaluado_id !== $empleado->id) {
            abort(403, 'No tens permís per al·legar sobre aquest resultat.');
        }

        // Verificar que no existe una alegación previa
        if ($resultado->alegacion()->exists()) {
            abort(400, 'Ja existeix una al·legació per aquest resultat.');
        }

        Log::info('Creando alegación:', [
            'resultado_id' => $resultado->id,
            'empleado_id' => $empleado->id,
            'texto_alegacion' => $request->texto_alegacion
        ]);

        $alegacion = new AlegacionResultado([
            'resultado_id' => $resultado->id,
            'empleado_id' => $empleado->id,
            'texto_alegacion' => $request->texto_alegacion,
            'fecha_alegacion' => now(),
            'estado' => 'Pendent'
        ]);

        $alegacion->save();

        // Notificar al evaluador
        Notificacion::crearNotificacion(
            $resultado->evaluacion->evaluador_id,
            'alegacion_nueva',
            'Nova al·legació rebuda per a l\'avaluació de ' . $empleado->nombre,
            route('evaluaciones.show', $resultado->evaluacion)
        );

        return redirect()->back()->with('success', 'Al·legació registrada correctament.');
    }

    public function responder(Request $request, AlegacionResultado $alegacion)
    {
        $request->validate([
            'estado' => 'required|in:Acceptada,Rebutjada',
            'respuesta' => 'required|string|min:10'
        ]);

        // Verificar que el usuario autenticado es el evaluador
        $evaluador = Empleado::where('nif', Auth::user()->nif)->firstOrFail();
        if ($alegacion->resultado->evaluacion->evaluador_id !== $evaluador->id) {
            abort(403, 'No tens permís per respondre a aquesta al·legació.');
        }

        // Si la alegación es aceptada, establecer la puntuación del resultado a 0
        if ($request->estado === 'Acceptada') {
            Log::info('Acceptant al·legació i establint puntuació a 0', [
                'alegacion_id' => $alegacion->id,
                'resultado_id' => $alegacion->resultado->id,
                'puntuacion_anterior' => $alegacion->resultado->puntuacion
            ]);

            $alegacion->resultado->update([
                'puntuacion' => 0
            ]);
        }

        $alegacion->update([
            'estado' => $request->estado,
            'respuesta' => $request->respuesta,
            'fecha_respuesta' => now(),
            'evaluador_id' => $evaluador->id
        ]);

        // Notificar al evaluado
        $estadoText = $request->estado === 'Acceptada' ? 'acceptada' : 'rebutjada';
        Notificacion::crearNotificacion(
            $alegacion->empleado_id,
            'alegacion_respondida',
            'La teva al·legació ha estat ' . $estadoText,
            route('evaluaciones.show', $alegacion->resultado->evaluacion)
        );

        return redirect()->back()->with('success', 'Respuesta registrada correctamente.');
    }
}
