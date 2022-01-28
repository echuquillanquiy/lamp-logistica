@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">            
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'cobranzaletra/1/edit?view=letra',size:'modal-fullscreen'})">
              <i class="fa fa-file-alt"></i> Letra
            </a>
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'cobranzaletra/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Cobranza de Letras</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
          <table class="table" id="tabla-cobranzaletra">
              <thead class="thead-dark">
                <tr>
                  <th>Fecha de registro</th>
                  <th>Fecha de confirmación</th>
                  <th>Responsable</th>
                  <th>N° Letra</th>
                  <th>Monto</th>
                  <th>Venta</th>
                  <th>Tipo de pago - Descripción</th>
                  <th width="10px">Estado</th>
                  <th width="10px"></th>
                </tr>
              </thead>
              @include('app.tablesearch',[
                  'searchs'=>['fecharegistro','fechaconfirmacion','responsable','','','ventacodigo','',''],
                  'search_url'=> url('backoffice/cobranzaletra')
              ])
              <tbody>
                  @foreach($cobranzaletras as $value)
                      <?php 
                      $tipopagodetalles = DB::table('tipopagodetalle')
                            ->leftJoin('banco as bancodeposito','bancodeposito.id','tipopagodetalle.deposito_banco')
                            ->leftJoin('banco as bancocheque','bancocheque.id','tipopagodetalle.cheque_banco')
                            ->leftJoin('users as usuariocliente','usuariocliente.id','tipopagodetalle.saldo_cliente')
                            ->where('idpagoletra',$value->id)
                            ->select(
                                  'tipopagodetalle.*',
                                  'bancodeposito.nombre as bancodepositonombre',
                                  'bancocheque.nombre as bancochequenombre',
                                  DB::raw('IF(usuariocliente.idtipopersona=1,
                                  CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                                  CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente')
                            )
                            ->get() 
                      ?>
                  <tr>
                    <td>{{$value->fecharegistro}}</td>
                    <td>{{$value->idestado==2?$value->fechaconfirmacion:'---'}}</td>
                    <td>{{$value->responsablenombre}}</td>
                    <td>{{$value->idestado==2?$value->numeroletra:'---'}}</td>
                    <td>{{$value->monedasimbolo}} {{$value->monto}}</td>
                    <td>{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>
                      @foreach($tipopagodetalles as $tvalue)
                      @if($tvalue->idtipopago==1)
                        EFECTIVO<br>
                      @elseif($tvalue->idtipopago==2)
                        DEPOSITO - {{$tvalue->bancodepositonombre}}, <b>N° Ope:</b> {{$tvalue->deposito_numerooperacion}}, <b>Fecha:</b> {{$tvalue->deposito_fecha}} {{$tvalue->deposito_hora}}<br>
                      @elseif($tvalue->idtipopago==3)
                        CHEQUE - {{$tvalue->bancochequenombre}}, <b>N°:</b> {{$tvalue->cheque_numero}}, <b>Emisión:</b> {{$tvalue->cheque_emision}}, <b>Vcto:</b> {{$tvalue->cheque_vencimiento}} {{$tvalue->deposito_hora}}<br>
                      @elseif($tvalue->idtipopago==4)
                        SALDO - <b>Cliente:</b> {{$tvalue->cliente}}<br>
                      @endif
                      @endforeach
                    </td>
                    <td>
                      @if($value->idestado==2)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                      @elseif($value->idestado==1)
                          <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
                      @elseif($value->idestado==3)
                          <div class="td-badge"><span class="badge badge-pill badge-danger"><i class="fas fa-ban"></i> Anulado</span></div> 
                      @endif
                    </td>
                    <td class="with-btn-group" nowrap>
                      <div class="btn-group">
                        <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                          Opción <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu pull-right">
                          @if($value->idestado==3)
                          <li><a href="javascript:;" onclick="modal({route:'cobranzaletra/{{ $value->id }}/edit?view=detalle'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                          @elseif($value->idestado==2)
                           <li><a href="javascript:;" onclick="modal({route:'cobranzaletra/{{ $value->id }}/edit?view=detalle'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                           @if($value->idaperturacierre==$idapertura)
                              <li><a href="javascript:;" onclick="modal({route:'cobranzaletra/{{ $value->id }}/edit?view=anular'})"><i class="fas fa-ban"></i> Anular</a></li>
                            @endif
                          @else
                          <li><a href="javascript:;" onclick="modal({route:'cobranzaletra/{{ $value->id }}/edit?view=confirmar'})"><i class="fa fa-check"></i> Confirmar</a></li>
                          <li><a href="javascript:;" onclick="modal({route:'cobranzaletra/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                          <li><a href="javascript:;" onclick="modal({route:'cobranzaletra/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                          @endif
                        </ul>
                      </div>
                    </td>
                  </tr>
                  @endforeach
              </tbody>
          </table>
          {{ $cobranzaletras->links('app.tablepagination', ['results' => $cobranzaletras]) }}
    </div>
</div>
</div>
@endsection
