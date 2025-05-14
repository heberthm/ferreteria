<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        $sales = Producto::with(['nombre', 'precio'])->latest()->paginate(10);
        return view('productos', compact('nombre'));
    }

}
