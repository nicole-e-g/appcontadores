<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use HasFactory, SoftDeletes;

    // Campos que el controlador puede llenar automáticamente
    protected $fillable = [
        'agremiado_id',
        'año',
        'mes_inicio',
        'mes_final',
        'comprobante',
        'monto',
        'estado',
        'anulado_por',
        'motivo_anulacion',
        'fecha_anulacion',
        'tipo_pago',
        'fecha_pago',
    ];

    /**
     * Relación: Un pago pertenece a un Agremiado
     */
    public function agremiado()
    {
        // Esto permite hacer $pago->agremiado->nombres
        return $this->belongsTo(Agremiado::class, 'agremiado_id');
    }
    public function getMesNombre($numero)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return $meses[$numero] ?? 'No definido';
    }
}