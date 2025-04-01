<?php

namespace App\Imports;

use App\Models\Empleado;
use App\Models\Periodo;
use App\Models\Rol;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class EmpleadosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Datos recibidos del Excel:', $row);
        
        // Convertir las claves del array a minúsculas
        $row = array_change_key_case($row, CASE_LOWER);
        
        Log::info('Datos después de convertir a minúsculas:', $row);

        // Validar que las columnas requeridas existan
        $required_columns = ['nombre', 'codigo', 'rol'];
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".ucfirst($column)."' no encontrada en el archivo");
            }
        }

        // Validar que los campos no estén vacíos y son del tipo correcto
        if (!isset($row['nombre']) || !is_string($row['nombre']) || trim($row['nombre']) === '' ||
            !isset($row['codigo']) || (string)$row['codigo'] === '' ||
            !isset($row['rol']) || (string)$row['rol'] === '') {
            Log::error('Valores inválidos:', [
                'nombre' => $row['nombre'] ?? 'no definido',
                'codigo' => $row['codigo'] ?? 'no definido',
                'rol' => $row['rol'] ?? 'no definido',
                'tipo_nombre' => gettype($row['nombre'] ?? null),
                'tipo_codigo' => gettype($row['codigo'] ?? null),
                'tipo_rol' => gettype($row['rol'] ?? null)
            ]);
            throw new \Exception("Los campos Nombre, Código y Rol no pueden estar vacíos");
        }

        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No hay ningún periodo activo');
        }

        // Buscar el rol por código
        $rol = Rol::where('codigo', $row['rol'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if (!$rol) {
            throw new \Exception("No se encontró el rol con código '{$row['rol']}' en el periodo actual");
        }

        // Verificar si ya existe un empleado con el mismo código en este periodo
        $existingEmpleado = Empleado::where('codigo', $row['codigo'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if ($existingEmpleado) {
            throw new \Exception("Ya existe un empleado con el código '{$row['codigo']}' en este periodo");
        }

        return new Empleado([
            'nombre' => trim($row['nombre']),
            'codigo' => trim((string)$row['codigo']),
            'rol_id' => $rol->id,
            'periodo_id' => $periodo->id,
        ]);
    }
}
