<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Empleado;
use App\Models\Mando;
use App\Models\Competencia;
use App\Models\Indicador;
use App\Models\Evaluacion;
use App\Models\MandoEmpleado;

class Periodo extends Model
{
    protected $fillable = [
        'nombre',
        'activo',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public static function getActivo()
    {
        return static::where('activo', true)->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($periodo) {
            if ($periodo->activo) {
                // Desactivar todos los otros periodos
                static::where('id', '!=', $periodo->id)->update(['activo' => false]);
            }
        });
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class);
    }

    public function mandos(): HasMany
    {
        return $this->hasMany(Mando::class);
    }

    public function competencias(): HasMany
    {
        return $this->hasMany(Competencia::class);
    }

    public function indicadores(): HasMany
    {
        return $this->hasMany(Indicador::class);
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
