<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivePeriod;

class Evaluacion extends Model
{
    use HasActivePeriod;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'evaluado_id',
        'evaluador_id',
        'periodo_id',
        'fecha',
        'tipo',
        'finalizada',
    ];

    protected $casts = [
        'fecha' => 'date',
        'finalizada' => 'boolean',
    ];

    public function evaluado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'evaluado_id');
    }

    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'evaluador_id');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(ResultadoEvaluacion::class);
    }

    public static function findOrCreateRegistro($evaluado_id, $evaluador_id, $periodo_id)
    {
        return static::create([
            'evaluado_id' => $evaluado_id,
            'evaluador_id' => $evaluador_id,
            'periodo_id' => $periodo_id,
            'tipo' => 'Registro',
            'finalizada' => false,
        ]);
    }
}
