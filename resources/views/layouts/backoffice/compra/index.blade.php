@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
   <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'compra/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Compras</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="90px">Código</th>
                <th width="100px">Comprobante</th>
                <th width="110px">Número</th>
                <th>Responsable</th>
                <th>Proveedor</th>
                <th width="10px">F. de Pago</th>
                <th width="100px">Total</th>
                <th width="100px">T. Pagado</th>
                <th width="100px">T. Deuda</th>
                <th width="100px">Fecha de registro</th>
                <th width="100px">Fecha de compra</th>
                <th width="10px">Estado</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['codigo','comprobante','seriecorrelativo','responsable','proveedor','','','','','','','',''],
                'search_url'=> url('backoffice/compra')
            ])
            <tbody>
                @foreach($compra as $value)
                <?php
                  $totalcompradevolucion = DB::table('compradevolucion')
                    ->where('compradevolucion.idestado',2)
                    ->where('compradevolucion.idcompra',$value->id)
                    ->sum('montorecibido');
              
                  $totalapagado = DB::table('compradevoluciondetalle')
                          ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                          ->where('compradevolucion.idestado',2)
                          ->where('compradevolucion.idcompra',$value->id)
                          ->sum(DB::raw('CONCAT(compradevoluciondetalle.cantidad*compradevoluciondetalle.preciounitario)'));
              
                  $totalpagado = 0;
                  $deudatotal = 0;
                  if($value->idformapago==1){
                      $totalpagado = $value->monto-$totalapagado;
                  }elseif($value->idformapago==2){
                      $totalpagado = DB::table('pagocredito')
                          ->where('idestado',2)
                          ->where('idcompra',$value->id)
                          ->sum('monto');
                      $deudatotal = $value->monto-$totalpagado-$totalapagado+$totalcompradevolucion;
                      $totalpagado = $totalpagado-$totalcompradevolucion;
                  }elseif($value->idformapago==3){
                      $totalpagado = DB::table('pagoletra')
                          ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                          ->where('pagoletra.idestado',2)
                          ->where('tipopagoletra.idcompra',$value->id)
                          ->sum('pagoletra.monto');
                      $deudatotal = $value->monto-$totalpagado;
                  }
                ?>
                <tr>
                  <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ $value->nombreComprobante }}</td>
                  <td>{{ $value->seriecorrelativo }}</td>
                  <td>{{ $value->responsablenombre }}</td>
                  <td>{{ $value->proveedor }}</td>
                  <td>{{ $value->nombreFormapago }}</td>
                  <td>
                    {{ $value->monedasimbolo }} {{ $value->monto }}
                  </td>
                  <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828;"'?>>
                    {{ $value->monedasimbolo }} {{ number_format($totalpagado, 2, '.', '') }}
                  </td>
                  <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828;"'?>>
                    {{ $value->monedasimbolo }} {{ number_format($deudatotal, 2, '.', '') }}
                  </td>
                  <td>{{ $value->fecharegistro }}</td>
                  <td>{{ ($value->idestado==2or$value->idestado==3) ? $value->fechaconfirmacion : '---' }}</td>
                  <td>
                    @if($value->idestado==1)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                    @elseif($value->idestado==2)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                    @elseif($value->idestado==3)
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>   
                    @endif  
                  </td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                         @if($value->idestado==1)
                          <li>
                            <a href="javascript:;" onclick="modal({route:'compra/{{ $value->id }}/edit?view=confirmar',size:'modal-fullscreen'})">
                                <i class="fa fa-check"></i> Confirmar
                            </a>
                          </li>
                          <li>
                            <a href="javascript:;" onclick="modal({route:'compra/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                                <i class="fa fa-edit"></i> Editar
                              </a>
                          </li>
                          <li>
                              <a href="javascript:;" onclick="modal({route:'compra/{{ $value->id }}/edit?view=ticket'})">
                                <i class="fa fa-file-alt"></i> Ticket de Compra
                             </a>
                          </li>
                          <li>
                            <a href="javascript:;" onclick="modal({route:'compra/{{ $value->id }}/edit?view=eliminar',size:'modal-fullscreen'})">
                                <i class="fa fa-trash"></i> Eliminar
                            </a>
                          </li>
                         @elseif($value->idestado==2)
                             <?php
                              $countcompradevolucions = DB::table('compradevolucion')
                                  ->where('compradevolucion.idestado',2)
                                  ->where('compradevolucion.idcompra',$value->id)
                                  ->count();
                             ?>
                             <li>
                               <a href="javascript:;" onclick="modal({route:'compra/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                                  <i class="fa fa-list-alt"></i> Detalle
                               </a>
                             </li>
                             <li>
                              <a href="javascript:;" onclick="modal({route:'compra/{{ $value->id }}/edit?view=ticket'})">
                                <i class="fa fa-file-alt"></i> Ticket de Compra
                             </a>
                             </li>
                             @if($value->idaperturacierre==$idapertura)
                                 @if($countcompradevolucions==0)
                                    <li>
                                      <a href="javascript:;" onclick="modal({route:'compra/{{ $value->id }}/edit?view=anular',size:'modal-fullscreen'})">
                                          <i class="fa fa-ban"></i> Anular
                                      </a>
                                    </li>
                                 @else
                                  <li style="background-color: #eca030;">
                                    <a href="javascript:;" style="color: #f8f9fa;">
                                        <i class="fa fa-ban"></i> No se puede anular, ya que <br>existe productos devueltos!.
                                    </a>
                                  </li>
                                 @endif 
                             @endif
                         @elseif($value->idestado==3)
                         <li><a href="javascript:;" onclick="modal({route:'compra/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                         @endif 
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $compra->links('app.tablepagination', ['results' => $compra]) }}
        </div>
    </div>
</div>
@endsection
