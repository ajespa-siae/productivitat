<?php

namespace App\Imports;

use App\Models\Indicador;
use App\Models\Periodo;
use App\Models\Competencia;
use App\Models\Grupo;
use App\Models\Rol;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class IndicadoresImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Datos recibidos del Excel:', $row);
        
        // Convertir las claves del array a minúsculas
        $row = array_change_key_case($row, CASE_LOWER);
        
        Log::info('Datos después de convertir a minúsculas:', $row);

        // Validar que las columnas requeridas existan
        $required_columns = ['competencia', 'grupo', 'rol', 'peso'];
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".ucfirst($column)."' no encontrada en el archivo");
            }
        }

        // Validar que los campos no estén vacíos y son del tipo correcto
        if (!isset($row['competencia']) || !is_string($row['competencia']) || trim($row['competencia']) === '' ||
            !isset($row['grupo']) || (string)$row['grupo'] === '' ||
            !isset($row['rol']) || (string)$row['rol'] === '' ||
            !isset($row['peso']) || !is_numeric($row['peso'])) {
            Log::error('Valores inválidos:', [
                'competencia' => $row['competencia'] ?? 'no definido',
                'grupo' => $row['grupo'] ?? 'no definido',
                'rol' => $row['rol'] ?? 'no definido',
                'peso' => $row['peso'] ?? 'no definido',
                'tipo_competencia' => gettype($row['competencia'] ?? null),
                'tipo_grupo' => gettype($row['grupo'] ?? null),
                'tipo_rol' => gettype($row['rol'] ?? null),
                'tipo_peso' => gettype($row['peso'] ?? null)
            ]);
            throw new \Exception("Los campos Competencia, Grupo, Rol y Peso no pueden estar vacíos y el Peso debe ser un número");
        }

        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No hay ningún periodo activo');
        }

        // Buscar o crear la competencia
        $competencia = Competencia::firstOrCreate(
            ['nombre' => trim($row['competencia'])],
            ['descripcion' => trim($row['competencia'])]
        );

        // Buscar el grupo por código
        $grupo = Grupo::where('codigo', $row['grupo'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if (!$grupo) {
            throw new \Exception("No se encontró el grupo con código '{$row['grupo']}' en el periodo actual");
        }

        // Buscar el rol por código
        $rol = Rol::where('codigo', $row['rol'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if (!$rol) {
            throw new \Exception("No se encontró el rol con código '{$row['rol']}' en el periodo actual");
        }

        // Verificar si ya existe un indicador con la misma combinación en este periodo
        $existingIndicador = Indicador::where('competencia_id', $competencia->id)
            ->where('grupo_id', $grupo->id)
            ->where('rol_id', $rol->id)
            ->where('periodo_id', $periodo->id)
            ->first();

        if ($existingIndicador) {
            throw new \Exception("Ya existe un indicador para la competencia '{$row['competencia']}' en el grupo '{$row['grupo']}' y rol '{$row['rol']}' en este periodo");
        }

        return new Indicador([
            'competencia_id' => $competencia->id,
            'grupo_id' => $grupo->id,
            'rol_id' => $rol->id,
            'periodo_id' => $periodo->id,
            'peso' => $row['peso'],
        ]);
    }
}
