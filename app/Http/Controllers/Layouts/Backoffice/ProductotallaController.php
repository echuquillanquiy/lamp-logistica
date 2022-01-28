<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ProductotallaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
      
        $productotallas = DB::table('productotalla')
            ->where('productotalla.nombre','LIKE','%'.$request->input('nombre').'%')
            ->orderBy('productotalla.nombre','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/productotalla/index',[
            'productotallas' => $productotallas
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
      
        return view('layouts/backoffice/productotalla/create');
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
                'nombre' => 'required|unique:productotalla',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'nombre.unique' => 'El "Nombre" ya existe, ingrese otro por favor.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('productotalla')->insert([
                'nombre' => $request->input('nombre'),
            ]);
          
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
    public function show($id)
    {
       //
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
      
        $productotalla = DB::table('productotalla')->whereId($id)->first();
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/productotalla/edit',[
                'productotalla' => $productotalla,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/productotalla/delete',[
                'productotalla' => $productotalla
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
      
        if($request->input('view') == 'editar') {
            $rules = [
                'nombre' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $productotalla = DB::table('productotalla')
                ->where('productotalla.id','<>',$id)
                ->where('productotalla.nombre',$request->input('nombre'))
                ->limit(1)
                ->first();
            if($productotalla!=''){
                $rules = [
                    'nombre' => 'required|unique:productotalla',
                ];
                $messages = [
                    'nombre.unique' => 'El "Nombre" ya existe, ingrese otro por favor.',
                ];
                $this->validate($request,$rules,$messages);
            }

            DB::table('productotalla')->whereId($id)->update([
                'nombre' => $request->input('nombre'),
            ]);
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
            DB::table('productotalla')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
