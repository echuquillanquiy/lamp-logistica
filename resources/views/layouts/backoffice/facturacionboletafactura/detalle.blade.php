<div class="modal-content">
  <div id="carga-facturacionboletafactura">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Comprobante</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
            <div class="row">
                <div class="col-md-7"> 
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Cliente</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="{{$facturacionboletafactura->cliente_numerodocumento}} - {{$facturacionboletafactura->cliente_razonsocial}}" disabled>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Dirección</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="{{$facturacionboletafactura->cliente_direccion}}" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Ubigeo</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="{{$facturacionboletafactura->cliente_ubigeo}} - {{$facturacionboletafactura->cliente_departamento}}, {{$facturacionboletafactura->cliente_provincia}}, {{$facturacionboletafactura->cliente_distrito}}" disabled>
                        </div>
                    </div> 
                </div>
                <div class="col-md-5"> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Agencia</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" value="{{$facturacionboletafactura->emisor_ruc}} - {{$facturacionboletafactura->emisor_razonsocial}}" disabled>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Moneda</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" value="{{$facturacionboletafactura->venta_tipomoneda}}" disabled>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Comprobante</label>
                        <div class="col-sm-8">
                            @if($facturacionboletafactura->venta_tipodocumento=='03')
                            <input class="form-control" type="text" value="BOLETA" disabled>
                            @elseif($facturacionboletafactura->venta_tipodocumento=='01')
                            <input class="form-control" type="text" value="FACTURA" disabled>
                            @endif 
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="tabla-facturacionboletafactura" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      <tr>
                        <th width="80px">Cod. Venta</th>
                        <th width="80px">Código</th>
                        <th>Nombre</th>
                        <th>U. Medida</th>
                        <th width="80px">Cantidad</th>
                        <th width="110px">P. Unitario</th>
                        <th width="110px">P. Total</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($facturacionboletafacturadetalles as $value)
                            <tr>
                              <td>{{$value->ventacodigo!=''?str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT):''}}</td>
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
                        <input class="form-control" type="text" value="{{$facturacionboletafactura->venta_valorventa}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">IGV (18%)</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{$facturacionboletafactura->venta_totalimpuestos}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Total</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{$facturacionboletafactura->venta_montoimpuestoventa}}" disabled>
                    </div>
                </div>
            </div>
            </div> 
    </div>
  </div>
</div>