<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivePeriod;

class Mando extends Model
{
    use HasActivePeriod;

    protected $table = 'mandos';

    protected $fillable = [
        'nif',
        'periodo_id',
        'grupo_id',
    ];

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'nif', 'nif');
    }
}
