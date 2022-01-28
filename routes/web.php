<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/prueba', function () {
    //dd('---');
    /*$idusuario = 231;
    DB::table('tipopagodetalle')->where('idusersresponsable',$idusuario)->delete();
    DB::table('tipopagoletra')->where('idusersresponsable',$idusuario)->delete();
    DB::table('movimiento')->where('idusuario',$idusuario)->delete();
    DB::table('compra')->where('idusuarioresponsable',$idusuario)->delete();
    DB::table('compradetalle')->join('compra','compra.id','compradetalle.idcompra')->where('idusuarioresponsable',$idusuario)->delete();
    DB::table('compradevolucion')->where('idusers',$idusuario)->delete();
    DB::table('compradevoluciondetalle')->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')->where('idusers',$idusuario)->delete();
    DB::table('pagoletra')->where('idusuario',$idusuario)->delete();
    DB::table('pagocredito')->where('idusuario',$idusuario)->delete();
    DB::table('venta')->where('idusuariocajero',$idusuario)->delete();
    DB::table('ventadetalle')->join('venta','venta.id','ventadetalle.idventa')->where('idusuariocajero',$idusuario)->delete();
    DB::table('notadevolucion')->where('idusuarioresponsable',$idusuario)->delete();
    DB::table('notadevoluciondetalle')->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')->where('idusuarioresponsable',$idusuario)->delete();
    DB::table('cobranzaletra')->where('idusuario',$idusuario)->delete();
    DB::table('cobranzacredito')->where('idusuario',$idusuario)->delete();
    DB::table('userssaldo')->where('idusuarioresponsable',$idusuario)->delete();*/
  
    /*$cobranzacreditos  = DB::table('cobranzacredito')->get();
    foreach($cobranzacreditos as $value){
        DB::table('tipopagodetalle')->insertGetId([
                              'fecharegistro' => $value->fecharegistro,
                              'fechaconfirmacion' => $value->fechaconfirmacion,
                              'monto' => $value->monto,
                              'deposito_banco' => $value->idbanco,
                              'deposito_numerocuenta' => $value->deposito_numerocuenta,
                              'deposito_fecha' => $value->deposito_fecha,
                              'deposito_hora' => $value->deposito_hora,
                              'deposito_numerooperacion' => $value->deposito_numerooperacion,
                              'cheque_banco' =>$value->idbanco,
                              'cheque_emision' => $value->cheque_emision,
                              'cheque_vencimiento' => $value->cheque_vencimiento,
                              'cheque_numero' => $value->cheque_numero,
                              'saldo_cliente' => 0,
                              'idtipopago' => $value->idtipopago,
                              'idmoneda' => $value->idmoneda,
                              'idaperturacierre' => $value->idaperturacierre,
                              'idcobranzacredito' => $value->id,
                              'idusersresponsable' => $value->idusuario,
                              'idestado' => 2
                          ]);
    }
    dd($cobranzacreditos);*/
//});

//Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::group(['middleware' => ['auth','verified']], function () {
    Route::resource('/backoffice/modulo', 'Layouts\Backoffice\ModuloController');
    Route::resource('/backoffice/inicio', 'Layouts\Backoffice\InicioController');  
    Route::resource('/backoffice/prueba', 'Layouts\Backoffice\PruebaController');
  
    $modulos = Cache::remember('modulo', 1, function() {
        return DB::table('rolesmodulo')
            ->join('roles','roles.id','rolesmodulo.idroles')
            ->join('role_user','role_user.role_id','roles.id')
            ->join('modulo','modulo.id','rolesmodulo.idmodulo')
            ->where('modulo.vista','<>','')
            ->where('modulo.controlador','<>','')
            ->where('modulo.idestado',1)
            ->get();
    });
    foreach($modulos as $value) {
        Route::resource($value->vista, $value->controlador);
    }
});