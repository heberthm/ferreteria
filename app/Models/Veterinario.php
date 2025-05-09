<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veterinario extends Model
{
    use HasFactory;

    protected $table = 'veterinarios';
    
    protected $fillable = [
        'nombre',
        'apellido',
        'especialidad',
        'num_colegiado',
        'telefono',
        'email'
    ];

    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }
}