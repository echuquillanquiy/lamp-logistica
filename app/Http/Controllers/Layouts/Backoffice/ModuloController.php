<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ModuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$request->user()->authorizeRoles($request->path());
      /**INSERTARA STOCK PRODUCTOS (GINO) */
//         $archivo     = file_get_contents('public/productos_actualizados_2.json');
  
//         $archivojson = json_decode($archivo);

//         $idmopro = DB::table('productomovimiento')->insertGetId([
//           'fecharegistro' => Carbon::now(),
//           'fecharecepcion' => Carbon::now(),
//           'codigo' => 340,
//           'motivo' => '',
//           'idtienda' => 1,
//           'idusers' => 1,
//           'idestadomovimiento' => 2,
//           'idestado' => 2,
//         ]);
//         foreach ($archivojson as $value) {
//           $producto = DB::table('producto')->where('codigoimpresion', $value->codigo)->first();
      
//           DB::table('productomovimientodetalle')->insert([
//             'motivo' => '',
//             'cantidad' => $value->cantidad,
//             'idunidadmedida' => 1,
//             'idproductomovimiento' => $idmopro,
//             'idproducto' => $producto->id,
//           ]);
//         }
//         dd($archivojson);    
        $where = [];
        $where[] = ['modulo.nombre','LIKE','%'.$request->input('modulonombre').'%'];
        $where[] = ['modulo.idmodulo',0];
      
        $modulos = DB::table('modulo')
            ->where($where)
            ->orderBy('orden','asc')
            ->get();
      
        return view('layouts/backoffice/modulo/index',[
            'modulos' => $modulos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //$request->user()->authorizeRoles($request->path());

        if($request->input('view')=='registrar'){
            return view('layouts/backoffice/modulo/create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$request->user()->authorizeRoles($request->path());

        if($request->input('view')=='create') {
          
            $rules = [
				        'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
			      ];
			      $messages = [
				        'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
			      ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'vista' => '',
                'controlador' => '',
                'idmodulo' => 0,
                'idestado' => $request->input('idestado'),
			]);
          
            return response()->json([
				'resultado' => 'CORRECTO',
				'mensaje' => 'Se ha registrado correctamente.'
			]);
        }elseif($request->input('view')=='createsubmodulo') {
          
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }elseif($request->input('view')=='createsubsubmodulo') {
          
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }elseif($request->input('view')=='createsistemamodulo') {
          
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //$request->user()->authorizeRoles($request->path());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //$request->user()->authorizeRoles($request->path());

        if($request->input('view')=='editar'){
            $modulos = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/edit',[
                'modulo' => $modulos
            ]);
        }elseif($request->input('view')=='registrarsubmodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/createsubmodulo',[
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='registrarsubsubmodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/createsubsubmodulo',[
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='registrarsistemamodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/createsistemamodulo',[
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='editarsubmodulo'){
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/editsubmodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='editarsubsubmodulo'){
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/editsubsubmodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='editarsistemamodulo'){
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/editsistemamodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
        }else if ($request->input('view')=='eliminar') {
            $modulos = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/delete',[
                'modulo' => $modulos
            ]);
        }else if ($request->input('view')=='eliminarsubmodulo') {
            $modulos = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/deletesubmodulo',[
                'modulo' => $modulos
            ]);
        }else if ($request->input('view')=='eliminarsubsubmodulo') {
            $modulos = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/deletesubsubmodulo',[
                'modulo' => $modulos
            ]);
        }else if ($request->input('view')=='eliminarsistemamodulo') {
            $modulos = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/deletesistemamodulo',[
                'modulo' => $modulos
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$request->user()->authorizeRoles($request->path());

        if($request->input('view')=='edit') {
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->whereId($id)->update([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view')=='editsubmodulo') {
            $rules = [
                'idmodulo' => 'required',
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'idmodulo.required' => 'El "Módulo" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->whereId($id)->update([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view')=='editsubsubmodulo') {
            $rules = [
                'idmodulo' => 'required',
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'idmodulo.required' => 'El "Módulo" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->whereId($id)->update([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view')=='editsistemamodulo') {
            $rules = [
                'idmodulo' => 'required',
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'idmodulo.required' => 'El "Módulo" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->whereId($id)->update([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //$request->user()->authorizeRoles($request->path());
        
        if($request->input('view')=='deletemodulo'){
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='deletesubmodulo'){
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='deletesubsubmodulo'){
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='deletesistemamodulo'){
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
            
    }
}
