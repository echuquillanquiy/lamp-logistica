<div class="modal-content">
  <div id="carga-notadevolucion">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Nota de Devolución</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="removemodal('#carga-notadevoluciondetalle')">×</button>
    </div>
    <div class="modal-body">
            <div class="table-responsive">
                <table class="table" style="margin-bottom: 5px;">
                     <thead class="thead-dark">
                        <tr>
                          <th width="90px">Código</th>
                          <th>Descripción</th>
                          <th>U. Medida</th>
                          <th width="40px">Cant.</th>
                          <th width="80px">P. Unitario</th>
                          <th width="50px">Cant.</th>
                          <th width="10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($ventadetalles as $value)
                        <tr>
                            <td>{{ str_pad($value['codigoproducto'], 6, "0", STR_PAD_LEFT) }}</td>
                            <td>{{$value['nombreproducto']}}</td>
                            <td>{{$value['unidad']}}</td>
                            <td>{{$value['cantidad']}}</td>
                            <td>{{$value['preciounitario']}}</td>
                            <td>{{$value['preciototal']}}</td>
                            <td class="with-btn" width="10px"><a href="javascript:;" class="btn btn-warning" 
                                onclick="agregarproducto(
                                     {{$value['id']}},
                                     '{{ str_pad($value['codigoproducto'], 6, "0", STR_PAD_LEFT) }}',
                                     '{{$value['nombreproducto']}}',
                                     
                                     '{{$value['preciounitario']}}',
                                     '{{$value['unidad']}}',
                                     '{{$value['cantidad']}}',
                                     $('#idmotivonotacredito').val()
                                 );" id="btnseleccionar{{$value['id']}}">
                              <i class="fas fa-plus"></i> Seleccionar</a></td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
    </div>
  </div>
</div>
<script> 
</script>