<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/producto/{{$producto->id}}',
                method: 'PUT',
                data:{
                    view: 'editar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/producto') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Editar Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Código</label>
            <div class="col-sm-9">
                <input type="text" value="{{$producto->codigoimpresion}}" id="codigoimpresion" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Nombre *</label>
            <div class="col-sm-9">
                <input type="text" value="{{$producto->nombreproducto}}" id="nombreproducto" class="form-control" />
            </div>
        </div>
       <div class="form-group row">
            <label class="col-sm-3 col-form-label">Categoría *</label>
            <div class="col-sm-9">
                <select id="idproductocategoria" class="form-control" >
                  <option></option>
                      @foreach($categorias as $value)
                      <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                </select>
            </div>
        </div>
      <div class="form-group row">
            <label class="col-sm-3 col-form-label">Marca  *</label>
            <div class="col-sm-9">
                <select class="form-control" id="idproductomarca" >
                      <option></option>
                      @foreach($marcas as $value)
                      <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                  </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Talla *</label>
            <div class="col-sm-9">
                   <select id="idproductotalla" class="form-control">
                     <option></option>
                      @foreach($tallas as $value)
                      <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                   </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">P. Mínimo</label>
            <div class="col-sm-9">
                <input type="text" value="{{$producto->preciotienda}}" id="preciotienda" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">P. Sugerido *</label>
            <div class="col-sm-9">
                <input type="text" value="{{$producto->precio}}" id="precio" class="form-control"/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>

<script>
$("#idproductomarca").select2({
    placeholder: "--  Seleccionar --"
}).val({{$producto->idproductomarca}}).trigger('change'); 
  
$("#idproductocategoria").select2({
    placeholder: "--  Seleccionar --"
}).val({{$producto->idproductocategoria}}).trigger('change');
  
$("#idproductotalla").select2({
    placeholder: "--  Seleccionar --"
}).val({{$producto->idproductotalla}}).trigger('change');
</script>