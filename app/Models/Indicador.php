<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivePeriod;

class Indicador extends Model
{
    use HasActivePeriod;

    protected $table = 'indicadores';

    protected $fillable = [
        'nombre',
        'descripcion',
        'competencia_id',
        'grupo_id',
        'rol_id',
        'sentido',
        'valor_minimo',
        'valor_maximo',
        'periodo_id',
    ];

    protected $casts = [
        'valor_minimo' => 'integer',
        'valor_maximo' => 'integer',
    ];

    public function competencia(): BelongsTo
    {
        return $this->belongsTo(Competencia::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }
}
