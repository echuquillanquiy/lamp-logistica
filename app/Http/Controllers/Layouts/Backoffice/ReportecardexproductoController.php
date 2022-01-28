<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\User;

use App\Exports\InvoicesExport;
use App\Exports\ReportecardexproductoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportecardexproductoController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $where = [];
      
        $where[] =  ['producto.id', $request->input('idproducto')];
        $where[] =  ['tienda.id', $request->input('idtienda')];
      
      
        // egresos
            $ventas = DB::table('ventadetalle')
                ->join('producto','producto.id','ventadetalle.idproducto')
                ->join('venta','venta.id','ventadetalle.idventa')
                ->join('tienda','tienda.id','venta.idtienda')
                ->join('users','users.id','venta.idusuariocajero')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->where('venta.idestado', 3)
                ->where($where)
                ->select(
                    'ventadetalle.cantidad as cantidad',
                    'ventadetalle.preciounitario as preciounitario',
                    'ventadetalle.preciototal as preciototal',
                    'venta.fechaconfirmacion as fechaconfirmacion',
                    'venta.codigo as codigo',
                    DB::raw('CONCAT("VENTA") as motivo'),
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as detalle'),
                    DB::raw('CONCAT("SALIDA") as tipo'),
                    'users.nombre as usuario'
                );
            $productotransferenciasrecpciones_envio = DB::table('productotransferenciadetalle')
                ->join('productotransferencia','productotransferencia.id','productotransferenciadetalle.idproductotransferencia')
                ->join('producto','producto.id','productotransferenciadetalle.idproducto')
                ->join('tienda','tienda.id','productotransferencia.idtiendaorigen')
                ->join('tienda as tiendadestino','tiendadestino.id','productotransferencia.idtiendadestino')
                ->join('users','users.id','productotransferencia.idusersorigen')
                ->where('productotransferencia.idestadotransferencia',2)
                ->where('productotransferencia.idestado',2)
                ->where($where)
                ->select(
                    'productotransferenciadetalle.cantidadenviado as cantidad',
                    DB::raw('CONCAT("") as preciounitario'),
                    DB::raw('CONCAT("") as preciototal'),
                    'productotransferencia.fechaenvio as fechaconfirmacion',
                    'productotransferencia.codigo as codigo',
                    DB::raw('CONCAT("TRANSFERENCIA") as motivo'),
                    DB::raw('CONCAT("Pendiente de envío a ",tiendadestino.nombre ) as detalle'),
                    DB::raw('CONCAT("SALIDA") as tipo'),
                    'users.nombre as usuario'
                );
          

            $productotransferenciasrecpciones_recepcion = DB::table('productotransferenciadetalle')
                ->join('productotransferencia','productotransferencia.id','productotransferenciadetalle.idproductotransferencia')
                ->join('producto','producto.id','productotransferenciadetalle.idproducto')
                ->join('tienda','tienda.id','productotransferencia.idtiendaorigen')
                ->join('tienda as tiendadestino','tiendadestino.id','productotransferencia.idtiendadestino')
                ->join('users','users.id','productotransferencia.idusersorigen')
                ->where('productotransferencia.idestadotransferencia',3)
                ->where('productotransferencia.idestado',2)
                ->where($where)
                ->select(
                    'productotransferenciadetalle.cantidadrecepcion as cantidad',
                    DB::raw('CONCAT("") as preciounitario'),
                    DB::raw('CONCAT("") as preciototal'),
                    'productotransferencia.fecharecepcion as fechaconfirmacion',
                    'productotransferencia.codigo as codigo',
                    DB::raw('CONCAT("TRANSFERENCIA") as motivo'),
                    DB::raw('CONCAT("Enviado a ",tiendadestino.nombre," (",productotransferencia.motivo,")") as detalle'),
                    DB::raw('CONCAT("SALIDA") as tipo'),
                    'users.nombre as usuario'
                );

            $productomovimientossalida = DB::table('productomovimientodetalle')
                ->join('productomovimiento','productomovimiento.id','productomovimientodetalle.idproductomovimiento')
                ->join('producto','producto.id','productomovimientodetalle.idproducto')
                ->join('tienda','tienda.id','productomovimiento.idtienda')
                ->join('users','users.id','productomovimiento.idusers')
                ->where('productomovimiento.idestadomovimiento',2)
                ->where('productomovimiento.idestado',2)
                ->where($where)
                ->select(
                    'productomovimientodetalle.cantidad as cantidad',
                    DB::raw('CONCAT("") as preciounitario'),
                    DB::raw('CONCAT("") as preciototal'),
                    'productomovimiento.fecharecepcion as fechaconfirmacion',
                    'productomovimiento.codigo as codigo',
                    DB::raw('CONCAT("MOVIMIENTO") as motivo'),
                    'productomovimiento.motivo as detalle',
                    DB::raw('CONCAT("SALIDA") as tipo'),
                    'users.nombre as usuario'
                );

            $compradevoluciones = DB::table('compradevoluciondetalle')
                ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                ->join('producto','producto.id','compradevoluciondetalle.idproducto')
                ->join('tienda','tienda.id','compradevolucion.idtienda')
                ->join('users','users.id','compradevolucion.idusers')
                ->where('compradevolucion.idestado',2)
                ->where($where)
                ->select(
                    'compradevoluciondetalle.cantidad as cantidad',
                    'compradevoluciondetalle.preciounitario as preciounitario',
                    DB::raw('CONCAT("") as preciototal'),
                    'compradevolucion.fechaconfirmacion as fechaconfirmacion',
                    'compradevolucion.codigo as codigo',
                    DB::raw('CONCAT("COMPRA DEVOLUCIÓN") as motivo'),
                    DB::raw('CONCAT("") as detalle'),
                    DB::raw('CONCAT("SALIDA") as tipo'),
                    'users.nombre as usuario'
                );
          
            // ingresos
          
            $compras = DB::table('compradetalle')
                ->join('producto','producto.id','compradetalle.idproducto')
                ->join('compra','compra.id','compradetalle.idcompra')
                ->join('tienda','tienda.id','compra.idtienda')
                ->join('users','users.id','compra.idusuarioresponsable')
                ->join('users as usuarioproveedor','usuarioproveedor.id','compra.idusuarioproveedor')
                ->where('compra.idestado', 2)
                ->where($where)
                ->select(
                    'compradetalle.cantidad as cantidad',
                    'compradetalle.preciounitario as preciounitario',
                    'compradetalle.preciototal as preciototal',
                    'compra.fechaconfirmacion as fechaconfirmacion',
                    'compra.codigo as codigo',
                    DB::raw('CONCAT("COMPRA") as motivo'),
                    DB::raw('IF(usuarioproveedor.idtipopersona=1,
                    CONCAT(usuarioproveedor.identificacion," - ",usuarioproveedor.apellidos,", ",usuarioproveedor.nombre),
                    CONCAT(usuarioproveedor.identificacion," - ",usuarioproveedor.apellidos)) as detalle'),
                    DB::raw('CONCAT("INGRESO") as tipo'),
                    'users.nombre as usuario'
                );
          
            $productotransferenciasenvios = DB::table('productotransferenciadetalle')
                ->join('productotransferencia','productotransferencia.id','productotransferenciadetalle.idproductotransferencia')
                ->join('producto','producto.id','productotransferenciadetalle.idproducto')
                ->join('tienda','tienda.id','productotransferencia.idtiendadestino')
                ->join('tienda as tiendaorigen','tiendaorigen.id','productotransferencia.idtiendaorigen')
                ->join('users','users.id','productotransferencia.idusersdestino')
                ->where('productotransferencia.idestadotransferencia',3)
                ->where('productotransferencia.idestado',2)
                ->where($where)
                ->select(
                    'productotransferenciadetalle.cantidadrecepcion as cantidad',
                    DB::raw('CONCAT("") as preciounitario'),
                    DB::raw('CONCAT("") as preciototal'),
                    'productotransferencia.fecharecepcion as fechaconfirmacion',
                    'productotransferencia.codigo as codigo',
                    DB::raw('CONCAT("TRANSFERENCIA") as motivo'),
                    DB::raw('CONCAT("Recibido de ",tiendaorigen.nombre," (",productotransferencia.motivo,")") as detalle'),
                    DB::raw('CONCAT("INGRESO") as tipo'),
                    'users.nombre as usuario'
                );

            $productomovimientosingreso = DB::table('productomovimientodetalle')
                ->join('productomovimiento','productomovimiento.id','productomovimientodetalle.idproductomovimiento')
                ->join('producto','producto.id','productomovimientodetalle.idproducto')
                ->join('tienda','tienda.id','productomovimiento.idtienda')
                ->join('users','users.id','productomovimiento.idusers')
                ->where('productomovimiento.idestadomovimiento',1)
                ->where('productomovimiento.idestado',2)
                ->where($where)
                ->select(
                    'productomovimientodetalle.cantidad as cantidad',
                    DB::raw('CONCAT("") as preciounitario'),
                    DB::raw('CONCAT("") as preciototal'),
                    'productomovimiento.fecharecepcion as fechaconfirmacion',
                    'productomovimiento.codigo as codigo',
                    DB::raw('CONCAT("MOVIMIENTO") as motivo'),
                    'productomovimiento.motivo as detalle',
                    DB::raw('CONCAT("INGRESO") as tipo'),
                    'users.nombre as usuario'
                );

            $notadevoluciones = DB::table('notadevoluciondetalle')
                ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                ->join('producto','producto.id','notadevoluciondetalle.idproducto')
                ->join('tienda','tienda.id','notadevolucion.idtienda')
                ->join('users','users.id','notadevolucion.idusuarioresponsable')
                ->where('notadevolucion.idestado',2)
                ->where($where)
                ->select(
                    'notadevoluciondetalle.cantidad as cantidad',
                    'notadevoluciondetalle.preciounitario as preciounitario',
                    DB::raw('CONCAT("") as preciototal'),
                    'notadevolucion.fechaconfirmacion as fechaconfirmacion',
                    'notadevolucion.codigo as codigo',
                    DB::raw('CONCAT("NOTA DE DEVOLUCIÓN") as motivo'),
                    DB::raw('CONCAT("") as detalle'),
                    DB::raw('CONCAT("INGRESO") as tipo'),
                    'users.nombre as usuario'
                );
      
      
        if($request->input('tipo')=='excel'){
          
            $s_compraventa = $compras
              ->union($ventas)
              ->union($productotransferenciasenvios)
              ->union($productomovimientosingreso)
              ->union($notadevoluciones)
              ->union($productotransferenciasrecpciones_envio)
              ->union($productotransferenciasrecpciones_recepcion)
              ->union($productomovimientossalida)
              ->union($compradevoluciones)
              ->orderBy('fechaconfirmacion','desc')
              ->get();
        
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $titulo      = 'Reporte de Movimiento de Productos';

            return Excel::download(new 
                                    ReportecardexproductoExport($s_compraventa, $titulo),
                                    $titulo.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }else {
            
            $s_compraventa = $compras
              ->union($ventas)
              ->union($productotransferenciasenvios)
              ->union($productomovimientosingreso)
              ->union($notadevoluciones)
              ->union($productotransferenciasrecpciones_envio)
              ->union($productotransferenciasrecpciones_recepcion)
              ->union($productomovimientossalida)
              ->union($compradevoluciones)
              ->orderBy('fechaconfirmacion','desc')
              ->paginate(10);

          
             $tiendas= DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportecardexproducto/index',[
               'producto' => $s_compraventa,
               'tiendas' => $tiendas
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
      
        if($id=='show-listarproducto'){
          
            $producto = DB::table('producto')
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                ->where('producto.compatibilidadnombre', 'LIKE', '%'.$request->input('buscar').'%')
                ->orWhere('producto.codigoimpresion', $request->input('buscar'))
                ->select(
                    'producto.id as id',
                     DB::raw('CONCAT(producto.codigoimpresion," - ",producto.compatibilidadnombre,", ",producto.compatibilidadmotor,", ",producto.compatibilidadmodelo) as text')
                )
                ->orderBy('producto.compatibilidadnombre','ASC')
                ->get(); 
          
            return $producto;
          
        }
    }
}
