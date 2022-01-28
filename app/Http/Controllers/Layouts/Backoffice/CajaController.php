<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $cajas = DB::table('caja')
            ->join('tienda','tienda.id','caja.idtienda')
            ->where('caja.nombre','LIKE','%'.$request->input('nombre').'%')
            ->where('tienda.nombre','LIKE','%'.$request->input('tiendanombre').'%')
            ->select(
                'caja.*',
                'tienda.nombre as tiendanombre'
            )
            ->orderBy('caja.nombre','desc')
            ->paginate(10);
      
        $monedasoles = DB::table('moneda')->whereId(1)->first();
        $monedadolares = DB::table('moneda')->whereId(2)->first();
      
        return view('layouts/backoffice/caja/index', [
            'cajas' => $cajas,
            'monedasoles' => $monedasoles,
            'monedadolares' => $monedadolares
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $tiendas = DB::table('tienda')->get();
        return view('layouts/backoffice/caja/create', [
          'tiendas' => $tiendas
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
        $request->user()->authorizeRoles( $request->path() );
      
        $rules = [
          'idtienda' => 'required',
          'nombre' => 'required',
        ];
        
        $messages = [
            'idtienda.required'   => 'El campo "Tienda" es Obligatorio.',
            'nombre.required'   => 'El campo "Nombre" es Obligatorio.',
        ];
      
        $this->validate($request,$rules,$messages);
      
        DB::table('caja')->insert([
          'nombre' => $request->nombre,
          'idtienda' => $request->idtienda,
          'idestado' => 1,
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
        $request->user()->authorizeRoles( $request->path() );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $tiendas = DB::table('tienda')->get();
      
        $caja = DB::table('caja')
            ->join('tienda','tienda.id','caja.idtienda')
            ->where('caja.id',$id)
            ->select(
                'caja.*',
                'tienda.nombre as tiendanombre'
            )
            ->first();
        
        if($request->input('view') == 'editar') { 
          
          return view('layouts/backoffice/caja/edit', [
            'tiendas' => $tiendas,
            'caja' => $caja
          ]);
          
        }else if($request->input('view') == 'eliminar') {
          
          return view('layouts/backoffice/caja/delete', [
            'caja' => $caja
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
    public function update(Request $request, $idcaja)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $rules = [
          'nombre' => 'required',
          'idestado' => 'required',
        ];
        
        $messages = [
            'nombre.required'   => 'El campo "Nombre" es Obligatorio.',
            'idestado.required' => 'El campo "Estado" es Obligatorio.',
        ];
      
        $this->validate($request,$rules,$messages);
      
        DB::table('caja')->whereId($idcaja)->update([
          'nombre' => $request->nombre,
          'idestado' => $request->idestado,
        ]);
      
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha editado correctamente.'
        ]);
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idcaja)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        DB::table('caja')->whereId($idcaja)->delete();
      
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha eliminado correctamente.'
        ]);
    }
}
