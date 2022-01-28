<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class BancocuentabancariaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
      
        $bancocuentabancarias = DB::table('bancocuentabancaria')
            ->join('banco','banco.id','bancocuentabancaria.idbanco')
            ->where('bancocuentabancaria.numerocuenta','LIKE','%'.$request->input('numerocuenta').'%')
            ->where('bancocuentabancaria.nombre','LIKE','%'.$request->input('nombre').'%')
            ->where('banco.nombre','LIKE','%'.$request->input('banco').'%')
            ->select(
                'bancocuentabancaria.*', 
                'banco.nombre as banco'   
            )
            ->orderBy('bancocuentabancaria.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/bancocuentabancaria/index',[
            'bancocuentabancarias' => $bancocuentabancarias
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
      
        $bancos = DB::table('banco')->get();
      
        return view('layouts/backoffice/bancocuentabancaria/create',[
            'bancos' => $bancos
        ]);
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
                'idbanco' => 'required',
                'nombre' => 'required',
                'numerocuenta' => 'required',
            ];
            $messages = [
                'idbanco.required' => 'El "Banco" es Obligatorio.',
                'nombre.required' => 'El "Nombre de cuenta" es Obligatorio.',
                'numerocuenta.required' => 'El "Número de cuenta" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('bancocuentabancaria')->insert([
                'nombre' => $request->input('nombre'),
                'numerocuenta' => $request->input('numerocuenta'),
                'idbanco' => $request->input('idbanco'),
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
      
        $bancocuentabancaria = DB::table('bancocuentabancaria')->whereId($id)->first();
        if($request->input('view') == 'editar') {
            $bancos = DB::table('banco')->get();
            return view('layouts/backoffice/bancocuentabancaria/edit',[
                'bancocuentabancaria' => $bancocuentabancaria,
                'bancos' => $bancos
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $bancos = DB::table('banco')->get();
            return view('layouts/backoffice/bancocuentabancaria/delete',[
                'bancocuentabancaria' => $bancocuentabancaria,
                'bancos' => $bancos
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
                'idbanco' => 'required',
                'nombre' => 'required',
                'numerocuenta' => 'required',
            ];
            $messages = [
                'idbanco.required' => 'El "Banco" es Obligatorio.',
                'nombre.required' => 'El "Nombre de cuenta" es Obligatorio.',
                'numerocuenta.required' => 'El "Número de cuenta" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('bancocuentabancaria')->whereId($id)->update([
                'nombre' => $request->input('nombre'),
                'numerocuenta' => $request->input('numerocuenta'),
                'idbanco' => $request->input('idbanco'),
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
            DB::table('bancocuentabancaria')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
