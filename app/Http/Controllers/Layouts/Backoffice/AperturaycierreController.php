<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class AperturaycierreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $tienda = DB::table('tienda')->whereId(1)->first();
      
        $where = [];
        $where1 = [];
        $where[] = ['tienda.nombre','LIKE','%'.$request->input('tiendanombre').'%'];
        $where[] = ['caja.nombre','LIKE','%'.$request->input('cajanombre').'%'];
        $where[] = ['usersresponsable.nombre','LIKE','%'.$request->input('usersresponsable').'%'];
        $where[] = ['usersrecepcion.nombre','LIKE','%'.$request->input('usersrecepcion').'%'];
        
        if(usersmaster()->id!=1){
          $where[] = ['aperturacierre.idusersresponsable',Auth::user()->id];
        }
      
        $where1[] = ['tienda.nombre','LIKE','%'.$request->input('tiendanombre').'%'];
        $where1[] = ['caja.nombre','LIKE','%'.$request->input('cajanombre').'%'];
        $where1[] = ['usersresponsable.nombre','LIKE','%'.$request->input('usersresponsable').'%'];
        $where1[] = ['usersrecepcion.nombre','LIKE','%'.$request->input('usersrecepcion').'%'];
        
      
        if(usersmaster()->id!=1){
          $where1[] = ['aperturacierre.idusersrecepcion',Auth::user()->id];
        }
  
        $aperturacierres = DB::table('aperturacierre')
            ->join('users as usersresponsable','usersresponsable.id','aperturacierre.idusersresponsable')
            ->join('users as usersrecepcion','usersrecepcion.id','aperturacierre.idusersrecepcion')
            ->join('caja','caja.id','aperturacierre.idcaja')
            ->join('tienda','tienda.id','caja.idtienda')
            ->where($where)
            ->orWhere($where1)
            ->select(
                'aperturacierre.*',
                'usersresponsable.nombre as usersresponsablenombre',
                'usersresponsable.apellidos as usersresponsableapellidos',
                'usersrecepcion.nombre as usersrecepcionnombre',
                'usersrecepcion.apellidos as usersrecepcionapellidos',
                'caja.nombre as cajanombre',
                'tienda.id as idtienda',
                'tienda.nombre as tiendanombre'
            )
            ->orderBy('aperturacierre.id','desc')
            ->paginate(10);
      
        $usersmaster = usersmaster();
      
        $monedasoles = DB::table('moneda')->whereId(1)->first();
        $monedadolares = DB::table('moneda')->whereId(2)->first();
      
        return view('layouts/backoffice/aperturaycierre/index',[
            'tienda' => $tienda,
            'aperturacierres' => $aperturacierres,
            'usersmaster' => $usersmaster,
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
     
        $users = usersmaster();
        $cajas = DB::table('caja')
            ->join('tienda','tienda.id','caja.idtienda')
            //->where('caja.idtienda',$users->idtienda)
            ->select(
                'caja.*',
                'tienda.nombre as tiendanombre'
            )
            ->get();
        $users = DB::table('users')->where('idestado',1)->get();
        $tienda = DB::table('tienda')->whereId(1)->first();
        return view('layouts/backoffice/aperturaycierre/create',[
            'tienda' => $tienda,
            'cajas' => $cajas,
            'users' => $users,
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
        if($request->input('view') == 'registrar') {
            $rules = [
                'idcaja' => 'required',
                'montoasignarsoles' => 'required',
                'montoasignardolares' => 'required',
                'idusersresponsable' => 'required',
                'idusers' => 'required',
            ];
            $messages = [
                'idcaja.required' => 'La "Caja" es Obligatorio.',
                'montoasignarsoles.required' => 'El "Monto a asignar S/." es Obligatorio.',
                'montoasignardolares.required' => 'El "Monto a asignar $" es Obligatorio.',
                'idusersresponsable.required' => 'El "Persona responsable" es Obligatorio.',
                'idusers.required' => 'El "Persona a asignar" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $caja = DB::table('caja')->whereId($request->input('idcaja'))->first();
            
            $caja = aperturacierre($caja->idtienda,$request->input('idusers'));
            if($caja['apertura']!=''){
                if($caja['apertura']->idestado==1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Usuario ya tiene una apertura de CAJA APERTURA EN PROCESO!!.'
                    ]);
                }elseif($caja['apertura']->idestado==2){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Usuario ya tiene una apertura de CAJA APERTURA PENDIENTE!!.'
                    ]);
                }elseif($caja['apertura']->idestado==3){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Usuario ya tiene una apertura de CAJA APERTURADA!!.'
                    ]);
                }elseif($caja['apertura']->idestado==4){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Usuario ya tiene un cierre de CAJA PENDIENTE!!.'
                    ]);
                }
            }
          
            $montocierresoles = DB::table('aperturacierre')
                ->where('aperturacierre.idcaja',$request->input('idcaja'))
                ->where('aperturacierre.idestado',5)
                ->sum('montocierresoles');
            $montocierredolares = DB::table('aperturacierre')
                ->where('aperturacierre.idcaja',$request->input('idcaja'))
                ->where('aperturacierre.idestado',5)
                ->sum('montocierredolares');
          
            if($montocierresoles<$request->input('montoasignarsoles')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo Suficiente en Soles, ingrese otro monto porfavor.'
                ]);
            }
            if($montocierredolares<$request->input('montoasignardolares')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo Suficiente en Dolares, ingrese otro monto porfavor.'
                ]);
            }

            DB::table('aperturacierre')->insert([
                'fecharegistro' => Carbon::now(),
                'montoasignarsoles' => $request->input('montoasignarsoles'),
                'montoasignardolares' => $request->input('montoasignardolares'),
                'montocierresoles' => '0.00',
                'montocierredolares' => '0.00',
                'idusersresponsable' => $request->input('idusersresponsable'),
                'idusersrecepcion' => $request->input('idusers'),
                'idcaja' => $request->input('idcaja'),
                'idestado' => 2,
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
    public function show(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
       if($id == 'show-saldoanterior') {
            $monedasoles = DB::table('moneda')->whereId(1)->first();
            $monedadolares = DB::table('moneda')->whereId(2)->first();
            $montocierresoles = DB::table('aperturacierre')
                ->where('aperturacierre.idcaja',$request->input('idcaja'))
                ->where('aperturacierre.idestado',5)
                ->sum('montocierresoles');
            $montocierredolares = DB::table('aperturacierre')
                ->where('aperturacierre.idcaja',$request->input('idcaja'))
                ->where('aperturacierre.idestado',5)
                ->sum('montocierredolares');
            return [
                'saldoactual' => $monedasoles->simbolo.' '.number_format($montocierresoles, 2, '.', '').' - '.$monedadolares->simbolo.' '.number_format($montocierredolares, 2, '.', '')
            ];
       }elseif($id == 'show-listarcliente'){
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
    public function edit(Request $request, $idaperturacierre)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $aperturacierre = DB::table('aperturacierre')
            ->join('users as usersresponsable','usersresponsable.id','aperturacierre.idusersresponsable')
            ->join('users as usersrecepcion','usersrecepcion.id','aperturacierre.idusersrecepcion')
            ->join('caja','caja.id','aperturacierre.idcaja')
            ->where('aperturacierre.id',$idaperturacierre)
            ->select(
                'aperturacierre.*',
                'usersresponsable.nombre as usersresponsablenombre',
                'usersresponsable.apellidos as usersresponsableapellidos',
                'usersrecepcion.identificacion as usersrecepcionidentificacion',
                'usersrecepcion.nombre as usersrecepcionnombre',
                'usersrecepcion.apellidos as usersrecepcionapellidos',
                'caja.nombre as cajanombre',
                'caja.idtienda as idtienda'
            )
            ->first();

        if($request->input('view') == 'editar') {
              
            $cajas = DB::table('caja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->select(
                    'caja.*',
                    'tienda.nombre as tiendanombre'
                )
                ->get();
            $users = DB::table('users')->where('idestado',1)->get();
            return view('layouts/backoffice/aperturaycierre/edit',[
                'cajas' => $cajas,
                'users' => $users,
                'aperturacierre' => $aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'confirmarenvio') {
              
            $cajas = DB::table('caja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->select(
                    'caja.*',
                    'tienda.nombre as tiendanombre'
                )
                ->get();
            $users = DB::table('users')->where('idestado',1)->get();
            return view('layouts/backoffice/aperturaycierre/confirmarenvio',[
                'cajas' => $cajas,
                'users' => $users,
                'aperturacierre' => $aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'anularenvio') {
              
            $cajas = DB::table('caja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->select(
                    'caja.*',
                    'tienda.nombre as tiendanombre'
                )
                ->get();
            $users = DB::table('users')->where('idestado',1)->get();
            return view('layouts/backoffice/aperturaycierre/anularenvio',[
                'cajas' => $cajas,
                'users' => $users,
                'aperturacierre' => $aperturacierre,
            ]);
        }elseif($request->input('view') == 'confirmarrecepcion') {
              
            $cajas = DB::table('caja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->select(
                    'caja.*',
                    'tienda.nombre as tiendanombre'
                )
                ->get();
            $users = DB::table('users')->where('idestado',1)->get();
            return view('layouts/backoffice/aperturaycierre/confirmarrecepcion',[
                'cajas' => $cajas,
                'users' => $users,
                'aperturacierre' => $aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'detalleapertura') {
              
            $cajas = DB::table('caja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->select(
                    'caja.*',
                    'tienda.nombre as tiendanombre'
                )
                ->get();
            $users = DB::table('users')->where('idestado',1)->get();
            return view('layouts/backoffice/aperturaycierre/detalleapertura',[
                'cajas' => $cajas,
                'users' => $users,
                'aperturacierre' => $aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'confirmarcierre') {
            return view('layouts/backoffice/aperturaycierre/confirmarcierre',[
                'aperturacierre' => $aperturacierre
            ]);
          
        }elseif($request->input('view') == 'confirmarrecepcioncierre') {
              
            $cajas = DB::table('caja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->select(
                    'caja.*',
                    'tienda.nombre as tiendanombre'
                )
                ->get();
            return view('layouts/backoffice/aperturaycierre/confirmarrecepcioncierre',[
                'cajas' => $cajas,
                'aperturacierre' => $aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'detallecierre') {
            return view('layouts/backoffice/aperturaycierre/detallecierre',[
                'aperturacierre' => $aperturacierre
            ]);
          
        }elseif($request->input('view') == 'anularenviocierre') {
              
            $cajas = DB::table('caja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->select(
                    'caja.*',
                    'tienda.nombre as tiendanombre'
                )
                ->get();
            return view('layouts/backoffice/aperturaycierre/anularenviocierre',[
                'cajas' => $cajas,
                'aperturacierre' => $aperturacierre,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $cajas = DB::table('caja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->select(
                    'caja.*',
                    'tienda.nombre as tiendanombre'
                )
                ->get();
            $users = DB::table('users')->where('idestado',1)->get();
            return view('layouts/backoffice/aperturaycierre/delete',[
                'cajas' => $cajas,
                'users' => $users,
                'aperturacierre' => $aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'pdfdetalle') {
            return view('layouts/backoffice/aperturaycierre/pdfdetalle',[
                'aperturacierre' => $aperturacierre,
            ]);
        }elseif($request->input('view') == 'pdfdetalle-pdf') {
            $pdf = PDF::loadView('layouts/backoffice/aperturaycierre/pdfdetalle-pdf',[
                'aperturacierre' => $aperturacierre
            ]);
            return $pdf->stream();
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idaperturacierre)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idcaja' => 'required',
                'montoasignarsoles' => 'required',
                'montoasignardolares' => 'required',
                'idusers' => 'required',
            ];
            $messages = [
                'idcaja.required' => 'La "Caja" es Obligatorio.',
                'montoasignarsoles.required' => 'El "Monto a asignar S/." es Obligatorio.',
                'montoasignardolares.required' => 'El "Monto a asignar $" es Obligatorio.',
                'idusers.required' => 'El "Persona a asignar" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $montocierresoles = DB::table('aperturacierre')
                ->where('aperturacierre.idcaja',$request->input('idcaja'))
                ->where('aperturacierre.idestado',5)
                ->sum('montocierresoles');
            $montocierredolares = DB::table('aperturacierre')
                ->where('aperturacierre.idcaja',$request->input('idcaja'))
                ->where('aperturacierre.idestado',5)
                ->sum('montocierredolares');
          
            if($montocierresoles<$request->input('montoasignarsoles')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo Suficiente en Soles, ingrese otro monto porfavor.'
                ]);
            }
            if($montocierredolares<$request->input('montoasignardolares')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo Suficiente en Dolares, ingrese otro monto porfavor.'
                ]);
            }

            DB::table('aperturacierre')->whereId($idaperturacierre)->update([
                'montoasignarsoles' => $request->input('montoasignarsoles'),
                'montoasignardolares' => $request->input('montoasignardolares'),
                'idusersresponsable' => Auth::user()->id,
                'idusersrecepcion' => $request->input('idusers'),
                'idcaja' => $request->input('idcaja'),
                'idestado' => 2
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }elseif($request->input('view') == 'anularenvio') {

            DB::table('aperturacierre')->whereId($idaperturacierre)->update([
                'idestado' => 1
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha rechazado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmarrecepcion') {

            DB::table('aperturacierre')->whereId($idaperturacierre)->update([
                'fechaconfirmacion' => Carbon::now(),
                'idestado' => 3
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmarcierre') {
          
            $efectivosoles = efectivo($idaperturacierre,1);
            $efectivodolares = efectivo($idaperturacierre,2); 

            DB::table('aperturacierre')->whereId($idaperturacierre)->update([
                'fechacierre' => Carbon::now(),
                'montocierresoles' => $efectivosoles['total'],
                'montocierredolares' => $efectivodolares['total'],
                'idestado' => 4
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmarrecepcioncierre') {

            DB::table('aperturacierre')->whereId($idaperturacierre)->update([
                'fechacierreconfirmacion' => Carbon::now(),
                'idestado' => 5
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'anularenviocierre') {

            DB::table('aperturacierre')->whereId($idaperturacierre)->update([
                'idestado' => 3
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha rechazado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idaperturacierre)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            DB::table('aperturacierre')
                ->whereId($idaperturacierre)
                ->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
