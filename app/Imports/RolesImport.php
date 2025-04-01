<?php

namespace App\Imports;

use App\Models\Rol;
use App\Models\Periodo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RolesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Datos recibidos del Excel:', $row);
        
        // Convertir las claves del array a minúsculas
        $row = array_change_key_case($row, CASE_LOWER);
        
        Log::info('Datos después de convertir a minúsculas:', $row);

        // Validar que las columnas requeridas existan
        $required_columns = ['nombre', 'codigo'];
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".ucfirst($column)."' no encontrada en el archivo");
            }
        }

        // Validar que los campos no estén vacíos y son del tipo correcto
        if (!isset($row['nombre']) || !is_string($row['nombre']) || trim($row['nombre']) === '' ||
            !isset($row['codigo']) || (string)$row['codigo'] === '') {
            Log::error('Valores inválidos:', [
                'nombre' => $row['nombre'] ?? 'no definido',
                'codigo' => $row['codigo'] ?? 'no definido',
                'tipo_nombre' => gettype($row['nombre'] ?? null),
                'tipo_codigo' => gettype($row['codigo'] ?? null)
            ]);
            throw new \Exception("Los campos Nombre y Código no pueden estar vacíos");
        }

        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No hay ningún periodo activo');
        }

        // Verificar si ya existe un rol con el mismo código en este periodo
        $existingRole = Rol::where('codigo', $row['codigo'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if ($existingRole) {
            throw new \Exception("Ya existe un rol con el código '{$row['codigo']}' en este periodo");
        }

        return new Rol([
            'nombre' => trim($row['nombre']),
            'codigo' => trim((string)$row['codigo']),
            'periodo_id' => $periodo->id,
        ]);
    }
}
