<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';
    
    protected $fillable = [
        'fecha_hora',
        'motivo',
        'diagnostico',
        'tratamiento',
        'observaciones',
        'mascota_id',
        'veterinario_id'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(Veterinario::class);
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class);
    }
}
