<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use Hash;

class InicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$tienda = DB::table('tienda')->whereId($idtienda)->first();
     
        return view('layouts/backoffice/inicio/index',[
            //'tienda' => $tienda
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($id == 'cambiar_permiso'){
            DB::table('role_user')
                ->where('user_id',Auth::user()->id)
                ->update([
								    'sesion' => 0
						    ]);
            DB::table('role_user')
                ->whereId($request->input('idrole_user'))
                ->update([
								    'sesion' => 1
						    ]);
            return redirect('backoffice/inicio');
        }
        elseif($id == 'show-listarclientes'){
            $usuarios = DB::table('users')
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'users.id as id',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.apellidos)) as text')
                )
                ->get();
          
            return $usuarios;
        }
        elseif($id == 'show-mostrarsaldousuario'){
          
            $monedasoles = DB::table('moneda')->whereId(1)->first();
            $monedadolares = DB::table('moneda')->whereId(2)->first();
          
            $saldousuariosoles = saldousuario($request->input('idcliente'),1);
            $saldousuariodolares = saldousuario($request->input('idcliente'),2);
            return [
                'saldototal' => $monedasoles->simbolo.$saldousuariosoles['total'].' - '.$monedadolares->simbolo.$saldousuariodolares['total']
            ];
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
        $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
        if($request->input('view')=='editperfil'){
            $ubigeos = DB::table('ubigeo')->get();
            return view('layouts/backoffice/inicio/editperfil',[
              'ubigeos' => $ubigeos,
              'usuario' => $usuario
            ]);
        }elseif($request->input('view')=='editcambiarclave'){
            $ubigeos = DB::table('ubigeo')->get();
            return view('layouts/backoffice/inicio/editpassword',[
              'ubigeos' => $ubigeos,
              'usuario' => $usuario
            ]);
        }elseif($request->input('view')=='cerrarsesiones'){
            Auth::logoutOtherDevices(usersmaster()->clave);
            DB::table('users')->whereId(usersmaster()->id)->update([
                'logincount' => 1
            ]);
            return redirect('/backoffice/inicio');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idsuario)
    {
        if($request->input('view')=='editperfil') {
            $rules = [
                    //'identificacion'    => 'required|numeric|digits:8',
                    'nombre'    => 'required',
                    'apellidos'    => 'required',
                    'idubigeo'    => 'required',
                    'direccion'    => 'required'
						];
						$messages = [
                    'identificacion.required'   => 'El "DNI" es Obligatorio.',
                    'identificacion.numeric'   => 'El "DNI" debe ser Númerico.',
                    'identificacion.digits'   => 'El "DNI" debe ser de 8 Digitos.',
                    'nombre.required'   => 'El "Nombre" es Obligatorio.',
                    'apellidos.required'   => 'El "Apellidos" es Obligatorio.',
                    'idubigeo.required'    => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'direccion.required'    => 'La "Dirección" es Obligatorio.',
						];
						$this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')->whereId($idsuario)->first();
            $imagen = uploadfile($usuario->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/admin/perfil/');

            DB::table('users')->whereId($idsuario)->update([
								'nombre' => $request->input('nombre'),
                'apellidos' => $request->input('apellidos'),
                //'identificacion' => $request->input('identificacion')!=null?$request->input('identificacion'):'',
                'email' => $request->input('email')!=null?$request->input('email'):'',
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'direccion' => $request->input('direccion')!=null?$request->input('direccion'):'',
                'imagen' => $imagen,
                'idubigeo' => $request->input('idubigeo')
						]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }elseif($request->input('view')=='editpassword') {
            $rules = [
								'antpassword' => 'required',
								'password' => 'required|string|min:3|confirmed',
								'password_confirmation' => 'required|required_with:passwordcsame:password|string|min:3',
						];
						$messages = [
								'antpassword.required' => 'La "Contraseña Actual" es Obligatorio.',
								'password.required' => 'La "Nueva Contraseña" es Obligatorio.',
								'password_confirmation.required' => 'El "Confirmar Nueva Contraseña" es Obligatorio.',
						];
						$this->validate($request,$rules,$messages);
          
            $user = DB::table('users')->whereId(Auth::user()->id)->where('clave',$request->input('antpassword'))->first();
            if($user==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'la "Contraseña Actual" no es correcta.'
                ]);
            }

            DB::table('users')->whereId(Auth::user()->id)->update([
								'clave' => $request->input('password'),
                'password' => Hash::make($request->input('password')),
						]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
