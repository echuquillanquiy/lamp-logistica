<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class TiendaController extends Controller
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
        $request->user()->authorizeRoles($request->path());
      
        $tiendas = DB::table('tienda')
          ->where('tienda.nombre','LIKE','%'.$request->input('searchtienda').'%')
          ->select(
              'tienda.*'
          )
          ->orderBy('tienda.id','desc')
          ->paginate(10);
        return view('layouts/backoffice/tienda/index',[
            'tiendas' => $tiendas,
            'idusers' => Auth::user()->id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
        
        return view('layouts/backoffice/tienda/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
       
   
            $rules = [
              'nombre' => 'required',
              'correo' => 'required',
              'numerotelefono' => 'required',
              'idubigeo' => 'required',
              'direccion' => 'required',
            ];
            $messages = [
              'nombre.required' => 'El "Nombre" es Obligatorio.',
              'correo.required' => 'El "Correo Elctrónico" es Obligatorio.',
              'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
              'idubigeo.required' => 'La "Ubicación (Ubigeo)" es Obligatorio.',
              'direccion.required' => 'La "Dirección" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $imagen = uploadfile('','',$request->file('imagen'),'/public/admin/tienda/');
          
            $idtienda = DB::table('tienda')->insertGetId([
                'fecharegistro' => Carbon::now(),
				        'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion')!=''?$request->input('descripcion'):'',
                'correo' => $request->input('correo')!=null?$request->input('correo'):'',
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'direccion' => $request->input('direccion')!=null?$request->input('direccion'):'',
                'referencia' => $request->input('referencia')!=null?$request->input('referencia'):'',
                'paginaweb' => $request->input('paginaweb')!=null?$request->input('paginaweb'):'',
                'imagen' => $imagen,
                'imagenicono' => '',
                'imagenfondo' => '',
                'terminoycondicion' => '',
                'facturador_serie' => 0,
                'facturador_idestado' => 1,
                'idubigeo' => $request->input('idubigeo'),
                'idusers' => Auth::user()->id,
                'idestado' => 1
			      ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje' => 'Se ha registrado correctamente.'
            ]);
     
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
        if($id == 'show-ubigeo'){
            $ubigeos = DB::table('ubigeo')
                ->where('ubigeo.departamento','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.provincia','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.distrito','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'ubigeo.id as id',
                   DB::raw('CONCAT(ubigeo.nombre) as text')
                )
                ->get();
            return $ubigeos;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
        $tienda = DB::table('tienda')
            ->leftJoin('ubigeo','ubigeo.id','tienda.idubigeo')
            ->where('tienda.id',$id)
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre'
            )
            ->first();
      
        if($request->input('view')=='editar'){
            return view('layouts/backoffice/tienda/edit',[
                'tienda' => $tienda,
            ]);
        }elseif($request->input('view')=='eliminar'){
            return view('layouts/backoffice/tienda/delete',[
                'tienda' => $tienda
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
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view')=='editar') {
            $rules = [
              'nombre' => 'required',
              'correo' => 'required',
              'numerotelefono' => 'required',
              'idubigeo' => 'required',
              'direccion' => 'required',
              'facturador_idestado' => 'required',
            ];
            $messages = [
              'nombre.required' => 'El "Nombre" es Obligatorio.',
              'correo.required' => 'El "Correo Elctrónico" es Obligatorio.',
              'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
              'idubigeo.required' => 'La "Ubicación (Ubigeo)" es Obligatorio.',
              'direccion.required' => 'La "Dirección" es Obligatorio.',
              'facturador_idestado.required' => 'El "Estado de SUNAT" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $tienda = DB::table('tienda')->whereId($id)->first();
            $imagen = uploadfile($tienda->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/admin/tienda/');
            $imagenicono = uploadfile($tienda->imagen,$request->input('imageniconoant'),$request->file('imagenicono'),'/public/admin/tienda/');
            $imagenfondo = uploadfile($tienda->imagen,$request->input('imagenfondoant'),$request->file('imagenfondo'),'/public/admin/tienda/');
          
            DB::table('tienda')->whereId($id)->update([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion')!=''?$request->input('descripcion'):'',
                'correo' => $request->input('correo')!=null?$request->input('correo'):'',
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'direccion' => $request->input('direccion')!=null?$request->input('direccion'):'',
                'referencia' => $request->input('referencia')!=null?$request->input('referencia'):'',
                'paginaweb' => $request->input('paginaweb')!=null?$request->input('paginaweb'):'',
                'imagen' => $imagen,
                'imagenicono' => $imagenicono,
                'imagenfondo' => $imagenfondo,
                'claveinterna' => $request->input('claveinterna')!=null?$request->input('claveinterna'):'',
                'facturador_serie' => $request->input('facturador_serie'),
                'facturador_idestado' => $request->input('facturador_idestado'),
                'idubigeo' => $request->input('idubigeo')
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
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view')=='eliminar'){
            $tienda = DB::table('tienda')->whereId($id)->first();
            uploadfile_eliminar($tienda->imagen,'/public/admin/tienda/');
            DB::table('tienda')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
