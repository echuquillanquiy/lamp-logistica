<div class="modal-content">
  <form action="javascript:;"
        id="formcotizacionventa"
        onsubmit="callback({
            route: 'backoffice/venta/{{$venta->id}}',
            method: 'PUT',
            data:{
                view: 'anular'
            }
        },
        function(resultado){
             location.href = '{{ url('backoffice/venta') }}';                                                  
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Anular Venta</h4>
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
                            <input type="text" value="{{$venta->agencianombrecomercial}}" class="form-control" disabled>
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
                        <th>Nombre</th>
                        <th>Motor</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>U. Medida</th>
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
                              <td>{{$value->productomotor}}</td>
                              <td>{{$value->productomarca}}</td>
                              <td>{{$value->productomodelo}}</td>
                              <td>{{$value->unidadmedidanombre}}</td>
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
                <?php
                $letras = DB::table('ventaletra')
                    ->where('idventa',$venta->id)
                    ->orderBy('ventaletra.numero','asc')
                    ->get();
                $ventatipopagos = DB::table('ventatipopago')
                    ->where('idventa',$venta->id)
                    ->orderBy('ventatipopago.id','asc')
                    ->get();
                ?>
                @include('app.formapago',[
                    'idformapago' => $venta->idformapago,
                    'creditoiniciopago' => $venta->fp_credito_fechainicio,
                    'creditofrecuencia' => $venta->fp_credito_frecuencia,
                    'creditodias' => $venta->fp_credito_dias,
                    'creditoultimopago' => $venta->fp_credito_ultimafecha,
                    'idgarante' => $venta->fp_letra_garante,
                    'letrafechainicio' => $venta->fp_letra_fechainicio,
                    'letrafrecuencia' => $venta->fp_letra_frecuencia,
                    'letracuota' => $venta->fp_letra_cuotas,
                    'letras' => $letras,
                    'ventatipopagos' => $ventatipopagos,
                    'disabled' => 'true'
                ])
            </div>
        </div> 
        <div class="alert alert-warning">¿Esta seguro de anular?</div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Anular</button>
    </div> 
  </form>
</div>