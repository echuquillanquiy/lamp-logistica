@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <!--a href="javascript:;" class="btn btn-warning" onclick="modal({route:'venta/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a-->
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'venta/create?view=registrarventarapida',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Venta rapida</a>
        </div>
        <h4 class="panel-title">Ventas</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="110px">Código</th>
                <th width="120px">Fecha de registro</th>
                <th width="100px">Tienda</th>
                <th width="100px">Vendedor</th>
                <th>Cliente</th>
                <th width="100px">Total</th>
                <th width="100px">Total Pagado</th>
                <th width="100px">Total Deuda</th>
                <th width="10px">Pago</th>
                <!--th width="10px">Comprobantes</th-->
                <th width="10px">Estado</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['codigo','date:fecharegistro','','vendedor','cliente','','','','','',''],
                'search_url'=> url('backoffice/venta')
            ])
            <tbody>
                @foreach($ventas as $value)
                <?php 
                  /**Validar Anulacion */
                  $cobranzacredito_exist = DB::table('cobranzacredito')
                    ->where('idventa', $value->id)
                    ->exists();
                  $cobranzaletra_exist   = DB::table('tipopagoletra')
                    ->join('venta', 'venta.id', 'tipopagoletra.idventa')
                    ->join('cobranzaletra', 'cobranzaletra.idtipopagoletra', 'tipopagoletra.id')
                    ->where('tipopagoletra.idventa', $value->id)->exists();
                  $notadevolucion_exist = DB::table('notadevolucion')->where('idventa', $value->id)->exists();
                  /**Fin Validar Anulacion */
              
                  $totalnotadevolucion = DB::table('notadevolucion')
                    ->where('notadevolucion.idestado',2)
                    ->where('notadevolucion.idventa',$value->id)
                    ->sum('total');
              
                  $totalapagado = DB::table('notadevoluciondetalle')
                          ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                          //->join('tipopagodetalle','tipopagodetalle.idnotadevolucion','notadevolucion.id')
                          ->where('notadevolucion.idestado',2)
                          ->where('notadevolucion.idventa',$value->id)
                          //->where('tipopagodetalle.idtipopago','<>',4)
                          ->sum(DB::raw('CONCAT(notadevoluciondetalle.cantidad*notadevoluciondetalle.preciounitario)'));
              
                  $totalpagado = $value->montorecibido;
                  $deudatotal = 0;
                  if($value->idformapago==2){
                      $totalpagado = DB::table('cobranzacredito')
                          ->where('idestado',2)
                          ->where('idventa',$value->id)
                          ->sum('monto');
                      $deudatotal = $value->montorecibido-$totalpagado-$totalapagado;
                  }elseif($value->idformapago==3){
                      $totalpagado = DB::table('cobranzaletra')
                          ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                          ->where('cobranzaletra.idestado',2)
                          ->where('tipopagoletra.idventa',$value->id)
                          ->sum('cobranzaletra.monto');
                      $deudatotal = $value->montorecibido-$totalpagado-$totalnotadevolucion;
                  }
                  // fin Total pagado
              
                  $guairemision = DB::table('facturacionguiaremision')->where('facturacionguiaremision.idventa', $value->id)->first();
                  
                  $facturacionboletafacturas = DB::table('facturacionboletafactura')
                      ->join('facturacionboletafacturadetalle','facturacionboletafacturadetalle.idfacturacionboletafactura','facturacionboletafactura.id')
                      ->where('facturacionboletafactura.idventa',$value->id)
                      ->orWhere('facturacionboletafacturadetalle.idventa',$value->id)
                      ->select(
                          'facturacionboletafactura.venta_serie as venta_serie',
                          'facturacionboletafactura.venta_correlativo as venta_correlativo'
                      )
                      ->orderBy('facturacionboletafactura.venta_serie','desc')
                      ->distinct('venta_serie')
                      ->get();
                  ?>
                <tr>
                  <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ $value->fechaconfirmacion }}</td>
                  <td>{{ $value->tiendanombre }}</td>
                  <td>{{ $value->nombreusuariovendedor }}</td>
                  <td>{{ $value->cliente }}</td>
                  <td>
                    {{ $value->monedasimbolo }} {{ $value->montorecibido }} <?php echo $totalapagado>0? '(-'.number_format($totalapagado, 2, '.', '').')':'' ?>
                  </td>
                  <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828;"'?>>
                    {{ $value->monedasimbolo }} {{ number_format($totalpagado, 2, '.', '') }}
                  </td>
                  <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828;"'?>>
                    {{ $value->monedasimbolo }} {{ number_format($deudatotal, 2, '.', '') }}
                  </td>
                  <td>{{ $value->nombreFormapago }}</td>
                  <!--td>
                    @foreach($facturacionboletafacturas as $valuefacturas)
                    <span class="badge badge-pill badge-warning">{{ $valuefacturas->venta_serie }}-{{ $valuefacturas->venta_correlativo }}</span>
                    @endforeach
                  </td-->
                  <td>
                    @if($value->idestado==2)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                    @elseif($value->idestado==3)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                    @elseif($value->idestado==4)
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>
                    @endif  
                  </td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                         @if($value->idestado==2)
                         <li><a href="javascript:;" onclick="modal({route:'venta/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                            <i class="fa fa-edit"></i> Editar
                         </a></li>
                        <li><a href="javascript:;" onclick="modal({route:'venta/{{ $value->id }}/edit?view=ticket'})">
                                <i class="fa fa-file-alt"></i> Ticket de Venta
                             </a></li>
                         <!--li><a href="javascript:;" onclick="modal({route:'venta/{{ $value->id }}/edit?view=proforma',size:'modal-fullscreen'})">
                            <i class="fa fa-file-alt"></i> PDF Proforma
                         </a></li-->
                         @elseif($value->idestado==3)
                         <li><a href="javascript:;" onclick="modal({route:'venta/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                           <i class="fa fa-list-alt"></i> Detalle</a></li>
                        <li><a href="javascript:;" onclick="modal({route:'venta/{{ $value->id }}/edit?view=ticket'})">
                                <i class="fa fa-file-alt"></i> Ticket de Venta
                             </a></li>
                         <!--li><a href="javascript:;" onclick="modal({route:'venta/{{ $value->id }}/edit?view=proforma',size:'modal-fullscreen'})">
                            <i class="fa fa-file-alt"></i> PDF Proforma
                         </a></li-->
                         @if(count($facturacionboletafacturas) == 0 && $cobranzacredito_exist && $cobranzaletra_exist && $notadevolucion_exist) 
                         <li><a href="javascript:;" onclick="modal({route:'venta/{{ $value->id }}/edit?view=anular',size:'modal-fullscreen'})">
                            <i class="fa fa-ban"></i> Anular
                         </a></li>
                         @endif 
                         @elseif($value->idestado==4)
                         <li><a href="javascript:;" onclick="modal({route:'venta/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                           <i class="fa fa-list-alt"></i> Detalle</a></li>
                         @endif 
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $ventas->links('app.tablepagination', ['results' => $ventas]) }}
        </div>
    </div>

</div>
@endsection