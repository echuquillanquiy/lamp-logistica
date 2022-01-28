<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Role;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use Hash;
use Peru\Http\ContextClient;
use Peru\Sunat\{HtmlParser, Ruc, RucParser};
use Peru\Jne\{Dni, DniParser};

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where = [];
        $where[] = ['tipopersona.nombre','LIKE','%'.$request->input('tipo').'%'];
        $where[] = ['users.identificacion','LIKE','%'.$request->input('identificacion').'%'];
        $where[] = ['users.nombre','LIKE','%'.$request->input('cliente').'%'];
        $where[] = ['users.idestado','LIKE','%'.$request->input('estado').'%'];
      
        $where1 = [];
        $where1[] = ['tipopersona.nombre','LIKE','%'.$request->input('tipo').'%'];
        $where1[] = ['users.identificacion','LIKE','%'.$request->input('identificacion').'%'];
        $where1[] = ['users.apellidos','LIKE','%'.$request->input('cliente').'%'];
        $where1[] = ['users.idestado','LIKE','%'.$request->input('estado').'%'];
      
        $usuarios = DB::table('users')
            ->join('tipopersona','tipopersona.id','=','users.idtipopersona')
            ->leftJoin('ubigeo','ubigeo.id','=','users.idubigeo')
            ->where($where)
            ->orWhere($where1)
            ->select(
                'users.*',
                'tipopersona.nombre as tipopersonanombre',
                'ubigeo.codigo as ubigeocodigo',
                'ubigeo.nombre as ubigeonombre'
            )
            ->orderBy('id','desc')
            ->paginate(10);

        return view('layouts/backoffice/usuario/index',[
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $tipopersonas = DB::table('tipopersona')->get();
        return view('layouts/backoffice/usuario/create',[
            'tipopersonas' => $tipopersonas
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
            if($request->input('idtipopersona')==1){
                $rules = [
                    'dni'    => 'required|numeric|digits:8',
                    'nombre'    => 'required',
                    'apellidos'    => 'required',
                    'idubigeo'    => 'required',
                    'direccion'    => 'required'
                ];
                $identificacion = $request->input('dni');
                $nombre = $request->input('nombre');
                $apellidos = $request->input('apellidos');
            }elseif($request->input('idtipopersona')==3){
                $rules = [
                    'dni'    => 'required|numeric',
                    'nombre'    => 'required',
                    'apellidos'    => 'required',
                    'idubigeo'    => 'required',
                    'direccion'    => 'required'
                ];
                $identificacion = $request->input('dni');
                $nombre = $request->input('nombre');
                $apellidos = $request->input('apellidos');
            }else{
                $rules = [
                    'ruc'    => 'required|numeric|digits:11',
                    'nombrecomercial'    => 'required',
                    'razonsocial'    => 'required',
                    'idubigeo'    => 'required',
                    'direccion'    => 'required'
                ];
                $identificacion = $request->input('ruc');
                $nombre = $request->input('nombrecomercial');
                $apellidos = $request->input('razonsocial');
            }
            $messages = [
                    'dni.required'   => 'La "Indentificación" es Obligatorio.',
                    'dni.numeric'   => 'La "Indentificación" debe ser Númerico.',
                    'dni.digits'   => 'La "Indentificación" debe ser de 8 Digitos.',
                    'nombre.required'   => 'El "Nombre" es Obligatorio.',
                    'apellidos.required'   => 'El "Apellidos" es Obligatorio.',
                    'ruc.required'   => 'El "RUC" es Obligatorio.',
                    'ruc.numeric'   => 'El "RUC" debe ser Númerico.',
                    'ruc.digits'   => 'El "RUC" debe ser de 11 Digitos.',
                    'nombrecomercial.required'   => 'El "Nombre Comercial" es Obligatorio.',
                    'razonsocial.required'   => 'El "Razón Social" es Obligatorio.',
                    'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'email.required'    => 'El "Correo Electrónico" es Obligatorio.',
                    'email.email'    => 'El "Correo Electrónico" es Incorrecto.',
                    'idubigeo.required'    => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'direccion.required'    => 'La "Dirección" es Obligatorio.',
                    'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if($identificacion == "00000000" or $identificacion == "00000000000"){  
            }else{
                $usuario = DB::table('users')
                  ->where('identificacion',$identificacion)
                  ->first();
                if($usuario!=''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "DNI/RUC" ya existe, Ingrese Otro por favor.'
                    ]);
                }
            }
            

            $imagen = uploadfile('','',$request->file('imagen'),'/public/admin/perfil/');
 
            $user = User::create([
                'nombre'         => $nombre,
                'apellidos'      => $apellidos!=null?$apellidos:'',
                'identificacion' => $identificacion!=null?$identificacion:'',
                'email'          => $request->input('email')!=null ? $request->input('email') : '',
                'email_verified_at' => Carbon::now(),
                'usuario'        => Carbon::now()->format("Ymdhisu"),
                'clave'          => '123',
                'password'       => Hash::make('123'),
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'direccion'      => $request->input('direccion')!=null?$request->input('direccion'):'',
                'imagen'         => $imagen,
                'idubigeo'       => $request->input('idubigeo')!=null?$request->input('idubigeo'):0,
                'idtipopersona'  => $request->input('idtipopersona'),
                'idestado'       => 2
            ]);
          
            $ubigeo = DB::table('ubigeo')->whereId($request->input('idubigeo'))->first();
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.',
                'cliente'   => $user,
                'ubigeo'   => $ubigeo
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
       if($id == 'show-ubigeo'){
            $ubigeos = DB::table('ubigeo')
                ->where('ubigeo.departamento','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.provincia','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.distrito','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'ubigeo.id as id',
                   DB::raw('CONCAT(ubigeo.nombre) as text')
                )
                ->get();
            return $ubigeos;
        }elseif($id == 'show-validaruc'){
            $consulta_sunat = consulta_sunat($request->input('ruc'));
            return response()->json($consulta_sunat);
        }elseif($id == 'show-dniruc') {
            return consultaDniRuc($request->numeroidentificacion, $request->idtipopersona);            
        }elseif($id == 'showbuscaridentificacion'){
            return consultaDniRuc($request->input('buscar_identificacion'), $request->input('tipo_persona'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idusuario)
    {
        $usuario = DB::table('users')
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->leftJoin('role_user','role_user.user_id','users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->where('users.id', $idusuario)
            ->select(
              'users.*',
              'ubigeo.nombre as ubigeonombre',
              'roles.id as idroles',
              'roles.description as descriptionrole'
            )
            ->first();

        if($request->input('view') == 'editar') {
            $ubigeos = DB::table('ubigeo')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/usuario/edit',[
                'usuario' => $usuario,
                'ubigeos' => $ubigeos,
                'tipopersonas' => $tipopersonas
            ]);
        }elseif($request->input('view')=='permiso'){
            $roles = DB::table('roles')->get();
            $tiendas = DB::table('tienda')->get();
            $role_users = DB::table('role_user')->where('user_id',$usuario->id)->orderBy('id','asc')->get();
            return view('layouts/backoffice/usuario/permiso',[
                'usuario' => $usuario,
                'roles' => $roles,
                'tiendas' => $tiendas,
                'role_users' => $role_users
            ]);  
        }elseif($request->input('view') == 'eliminar') {
            $ubigeos = DB::table('ubigeo')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/usuario/delete',[
                'usuario' => $usuario,
                'ubigeos' => $ubigeos,
                'tipopersonas' => $tipopersonas
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
    public function update(Request $request, $idusuario)
    {
  
        if($request->input('view') == 'editar') {
            if($request->input('idtipopersona')==1){
                $rules = [
                    'nombre'    => 'required',
                    'idubigeo'    => 'required',
                    'direccion'    => 'required'
                ];
                $identificacion = $request->input('dni');
                $nombre = $request->input('nombre');
                $apellidos = $request->input('apellidos');
            }else{
                $rules = [
                    'ruc'    => 'required',
                    'nombrecomercial'    => 'required',
                    'razonsocial'    => 'required',
                    'idubigeo'    => 'required',
                    'direccion'    => 'required'
                ];
                $identificacion = $request->input('ruc');
                $nombre = $request->input('nombrecomercial');
                $apellidos = $request->input('razonsocial');
            }
            $messages = [
                    'dni.required'   => 'El "DNI" es Obligatorio.',
                    'nombre.required'   => 'El "Nombre" es Obligatorio.',
                    'apellidos.required'   => 'El "Apellidos" es Obligatorio.',
                    'ruc.required'   => 'El "RUC" es Obligatorio.',
                    'nombrecomercial.required'   => 'El "Nombre Comercial" es Obligatorio.',
                    'razonsocial.required'   => 'El "Razón Social" es Obligatorio.',
                    'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'email.required'    => 'El "Correo Electrónico" es Obligatorio.',
                    'email.email'    => 'El "Correo Electrónico" es Incorrecto.',
                    'idubigeo.required'    => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'direccion.required'    => 'La "Dirección" es Obligatorio.',
                    'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);


            $usuario = DB::table('users')
                ->where('id','<>',$idusuario)
                ->where('identificacion',$identificacion)
                ->first();
            if($usuario!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "DNI" ya existe, Ingrese Otro por favor.'
                ]);
            }

            $usuario = DB::table('users')->whereId($idusuario)->first();
            $imagen = uploadfile($usuario->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/admin/perfil/');
 
            DB::table('users')->whereId($idusuario)->update([
                'nombre'         => $nombre,
                'apellidos'      => $apellidos!=null?$apellidos:'',
                'identificacion' => $identificacion!=null?$identificacion:'',
                'email'          => $request->input('email')!=null ? $request->input('email') : '',
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'direccion'      => $request->input('direccion')!=null?$request->input('direccion'):'',
                'imagen'         => $imagen,
                'idubigeo'       => $request->input('idubigeo')!='null'?$request->input('idubigeo'):0,
                'idtipopersona'  => $request->input('idtipopersona')
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view')=='editpermiso') {

            $rules = [
                'usuario' => 'required',
                'idestado' => 'required',
                'permisos' => 'required',
            ];
            $messages = [
                'usuario.required' => 'El "Usuario" es Obligatorio.',
                'usuario.unique' => 'El "Usuario" ya existe, ingrese otro.',
            ];
          
            $this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')
                ->where('id','<>',$idusuario)
                ->where('usuario',$request->input('usuario'))
                ->first();
            if($usuario!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario" ya existe, Ingrese Otro por favor.'
                ]);
            }
          
            $permisos = explode('&', $request->input('permisos'));
            for($i = 1;$i <  count($permisos);$i++){
                $item = explode(',', $permisos[$i]);
                if($item[0]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La "Tienda" es Obligatorio.'
                    ]);
                    break;
                }elseif($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Permiso" es Obligatorio.'
                    ]);
                    break;
                }
            } 
          
            if($request->input('password')!=''){
                DB::table('users')->whereId($idusuario)->update([
                    'usuario' => $request->input('usuario'),
                    'clave' => $request->input('password'),
                    'password' => Hash::make($request->input('password')),
                    'idestado' => $request->input('idestado'),
                ]);
            }else{
                DB::table('users')->whereId($idusuario)->update([
                    'usuario' => $request->input('usuario'),
                    'idestado' => $request->input('idestado'),
                ]);
            }
            
            DB::table('role_user')->where('user_id',$idusuario)->delete();
            $permisos = explode('&', $request->input('permisos'));
            
            for($i = 1; $i < count($permisos); $i++){
                $item = explode(',',$permisos[$i]);
                $sesion = 0;
                if($i==1){
                    $sesion = 1;
                }
                DB::table('role_user')->insert([
                  'sesion' => $sesion,
                  'role_id' => $item[1],
                  'user_id' => $idusuario,
                  'idtienda' => $item[0],
                  'created_at' => Carbon::now(),
                  'updated_at' => Carbon::now()
                ]);
            } 
          
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
    public function destroy(Request $request, $idusuario)
    {
        if($request->input('view') == 'eliminar') {
            $usuario = DB::table('users')->whereId($idusuario)->first();
            uploadfile_eliminar($usuario->imagen,'/public/admin/perfil/');
            DB::table('role_user')
                ->where('user_id',$idusuario)
                ->delete();
            DB::table('users')
                ->where('id',$idusuario)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                    'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
