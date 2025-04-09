<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'nombre',
        'apellidos',
        'nif',
    ];

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'roles_empleados')
                    ->withPivot('rol_id')
                    ->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'roles_empleados')
                    ->withPivot('grupo_id')
                    ->withTimestamps();
    }

    public function rolesEmpleados(): HasMany
    {
        return $this->hasMany(RolEmpleado::class, 'nif', 'nif');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function mandosEmpleados(): HasMany
    {
        return $this->hasMany(MandoEmpleado::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'nif', 'nif');
    }
}
