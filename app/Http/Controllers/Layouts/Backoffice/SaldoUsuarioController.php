<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class SaldoUsuarioController extends Controller
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
        $request->user()->authorizeRoles( $request->path() );
      
        $saldousuarios = DB::table('userssaldo')
          ->join('users', 'users.id', 'userssaldo.idusuarioresponsable')
          ->join('moneda', 'moneda.id', 'userssaldo.idmoneda')
//           ->where('tienda.nombre','LIKE','%'.$request->input('searchtienda').'%')
          ->select(
              'userssaldo.*',
              DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.nombre)) as nombre'),
              'moneda.simbolo as monedasimbolo'
          )
          ->orderBy('userssaldo.id','desc')
          ->paginate(10);
      
        return view('layouts/backoffice/saldousuario/index',[
            'saldousuarios' => $saldousuarios,
//             'idusers' => Auth::user()->id
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
      
        $formapago = DB::table('formapago')->get();
        return view('layouts/backoffice/saldousuario/create',[
            'formapago' => $formapago,
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
      
        dd($request->input('idformapago'));
        $rules = [
          'idcliente'    => 'required',
          'saldoagregar' => 'required',
          'motivo'       => 'required',
        ];
        
        $messages = [
          'idcliente.required'    => 'El campo "Cliente" es Obligatorio.',
          'saldoagregar.required' => 'El campo "Saldo Agregar" es Obligatorio.',
          'motivo.required'       => 'El campo "Motivo" es Obligatorio.',
        ];
      
        $this->validate($request,$rules,$messages);
        
        if($request->input('idformapago') == 1){
          
        }
      
        DB::table('saldousers')->insert([
          'fecharegistro'            => Carbon::now(),
          'fechaconfirmacion'        => Carbon::now(),
          'fechaanulacion'           => Carbon::now(),
          'codigo'                   => '',
          'monto'                    => $request->saldoagregar,
          'motivo'                   => $request->motivo,
          'deposito_numerocuenta'    => '',
          'deposito_fecha'           => '',
          'deposito_hora'            => '',
          'deposito_numerooperacion' => '',
          'cheque_emision'           => '',
          'cheque_vencimiento'       => '',
          'cheque_numero'            => '',
          'banco'                    => '',
          'idformapago'              => '',
          'idtipopago'               => '',
          'idtiposaldousers'         => '',
          'idmoneda'                 => '',
          'idusuario'                => $request->idcliente,
          'idnotadevolucion'         => '',
          'idtienda'                 => '',
          'idusuarioresponsable'     => '',
          'idaperturacierre'         => '',
          'idestado'                 => 1,
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
      
      if ($id == 'show-listarusuario') {
         $usuarios = DB::table('users')
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'users.id as id',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.nombre)) as text')
                )
                ->get();
          
         return $usuarios;
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idsaldousuario)
    {
        $request->user()->authorizeRoles( $request->path() );
      
       $saldousuario = DB::table('userssaldo')
          ->join('users', 'users.id', 'userssaldo.idusuarioresponsable')
          ->where('userssaldo.id', $idsaldousuario)
          ->select(
              'userssaldo.*',
              DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.nombre)) as nombre')
          )
          ->orderBy('userssaldo.id','desc')
          ->first();
      
      if ($request->view == 'editar') {
         return view('layouts/backoffice/saldousuario/edit',[
           'saldousuario' => $saldousuario
         ]);
      }else if ($request->view == 'anular') {
         return view('layouts/backoffice/saldousuario/anular',[
           'saldousuario' => $saldousuario
         ]);
      }else if ($request->view == 'detalle') {
         return view('layouts/backoffice/saldousuario/detalle',[
           'saldousuario' => $saldousuario
         ]);
      }else if ($request->view == 'confirmar') {
         return view('layouts/backoffice/saldousuario/confirmar',[
           'saldousuario' => $saldousuario
         ]);
      }else if ($request->view == 'eliminar') {
         return view('layouts/backoffice/saldousuario/delete',[
           'saldousuario' => $saldousuario
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
    public function update(Request $request, $idsaldousuario)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if ($request->view == 'editar') {
          $rules = [
            'idcliente'    => 'required',
            'saldoagregar' => 'required',
            'motivo'       => 'required',
          ];

          $messages = [
            'idcliente.required'    => 'El campo "Cliente" es Obligatorio.',
            'saldoagregar.required' => 'El campo "Saldo Agregar" es Obligatorio.',
            'motivo.required'       => 'El campo "Motivo" es Obligatorio.',
          ];

          $this->validate($request,$rules,$messages);

          DB::table('saldousers')->whereId($idsaldousuario)->update([
            'monto'             => $request->saldoagregar,
            'motivo'            => $request->motivo,
            'idusuario'         => $request->idcliente,
          ]);

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje' => 'Se ha actualizado correctamente.'
        ]);
      }else if ($request->view == 'confirmar') {
          
        DB::table('saldousers')->whereId($idsaldousuario)->update([
            'fechaconfirmacion' => Carbon::now(),
            'idestado'          => 2,
        ]);

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje' => 'Se ha confirmado correctamente.'
        ]);
        
      }else if ($request->view == 'anular') {
          
        DB::table('saldousers')->whereId($idsaldousuario)->update([
            'fechaanulacion' => Carbon::now(),
            'idestado'       => 3,
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
    public function destroy(Request $request, $idsaldousuario)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        DB::table('saldousers')->whereId($idsaldousuario)->delete();

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje' => 'Se ha eliminado correctamente.'
        ]);
    }
}
