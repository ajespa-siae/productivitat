<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrupoServicio extends Model
{
    protected $table = 'grupos_servicios';

    protected $fillable = [
        'codigo_grupo',
        'servicio',
    ];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'codigo_grupo', 'codigo');
    }
}
