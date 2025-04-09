<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolEmpleado extends Model
{
    protected $table = 'roles_empleados';

    protected $fillable = [
        'nif',
        'grupo_id',
        'rol_id',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'nif', 'nif');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }
}
