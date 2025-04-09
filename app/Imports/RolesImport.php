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

        // Mapear las columnas en catalán a las columnas en español
        $row = array_combine(
            array_map(function ($key) {
                return match($key) {
                    'nom' => 'nombre',
                    'codi' => 'codigo',
                    default => $key
                };
            }, array_keys($row)),
            array_values($row)
        );

        // Validar que las columnas requeridas existan
        $required_columns = [
            'nombre', // nom
            'codigo'  // codi
        ];
        $column_names = [
            'nombre' => 'Nom',
            'codigo' => 'Codi'
        ];
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".$column_names[$column]."' no trobada a l'arxiu");
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
            throw new \Exception("Els camps Nom i Codi no poden estar buits");
        }

        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No existeix un període actiu');
        }

        // Verificar si ya existe un rol con el mismo código en este periodo
        $existingRole = Rol::where('codigo', $row['codigo'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if ($existingRole) {
            throw new \Exception("Ja existeix un rol amb el codi '{$row['codigo']}' en aquest període");
        }

        return new Rol([
            'nombre' => trim($row['nombre']),
            'codigo' => trim((string)$row['codigo']),
            'periodo_id' => $periodo->id,
        ]);
    }
}
