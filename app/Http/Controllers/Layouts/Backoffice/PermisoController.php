<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PermisoController extends Controller
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

        $permisos = DB::table('roles')
            ->orderBy('id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/permiso/index',[
            'permisos' => $permisos
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

        return view('layouts/backoffice/permiso/create');
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

        if($request->input('view')=='registrar') {
            $rules = [
                'nombre' => 'required',
            ];
            $messages = [
                      'nombre.required' => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('roles')->insertGetId([
                'name' => '---',
                'description' => $request->input('nombre')
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

        $permiso = DB::table('roles')->whereId($id)->first();
        if($request->input('view')=='editar'){
            return view('layouts/backoffice/permiso/edit',[
                'permiso' => $permiso
            ]);
        }elseif($request->input('view')=='editarmodulo'){
            $modulos = DB::table('modulo')
                ->where('idmodulo','0')
                ->where('idestado',1)
                ->orderBy('orden','asc')
                ->get();
            
            return view('layouts/backoffice/permiso/editmodulo',[
                'permiso' => $permiso,
                'modulos' => $modulos
            ]);
        }elseif ($request->input('view')=='eliminar') {
            $permiso = DB::table('roles')->whereId($id)->first();
            return view('layouts/backoffice/permiso/delete',[
                'permiso' => $permiso
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

        if($request->input('view')=='editar') {
            $rules = [
                'nombre' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('roles')->whereId($id)->update([
                'description' => $request->input('nombre')
			      ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view')=='editarmodulo') {

            DB::table('rolesmodulo')->where('idroles',$id)->delete();
            $list = explode(',',$request->input('idmodulos'));
            $idmodulos = '';
            for ($i=1; $i < count($list); $i++) { 
                $idmodulos = $idmodulos.$list[$i];
                DB::table('rolesmodulo')->insert([
                    'idroles' => $id,
                    'idmodulo' => $list[$i]
                ]);
            }      

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
        
        if($request->input('view')=='eliminar'){
            DB::table('roles')->whereId($id)->delete();
            DB::table('rolesmodulo')->where('idroles',$id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
            
    }
}
