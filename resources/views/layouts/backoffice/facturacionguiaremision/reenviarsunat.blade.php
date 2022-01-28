<div class="modal-content">
  <div id="carga-facturacionguiaremision">
    <div class="modal-header">
        <h4 class="modal-title">Reenviar Guia de Remisión</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <div class="container">
        
      </div>
      <form action="javascript:;" id="formfacturacionguiaremision" onsubmit="callback({
                                                                                      route: 'backoffice/facturacionguiaremision/{{ $facturacionguiaremision->id }}',
                                                                                      method: 'PUT',
                                                                                      carga: '#carga-facturacionguiaremision',
                                                                                      idform: 'formfacturacionguiaremision',
                                                                                      data:{
                                                                                          view: 'reenviarsunat',
                                                                                      }
                                                                                  },
                                                                                  function(resultado){
                                                                                      location.reload();                                                     
                                                                                  },this)"> 
            <input type="hidden" id="idventa">
            <input type="hidden" id="idfacturacion">
            <div class="row">
                <div class="col-md-7">
                    <h4>General</h4>
                    <div class="form-group row">
                       <div class="col-sm-6">
                            <label>Remitente *</label>
                            <input type="text" class="form-control" value="{{ $facturacionguiaremision->emisor_ruc }} {{ $facturacionguiaremision->emisor_razonsocial }}" disabled>
                        </div>
                       <div class="col-sm-6">
                            <label>Destinatario *</label>
                            <input type="text" class="form-control" value="{{ $facturacionguiaremision->despacho_destinatario_numerodocumento }} - {{ $facturacionguiaremision->despacho_destinatario_razonsocial }}" disabled>
                       </div>
                    </div>
                    <div class="form-group row">
                       <div class="col-sm-6">
                            <label>Punto de Partida *</label>
                            <input type="text" class="form-control" value="{{ $ubigeo_partida->nombre }}" disabled>
                        </div>
                       <div class="col-sm-6">
                            <label>Punto de Llegada *</label>
                            <input type="text" class="form-control" value="{{ $ubigeo_llegada->nombre }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                       <div class="col-sm-6">
                            <label>Dirección de Partida *</label>
                            <input type="text" id="direccionpartida" class="form-control" value="{{ $facturacionguiaremision->envio_direccionpartida }}" disabled>
                        </div>
                       <div class="col-sm-6">
                            <label>Dirección de Llegada *</label>
                            <input type="text" id="direccionllegada" class="form-control" value="{{ $facturacionguiaremision->envio_direccionllegada }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-5"> 
                    <h4>Detalle de Traslado</h4>
                    <div class="form-group row">
                       <div class="col-sm-4">
                            <label>Motivo *</label>
                             <input type="text" class="form-control" value="{{ $facturacionguiaremision->envio_descripciontraslado }}" disabled>
                        </div>
                       <div class="col-sm-4">
                            <label>Fecha de Emisión *</label>
                            <input class="form-control" type="date" id="fechaemision" value="{{ date('Y-m-d') }}" disabled>
                        </div>
                       <div class="col-sm-4">
                            <label>Fecha de Traslado *</label>
                            <input class="form-control" type="date" id="fechatraslado" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label>Nombre del Transportista *</label>
                            <input type="text" class="form-control" value="{{ $facturacionguiaremision->transporte_numerodocumento }} - {{ $facturacionguiaremision->transporte_razonsocial }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label>Observación *</label>
                            <input type="text" id="observacion" class="form-control" value="{{ $facturacionguiaremision->despacho_observacion }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="table-responsive">
                <table class="table" id="tabla-facturacionguiaremision" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>U. Medida</th>
                        <th width="10px">Stock</th>
                        <th width="80px">Cantidad</th>
                        </th>
                      </tr>
                    </thead>
                    <tbody num="0">
                      @foreach($facturacionguiaremisiondetalles as $value)
                      <?php $stock = stock_producto($facturacionguiaremision->idtienda, $value->idproducto);?>
                       <tr>
                         <td>{{ $value->codigo }}</td>
                         <td>{{ $value->descripcion }}</td>
                         <td>{{ $value->unidad }}</td>
                         <td>{{ $stock['total'] }}</td>
                         <td>{{ $value->cantidad }}</td>
                       </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
   
      </form>
    </div>
    <div class="modal-footer">
      <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacionguiaremision').submit();">Reenviar a Sunat</a>
    </div> 
  </div>
</div>