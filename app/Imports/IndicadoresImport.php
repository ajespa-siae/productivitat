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

        // Mapear las columnas en catalán a las columnas en español
        $row = array_combine(
            array_map(function ($key) {
                return match($key) {
                    'competencia' => 'competencia',
                    'competència' => 'competencia',
                    'grup' => 'grupo',
                    'tipo' => 'tipo',
                    'tipus' => 'tipo',
                    'sentit' => 'sentido',
                    'valor_minim' => 'valor_minimo',
                    'valor_mínim' => 'valor_minimo',
                    'valor_maxim' => 'valor_maximo',
                    'valor_màxim' => 'valor_maximo',
                    default => $key
                };
            }, array_keys($row)),
            array_values($row)
        );

        // Validar que las columnas requeridas existan
        $required_columns = [
            'competencia',  // competència
            'grupo',        // grup
            'rol',         // rol
            'tipo',        // tipus
            'sentido',     // sentit
            'valor_minimo',// valor_mínim
            'valor_maximo' // valor_màxim
        ];
        $column_names = [
            'competencia' => 'Competència',
            'grupo' => 'Grup',
            'rol' => 'Rol',
            'tipo' => 'Tipus',
            'sentido' => 'Sentit',
            'valor_minimo' => 'Valor mínim',
            'valor_maximo' => 'Valor màxim'
        ];
        foreach ($required_columns as $column) {
            if (!isset($row[$column])) {
                Log::error("Columna no encontrada: {$column}");
                throw new \Exception("Columna requerida '".$column_names[$column]."' no trobada a l'arxiu");
            }
        }

        // Validar que los campos no estén vacíos y son del tipo correcto
        if (!isset($row['competencia']) || !is_string($row['competencia']) || trim($row['competencia']) === '' ||
            !isset($row['grupo']) || (string)$row['grupo'] === '' ||
            !isset($row['rol']) || (string)$row['rol'] === '' ||
            !isset($row['tipo']) || (string)$row['tipo'] === '' ||
            !isset($row['sentido']) || (string)$row['sentido'] === '' ||
            !isset($row['valor_minimo']) || !is_numeric($row['valor_minimo']) ||
            !isset($row['valor_maximo']) || !is_numeric($row['valor_maximo'])) {
            Log::error('Valores inválidos:', [
                'competencia' => $row['competencia'] ?? 'no definido',
                'grupo' => $row['grupo'] ?? 'no definido',
                'rol' => $row['rol'] ?? 'no definido',
                'tipo' => $row['tipo'] ?? 'no definido',
                'sentido' => $row['sentido'] ?? 'no definido',
                'valor_minimo' => $row['valor_minimo'] ?? 'no definido',
                'valor_maximo' => $row['valor_maximo'] ?? 'no definido',
                'tipo_competencia' => gettype($row['competencia'] ?? null),
                'tipo_grupo' => gettype($row['grupo'] ?? null),
                'tipo_rol' => gettype($row['rol'] ?? null),
                'tipo_tipo' => gettype($row['tipo'] ?? null),
                'tipo_sentido' => gettype($row['sentido'] ?? null),
                'tipo_valor_minimo' => gettype($row['valor_minimo'] ?? null),
                'tipo_valor_maximo' => gettype($row['valor_maximo'] ?? null)
            ]);
            throw new \Exception("Les columnes Competència, Grup, Rol, Tipus, Sentit, Valor mínim i Valor màxim no poden estar buides. Les valors mínim i màxim han de ser números.");
        }

        // Validar que el tipo sea válido
        $tipo = ucfirst(strtolower(trim($row['tipo'])));
        if (!in_array($tipo, ['Objectiu', 'Subjectiu'])) {
            throw new \Exception("El tipus ha de ser 'Objectiu' o 'Subjectiu'");
        }

        // Validar que el sentido sea válido
        $sentido = ucfirst(strtolower(trim($row['sentido'])));
        if (!in_array($sentido, ['Positiu', 'Negatiu'])) {
            throw new \Exception("El sentit ha de ser 'Positiu' o 'Negatiu'");
        }

        // Validar que valor_minimo sea menor que valor_maximo
        if ($row['valor_minimo'] >= $row['valor_maximo']) {
            throw new \Exception("El valor mínim ha de ser menor que el valor màxim");
        }

        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No existeix un període actiu');
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
            throw new \Exception("No existeix un grup amb codi '{$row['grupo']}' en el període actual");
        }

        // Buscar el rol por código
        $rol = Rol::where('codigo', $row['rol'])
            ->where('periodo_id', $periodo->id)
            ->first();

        if (!$rol) {
            throw new \Exception("No existeix un rol amb codi '{$row['rol']}' en el període actual");
        }

        // Verificar si ya existe un indicador con la misma combinación en este periodo
        $existingIndicador = Indicador::where('competencia_id', $competencia->id)
            ->where('grupo_id', $grupo->id)
            ->where('rol_id', $rol->id)
            ->where('periodo_id', $periodo->id)
            ->first();

        if ($existingIndicador) {
            throw new \Exception("Ja existeix un indicador per a la competència '{$row['competencia']}' en el grup '{$row['grupo']}' i rol '{$row['rol']}' en aquest període");
        }

        return new Indicador([
            'competencia_id' => $competencia->id,
            'grupo_id' => $grupo->id,
            'rol_id' => $rol->id,
            'periodo_id' => $periodo->id,
            'tipo' => $tipo,
            'sentido' => $sentido,
            'valor_minimo' => $row['valor_minimo'],
            'valor_maximo' => $row['valor_maximo'],
        ]);
    }
}
