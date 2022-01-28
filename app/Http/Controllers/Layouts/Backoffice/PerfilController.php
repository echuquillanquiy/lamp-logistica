<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PerfilController extends Controller
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
        $ubigeos = DB::table('ubigeo')->get();
        $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
        return view('layouts/backoffice/perfil/index',[
          'ubigeos' => $ubigeos,
          'usuario' => $usuario
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
        if($request->input('view')=='editcambiarclave'){
            $ubigeos = DB::table('ubigeo')->get();
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            return view('layouts/backoffice/perfil/editpassword',[
              'ubigeos' => $ubigeos,
              'usuario' => $usuario
            ]);
        }elseif($request->input('view')=='editmetodopago'){
            $ubigeos = DB::table('ubigeo')->get();
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            $bancos = DB::table('banco')->get();
            return view('layouts/backoffice/perfil/editmetodopago',[
              'ubigeos' => $ubigeos,
              'usuario' => $usuario,
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
        if($request->input('view')=='editperfil') {
            $rules = [
								'nombre' => 'required',
								'apellidos' => 'required',
								'email' => 'required',
						];
						$messages = [
								'nombre.required' => 'El "Nombre" es Obligatorio.',
								'apellidos.required' => 'Los "Apellidos" es Obligatorio.',
								'email.required' => 'El "Correo Electrónico" es Obligatorio.',
						];
						$this->validate($request,$rules,$messages);
          
            if($request->input('imagenant')!='') {
              $imagen = $request->input('imagenant');
            }else{
              $usuario = DB::table('users')->whereId($id)->first();
              $rutaimagen = getcwd().'/public/backoffice/usuario/'.$id.'/perfil/'.$usuario->imagen;
              if(file_exists($rutaimagen) && $usuario->imagen!='') {
                  unlink($rutaimagen);
              }
              $imagen = '';
              if($request->file('imagen')!='') {
                if ($request->file('imagen')->isValid()) {                  
                    list($nombre,$ext) = explode(".", $request->file('imagen')->getClientOriginalName());
                    $imagen = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                    $request->file('imagen')->move(getcwd().'/public/backoffice/usuario/'.$id.'/perfil/', $imagen);
                }
              }
            }

            DB::table('users')->whereId($id)->update([
								'nombre' => $request->input('nombre'),
                'apellidos' => $request->input('apellidos'),
                'identificacion' => $request->input('identificacion')!=null?$request->input('identificacion'):'',
                'email' => $request->input('email'),
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
    public function destroy(Request $request, $id)
    {
        //
    }
}
