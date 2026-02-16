<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Curso extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;
    protected $fillable = [
        'nombre_curso',
        'organizador',
        'modalidad',
        'horas_lectivas',
        'fecha_inicio',
        'fecha_fin',
        'ponente_nombres',
        'ponente_especialidad',
        'estado',
        'imagen_path',
        'certificado_path',
        'firma1_path',
        'firma2_path',
    ];
}
