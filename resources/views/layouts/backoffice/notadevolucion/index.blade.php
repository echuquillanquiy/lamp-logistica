@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'notadevolucion/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Notas de Devolución</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="100px">Cod. Venta</th>
                <th width="100px">Tipo</th>
                <th width="100px">Código</th>
                <th width="100px">Tienda</th>
                <th>Responsable</th>
                <th>Cliente</th>
                <th>Tipo de pago - Descripción</th>
                <th width="100px">Devuelto</th>
                <th width="100px">Descontado</th>
                <th width="85px">Fecha de confirmación</th>
                <th width="10px">Estado</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['codigoventa','tipoventa','codigo','','responsable','cliente','','','',''],
                'search_url'=> url('backoffice/notadevolucion')
            ])
            <tbody>
                <?php $fechaactual = Carbon\Carbon::now() ?>
                @foreach($notadevolucions as $value)
                <?php 
                  $total = DB::table('notadevoluciondetalle')
                          ->where('idnotadevolucion',$value->id)
                          ->sum(DB::raw('CONCAT(preciounitario*cantidad)'));
                /*if($value->total==0){
                    DB::table('notadevolucion')->whereId($value->id)->update([
                      'total' => $total,
                    ]);
                             DB::table('tipopagodetalle')->insertGetId([
                                  'fecharegistro' => $fechaactual,
                                  'fechaconfirmacion' => $fechaactual,
                                  'monto' => $total,
                                  'deposito_banco' => 0,
                                  'deposito_numerocuenta' => '',
                                  'deposito_fecha' => '',
                                  'deposito_hora' => '',
                                  'deposito_numerooperacion' => '',
                                  'cheque_banco' => 0,
                                  'cheque_emision' => '',
                                  'cheque_vencimiento' => '',
                                  'cheque_numero' => '',
                                  'saldo_cliente' => 0,
                                  'idtipopago' => 1,
                                  'idmoneda' => 1,
                                  'idaperturacierre' => $value->idaperturacierre,
                                  'idnotadevolucion' => $value->id,
                                  'idusersresponsable' => Auth::user()->id,
                                  'idestado' => 2
                              ]);
                  
                }*/
                ?>
                <tr>
                  <td>{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ $value->formapagonombreventa }} ({{ $value->ventamontorecibido }})</td>
                  <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ $value->tiendanombre }}</td>
                  <td>{{ $value->responsablenombre }}</td>
                  <td>{{ $value->cliente }}</td>
                  <td>
                      <?php echo tipopago_detalle('pagocredito',$value->id)['detalle'] ?>
                  </td>
                  <td>
                    {{ $value->monedasimbolo }} {{ $value->total }}
                  </td>
                  <td>
                    {{ $value->monedasimbolo }} {{ number_format($total, 2, '.', '') }}
                  </td>
                  <td>
                    @if($value->idestado==2 or $value->idestado==3)
                        {{$value->fechaconfirmacion}}
                    @else
                        --- 
                    @endif
                  </td>
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
                            <li><a href="javascript:;" onclick="modal({route:'notadevolucion/{{ $value->id }}/edit?view=confirmar',size:'modal-fullscreen'})"><i class="fa fa-check"></i> Confirmar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'notadevolucion/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})"><i class="fa fa-edit"></i> Editar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'notadevolucion/{{ $value->id }}/edit?view=eliminar',size:'modal-fullscreen'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                         @elseif($value->idestado==2)
                              <li><a href="javascript:;" onclick="modal({route:'notadevolucion/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                                <i class="fa fa-list-alt"></i> Detalle</a></li>
                              <li><a href="javascript:;" onclick="modal({route:'notadevolucion/{{ $value->id }}/edit?view=ticket'})">
                                <i class="fa fa-file-alt"></i> Ticket Cliente</a></li>
<!--                              <li><a href="javascript:;" onclick="modal({route:'notadevolucion/{{ $value->id }}/edit?view=proformacliente',size:'modal-fullscreen'})">
                                <i class="fa fa-file-alt"></i> PDF Cliente
                             </a></li> -->
                            @if($value->idaperturacierre==$idapertura)
                               <li><a href="javascript:;" onclick="modal({route:'notadevolucion/{{ $value->id }}/edit?view=anular',size:'modal-fullscreen'})">
                                  <i class="fa fa-ban"></i> Anular
                               </a></li>
                            @endif
                         @elseif($value->idestado==3)
                              <li><a href="javascript:;" onclick="modal({route:'notadevolucion/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                                <i class="fa fa-list-alt"></i> Detalle</a></li>
                         @endif
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $notadevolucions->links('app.tablepagination', ['results' => $notadevolucions]) }}
        </div>
    </div>

</div>
@endsection