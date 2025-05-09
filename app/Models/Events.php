<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title','start','end','cliente','telefono','email','descripcion','medico','color'];



    public function setClienteAttribute($value)
    {
        $this->attributes['cliente'] = strtolower($value);
    }
 
    public function getClienteAttribute($value)
     {
         return ucwords($value);
     }



}


