<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasActivePeriod;
use App\Models\Empleado;
use App\Models\Indicador;
use App\Models\Periodo;

class Rol extends Model
{
    use HasActivePeriod;

    protected $table = 'roles';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
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
