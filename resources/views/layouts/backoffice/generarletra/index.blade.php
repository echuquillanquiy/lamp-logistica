@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'generarletra/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Generar Letras</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="110px">Código</th>
                <th width="120px">Fecha de registro</th>
                <th width="100px">Vendedor</th>
                <th>Cliente</th>
                <th width="100px">Total Pagado</th>
                <th width="100px">Deuda Total</th>
                <th width="10px">Pago</th>
                <th width="10px">Estado</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['codigo','date:fecharegistro','vendedor','cliente','','','','',''],
                'search_url'=> url('backoffice/venta')
            ])
            <tbody>
                @foreach($ventas as $value)
                <?php 
                  $facturacionboletafacturas = DB::table('facturacionboletafactura')
                      ->join('facturacionboletafacturadetalle','facturacionboletafacturadetalle.idfacturacionboletafactura','facturacionboletafactura.id')
                      ->where('facturacionboletafactura.idventa',$value->id)
                      ->orWhere('facturacionboletafacturadetalle.idventa',$value->id)
                      ->select(
                          'facturacionboletafactura.*'
                      )
                      ->orderBy('facturacionboletafactura.id','desc')
                      ->get();
                  $totalnotadevolucion = DB::table('notadevolucion')
                      ->where('notadevolucion.idventa',$value->id)
                      ->sum('total');
                  $totalpagado = 0;
                  $deudatotal = 0;
                  if($value->idformapago==1){
                      $totalpagado = $value->montorecibido-$totalnotadevolucion;
                  }elseif($value->idformapago==2){
                      $totalpagado = DB::table('cobranzacredito')
                          ->where('idestado',2)
                          ->where('idventa',$value->id)
                          ->sum('monto');
                      $deudatotal = $value->montorecibido-$totalpagado-$totalnotadevolucion;
                  }elseif($value->idformapago==3){
                      $totalpagado = DB::table('cobranzaletra')
                          ->join('ventaletra','ventaletra.id','cobranzaletra.idventaletra')
                          ->where('cobranzaletra.idestado',2)
                          ->where('ventaletra.idventa',$value->id)
                          ->sum('cobranzaletra.monto');
                      $deudatotal = $value->montorecibido-$totalpagado-$totalnotadevolucion;
                  }
                  ?>
                <tr>
                  <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ $value->fechaconfirmacion }}</td>
                  <td>{{ $value->nombreusuariovendedor }}</td>
                  <td>{{ $value->cliente }}</td>
                  <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828;"'?>>
                    {{ $value->monedasimbolo }} {{ number_format($totalpagado, 2, '.', '') }}
                  </td>
                  <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828;"'?>>
                    {{ $value->monedasimbolo }} {{ number_format($deudatotal, 2, '.', '') }}
                  </td>
                  <td>{{ $value->nombreFormapago }}</td>
                  <td>
                    @if($value->idestado==5)
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
                         @if($value->idestado==5)
                         <li><a href="javascript:;" onclick="modal({route:'generarletra/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                           <i class="fa fa-list-alt"></i> Detalle</a></li>
                         <li><a href="javascript:;" onclick="modal({route:'generarletra/{{ $value->id }}/edit?view=anular',size:'modal-fullscreen'})">
                            <i class="fa fa-ban"></i> Anular
                         </a></li>
                         @elseif($value->idestado==4)
                         <li><a href="javascript:;" onclick="modal({route:'generarletra/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
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