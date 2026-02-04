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
        'sexo',
        'sede',
        'fecha_nacimiento',
        'celular',
        'estado',
        'correo',
        'fin_habilitacion'
    ];

    protected $appends = ['estado'];

    public function getestadoAttribute()
    {
        // Si no tiene fecha de fin, mantenemos lo que diga la DB
        if (!$this->fin_habilitacion) {
            return 'Inhabilitado';
        }

        // Comparamos HOY contra la fecha de fin (sin importar las horas)
        $hoy = \Carbon\Carbon::now()->startOfDay();
        $vencimiento = \Carbon\Carbon::parse($this->fin_habilitacion)->startOfDay();

        return ($hoy->lte($vencimiento)) ? 'Habilitado' : 'Inhabilitado';
    }

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

    public function getSexoAttribute($value)
    {
        if ($value === 'F') {
            return 'Femenino';
        }
        return ($value === 'M') ? 'Masculino' : '';
    }

}
