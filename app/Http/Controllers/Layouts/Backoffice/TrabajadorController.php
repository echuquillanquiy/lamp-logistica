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

class TrabajadorController extends Controller
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
        $where[] = ['users.idestado',1];
      
        $where1 = [];
        $where1[] = ['tipopersona.nombre','LIKE','%'.$request->input('tipo').'%'];
        $where1[] = ['users.identificacion','LIKE','%'.$request->input('identificacion').'%'];
        $where1[] = ['users.apellidos','LIKE','%'.$request->input('cliente').'%'];
        $where1[] = ['users.idestado',1];
      
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

        return view('layouts/backoffice/trabajador/index',[
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
        $usuarios = DB::table('users')->where('users.idestado', 2)->get();
        return view('layouts/backoffice/trabajador/create', [
            'usuarios' => $usuarios
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
                'idusuario' => 'required'
            ];
            $messages = [
                'idusuario.required'  => 'El "Usuario" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('users')->whereId($request->input('idusuario'))->update([
                'idestado' => 1
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
            return view('layouts/backoffice/trabajador/edit',[
                'usuario' => $usuario,
                'ubigeos' => $ubigeos,
                'tipopersonas' => $tipopersonas
            ]);
        }elseif($request->input('view')=='permiso'){
            $roles = DB::table('roles')->get();
            $tiendas = DB::table('tienda')->get();
            $role_users = DB::table('role_user')->where('user_id',$usuario->id)->orderBy('id','asc')->get();
            return view('layouts/backoffice/trabajador/permiso',[
                'usuario' => $usuario,
                'roles' => $roles,
                'tiendas' => $tiendas,
                'role_users' => $role_users
            ]);  
        }elseif($request->input('view') == 'eliminar') {
            $ubigeos = DB::table('ubigeo')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/trabajador/delete',[
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
            
            if($request->input('idestado') == 2){
              
                DB::table('users')->whereId($idusuario)->update([
                    'usuario'      => $request->input('usuario'),
                    'idestado'     => $request->input('idestado')
                ]);
                DB::table('role_user')->where('user_id',$idusuario)->delete();
              
                return response()->json([
                    'resultado' => 'CORRECTO',
                    'mensaje'   => 'Se ha actualizado correctamente.'
                ]);
              
            }else{
                $rules = [
                    'usuario'     => 'required',
                    'idestado'    => 'required',
                    'permisos'    => 'required',
                ];
                $messages = [
                    'usuario.required'      => 'El "Usuario" es Obligatorio.',
                    'usuario.unique'        => 'El "Usuario" ya existe, ingrese otro.',
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
                        'usuario'      => $request->input('usuario'),
                        'clave'        => $request->input('password'),
                        'password'     => Hash::make($request->input('password')),
                        'idestado'     => $request->input('idestado'),
                    ]);
                }else{
                    DB::table('users')->whereId($idusuario)->update([
                        'usuario'      => $request->input('usuario'),
                        'idestado'     => $request->input('idestado'),
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
                      'sesion'     => $sesion,
                      'role_id'    => $item[1],
                      'user_id'    => $idusuario,
                      'idtienda'   => $item[0],
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
