<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id_administrador';
    public $incrementing = true;
    protected $keyType = 'int';
    
    /**
     * Los atributos que se pueden asignar en masa.
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'user',
        'password',
        'rol',
    ];

    /**
     * Los atributos que deben ocultarse.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function isSuperAdmin(): bool
    {
        // Comprueba si el valor en la columna 'rol'
        // es exactamente 'superadmin'
        return $this->rol === 'superadmin';
    }
}