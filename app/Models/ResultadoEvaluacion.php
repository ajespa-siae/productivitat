<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultadoEvaluacion extends Model
{
    protected $table = 'resultados_evaluacion';

    protected $fillable = [
        'evaluacion_id',
        'indicador_id',
        'puntuacion',
        'comentario',
    ];

    protected $casts = [
        'puntuacion' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!isset($model->puntuacion)) {
                $model->puntuacion = 1;
            }
        });
    }

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function alegacion()
    {
        return $this->hasOne(AlegacionResultado::class, 'resultado_id');
    }

    public function indicador(): BelongsTo
    {
        return $this->belongsTo(Indicador::class);
    }
}
