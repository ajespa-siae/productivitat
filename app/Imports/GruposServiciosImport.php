<?php

namespace App\Imports;

use App\Models\Grupo;
use App\Models\GrupoServicio;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class GruposServiciosImport implements ToModel, WithHeadingRow
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
                    'grup' => 'grupo',
                    'servei' => 'servicio',
                    default => $key
                };
            }, array_keys($row)),
            array_values($row)
        );

        // Validar que las columnas requeridas existan
        $required_columns = [
            'grupo',    // grup
            'servicio', // servei
        ];
        $column_names = [
            'grupo' => 'Grup',
            'servicio' => 'Servei',
        ];
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".$column_names[$column]."' no trobada a l'arxiu");
            }
        }

        // Validar que los campos no estén vacíos
        if (!isset($row['grupo']) || (string)$row['grupo'] === '' ||
            !isset($row['servicio']) || (string)$row['servicio'] === '') {
            Log::error('Valores inválidos:', [
                'grupo' => $row['grupo'] ?? 'no definido',
                'servicio' => $row['servicio'] ?? 'no definido',
            ]);
            throw new \Exception("Els camps Grup i Servei no poden estar buits");
        }

        // Verificar si el grupo existe
        $grupo = Grupo::where('codigo', $row['grupo'])->first();
        if (!$grupo) {
            throw new \Exception("No existeix un grup amb codi '{$row['grupo']}'");
        }

        // Verificar si ya existe esta combinación de grupo y servicio
        $existingGrupoServicio = GrupoServicio::where('codigo_grupo', $row['grupo'])
            ->where('servicio', $row['servicio'])
            ->first();

        if ($existingGrupoServicio) {
            throw new \Exception("Ja existeix el servei '{$row['servicio']}' per al grup '{$row['grupo']}'");
        }

        return new GrupoServicio([
            'codigo_grupo' => trim((string)$row['grupo']),
            'servicio' => trim((string)$row['servicio']),
        ]);
    }
}
