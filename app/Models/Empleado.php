<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivePeriod;

class Empleado extends Model
{
    use HasActivePeriod;

    protected $table = 'empleados';

    protected $fillable = [
        'nombre',
        'apellidos',
        'nif',
        'grupo_id',
        'rol_id',
        'periodo_id',
    ];

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

    public function mandosEmpleados(): HasMany
    {
        return $this->hasMany(MandoEmpleado::class);
    }
}
