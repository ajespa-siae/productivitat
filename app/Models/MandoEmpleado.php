<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MandoEmpleado extends Model
{
    protected $table = 'mandos_empleados';

    protected $fillable = [
        'mando_id',
        'empleado_id',
        'periodo_id',
    ];

    public function mando(): BelongsTo
    {
        return $this->belongsTo(Mando::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }
}
