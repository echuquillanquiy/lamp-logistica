<?php

namespace App;

use App\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth; 

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'nombre',
      'apellidos',
      'identificacion',
      'email',
      'email_verified_at',
      'usuario',
      'clave',
      'password',
      'numerotelefono',
      'direccion',
      'imagen',
      'idubigeo',
      'idtipopersona',
      'idestado'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
  
    // ROLES
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
  
    public function authorizeRoles($roles,$idtienda=0)
    {
        abort_unless($this->hasAnyRole($roles,$idtienda), 401);
        return true;
    }
    public function hasAnyRole($roles,$idtienda)
    {
        /*if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {*/
            if ($this->hasRole($roles,$idtienda)) {
                 return true; 
            }   
        /*}*/
        return false;
    }
    public function hasRole($role,$idtienda)
    {
        
        $list_vista = explode('/',$role);
        if(Auth::user()->idtienda!=0){
            if(count($list_vista)>4){
                $role = $list_vista[0].'/'.$list_vista[1].'/'.$list_vista[2].'/{idtienda}/'.$list_vista[4];
            }
            if($this->roles()
                ->join('rolesmodulo','rolesmodulo.idroles','roles.id')
                ->join('modulo','modulo.id','rolesmodulo.idmodulo')
                ->join('users','users.id','role_user.user_id')
                ->where('modulo.vista',$role)
                ->where('modulo.idestado',1)
                ->where('users.idtienda',$idtienda)
                ->first()) {
                return true;
            }
        }else{
            if(count($list_vista)>4){
                $role = $list_vista[0].'/'.$list_vista[1].'/'.$list_vista[2].'/{idtienda}/'.$list_vista[4];
                if(Auth::user()->id==1){
                    if($this->roles()
                        ->join('rolesmodulo','rolesmodulo.idroles','roles.id')
                        ->join('modulo','modulo.id','rolesmodulo.idmodulo')
                        ->where('modulo.vista',$role)
                        ->where('modulo.idestado',1)
                        ->first()) {
                        return true;
                    }
                }else{
                    if($this->roles()
                        ->join('rolesmodulo','rolesmodulo.idroles','roles.id')
                        ->join('modulo','modulo.id','rolesmodulo.idmodulo')
                        ->join('users','users.id','role_user.user_id')
                        ->join('tienda','tienda.idusers','users.id')
                        ->where('modulo.vista',$role)
                        ->where('modulo.idestado',1)
                        ->where('tienda.id',$idtienda)
                        ->first()) {
                        return true;
                    }
                }   
            }elseif(count($list_vista)>=2){
                $role = $list_vista[0].'/'.$list_vista[1];
                if($this->roles()
                    ->join('rolesmodulo','rolesmodulo.idroles','roles.id')
                    ->join('modulo','modulo.id','rolesmodulo.idmodulo')
                    ->where('modulo.vista',$role)
                    ->where('modulo.idestado',1)
                    ->first()) {
                    return true;
                }
            }    
        }
        return false;
    }
}
