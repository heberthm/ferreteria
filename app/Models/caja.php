<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class caja extends Model
{
    use HasFactory;

 protected $fillable = [
        'fecha_caja',
        'saldo_inicial',
        'saldo_final',
        'total_ingresos',
        'total_egresos',
        'estado_caja',
        'user_id',
        'observaciones'
 ];

 // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con transacciones (si las tienes)
  //  public function transactions()
  //  {
  //      return $this->hasMany(Transaction::class);
  //  }

    // Método para verificar si la caja está abierta
    public function isOpen()
    {
        return $this->status === 'open';
    }

    // Método para calcular el saldo actual
    public function currentBalance()
    {
        return $this->saldo_inicial + $this->total_ingresos - $this->total_egresos;
    }


}
