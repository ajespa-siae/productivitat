<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasActivePeriod;

class Competencia extends Model
{
    use HasActivePeriod;

    protected $table = 'competencias';
    
    protected $fillable = [
        'nombre',
        'periodo_id',
    ];

    public function indicadores(): HasMany
    {
        return $this->hasMany(Indicador::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }
}
