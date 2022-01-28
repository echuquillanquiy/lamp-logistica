<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class SeguridadIpsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
      
        $seguridadips = DB::table('seguridadips')
            ->orderBy('seguridadips.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/seguridadips/index',[
            'seguridadips' => $seguridadips
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
      
        return view('layouts/backoffice/seguridadips/create');
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
                'nombre' => 'required',
                'ip'     => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'ip.required'     => 'El "Ip" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('seguridadips')->insert([
                'nombre' => $request->input('nombre'),
                'ip'     => $request->input('ip'),
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
      
        $seguridadips = DB::table('seguridadips')->whereId($id)->first();
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/seguridadips/edit',[
                'seguridadips' => $seguridadips
            ]);
        }elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/seguridadips/delete',[
                'seguridadips' => $seguridadips,
            ]);
        }elseif($request->input('view') == 'detalle') {
            return view('layouts/backoffice/seguridadips/detalle',[
                'seguridadips' => $seguridadips,
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
                'ip'     => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'ip.required'     => 'El "Ip" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('seguridadips')->whereId($id)->update([
                 'nombre' => $request->input('nombre'),
                 'ip'     => $request->input('ip'),
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
            DB::table('seguridadips')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
