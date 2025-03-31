<?php

namespace App\Models\Traits;

use App\Models\Periodo;

trait HasActivePeriod
{
    protected static function bootHasActivePeriod()
    {
        static::creating(function ($model) {
            if (!$model->periodo_id) {
                $periodo = Periodo::getActivo();
                if (!$periodo) {
                    throw new \Exception('No hay ningún periodo activo. Por favor, active un periodo antes de continuar.');
                }
                $model->periodo_id = $periodo->id;
            }
        });
    }
}
