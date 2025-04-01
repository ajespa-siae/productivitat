<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Mando;
use App\Models\Empleado;
use App\Models\Indicador;
use App\Models\Periodo;
use App\Models\Traits\HasActivePeriod;

class Evaluacion extends Model
{
    use HasActivePeriod;

    protected $table = 'evaluacion';

    protected $fillable = [
        'mando_id',
        'empleado_id',
        'indicador_id',
        'puntuacion',
        'periodo_id',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
    ];

    public function mando(): BelongsTo
    {
        return $this->belongsTo(Mando::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function indicador(): BelongsTo
    {
        return $this->belongsTo(Indicador::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }
}
