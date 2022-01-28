<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $where = [];
        $where[] = ['movimiento.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where[] = ['users.nombre','LIKE','%'.$request->input('responsable').'%'];
        $where[] = ['tipomovimiento.nombre','LIKE','%'.$request->input('tipo').'%'];
        $where[] = ['movimiento.concepto','LIKE','%'.$request->input('concepto').'%'];
        $where[] = ['movimiento.monto','LIKE','%'.$request->input('monto').'%'];
       
        $movimientos  = DB::table('movimiento')
            ->join('tipomovimiento','tipomovimiento.id','movimiento.idtipomovimiento')
            ->join('moneda','moneda.id','movimiento.idmoneda')
            ->join('users','users.id','movimiento.idusuario')
            //->where('movimiento.idusuario',Auth::user()->id)
            ->where('movimiento.idtienda',usersmaster()->idtienda)
            ->where($where)
            ->select(
                'movimiento.*',
                'tipomovimiento.nombre as tipomovimientonombre',
                'users.nombre as responsablenombre',
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('movimiento.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/movimiento/index',[
            'movimientos' => $movimientos,
            'idapertura' => aperturacierre(usersmaster()->idtienda,Auth::user()->id)['idapertura']
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
      
        $tipomovimientos = DB::table('tipomovimiento')->get();
        $tipopagos = DB::table('tipopago')->get();
        $monedas = DB::table('moneda')->get();
   
        return view('layouts/backoffice/movimiento/create',[
            'tipomovimientos' => $tipomovimientos,
            'tipopagos' => $tipopagos,
            'monedas' => $monedas,
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
      
   
        if($request->input('view') == 'registrar') {
            $rules = [
                'idtipomovimiento' => 'required',
                'concepto' => 'required',
                'idmoneda' => 'required',
            ];
          
            $messages = [
                'idtipomovimiento.required'   => 'El "Tipo de Movimiento" es Obligatorio.',
                'concepto.required'   => 'El "Concepto" es Obligatorio.',
                'idmoneda.required'   => 'La "Moneda" es Obligatorio.',
            ];
          
            $formapago_validar = formapago_validar('',$request,$rules,$messages);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);

            if($formapago_validar['total']<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto debe ser mayor a 0.00.'
                ]);
            }
          
            $movimiento = DB::table('movimiento')
                ->orderBy('movimiento.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimiento!=''){
                $codigo = $movimiento->codigo+1;
            }
          
            $idmovimiento = DB::table('movimiento')->insertGetId([
                'codigo' => $codigo,
                'fecharegistro'=> Carbon::now(),            
                'monto'=> $formapago_validar['total'],         
                'concepto'=> $request->input('concepto'),
                'idformapago'=> $request->input('idformapago'),
                'idmoneda'=> $request->input('idmoneda'),
                'idtipomovimiento'=> $request->input('idtipomovimiento'),
                'idusuario'=> Auth::user()->id,
                'idaperturacierre'=> 0,
                'idtienda'=> usersmaster()->idtienda,
                'idestado'=> 1
            ]);
          
            DB::table('tipopagodetalle')->where('idmovimiento',$idmovimiento)->delete();
          
            //forma de pago
            formapago_insertar(
                $request,
                'movimiento',
                $idmovimiento
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
       if($id == 'show-listarcliente'){
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
    public function edit(Request $request, $idmovimiento)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $movimiento = DB::table('movimiento')
            ->join('tipomovimiento','tipomovimiento.id','movimiento.idtipomovimiento')
            ->where('movimiento.id',$idmovimiento)
            ->select(
                'movimiento.*',
                'tipomovimiento.nombre as tipomovimientonombre'
            )
            ->first();
        if($request->input('view') == 'editar') {
            $tipomovimientos = DB::table('tipomovimiento')->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/movimiento/edit',[
                'movimiento' => $movimiento,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'confirmar') {
            $tipomovimientos = DB::table('tipomovimiento')->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/movimiento/confirmar',[
                'movimiento' => $movimiento,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'detalle') {
            $tipomovimientos = DB::table('tipomovimiento')->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/movimiento/detalle',[
                'movimiento' => $movimiento,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'anular') {
            $tipomovimientos = DB::table('tipomovimiento')->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/movimiento/anular',[
                'movimiento' => $movimiento,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $tipomovimientos = DB::table('tipomovimiento')->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/movimiento/delete',[
                'movimiento' => $movimiento,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
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
    public function update(Request $request, $idmovimiento)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idtipomovimiento' => 'required',
                'concepto' => 'required',
                'idmoneda' => 'required',
            ];
          
            $messages = [
                'idtipomovimiento.required'   => 'El "Tipo de Movimiento" es Obligatorio.',
                'concepto.required'   => 'El "Concepto" es Obligatorio.',
                'idmoneda.required'   => 'La "Moneda" es Obligatorio.',
            ];
          
            $formapago_validar = formapago_validar('',$request,$rules,$messages);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);

            DB::table('movimiento')->whereId($idmovimiento)->update([         
                'monto'=> $formapago_validar['total'],         
                'concepto'=> $request->input('concepto'),
                'idformapago'=> $request->input('idformapago'),
                'idmoneda'=> $request->input('idmoneda'),
                'idtipomovimiento'=> $request->input('idtipomovimiento'),
                'idusuario'=> Auth::user()->id,
                'idtienda'=> usersmaster()->idtienda
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idmovimiento',$idmovimiento)->delete();
            formapago_insertar(
                $request,
                'movimiento',
                $idmovimiento
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmar') {
          
            $movimiento = DB::table('movimiento')->whereId($idmovimiento)->first();
            $formapago_validar = formapago_validar('',$request,[],[],$movimiento->idtipomovimiento);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);

            DB::table('movimiento')->whereId($idmovimiento)->update([    
                'fechaconfirmacion'=> Carbon::now(),
                'idaperturacierre'=> $formapago_validar['idaperturacierre'],
                'idusuario'=> Auth::user()->id,
                'idtienda'=> usersmaster()->idtienda,
                'idestado'=> 2
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idmovimiento',$idmovimiento)->delete();
            formapago_insertar(
                $request,
                'movimiento',
                $idmovimiento
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'anular') {
            // Apertura de caja
            $aperturacierre = aperturacierre(usersmaster()->idtienda,Auth::user()->id);
            if($aperturacierre['resultado']=='ERROR'){
                return response()->json([
                    'resultado' => $aperturacierre['resultado'],
                    'mensaje'   => $aperturacierre['mensaje']
                ]);
            }
            $idapertura = $aperturacierre['idapertura'];
            // Fin Apertura de caja
          
            $movimiento = DB::table('movimiento')->whereId($idmovimiento)->first();
          
            if($movimiento->idaperturacierre != $idapertura){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede anular.'
                ]);
            }

            DB::table('movimiento')->whereId($idmovimiento)->update([     
                'fechaanulacion'   => Carbon::now(),
                'idestado'         => 3
            ]);
          
            // tipo pago detalle
            DB::table('tipopagodetalle')->where('idmovimiento',$idmovimiento)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 3
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idmovimiento)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            DB::table('tipopagodetalle')->where('idmovimiento',$idmovimiento)->delete();
            DB::table('movimiento')->whereId($idmovimiento)->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
