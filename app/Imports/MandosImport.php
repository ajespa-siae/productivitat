<?php

namespace App\Imports;

use App\Models\Mando;
use App\Models\Periodo;
use App\Models\Empleado;
use App\Models\Grupo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class MandosImport implements ToModel, WithHeadingRow
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
                    'nif' => 'nif',
                    'grup' => 'grupo',
                    default => $key
                };
            }, array_keys($row)),
            array_values($row)
        );

        // Validar que las columnas requeridas existan
        $required_columns = ['nif', 'grupo'];
        $column_names = [
            'nif' => 'NIF',
            'grupo' => 'Grup'
        ];
        
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".$column_names[$column]."' no trobada a l'arxiu");
            }
        }

        // Validar que los campos no estén vacíos
        if (!isset($row['nif']) || (string)$row['nif'] === '' ||
            !isset($row['grupo']) || (string)$row['grupo'] === '') {
            Log::error('Valores inválidos:', [
                'nif' => $row['nif'] ?? 'no definido',
                'grupo' => $row['grupo'] ?? 'no definido',
                'tipo_nif' => gettype($row['nif'] ?? null),
                'tipo_grupo' => gettype($row['grupo'] ?? null)
            ]);
            throw new \Exception("Els camps NIF i Grup no poden estar buits");
        }

        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No existeix un període actiu');
        }

        // Buscar el empleado por NIF en el periodo actual
        $empleado = Empleado::where('nif', $row['nif'])
            ->where('periodo_id', $periodo->id)
            ->first();
            
        if (!$empleado) {
            throw new \Exception("No s'ha trobat cap empleat amb NIF '{$row['nif']}' en aquest període");
        }

        // Buscar el grupo por código
        $grupo = Grupo::where('codigo', $row['grupo'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if (!$grupo) {
            throw new \Exception("No s'ha trobat cap grup amb codi '{$row['grupo']}' en aquest període");
        }

        // Verificar si ya existe un mando con el mismo NIF en este periodo
        $existingMando = Mando::where('nif', $row['nif'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if ($existingMando) {
            throw new \Exception("Ja existeix un comandament amb NIF '{$row['nif']}' en aquest període");
        }

        return new Mando([
            'nif' => $row['nif'],
            'grupo_id' => $grupo->id,
            'periodo_id' => $periodo->id,
        ]);
    }
}
