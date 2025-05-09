<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    
    protected $fillable = [

        'title',
        'start',
        'end',
        'cliente',
        'telefono',
        'email',
        'motivo',
        'estado', // 'pendiente', 'confirmada', 'cancelada', 'completada'
        'medico',
        'color'

    
    ];

    /*
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    */

    
    public function setClienteAttribute($value)
    {
        $this->attributes['cliente'] = strtolower($value);
    }
 
    public function getClienteAttribute($value)
     {
         return ucwords($value);
     }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(Veterinario::class);
    }
}