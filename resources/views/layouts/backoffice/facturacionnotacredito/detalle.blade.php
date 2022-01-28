<div class="modal-content">
  <div id="carga-facturacionnotacredito">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Nota de Crédito</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
            <div class="row">
                <div class="col-md-7"> 
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Cliente</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="{{$facturacionnotacredito->cliente_numerodocumento}} - {{$facturacionnotacredito->cliente_razonsocial}}" disabled>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Dirección</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="{{$facturacionnotacredito->cliente_direccion}}" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Ubigeo</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="{{$facturacionnotacredito->cliente_ubigeo}} - {{$facturacionnotacredito->cliente_departamento}}, {{$facturacionnotacredito->cliente_provincia}}, {{$facturacionnotacredito->cliente_distrito}}" disabled>
                        </div>
                    </div> 
                </div>
                <div class="col-md-5"> 
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Agencia</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" value="{{$facturacionnotacredito->emisor_ruc}} - {{$facturacionnotacredito->emisor_razonsocial}}" disabled>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Moneda</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" value="{{$facturacionnotacredito->notacredito_tipomoneda}}" disabled>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tipo</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" value="{{$facturacionnotacredito->motivonotacreditonombre}}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Motivo</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" value="{{$facturacionnotacredito->notacredito_descripcionmotivo}}" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="tabla-facturacionnotacredito" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>U. Medida</th>
                        <th width="80px">Cantidad</th>
                        <th width="110px">P. Unitario</th>
                        <th width="110px">P. Total</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($facturacionnotacreditodetalles as $value)
                            <tr>
                              <td>{{$value->codigoproducto}}</td>
                              <td>{{$value->descripcion}}</td>
                              <td>{{$value->unidad=='NIU'?'UND':$value->unidad}}</td>
                              <td>{{$value->cantidad}}</td>
                              <td>{{$value->montopreciounitario}}</td>
                              <td>{{number_format($value->montopreciounitario*$value->cantidad, 2, '.', '')}}</td>   
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
                    <label class="col-sm-4 col-form-label">Sub Total</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{$facturacionnotacredito->notacredito_valorventa}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">IGV (18%)</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{$facturacionnotacredito->notacredito_totalimpuestos}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Total</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{$facturacionnotacredito->notacredito_montoimpuestoventa}}" disabled>
                    </div>
                </div>
            </div>
            </div> 
    </div>
  </div>
</div>