<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacuna extends Model
{
    use HasFactory;

    protected $table = 'vacunas';
    
    protected $fillable = [
        'nombre',
        'fecha_aplicacion',
        'fecha_proxima',
        'lote',
        'observaciones',
        'mascota_id',
        'veterinario_id'
    ];

    protected $casts = [
        'fecha_aplicacion' => 'date',
        'fecha_proxima' => 'date',
    ];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(Veterinario::class);
    }
}
