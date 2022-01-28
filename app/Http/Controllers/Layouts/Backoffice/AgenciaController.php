<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class AgenciaController extends Controller
{
    public function index(Request $request)
    {
        $where = [];
        $where[] = ['agencia.ruc','LIKE','%'.$request->input('ruc').'%'];
        $where[] = ['agencia.nombrecomercial','LIKE','%'.$request->input('nombrecomercial').'%'];
        $where[] = ['agencia.razonsocial','LIKE','%'.$request->input('razonsocial').'%'];   
        
        $agencias = DB::table('agencia')
            ->where($where)
            ->orderBy('agencia.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/agencia/index',[
            'agencias' => $agencias
        ]);
    }
    public function create(Request $request)
    {
        return view('layouts/backoffice/agencia/create');
    }
    public function store(Request $request)
    {
        if($request->input('view') == 'registrar') {
            $rules = [
                'ruc' => 'required',   
                'nombrecomercial' => 'required',   
                'razonsocial' => 'required',   
                'idestadoempresa' => 'required',    
                'idestado' => 'required',            
            ];
            $messages = [
                'ruc.required' => 'El "RUC" es Obligatorio.',
                'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                'razonsocial.required' => 'La "Razón Comercial" es Obligatorio.',
                'idestadoempresa.required' => 'El "Estado de Empresa" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
  
            $imagen = uploadfile('','',$request->file('imagen'),'/public/admin/agencia/');
            
            if($request->input('idestado')==1){
                DB::table('agencia')->update([
                    'idestado' => 2,
                ]);
            }
          
            DB::table('agencia')->insert([
               'ruc' => $request->input('ruc'),
               'nombrecomercial' => $request->input('nombrecomercial'),
               'razonsocial' => $request->input('razonsocial'),
               'sunat_usuario' => 'DEMO',
               'sunat_clave' => 'DEMO',
               'sunat_certificado' => 'SIN CERTIFICADO',
               'idestadoempresa' => $request->input('idestadoempresa'),
               'idestado' => $request->input('idestado')
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $id)
    {
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

    public function edit(Request $request, $id)
    {
        $agencia = DB::table('agencia')
            ->where('agencia.id',$id)
            ->select(
                'agencia.*'
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/agencia/edit',[
              'agencia' => $agencia
            ]);
        }elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/agencia/delete',[
              'agencia' => $agencia
            ]);
        }
    }

    public function update(Request $request, $id)
    {

        if($request->input('view') == 'editar') {
            $rules = [
                'ruc' => 'required',   
                'nombrecomercial' => 'required',   
                'razonsocial' => 'required',  
                'idestadoempresa' => 'required',     
                'idestado' => 'required',       
                'sunat_usuario' => 'required',      
                'sunat_clave' => 'required',      
                'sunat_certificado' => 'required',                 
            ];
            $messages = [
                'ruc.required' => 'El "RUC" es Obligatorio.',
                'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                'razonsocial.required' => 'La "Razón Comercial" es Obligatorio.',
                'idubigeo.required' => 'La "Ubicación (Ubigeo)" es Obligatorio.',
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'idestadoempresa.required' => 'El "Estado de Empresa" es Obligatorio.',
                'idestado.required' => 'El "Estado de Empresa" es Obligatorio.',
                'sunat_usuario.required' => 'El "Usuario Sol" es Obligatorio.',
                'sunat_clave.required' => 'La "Clave Sol" es Obligatorio.',
                'sunat_certificado.required' => 'La "Ruta de Certificado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
        
            if($request->input('idestadoempresa')==1){
                DB::table('agencia')->update([
                    'idestadoempresa' => 2,
                ]);
            }

            DB::table('agencia')->whereId($id)->update([
                'ruc' => $request->input('ruc'),
                'nombrecomercial' => $request->input('nombrecomercial'),
                'razonsocial' => $request->input('razonsocial'),
                'sunat_usuario' => $request->input('sunat_usuario')!=''?$request->input('sunat_usuario'):'',
                'sunat_clave' => $request->input('sunat_usuario')!=''?$request->input('sunat_clave'):'',
                'sunat_certificado' => $request->input('sunat_usuario')!=''?$request->input('sunat_certificado'):'',
                'idestadoempresa' => $request->input('idestadoempresa'),
                'idestado' => $request->input('idestado'),
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
            
    }


    public function destroy(Request $request, $id)
    {
        if($request->input('view') == 'eliminar') {
            DB::table('agencia')
                ->whereId($id)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
