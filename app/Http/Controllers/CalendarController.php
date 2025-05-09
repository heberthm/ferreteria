<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Response;

use App\Models\Events;



class CalendarController extends Controller
{

  public function index()
  {
    if (request()->ajax()) {

      $start = (!empty($_GET["start"])) ? ($_GET["start"]) : ('');

      $end =   (!empty($_GET["end"])) ? ($_GET["end"]) : ('');

      $data = Events::whereDate('start', '>=', $start)->whereDate('end', '<=', $end)->get();

      return Response::json($data);
    }
    return view('citas_medicas');
  }



  /* ============================= */
  /* GUARDAR DATOS DE EVENTOS  */
  /* ============================= */

  public function create(Request $request)

  {


    $validatedData = $request->validate([

      'servicios2'   =>    'required|max:90',

      'cliente'      =>    'required|max:35',

      'telefono'     =>    'required|max:25',

      'medico'       =>    'required|max:45',

      'color'        =>    'required|max:25'
      
    ]);



    $insertArr = [

      'title' => $request->servicios2,

      'start' => $request->start,

      'end' => $request->end,

      'cliente' => $request->cliente,

      'telefono' => $request->telefono,

      'userId' => $request->userId,

      'descripcion' => $request->descripcion,

      'medico' => $request->medico,

      'color' => $request->color

    ];

    $event = Events::insert($insertArr);

    return Response::json($event);

  }



  /* ============================================ */
  /* EDITAR DATOS DE EVENTOS - CAMBIO DE HORA */
  /* ============================================== */


  public function update(Request $request)

  {
    $where = array('id' => $request->id);

    $updateArr = [

      'title' => $request->title,

      'start' => $request->start,

      'end' => $request->end,
    ];

    $event  = Events::where($where)->update($updateArr);

    return Response::json($event);

  }



  /* =============================================== */
  /* ACTUALIZAR DATOS DE EVENTOS - VENTANA MODAL  */
  /* ================================================= */

  public function update_event(Request $request)

  {

    $id = array('id' => $request->id);

    $updateArray = [

      'title' => $request->servicios,

      'cliente' => $request->cliente,

      'telefono' => $request->telefono,

      'userId' => $request->userId,

      'descripcion' => $request->descripcion,

      'medico' => $request->medico,

      'color' => $request->color2,

      'start' => $request->hora_ini,

      'end' => $request->hora_fin,
    ];

    $event  = Events::where($id)->update($updateArray);

    return Response::json($event);

  }




  /* ============================= */
  /* ELIMINAR DATOS DE EVENTOS  */
  /* ============================= */

  public function destroy(Request $request)

  {

    $event = Events::where('id', $request->id)->delete();

    return Response::json($event);

  }
  
}
