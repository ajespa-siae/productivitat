<?php

namespace App\Imports;

use App\Models\Empleado;
use App\Models\Grupo;
use App\Models\Rol;
use App\Models\Periodo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class EmpleadosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No hay ningún periodo activo. Por favor, active un periodo antes de importar empleados.');
        }

        $grupo = Grupo::where('codigo', $row['grupo'])
            ->where('periodo_id', $periodo->id)
            ->first();
            
        $rol = Rol::where('codigo', $row['rol'])
            ->where('periodo_id', $periodo->id)
            ->first();

        return new Empleado([
            'nombre' => $row['nombre'],
            'apellidos' => $row['apellidos'],
            'nif' => $row['nif'],
            'grupo_id' => $grupo ? $grupo->id : null,
            'rol_id' => $rol ? $rol->id : null,
            'periodo_id' => $periodo->id,
        ]);
    }
}
