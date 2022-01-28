<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Enviar Guia de Remisión</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      
        <form action="javascript:;"  id="formfacturacionguiaremision" onsubmit="callback({
                                                                                      route: 'backoffice/facturacionguiaremision/{{ $facturacionguiaremision->id }}',
                                                                                      method: 'PUT',
                                                                                      carga: '#carga-facturacionguiaremision',
                                                                                      idform: 'formfacturacionguiaremision',
                                                                                      data:{
                                                                                          view: 'enviarsunat'
                                                                                      }
                                                                                  },
                                                                                  function(resultado){
                                                                                     location.href = '{{ url('backoffice/facturacionguiaremision') }}';                                                  
                                                                                  },this)"> 
            <div class="row">
                <div class="col-md-7">
                    <h4>General</h4>
                    <div class="form-group row">
                       <div class="col-sm-6">
                           <label>Remitente *</label>
                           <input type="text" class="form-control" value="{{ $facturacionguiaremision->emisor_ruc }} - {{ $facturacionguiaremision->emisor_razonsocial }}" disabled>
                        </div>
                       <div class="col-sm-6">
                            <label>Destinatario *</label>
                            <input type="text" class="form-control" value="{{ $facturacionguiaremision->despacho_destinatario_numerodocumento }} - {{ $facturacionguiaremision->despacho_destinatario_razonsocial }}" disabled>
                       </div>
                    </div>
                    <div class="form-group row">
                       <div class="col-sm-6">
                            <label>Punto de Partida *</label>
                            <input type="text" class="form-control" value="{{ $ubigeo_puntopartida->nombre }}" disabled>
                        </div>
                       <div class="col-sm-6">
                            <label>Punto de Llegada *</label>
                            <input type="text" class="form-control" value="{{ $ubigeo_puntollegada->nombre }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                       <div class="col-sm-6">
                            <label>Dirección de Partida *</label>
                            <input type="text" class="form-control" value="{{ $facturacionguiaremision->envio_direccionpartida }}" disabled>
                        </div>
                       <div class="col-sm-6">
                            <label>Dirección de Llegada *</label>
                            <input type="text" class="form-control" value="{{ $facturacionguiaremision->envio_direccionllegada }}" disabled>
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
                            <input class="form-control" type="date" value="{{ date_format(date_create( $facturacionguiaremision->guiaremision_fechaemision ), 'Y-m-d') }}" disabled>
                        </div>
                       <div class="col-sm-4">
                            <label>Fecha de Traslado *</label>
                            <input class="form-control" type="date" value="{{ date_format(date_create( $facturacionguiaremision->despacho_fechaemision ), 'Y-m-d') }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label>Nombre del Transportista *</label>
                            <input type="text" class="form-control" value="{{ $facturacionguiaremision->transporte_choferdocumento }} - {{ $chofer_user->apellidos }}, {{ $chofer_user->nombre }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label>Observación *</label>
                            <input type="text"  class="form-control" value="{{ $facturacionguiaremision->despacho_observacion }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
       
      
            <div class="table-responsive">
                <table class="table" id="tabla-facturacionguiaremision" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      <tr>
                        <th>Código</th>
                        <th>Nombre / Motor / Marca / Modelo</th>
                        <th>U. Medida</th>
                        <th width="80px">Cantidad</th>
                        <th width="110px">P. Unitario</th>
                        <th width="110px">P. Total</th>
                        <th width="10px" class="with-btn">
                        </th>
                      </tr>
                    </thead>
                    <tbody num="0">
                      @foreach($facturacionguiaremisiondetalles as $value)
                        <tr>
                          <td>{{ $value->codigo }}</td>
                          <td>{{ $value->descripcion }}</td>
                          <td>{{ $value->unidad }}</td>
                          <td>{{ $value->cantidad }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Total</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" id="totalventa" placeholder="0.00" disabled>
                    </div>
                </div>
            </div>
            </div> 
         </form> 
    </div>
   <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacionguiaremision').submit();">Enviar a Sunat</a>
    </div> 
</div>