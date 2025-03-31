<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Empleado;
use App\Models\Indicador;
use App\Models\Periodo;
use App\Models\Traits\HasActivePeriod;

class Grupo extends Model
{
    use HasActivePeriod;

    protected $table = 'grupos';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'periodo_id',
    ];

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class);
    }

    public function indicadores(): HasMany
    {
        return $this->hasMany(Indicador::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }
}
