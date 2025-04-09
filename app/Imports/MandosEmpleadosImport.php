<?php

namespace App\Imports;

use App\Models\MandoEmpleado;
use App\Models\Periodo;
use App\Models\Mando;
use App\Models\Empleado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class MandosEmpleadosImport implements ToModel, WithHeadingRow
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
                    'nif_comandament' => 'nif_mando',
                    'nif_empleat' => 'nif_empleado',
                    default => $key
                };
            }, array_keys($row)),
            array_values($row)
        );

        // Validar que las columnas requeridas existan
        $required_columns = ['nif_mando', 'nif_empleado'];
        $column_names = [
            'nif_mando' => 'NIF Comandament',
            'nif_empleado' => 'NIF Empleat'
        ];
        
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".$column_names[$column]."' no trobada a l'arxiu");
            }
        }

        // Validar que los campos no estén vacíos
        if (!isset($row['nif_mando']) || (string)$row['nif_mando'] === '' ||
            !isset($row['nif_empleado']) || (string)$row['nif_empleado'] === '') {
            Log::error('Valores inválidos:', [
                'nif_mando' => $row['nif_mando'] ?? 'no definido',
                'nif_empleado' => $row['nif_empleado'] ?? 'no definido',
                'tipo_nif_mando' => gettype($row['nif_mando'] ?? null),
                'tipo_nif_empleado' => gettype($row['nif_empleado'] ?? null)
            ]);
            throw new \Exception("Els camps NIF Comandament i NIF Empleat no poden estar buits");
        }

        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No existeix un període actiu');
        }

        // Buscar el mando por NIF
        $mando = Mando::where('nif', $row['nif_mando'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if (!$mando) {
            throw new \Exception("No s'ha trobat cap comandament amb NIF '{$row['nif_mando']}' en aquest període");
        }

        // Buscar el empleado por NIF
        $empleado = Empleado::where('nif', $row['nif_empleado'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if (!$empleado) {
            throw new \Exception("No s'ha trobat cap empleat amb NIF '{$row['nif_empleado']}' en aquest període");
        }

        // Verificar si ya existe esta relación
        $existingRelation = MandoEmpleado::where('mando_id', $mando->id)
            ->where('empleado_id', $empleado->id)
            ->first();

        if ($existingRelation) {
            throw new \Exception("Ja existeix una relació entre el comandament amb NIF '{$row['nif_mando']}' i l'empleat amb NIF '{$row['nif_empleado']}'");
        }

        return new MandoEmpleado([
            'mando_id' => $mando->id,
            'empleado_id' => $empleado->id,
        ]);
    }
}
