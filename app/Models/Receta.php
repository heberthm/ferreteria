<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $table = 'recetas';
    
    protected $fillable = [
        'fecha',
        'indicaciones',
        'consulta_id'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }

    public function medicamentos()
    {
        return $this->belongsToMany(Medicamento::class)
                    ->withPivot(['dosis', 'frecuencia', 'duracion'])
                    ->withTimestamps();
    }
}
