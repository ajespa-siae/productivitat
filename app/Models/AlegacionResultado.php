<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ResultadoEvaluacion;
use App\Models\Empleado;

class AlegacionResultado extends Model
{
    use HasFactory;

    protected $table = 'alegaciones_resultado';

    protected $fillable = [
        'resultado_id',
        'empleado_id',
        'texto_alegacion',
        'fecha_alegacion',
        'estado',
        'respuesta',
        'fecha_respuesta',
        'evaluador_id'
    ];

    protected $casts = [
        'fecha_alegacion' => 'datetime',
        'fecha_respuesta' => 'datetime'
    ];

    public function resultado()
    {
        return $this->belongsTo(ResultadoEvaluacion::class, 'resultado_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function evaluador()
    {
        return $this->belongsTo(Empleado::class, 'evaluador_id');
    }
}
