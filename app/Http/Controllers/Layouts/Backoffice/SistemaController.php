<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class SistemaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    public function index(Request $request)
    {
    
        $request->user()->authorizeRoles($request->path());
        $sistema = DB::table('sistema')
            ->get();
        return view('layouts/backoffice/sistema/index',[
            'sistema' => $sistema
        ]);
    }
  
    public function create(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
    }
  
    public function store(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
    }

    public function show(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
        if($id == 'show-seleccionarimagenlogin'){
            $imagenlogin = DB::table('sistemaimagenlogin')
                ->where('idsistema',$request->input('idsistema'))
                ->orderBy('sistemaimagenlogin.id','desc')
                ->get();            
            $html = '<div class="row">';
            foreach($imagenlogin as $value){
                $html = $html.'<div class="col-sm-4" id="cont-imgimagengalery'.$value->id.'">
                <div style="
                    width: 100%;
                    height: 195px;
                    background-image: url('.url('public/admin/sistema/'.$value->imagen).');
                    background-repeat: no-repeat;
                    background-size: cover;
                    background-position: center;
                    background-color: #31353d;
                    margin-bottom: 5px;">
                    <a href="javascript:;" onclick="removeimagensistema('.$value->id.')" style="
                            left: 10px;
                            top: 10px;
                            font-size: 18px;
                            background-color: #c12e2e;
                            padding: 2px;
                            padding-left: 9px;
                            padding-right: 9px;
                            border-radius: 15px;
                            color: #fff;
                            font-weight: bold;
                            cursor: pointer;
                            position: absolute;
                            z-index: 10;
                        }">x</a>
                    </div></div>';
            }
            $html = $html.'</div>';
            return $html;
        }
    }
  
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
        $sistema = DB::table('sistema')->first();
        return view('layouts/backoffice/sistema/edit',[
            'sistema' => $sistema
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view')=='editar') {
            $rules = [
              'nombre' => 'required'
            ];
            $messages = [
              'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);          
            $sistema = DB::table('sistema')->whereId($id)->first();
            $imagenicono = uploadfile($sistema->imagenicono,$request->input('imageniconoant'),$request->file('imagenicono'),'/public/admin/sistema/');
            $imagenlogo = uploadfile($sistema->imagenlogo,$request->input('imagenlogoant'),$request->file('imagenlogo'),'/public/admin/sistema/');
            DB::table('sistema')->whereId($id)->update([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion')!=''?$request->input('descripcion'):'',
                'slogan' => $request->input('slogan')!=''?$request->input('slogan'):'',
                'numerotelefono' => $request->input('numerotelefono')!=''?$request->input('numerotelefono'):'',
                'correo' => $request->input('correo')!=''?$request->input('correo'):'',
                'paginaweb' => $request->input('paginaweb')!=''?$request->input('paginaweb'):'',
                'imagenlogo' => $imagenlogo,
                'imagenicono' => $imagenicono
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view')=='registrarimagen'){
            if($request->file('imagenlogin')==null){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe seleccionar una imagen!!.'
                ]);
            }
            $imagenlogin = uploadfile('','',$request->file('imagenlogin'),'/public/admin/sistema/');
            DB::table('sistemaimagenlogin')->insert([
                'imagen' => $imagenlogin,
                'idsistema' => $id
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
        if($request->input('view') == 'eliminarimagen') {
            $sistemaimagenlogin = DB::table('sistemaimagenlogin')->whereId($id)->first();
            if($sistemaimagenlogin!=''){
                uploadfile_eliminar($sistemaimagenlogin->imagen,'/public/admin/sistema/');
            }
            DB::table('sistemaimagenlogin')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
