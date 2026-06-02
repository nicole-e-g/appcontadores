<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipanteCongreso extends Model
{
    use HasFactory;
    protected $table = 'participante_congresos';

    protected $fillable = [
        'dni', 'nombres', 'apellidos', 'email', 'celular', 'modalidad'
    ];
}
