<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th width="100px">Tienda - Caja</th>
      <th>Fecha de Apertura</th>
      <th>Fecha de Cierre</th>
      <th>Responsable</th>
      <th>Recepción</th>
      <th>Apertura</th>
      <th>Cierre Efectivo</th>
      <th>Estado</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($aperturacierre as $value)
    <tr>
      <td>{{$value->tiendanombre}} - {{$value->cajanombre}}</td>
      <td>{{$value->fechaconfirmacion!=''?$value->fechaconfirmacion:'---'}}</td>
      <td>{{$value->fechacierreconfirmacion!=''?$value->fechacierreconfirmacion:'---'}}</td>
      <td>{{$value->responsableapellidos}}, {{$value->responsablenombre}}</td>
      <td>{{$value->recepcionapellidos}}, {{$value->recepcionnombre}}</td>
      <td>
        {{$monedasoles->simbolo}} {{$value->montoasignarsoles}} - {{$monedadolares->simbolo}} {{$value->montoasignardolares}}
      </td>
      <td>
        @if($value->fechacierreconfirmacion!='')
            {{$monedasoles->simbolo}} {{$value->montocierresoles}} - {{$monedadolares->simbolo}} {{$value->montocierredolares}}
        @else
            ---
        @endif
      </td>
      <td>
        @if($value->idestado==1)
          Apertura En Proceso
        @elseif($value->idestado==2)
          Apertura Pendiente 
        @elseif($value->idestado==3)
          Aperturado
        @elseif($value->idestado==4)
          Cierre Pendiente
        @elseif($value->idestado==5)
          Caja Cerrada
        @endif
      </td>
      <td>
        <a href="javascript:;" onclick="modal({route:'reporteaperturacierre/{{ $value->id }}/edit?view=pdfdetalle',size:'modal-fullscreen'})">
            <span class="badge badge-pill badge-warning"><i class="fa fa-file-alt"></i> PDF Detalle</span>            
        </a>
        <a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=detallecierre'})">
            <span class="badge badge-pill badge-info"><i class="fa fa-list-alt"></i> Detalle</span>            
        </a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>