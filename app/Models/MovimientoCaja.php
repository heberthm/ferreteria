<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'caja_id',
        'tipo',
        'monto',
        'descripcion',
        'fecha'
    ];
    
    protected $casts = [
        'fecha' => 'datetime',
    ];
    
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}