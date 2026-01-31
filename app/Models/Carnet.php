<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carnet extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'pago_id',
        'agremiado_id',
        'tipo_tramite',
        'estado_entrega',
        'fecha_entrega',
        'entregado_por', // ¡Para que se guardar el rastro!
    ];

    // Para que se relacione con la tabla pago
    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    // Para que se relacione con la tabla agremiado
    public function agremiado()
    {
        // Relaciona el agremiado_id del carnet con el ID de la tabla agremiados
        return $this->belongsTo(Agremiado::class, 'agremiado_id');
    }
}
