<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ProductocatalogoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $request->user()->authorizeRoles($request->path());     
      return view('layouts/backoffice/productocatalogo/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
        if($request->input('view') == 'registrar'){        
            return view('layouts/backoffice/productocatalogo/create');
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
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view') == 'registrar') {
            $rules = [
               // 'idproductomarca'         => 'required',
            ];
            $messages = [
               // 'idproductomarca.required'=> 'La "Marca de Producto" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);        
          
             $id =  DB::table('productocatalogo')->insertGetId([
                'item'                  => $request->input('item')!=''?$request->input('item'):'',
                'pumptype'              => $request->input('pumptype')!=''?$request->input('pumptype'):'',
                'codigobomba'           => $request->input('codigobomba')!=''?$request->input('codigobomba'):'',
                'codigoalternativo'     => $request->input('codigoalternativo')!=''?$request->input('codigoalternativo'):'',
                'elemento'              => $request->input('elemento')!=''?$request->input('elemento'):'',
                'compatibleelemento'    => $request->input('compatibleelemento')!=''?$request->input('compatibleelemento'):'',
                'stampadoelemento'      => $request->input('stampadoelemento')!=''?$request->input('stampadoelemento'):'',
                'diametroelemento'     => $request->input('diametroelemento')!=''?$request->input('diametroelemento'):'',
                'valvula'               => $request->input('valvula')!=''?$request->input('valvula'):'',
                'motor'                 => $request->input('motor')!=''?$request->input('motor'):'',
                'feedpump'              => $request->input('feedpump')!=''?$request->input('feedpump'):'',
                'tipodecilindro'        => $request->input('tipodecilindro')!=''?$request->input('tipodecilindro'):'',
                'powerhp'               => $request->input('powerhp')!=''?$request->input('powerhp'):'',
                'variador'              => $request->input('variador')!=''?$request->input('variador'):'',
                'gobernadorbomba'       => $request->input('gobernadorbomba')!=''?$request->input('gobernadorbomba'):'',
                'oem'                   => $request->input('oem')!=''?$request->input('oem'):'',
                'rpm'                   => $request->input('rpm')!=''?$request->input('rpm'):'',
                'marca'                 => $request->input('marca')!=''?$request->input('marca'):'',
               // 'idproductomarca'       => $request->input('idproductomarca'),
            ]); 
          
            load_json_productoscatalogo();
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
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
     /*   if($id == 'listar_productomarca'){
            $productos = DB::table('productomarca')
                ->where('productomarca.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'productomarca.id as id',
                   DB::raw('CONCAT(productomarca.nombre) as text')
                )
                ->orderBy('productomarca.nombre','asc')
                ->get();
            return $productos;
        }*/
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
        $productocatalogo = DB::table('productocatalogo')
         // ->join('productomarca','productomarca.id','productocatalogo.idproductomarca')
          ->where('productocatalogo.id',$id)
          ->select(
             'productocatalogo.*',
            //  'productomarca.nombre as productomarca'
            )
          ->first();
      
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/productocatalogo/edit',[
                'productocatalogo' => $productocatalogo,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/productocatalogo/delete',[
                'productocatalogo' => $productocatalogo
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
    public function update(Request $request, $idproductocatalogo)
    {
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view') == 'editar') {
            $rules = [
             //   'idproductomarca' => 'required',
            ];
            $messages = [
               // 'idproductomarca.required' => 'La "Marca de Producto" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            DB::table('productocatalogo')->whereId($idproductocatalogo)->update([
                'item'              => $request->input('item')!=''?$request->input('item'):'',
                'pumptype'          => $request->input('pumptype')!=''?$request->input('pumptype'):'',
                'codigobomba'       => $request->input('codigobomba')!=''?$request->input('codigobomba'):'',
                'codigoalternativo' => $request->input('codigoalternativo')!=''?$request->input('codigoalternativo'):'',
                'elemento'          => $request->input('elemento')!=''?$request->input('elemento'):'',
                'compatibleelemento'=> $request->input('compatibleelemento')!=''?$request->input('compatibleelemento'):'',
                'stampadoelemento'  => $request->input('stampadoelemento')!=''?$request->input('stampadoelemento'):'',
                'diametroelemento' => $request->input('diametroelemento')!=''?$request->input('diametroelemento'):'',
                'valvula'           => $request->input('valvula')!=''?$request->input('valvula'):'',
                'motor'             => $request->input('motor')!=''?$request->input('motor'):'',
                'feedpump'          => $request->input('feedpump')!=''?$request->input('feedpump'):'',
                'tipodecilindro'    => $request->input('tipodecilindro')!=''?$request->input('tipodecilindro'):'',
                'powerhp'           => $request->input('powerhp')!=''?$request->input('powerhp'):'',
                'variador'          => $request->input('variador')!=''?$request->input('variador'):'',
                'gobernadorbomba'   => $request->input('gobernadorbomba')!=''?$request->input('gobernadorbomba'):'',
                'oem'               => $request->input('oem')!=''?$request->input('oem'):'',
                'rpm'               => $request->input('rpm')!=''?$request->input('rpm'):'',
                'marca'               => $request->input('marca')!=''?$request->input('marca'):'',
                //'idproductomarca'   => $request->input('idproductomarca'),
              
            ]);
          
            load_json_productoscatalogo();
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
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
      
        if($request->input('view') == 'eliminar') {
         
                 DB::table('productocatalogo')->whereId($id)->delete();
          
            load_json_productoscatalogo();
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        } 
    }
}
