<div class="modal-content">
  <div id="carga-facturacioncomunicacionbaja">
    <div class="modal-header">
      <h4 class="modal-title">Reenviar Comunicación de Baja</h4>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      
      <style>
        .mx-select2 .select2-container {
            text-align: center;
        }
        .mx-select2 .select2-container .select2-selection--single, .select2-container--default .select2-selection--multiple {
            height: 40px;
        }
        .mx-select2 .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            font-size: 16px;
        }
      </style>
      <form action="javascript:;" id="formfacturacioncomunicacionbaja" onsubmit="callback({
                                                                                  route: 'backoffice/facturacioncomunicacionbaja/{{ $facturacioncomunicacionbaja->id }}',
                                                                                  method: 'PUT',
                                                                                  carga: '#carga-facturacioncomunicacionbaja',
                                                                                  idform: 'formfacturacioncomunicacionbaja',
                                                                                  data:{
                                                                                      view: 'reenviarsunat',
                                                                                  }
                                                                              },
                                                                              function(resultado){
                                                                                  location.reload();                                     
                                                                              },this)"> 
      <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <input class="form-control" type="text" id="correlativo" value="{{ $facturacioncomunicacionbaja->comunicacionbaja_correlativo }}" placeholder="Correlativo" disabled style="height: 40px;font-size: 16px;text-align: center;"/>
        </div>
        <div class="col-md-4"></div>
      </div>
        <div class="row">
            <div class="col-md-7"> 
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Agencia</label>
                    <div class="col-sm-10">                          
                        <input type="text" id="agencia" class="form-control" value="{{ $facturacioncomunicacionbaja->emisor_ruc }} - {{ $facturacioncomunicacionbaja->emisor_razonsocial }}" disabled>
                    </div>
                </div>   
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Cliente</label>
                    <div class="col-sm-10">
                        <input type="text" id="cliente" class="form-control" value="{{ $facturacioncomunicacionbaja->cliente_numerodocumento }} - {{ $facturacioncomunicacionbaja->cliente_razonsocial }}" disabled>
                    </div>
                </div>   
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Moneda</label>
                    <div class="col-sm-10">
                        <input type="text" id="moneda" class="form-control" value="{{ $facturacioncomunicacionbaja->venta_tipomoneda }}" disabled>
                    </div>
                </div>   
               <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Descripcion de Motivo</label>
                    <div class="col-sm-10">
                        <input type="text" id="motivo" class="form-control" value="{{ $facturacioncomunicacionbaja->descripcionmotivobaja }}" disabled>
                    </div>
                </div>   
            </div>
            <div class="col-md-5">  
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Fecha Emisión</label>
                    <div class="col-sm-8">
                        <input type="text" id="fechaemision" class="form-control" value="{{ $facturacioncomunicacionbaja->venta_fechaemision }}" disabled>
                    </div>
                </div>  
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Comprobante</label>
                    <div class="col-sm-8">
                        <input type="text" id="tipodocumento" class="form-control" value="Factura" disabled>
                    </div>
                </div>  
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Serie - Correlativo</label>
                    <div class="col-sm-8">
                        <input type="text" id="seriecorrelativo" class="form-control" value="{{ $facturacioncomunicacionbaja->serie }} - {{ $facturacioncomunicacionbaja->correlativo }}" disabled>
                    </div>
                </div>  
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="tabla-facturacioncomunicacionbaja" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>U. Medida</th>
                    <th width="80px">Cantidad</th>
                    <th width="110px">P. Unitario</th>
                    <th width="110px">P. Total</th>
                  </tr>
                </thead>
                <tbody num="0">
                  @foreach($facturacionboletafacturadetalles as $value)
                    <tr>
                      <td>{{ $value->codigoproducto }}</td>
                      <td>{{ $value->descripcion }}</td>
                      <td>{{ $value->unidad }}</td>
                      <td>{{ $value->cantidad }}</td>
                      <td>{{ $value->montopreciounitario }}</td>
                      <td>{{ number_format($value->montopreciounitario * $value->cantidad, 2, '.',  '') }} </td>
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
        <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacioncomunicacionbaja').submit();">Reenviar Sunat</a>
      </div> 
  </div>
</div>