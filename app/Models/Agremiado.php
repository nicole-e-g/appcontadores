<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Agremiado extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'matricula',
        'fecha_matricula',
        'dni',
        'ruc',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'celular',
        'estado',
        'correo',
        'fin_habilitacion'
    ];

    // Si quieres tratar deleted_at como una fecha de Carbon automáticamente:
    protected $casts = [
        'fin_habilitacion' => 'date',
        'deleted_at' => 'datetime',
        'celular' => 'array',
        'correo' => 'array',
    ];

        /**
     * Un agremiado puede tener muchos pagos realizados
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'agremiado_id');
    }
}
