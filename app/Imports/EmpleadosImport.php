<?php

namespace App\Imports;

use App\Models\Empleado;
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

        // Mapear las columnas en catalán a las columnas en español
        $row = array_combine(
            array_map(function ($key) {
                return match($key) {
                    'nom' => 'nombre',
                    'cognoms' => 'apellidos',
                    default => $key
                };
            }, array_keys($row)),
            array_values($row)
        );

        // Validar que las columnas requeridas existan
        $required_columns = [
            'nombre',    // nom
            'apellidos', // cognoms
            'nif',      // nif
        ];
        $column_names = [
            'nombre' => 'Nom',
            'apellidos' => 'Cognoms',
            'nif' => 'NIF',
        ];
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".$column_names[$column]."' no trobada a l'arxiu");
            }
        }

        // Validar que los campos no estén vacíos y son del tipo correcto
        if (!isset($row['nombre']) || !is_string($row['nombre']) || trim($row['nombre']) === '' ||
            !isset($row['apellidos']) || !is_string($row['apellidos']) || trim($row['apellidos']) === '' ||
            !isset($row['nif']) || (string)$row['nif'] === '') {
            Log::error('Valores inválidos:', [
                'nombre' => $row['nombre'] ?? 'no definido',
                'apellidos' => $row['apellidos'] ?? 'no definido',
                'nif' => $row['nif'] ?? 'no definido',
                'tipo_nombre' => gettype($row['nombre'] ?? null),
                'tipo_apellidos' => gettype($row['apellidos'] ?? null),
                'tipo_nif' => gettype($row['nif'] ?? null),
            ]);
            throw new \Exception("Els camps Nom, Cognoms i NIF no poden estar buits");
        }

        // Verificar si ya existe un empleado con el mismo NIF
        $existingEmpleado = Empleado::where('nif', $row['nif'])->first();

        if ($existingEmpleado) {
            throw new \Exception("Ja existeix un empleat amb NIF '{$row['nif']}'");
        }

        return new Empleado([
            'nombre' => trim($row['nombre']),
            'apellidos' => trim($row['apellidos']),
            'nif' => trim((string)$row['nif']),
        ]);
    }
}
