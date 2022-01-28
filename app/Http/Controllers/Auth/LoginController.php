<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;


use DB;
use Auth;
use Carbon\Carbon;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo = 'backoffice/inicio';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
  
    protected function authenticated(Request $request, $user)
    {
        $logincount = 0;
        if(usersmaster()->logincount!=''){
            $logincount = usersmaster()->logincount;
        }
        
        $logincount = $logincount+1;

        DB::table('users')->whereId(usersmaster()->id)->update([
            'logincount' => $logincount
        ]);
    
        $motivo = 'Inicio de Sesión';
        if($logincount>1){
            $motivo = 'Inicio de Sesión en varios navegadores';
        }
        
        DB::table('proteccionseguridad')->insert([
            'fecharegistro' =>  Carbon::now(),
            'motivo'        =>  $motivo,
            'ipaddress'     =>  $request->getClientIp(),
            'navegador'     =>  obtenerBrowser(),
            'ubicacion'     =>  obtenerUbicacion($request->getClientIp()) != null ? obtenerUbicacion($request->getClientIp()) : '',
            'url'           =>  $request->fullUrl(),
            'usuario'       =>  usersmaster()->nombre,
            'tienda'        =>  usersmaster()->tiendanombre,
            'idestado'      =>  1
        ]);
    }
  
    public function sendFailedLoginResponse(Request $request)
    {   
            DB::table('proteccionseguridad')->insert([
                'fecharegistro' =>  Carbon::now(),
                'motivo'    => 'Error de Sesión',
                'ipaddress'    => $request->getClientIp(),
                'navegador'     => obtenerBrowser(),
                'ubicacion'     => obtenerUbicacion($request->getClientIp()) != null ? obtenerUbicacion($request->getClientIp()) : '',
                'url'           => $request->fullUrl(),
                'usuario'      =>  $request->{$this->username()},
                'tienda'      =>  '',
                'idestado'      => 1
            ]);
      
            return redirect('/login')
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => 'Usuario y/o Contraseña es incorrecto',
                ]);
    }
  
    /*public function username()
    {
        return 'usuario';
    }*/
  
    protected function credentials($request)
    {
        return [
            'usuario' => $request->{$this->username()}, 
            'password' => $request->password, 
            'idestado' => 1
        ];
    }
  
    public function logout(Request $request)
    {
        $logincount = 0;
        if(usersmaster()->logincount!=''){
            $logincount = usersmaster()->logincount;
            if($logincount<=0){
                $logincount = 0;
            }
        }
      
        DB::table('users')->whereId(usersmaster()->id)->update([
            'logincount' => $logincount-1
        ]);
      
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/login');
    }
}
