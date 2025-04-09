<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empleado;
use App\Models\Periodo;
use App\Models\MandoEmpleado;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Log::info('DashboardController@index iniciando');
        $user = Auth::user();
        
        // Obtener el periodo activo
        $periodoActivo = Periodo::where('activo', true)->first();
        \Illuminate\Support\Facades\Log::info('Buscando periodo activo:', [
            'periodo_encontrado' => $periodoActivo ? true : false,
            'periodo_id' => $periodoActivo ? $periodoActivo->id : null,
            'periodo_nombre' => $periodoActivo ? $periodoActivo->nombre : null
        ]);

        // Obtener empleados a evaluar (si el usuario es evaluador)
        $empleadosAEvaluar = [];

        // Solo cargar datos de evaluador si el usuario es mando
        if ($user->is_mando && $periodoActivo) {
            $mando = \App\Models\Mando::where('nif', $user->nif)
                ->where('periodo_id', $periodoActivo->id)
                ->first();

            if ($mando) {
                $today = Carbon::now();
                // Obtener los empleados relacionados a través de MandoEmpleado y que tengan roles vigentes
                $empleadosAEvaluar = Empleado::query()
                    ->select('empleados.*')
                    ->join('mandos_empleados', 'empleados.id', '=', 'mandos_empleados.empleado_id')
                    ->join('roles_empleados', 'empleados.nif', '=', 'roles_empleados.nif')
                    ->where('mandos_empleados.mando_id', $mando->id)
                    ->where(function($query) use ($today) {
                        $query->where(function($q) use ($today) {
                            $q->where('roles_empleados.fecha_inicio', '<=', $today)
                              ->where(function($q2) use ($today) {
                                  $q2->whereNull('roles_empleados.fecha_fin')
                                     ->orWhere('roles_empleados.fecha_fin', '>=', $today);
                              });
                        });
                    })
                    ->orderBy('empleados.nombre')
                    ->orderBy('empleados.apellidos')
                    ->distinct()
                    ->get();
            }
        }

        // Obtener evaluaciones recientes donde el usuario es evaluador
        $evaluacionesRecientes = collect();
        if ($periodoActivo && $user->is_mando) {
            // Obtener el empleado evaluador basado en el NIF del usuario
            $evaluador = \App\Models\Empleado::where('nif', $user->nif)->first();
            \Illuminate\Support\Facades\Log::info('Buscando evaluaciones para evaluador:', [
                'user_nif' => $user->nif,
                'evaluador_id' => $evaluador ? $evaluador->id : null
            ]);

            if ($evaluador) {
                // Primero obtenemos los empleados con evaluaciones
                $empleadosConEvaluaciones = \App\Models\Evaluacion::with(['evaluado'])
                    ->select('evaluado_id')
                    ->where('evaluador_id', $evaluador->id)
                    ->where('periodo_id', $periodoActivo->id)
                    ->where('tipo', 'Registro')
                    ->where('finalizada', false)
                    ->groupBy('evaluado_id')
                    ->get();

                \Illuminate\Support\Facades\Log::info('Empleados con evaluaciones encontrados:', [
                    'count' => $empleadosConEvaluaciones->count()
                ]);

                // Luego, para cada empleado, obtenemos su primera evaluación junto con todos sus resultados
                $evaluacionesRecientes = collect();
                foreach ($empleadosConEvaluaciones as $empleadoEval) {
                    $evaluacion = \App\Models\Evaluacion::with(['evaluado'])
                        ->where('evaluado_id', $empleadoEval->evaluado_id)
                        ->where('evaluador_id', $evaluador->id)
                        ->where('periodo_id', $periodoActivo->id)
                        ->where('tipo', 'Registro')
                        ->where('finalizada', false)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($evaluacion) {
                        // Cargar todos los resultados para este empleado
                        $evaluacion->totalResultados = \App\Models\ResultadoEvaluacion::whereHas('evaluacion', function($query) use ($evaluacion, $evaluador, $periodoActivo) {
                            $query->where('evaluado_id', $evaluacion->evaluado_id)
                                ->where('evaluador_id', $evaluador->id)
                                ->where('periodo_id', $periodoActivo->id)
                                ->where('tipo', 'Registro')
                                ->where('finalizada', false);
                        })->count();

                        $evaluacionesRecientes->push($evaluacion);
                    }
                }

                $evaluacionesRecientes = $evaluacionesRecientes->take(5);
            }
        }

        // Obtener evaluaciones recibidas por el usuario
        $evaluacionesRecibidas = collect();
        \Illuminate\Support\Facades\Log::info('Verificando periodo activo:', [
            'periodo_activo_exists' => isset($periodoActivo),
            'periodo_activo' => $periodoActivo ?? null
        ]);

        if ($periodoActivo) {
            \Illuminate\Support\Facades\Log::info('Periodo activo:', [
                'periodo_id' => $periodoActivo->id,
                'periodo_nombre' => $periodoActivo->nombre,
                'fecha_inicio' => $periodoActivo->fecha_inicio,
                'fecha_fin' => $periodoActivo->fecha_fin
            ]);

            // Obtener el empleado basado en el NIF del usuario
            \Illuminate\Support\Facades\Log::info('Buscando empleado para evaluaciones recibidas:', [
                'user_nif' => $user->nif,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_ldap_guid' => $user->ldap_guid
            ]);

            // Intentar encontrar el empleado por NIF
            $query = \App\Models\Empleado::where('nif', $user->nif);

            \Illuminate\Support\Facades\Log::info('Query de búsqueda de empleado:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $empleado = $query->first();

            \Illuminate\Support\Facades\Log::info('Empleado encontrado:', [
                'empleado_id' => $empleado ? $empleado->id : null,
                'empleado_nif' => $empleado ? $empleado->nif : null,
                'empleado_nombre' => $empleado ? $empleado->nombre : null
            ]);
            
            if ($empleado) {
                \Illuminate\Support\Facades\Log::info('Buscando evaluaciones recibidas:', [
                    'empleado_id' => $empleado->id,
                    'periodo_id' => $periodoActivo->id
                ]);

                \Illuminate\Support\Facades\Log::info('Buscando evaluaciones para empleado:', [
                    'empleado_id' => $empleado->id,
                    'periodo_id' => $periodoActivo->id
                ]);

                $query = \App\Models\Evaluacion::with(['evaluador'])
                    ->where('evaluado_id', $empleado->id)
                    ->where('periodo_id', $periodoActivo->id)
                    ->where('tipo', 'Registro')
                    ->where('finalizada', false)
                    ->orderBy('created_at', 'desc')
                    ->take(5);

                \Illuminate\Support\Facades\Log::info('Query SQL:', [
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ]);

                $evaluacionesRecibidas = $query->get();

                \Illuminate\Support\Facades\Log::info('Evaluaciones encontradas:', [
                    'count' => $evaluacionesRecibidas->count(),
                    'evaluaciones' => $evaluacionesRecibidas->map(function($e) {
                        return [
                            'id' => $e->id,
                            'evaluador_id' => $e->evaluador_id,
                            'evaluador_nombre' => $e->evaluador ? $e->evaluador->nombre : null,
                            'created_at' => $e->created_at
                        ];
                    })
                ]);

                \Illuminate\Support\Facades\Log::info('Evaluaciones recibidas encontradas:', [
                    'count' => $evaluacionesRecibidas->count(),
                    'evaluaciones' => $evaluacionesRecibidas->map(function($e) {
                        return [
                            'id' => $e->id,
                            'evaluador_id' => $e->evaluador_id,
                            'evaluador_nombre' => $e->evaluador ? $e->evaluador->nombre : null
                        ];
                    })
                ]);

                // Cargar el total de resultados para cada evaluación
                foreach ($evaluacionesRecibidas as $evaluacion) {
                    $evaluacion->totalResultados = \App\Models\ResultadoEvaluacion::whereHas('evaluacion', function($query) use ($evaluacion) {
                        $query->where('evaluado_id', $evaluacion->evaluado_id)
                            ->where('evaluador_id', $evaluacion->evaluador_id)
                            ->where('periodo_id', $evaluacion->periodo_id)
                            ->where('tipo', 'Registro')
                            ->where('finalizada', false);
                    })->count();
                }
            }
        }

        // Obtener los indicadores para el modal de nueva evaluación
        $indicadores = \App\Models\Indicador::all();

        return view('dashboard.index', [
            'empleadosEvaluables' => $empleadosAEvaluar,
            'indicadores' => $indicadores,
            'user' => $user,
            'empleadosAEvaluar' => $empleadosAEvaluar,
            'evaluacionesRecientes' => $evaluacionesRecientes,
            'evaluacionesRecibidas' => $evaluacionesRecibidas,
            'periodoActivo' => $periodoActivo
        ]);
    }
}
