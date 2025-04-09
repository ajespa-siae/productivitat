<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use App\Models\ResultadoEvaluacion;
use App\Models\Indicador;
use App\Models\Periodo;
use App\Models\Empleado;
use App\Models\RolEmpleado;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EvaluacionController extends Controller
{
    public function create()
    {
        $periodo = Periodo::where('activo', true)->firstOrFail();
        $user = Auth::user();
        $evaluador = Empleado::where('nif', $user->nif)->firstOrFail();
        
        // Get employees that can be evaluated by the current user
        $empleadosEvaluables = Empleado::whereHas('rolEmpleados', function($query) use ($evaluador) {
            $query->where('evaluador_id', $evaluador->id);
        })->get();
        
        $indicadores = Indicador::all();
        
        return view('evaluaciones.create', [
            'empleados' => $empleadosEvaluables,
            'indicadores' => $indicadores,
            'periodo' => $periodo
        ]);
    }
    public function store(Request $request)
    {
        Log::info('Recibida solicitud de evaluación:', $request->all());

        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'indicador_id' => 'required|exists:indicadores,id',
            'puntuacion' => 'required|numeric|min:0|max:10',
            'comentario' => 'nullable|string|max:65535',
        ]);

        try {
            // Obtener el periodo activo
            $periodo = Periodo::where('activo', true)->firstOrFail();

            // Obtener el empleado evaluador basado en el NIF del usuario autenticado
            $user = Auth::user();
            Log::info('Usuario autenticado:', ['nif' => $user->nif]);

            $evaluador = Empleado::where('nif', $user->nif)->first();
            if (!$evaluador) {
                Log::error('No se encontró el empleado evaluador con NIF: ' . $user->nif);
                throw new \Exception('No se encontró el empleado evaluador');
            }
            Log::info('Empleado evaluador encontrado:', ['id' => $evaluador->id]);

            // Buscar o crear una evaluación para este empleado en este período
            $evaluacion = Evaluacion::firstOrCreate(
                [
                    'evaluado_id' => $request->empleado_id,
                    'evaluador_id' => $evaluador->id,
                    'periodo_id' => $periodo->id,
                    'tipo' => 'Registro',
                ],
                [
                    'finalizada' => false,
                ]
            );

            Log::info('Evaluación encontrada/creada:', ['evaluacion_id' => $evaluacion->id]);

            // Crear el resultado
            $resultado = ResultadoEvaluacion::create([
                'evaluacion_id' => $evaluacion->id,
                'indicador_id' => $request->indicador_id,
                'puntuacion' => $request->puntuacion,
                'comentario' => $request->comentario,
            ]);

            Log::info('Resultado creado:', ['resultado_id' => $resultado->id]);

            // Notificar al empleado evaluado
            $evaluado = Empleado::find($request->empleado_id);
            $indicador = Indicador::find($request->indicador_id);
            Notificacion::crearNotificacion(
                $request->empleado_id,
                'evaluacion_nueva',
                'Has rebut una nova avaluació de l\'indicador "' . $indicador->nombre . '" per part de ' . $evaluador->nombre,
                route('evaluaciones.show', $evaluacion)
            );

            return redirect()->back()->with('success', 'Avaluació registrada correctament.');
        } catch (\Exception $e) {
            Log::error('Error al guardar la evaluación: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar la evaluación: ' . $e->getMessage());
        }
    }

    public function show(\App\Models\Evaluacion $evaluacion)
    {
        // Verificar que el usuario actual es el evaluador
        $user = auth()->user();
        $evaluador = \App\Models\Empleado::where('nif', $user->nif)->firstOrFail();
        
        // Verificar que el usuario actual es el evaluador o el evaluado
        $empleado = \App\Models\Empleado::where('nif', $user->nif)->firstOrFail();
        
        if ($evaluacion->evaluador_id !== $empleado->id && $evaluacion->evaluado_id !== $empleado->id) {
            Log::warning('Acceso denegado a evaluación:', [
                'user_nif' => $user->nif,
                'evaluacion_id' => $evaluacion->id,
                'evaluador_id' => $evaluacion->evaluador_id,
                'evaluado_id' => $evaluacion->evaluado_id
            ]);
            abort(403, 'No tienes permiso para ver esta evaluación');
        }

        // Cargar todos los resultados para este empleado en el mismo período
        $resultados = \App\Models\ResultadoEvaluacion::with(['indicador', 'evaluacion.evaluador'])
            ->whereHas('evaluacion', function($query) use ($evaluacion) {
                $query->where('evaluado_id', $evaluacion->evaluado_id)
                    ->where('evaluador_id', $evaluacion->evaluador_id)
                    ->where('periodo_id', $evaluacion->periodo_id)
                    ->where('tipo', 'Registro')
                    ->where('finalizada', false);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Log para debug
        \Illuminate\Support\Facades\Log::info('Resultados encontrados:', [
            'total' => $resultados->count(),
            'evaluacion_id' => $evaluacion->id,
            'evaluado_id' => $evaluacion->evaluado_id,
            'evaluador_id' => $evaluacion->evaluador_id
        ]);

        // Agrupar los resultados por indicador
        $resultadosPorIndicador = $resultados->groupBy('indicador_id');
        
        return view('evaluaciones.show', [
            'evaluacion' => $evaluacion->load(['evaluado', 'evaluador']),
            'resultadosPorIndicador' => $resultadosPorIndicador
        ]);
    }

    public function getIndicadores(Request $request)
    {
        try {
            $request->validate([
                'empleado_id' => 'required|exists:empleados,id',
            ]);

            // Obtener el empleado
            $empleado = Empleado::findOrFail($request->empleado_id);

            // Obtener el rol y grupo actual del empleado (el más reciente sin fecha_fin)
            $rolEmpleado = RolEmpleado::where('nif', $empleado->nif)
                ->whereNull('fecha_fin')
                ->orderBy('fecha_inicio', 'desc')
                ->firstOrFail();

            // Obtener los indicadores asociados al rol y grupo del empleado
            $indicadores = Indicador::query()
                ->where('grupo_id', $rolEmpleado->grupo_id)
                ->where('rol_id', $rolEmpleado->rol_id)
                ->where('tipo_evaluacion', 'Registre')
                ->where('periodo_id', Periodo::where('activo', true)->value('id'))
                ->get(['id', 'nombre']); // Solo necesitamos estos campos para el select

            // Asegurarnos de que siempre devolvemos un array, incluso si está vacío
            return response()->json($indicadores->toArray());
        } catch (\Exception $e) {
            \Log::error('Error en getIndicadores: ' . $e->getMessage());
            // Devolver un array vacío en caso de error para evitar el error de forEach
            return response()->json([], 500);
        }
    }
}
