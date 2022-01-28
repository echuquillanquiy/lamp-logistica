<div class="modal-content">
  <div id="carga-cotizacionventa">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Venta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-4"> 
            </div>
            <div class="col-md-4"> 
                <input class="form-control" type="text" value="{{str_pad($venta->codigo, 8, "0", STR_PAD_LEFT)}}" placeholder="Código de Cotización" style="height: 40px;font-size: 16px;text-align: center;" disabled/>
            </div>
        </div>
            <div class="row">
                <div class="col-md-7"> 
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Cliente</label>
                        <div class="col-sm-10">
                            <input type="text" value="{{$venta->cliente}}" class="form-control" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Dirección</label>
                        <div class="col-sm-10">
                            <input type="text" value="{{$venta->direccionusuariocliente}}" class="form-control" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Ubigeo</label>
                        <div class="col-sm-10">
                            <input type="text" value="{{$venta->ubigeoclientecodigo}} - {{$venta->ubigeoclientenombre}}" class="form-control" disabled>
                        </div>
                    </div>  
                </div>
                <div class="col-md-5"> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Agencia</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{$venta->agenciaruc}} - {{$venta->agencianombrecomercial}}" class="form-control" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Comprobante</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{$venta->tipocomprobantenombre}}" class="form-control" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha de confirmación</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{$venta->fechaconfirmacion}}" class="form-control" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="tabla-ventacotizacion" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th width="50px">Cantidad</th>
                        <th width="80px">P. Unitario</th>
                        <th width="80px">P. Total</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $totalfinal = 0 ?>
                        @foreach($ventadetalles as $value)
                            <?php 
                            $total = number_format(($value->cantidad*$value->preciounitario), 2, '.', '');
                            $totalfinal = $totalfinal+$total; 
                            ?>
                            <tr>
                              <td>{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}}</td>
                              <td>{{$value->productonombre}}</td>
                              <td>{{$value->cantidad}}</td>
                              <td>{{$value->preciounitario}}</td>
                              <td>{{$total}}</td>   
                           </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
            <div class="row">
            <div class="col-md-6">
                <?php
                $subtotal = number_format($totalfinal/1.18, 2, '.', '');
                $igv = number_format($totalfinal-$subtotal, 2, '.', '');
                ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Sub Total</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{$subtotal}}" placeholder="0.00" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">IGV (18%)</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{$igv}}" placeholder="0.00" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Total</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" value="{{number_format($totalfinal, 2, '.', '')}}" placeholder="0.00" disabled>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                @include('app.formapago',[
                    'modulo' => 'venta',
                    'idmodulo' => $venta->id,
                    'disabled' => 'true'
                ])
            </div>
        </div> 
    </div>
  </div>
</div>