<?php

namespace App\Imports;

use App\Models\Indicador;
use App\Models\Periodo;
use App\Models\Competencia;
use App\Models\Grupo;
use App\Models\Rol;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IndicadoresImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No hay ningún periodo activo');
        }

        $competencia = Competencia::where('nombre', $row['competencia'])
            ->where('periodo_id', $periodo->id)
            ->first();
        if (!$competencia) {
            throw new \Exception("No se encontró la competencia: {$row['competencia']}");
        }

        $grupo = Grupo::where('nombre', $row['grupo'])
            ->where('periodo_id', $periodo->id)
            ->first();
        if (!$grupo) {
            throw new \Exception("No se encontró el grupo: {$row['grupo']}");
        }

        $rol = Rol::where('nombre', $row['rol'])
            ->where('periodo_id', $periodo->id)
            ->first();
        if (!$rol) {
            throw new \Exception("No se encontró el rol: {$row['rol']}");
        }

        if (!in_array($row['sentido'], ['positiu', 'negatiu'])) {
            throw new \Exception("El sentido debe ser 'positiu' o 'negatiu': {$row['sentido']}");
        }

        if (!is_numeric($row['valor_minimo']) || $row['valor_minimo'] < 0 || $row['valor_minimo'] > 10) {
            throw new \Exception("El valor mínimo debe ser un número entre 0 y 10: {$row['valor_minimo']}");
        }

        if (!is_numeric($row['valor_maximo']) || $row['valor_maximo'] < 0 || $row['valor_maximo'] > 10) {
            throw new \Exception("El valor máximo debe ser un número entre 0 y 10: {$row['valor_maximo']}");
        }

        if ($row['valor_minimo'] > $row['valor_maximo']) {
            throw new \Exception("El valor mínimo no puede ser mayor que el valor máximo: {$row['valor_minimo']} > {$row['valor_maximo']}");
        }

        return new Indicador([
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'] ?? null,
            'competencia_id' => $competencia->id,
            'grupo_id' => $grupo->id,
            'rol_id' => $rol->id,
            'sentido' => $row['sentido'],
            'valor_minimo' => $row['valor_minimo'],
            'valor_maximo' => $row['valor_maximo'],
            'periodo_id' => $periodo->id,
        ]);
    }
}
