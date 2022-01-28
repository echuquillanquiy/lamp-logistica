<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ProductounidadmedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
      
        $productounidadmedidas = DB::table('productounidadmedida')
            ->where('productounidadmedida.codigo','LIKE','%'.$request->input('codigo').'%')
            ->where('productounidadmedida.nombre','LIKE','%'.$request->input('nombre').'%')
            ->orderBy('productounidadmedida.nombre','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/productounidadmedida/index',[
            'productounidadmedidas' => $productounidadmedidas
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
      
        return view('layouts/backoffice/productounidadmedida/create');
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
                'nombre' => 'required|unique:productounidadmedida',
                'numero' => 'required',
                'codigo' => 'required'
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'nombre.unique' => 'El "Nombre" ya existe, ingrese otro por favor.',
                'numero.required' => 'El "Número" es Obligatorio.',
                'codigo.required' => 'El "Código" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('productounidadmedida')->insert([
                'nombre' => $request->input('nombre'),
                'numero' => $request->input('numero'),
                'codigo' => $request->input('codigo'),
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
      
        $productounidadmedida = DB::table('productounidadmedida')->whereId($id)->first();
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/productounidadmedida/edit',[
                'productounidadmedida' => $productounidadmedida,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/productounidadmedida/delete',[
                'productounidadmedida' => $productounidadmedida
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
                'numero' => 'required',
                'codigo' => 'required'
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'numero.required' => 'El "Número" es Obligatorio.',
                'codigo.required' => 'El "Código" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            $productounidadmedida = DB::table('productounidadmedida')
                ->where('productounidadmedida.id','<>',$id)
                ->where('productounidadmedida.nombre',$request->input('nombre'))
                ->limit(1)
                ->first();
            if($productounidadmedida!=''){
                $rules = [
                    'nombre' => 'required|unique:productounidadmedida',
                ];
                $messages = [
                    'nombre.unique' => 'El "Nombre" ya existe, ingrese otro por favor.',
                ];
                $this->validate($request,$rules,$messages);
            }

            DB::table('productounidadmedida')->whereId($id)->update([
                'nombre' => $request->input('nombre'),
                'numero' => $request->input('numero'),
                'codigo' => $request->input('codigo'),
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
            DB::table('productounidadmedida')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
