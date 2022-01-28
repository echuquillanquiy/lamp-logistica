<?php

namespace App\Http\Controllers\Layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use Mail;

class MasterController extends Controller
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
        $users = DB::table('users')->whereId(Auth::user()->id)->first();
        return view('layouts/backoffice/master',[
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->input('view')=="pagousuario"){
            $rules = [
              "email_patrocinador" => "required"
            ];
            $messages = [
              "email_patrocinador.required" => 'El campo Patrocinador es obligatorio'
            ];
            $this->validate($request,$rules,$messages);

            $usuario_patrocinador = DB::table('users')
                ->join('red','red.idusershijo','=','users.id')
                ->where('users.usuario',$request->input('email_patrocinador'))
                ->select('users.*')
                ->first();
            if($usuario_patrocinador==''){
                $usuario_patrocinador = DB::table('users')
                    ->where('usuario',$request->input('email_patrocinador'))
                    ->where('id',1)
                    ->select('users.*')
                    ->first();
                if($usuario_patrocinador==''){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje' => 'El patrocinador no existe, ingrese otro por favor.'
                    ]);
                }
            }

            $plan = DB::table('plan')->whereId($request->input('idplan'))->first();

            $voucher = '';
            if($request->file('imagen')!='') {
                if ($request->file('imagen')->isValid()){                  
                    list($nombre,$ext) = explode(".", $request->file('imagen')->getClientOriginalName());
                    $voucher = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                    $request->file('imagen')->move(getcwd().'/public/backoffice/usuario/'.Auth::user()->id.'/voucher/', $voucher);
                }
            }else{
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje' => 'Debe Subir un voucher.'
                ]);
            }


            $repartir = reparticion_bono(Auth::user()->id);

            if($repartir['cantidadveces']==0){
                $idred = DB::table('red')->insertGetId([
                  'iduserspatrocinador' => $usuario_patrocinador->id,
                  'iduserspadre' => $usuario_patrocinador->id,
                  'idusershijo' => Auth::user()->id,
                  'fecharegistro' => Carbon::now()
                ]);
            }else{
                $red = DB::table('red')->where('idusershijo',Auth::user()->id)->first();
                $idred = $red->id;
            }

            DB::table('planadquirido')->insert([
              'idred' => $idred,
              'fechacompra' => Carbon::now(),
              'idplan' => $request->input('idplan'),
              'costo' => $plan->costo,
              'mespagado' => '',
              'banco' => 'OFICINA',
              'nrocuenta' => '---',
              'voucher' => $voucher
            ]);

            //email consumidor
            $emailuser = DB::table('users')->whereId(Auth::user()->id)->first();
            $para = $emailuser->email;
            $titulo = 'Kayllapi Consumidor';
            $descripcion = 'Pendiente';
            $nombre = $emailuser->nombre;

            Mail::send('app/emailconsumidorpendiente',['nombre' => $nombre], function($msj) use ($para,$titulo,$descripcion){
                $msj->from('ventas@kayllapi.com', $titulo);
                $msj->to($para)->subject($descripcion);
            });
            //fin email consumidor

            //email patrocinador
            $para = $usuario_patrocinador->email;
            $titulo = 'Kayllapi Consumidor';
            $descripcion = 'Pendiente';
            $nombre = $emailuser->nombre;
            Mail::send('app/emailpatrocinadorpendiente',['nombre' => $nombre], function($msj) use ($para,$titulo,$descripcion){
                $msj->from('ventas@kayllapi.com', $titulo);
                $msj->to($para)->subject($descripcion);
            });
            //fin email patrocinador

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }elseif($request->input('view')=="pagotienda"){
            $rules = [
              //"terminosycondiciones" => "required",
              //"idbanco" => "required",
              //"nrocuenta" => "required",
              /*"empresaruc" => "required",
              "empresanombrecomercial" => "required",
              "empresarazonsocial" => "required",
              "empresadireccion" => "required",*/
            ];
            $messages = [
              //"terminosycondiciones.required" => '',
              //"idbanco" => 'El campo Banco es obligatorio',
              //"nrocuenta" => 'El campo Nro de Cuenta es obligatorio',
              /*"empresaruc.required" => 'El campo "RUC" es obligatorio',
              "empresanombrecomercial.required" => 'El campo "Nombre Comercial" es obligatorio',
              "empresarazonsocial.required" => 'El campo "Razón Social" es obligatorio',
              "empresadireccion.required" => 'El campo "Dirección Fiscal" es obligatorio',*/
            ];
            $this->validate($request,$rules,$messages);
          
            if($request->input('terminosycondiciones')=='') {
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje' => 'Los Terminos y Condiciones es Obligatorio.'
                ]);
            }
            /*$imagen = '';
            if($request->file('imagen')!='') {
                if ($request->file('imagen')->isValid()){                  
                    list($nombre,$ext) = explode(".", $request->file('imagen')->getClientOriginalName());
                    $imagen = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                    $request->file('imagen')->move(getcwd().'/public/backoffice/tienda/'.$request->input('idtienda').'/voucherpago/', $imagen);
                }
            }else{
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje' => 'Debe Subir un voucher.'
                ]);
            }*/
          
            $fecha = Carbon::now();
            $ultima_fecha = date("Y-m-d h:m:s",strtotime($fecha."+ 1 year"));
             
            DB::table('pagotienda')->insert([
              'fechapago' => $fecha,
              'fechaconfirmacion' => $fecha,
              'costo' => '0.00',
              'fechainicio' => $fecha,
              'fechafin' => $ultima_fecha,
              /*'banco' => $request->input('idbanco'),
              'nrocuenta' => $request->input('nrocuenta'),
              'voucher' => $imagen,*/
              'banco' => 0,
              'nrocuenta' => '',
              'voucher' => '',
              'idtienda' => $request->input('idtienda'),
              'idestado' => 1
            ]);
          
            /*DB::table('empresa')->insert([
              'ruc' => $request->input('empresaruc'),
              'nombrecomercial' => $request->input('empresanombrecomercial'),
              'razonsocial' => $request->input('empresarazonsocial'),
              'direccion' => $request->input('empresadireccion'),
              'idtienda' => $request->input('idtienda')
            ]);*/

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }elseif($request->input('view') == 'pagoculqi') {
            $rules = [
              "email_patrocinador" => "required"
            ];
            $messages = [
              "email_patrocinador.required" => 'El campo Patrocinador es obligatorio'
            ];
            $this->validate($request,$rules,$messages);

            $usuario_patrocinador = DB::table('users')
                ->join('red','red.idusershijo','=','users.id')
                ->where('users.usuario',$request->input('email_patrocinador'))
                ->select('users.*')
                ->first();
            if($usuario_patrocinador==''){
                $usuario_patrocinador = DB::table('users')
                    ->where('usuario',$request->input('email_patrocinador'))
                    ->where('id',1)
                    ->select('users.*')
                    ->first();
                if($usuario_patrocinador==''){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje' => 'El patrocinador no existe, ingrese otro por favor.'
                    ]);
                }
            }

            $plan = DB::table('plan')->whereId($request->input('idplan'))->first();

            //--- CULQI
            $SECRET_API_KEY = "sk_live_HvB9KaCG7LFEtxzK";
            $culqi = new Culqi(array('api_key' => $SECRET_API_KEY));

            /*$culqi->Charges->create(
                array(
                    "moneda"=> "PEN",
                    "monto"=> 100,
                    "descripcion"=> 'Dale un aire de frescura a tu comunicación con un smartphone.',
                    "pedido"=> time(),
                    "codigo_pais"=> "PE",
                    "ciudad"=> "Lima",  
                    "usuario"=> "71701956",
                    "direccion"=> "Avenida Lima 1232",
                    "telefono"=> 12313123,
                    "nombres"=> "Stephan",
                    "apellidos"=> "Vargas",
                    "email"=> "stephan.vargas@culqi.com",
                    "source_id" => $request->input('token')
                )
            );*/


            //Crear Cliente
            /*$culqi->Customers->create(
              array(
                "address" => "Av. Lima 123",
                "address_city" => "Lima",
                "country_code" => "PE",
                "email" => $request->input('correo'),
                "first_name" => "Juan",
                "last_name" => "Rivera",
                "phone_number" => 999999999
              )
            );*/
            //Crear Cargo

            $users = DB::table('users')
                ->LeftJoin('ubigeo','ubigeo.id','=','users.idubigeo')
                ->where('users.id',Auth::user()->id)
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
          
            //dd($users);

            $culqi->Charges->create(
             array(
                  "amount" => $plan->costo*100,
                  "currency_code" => "PEN",
                  "email" => $users->email,
                  "source_id" => $request->input('token'),

                  "address" => $users->direccion,
                  "address_city" => $users->ubigeonombre,
                  "country_code" => "PE",
                  "first_name" => $users->nombre,
                  "last_name" => $users->apellidos,
                  "phone_number" => $users->numerotelefono
               )
            );
            //--- FIN CULQI


            $repartir = reparticion_bono(Auth::user()->id);
            if($repartir['cantidadveces']==0){
                $idred = DB::table('red')->insertGetId([
                  'iduserspatrocinador' => $usuario_patrocinador->id,
                  'iduserspadre' => $usuario_patrocinador->id,
                  'idusershijo' => Auth::user()->id,
                  'fecharegistro' => Carbon::now()
                ]);
            }else{
                $red = DB::table('red')->where('idusershijo',Auth::user()->id)->first();
                $idred = $red->id;
            }

            DB::table('planadquirido')->insert([
              'idred' => $idred,
              'fechacompra' => Carbon::now(),
              'idplan' => $request->input('idplan'),
              'costo' => $plan->costo,
              'mespagado' => '',
              'banco' => 'CULQI',
              'nrocuenta' => '---',
              'voucher' => $request->input('token')
            ]);


            //email consumidor
            $emailuser = DB::table('users')->whereId(Auth::user()->id)->first();
            $para = $emailuser->email;
            $titulo = 'Kayllapi Consumidor';
            $descripcion = 'Pendiente';
            $nombre = $emailuser->nombre;

            Mail::send('app/emailconsumidorpendiente',['nombre' => $nombre], function($msj) use ($para,$titulo,$descripcion){
                $msj->from('ventas@kayllapi.com', $titulo);
                $msj->to($para)->subject($descripcion);
            });
            //fin email consumidor

            //email patrocinador
            $para = $usuario_patrocinador->email;
            $titulo = 'Kayllapi Consumidor';
            $descripcion = 'Pendiente';
            $nombre = $emailuser->nombre;
            Mail::send('app/emailpatrocinadorpendiente',['nombre' => $nombre], function($msj) use ($para,$titulo,$descripcion){
                $msj->from('ventas@kayllapi.com', $titulo);
                $msj->to($para)->subject($descripcion);
            });
            //fin email patrocinador

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
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
        if($id=="showplan"){
          
          $planadquirido = planadquirido(Auth::user()->id);
          $mesapagar = '';
          $mesapagaractual = '';
          if($planadquirido['data']!=''){
              setlocale(LC_TIME, "spanish");
              $mesapagar = ucfirst(strftime("%B de %Y",strtotime(date($planadquirido['data']->mespagado)."+ 1 month")));
              $mesapagaractual = date("Y-m",strtotime($planadquirido['data']->mespagado."+ 1 month"));
          }
          $plan = DB::table('plan')->whereId($request->input('id'))->first();
    
          $planestado = 0;
          $plannombre = '';
          $planmonto = 0;
          $plandescripcion = '';
          if($plan!=''){
              if($plan->id != 1){
                  $planestado = 1;
              }
              if($plan->id==2){
                  $plandescripcion = '1 Capacitación + Certificación';
              }elseif($plan->id==3){
                  $plandescripcion = '2 Capacitaciones + Certificaciones';
              }elseif($plan->id==4){
                  $plandescripcion = '6 Capacitaciones + Certificaciones';
              }
              $planmonto = $plan->costo;
              $plannombre = $plan->nombre;
              $plan = 'Plan '.$plan->nombre.' - S/. <b>'.$plan->costo.'</b>';
          }
          return array(
            'plan' => $plan,
            'planmonto' => $planmonto,
            'plannombre' => 'Plan '.$plannombre,
            'plandescripcion' => $plandescripcion,
            'planestado' => $planestado,
            'planadquirido' => $planadquirido,
            'mesapagar' => $mesapagar,
            'mesapagaractual' => $mesapagaractual
          );
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
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
        //
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
        //
    }
}
