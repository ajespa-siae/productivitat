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
        'nombre',
        'apellidos',
        'nif',
        'periodo_id',
    ];

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }
}
