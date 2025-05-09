<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    use HasFactory;

    protected $table = 'mascotas';
    
    protected $fillable = [
        'nombre',
        'especie',
        'raza',
        'fecha_nacimiento',
        'sexo',
        'color',
        'peso',
        'cliente_id'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }

    public function vacunas()
    {
        return $this->hasMany(Vacuna::class);
    }
}
