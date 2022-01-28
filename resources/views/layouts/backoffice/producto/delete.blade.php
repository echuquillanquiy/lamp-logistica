<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/producto/{{$producto->id}}',
                method: 'DELETE',
                data:{
                    view: 'eliminar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/producto') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Código</label>
            <div class="col-sm-9">
                <input type="text" value="{{str_pad($producto->codigoimpresion, 6, "0", STR_PAD_LEFT)}}" id="codigoimpresion" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Nombre</label>
            <div class="col-sm-9">
                 <input type="text" value="{{$producto->nombreproducto}}" id="nombreproducto" class="form-control" disabled />
            </div>
        </div>
      <div class="form-group row">
            <label class="col-sm-3 col-form-label">Categoría</label>
            <div class="col-sm-9">
                <select id="idproductocategoria" class="form-control" disabled>
                  <option></option>
                      @foreach($categorias as $value)
                      <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Marca</label>
            <div class="col-sm-9">
                <select class="form-control" id="idproductomarca" disabled>
                      <option></option>
                      @foreach($marcas as $value)
                      <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                  </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Talla</label>
            <div class="col-sm-9">
                   <select id="idproductotalla" class="form-control" disabled>
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
                <input type="text" value="{{$producto->preciotienda}}" id="preciotienda" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">P. Sugerido</label>
            <div class="col-sm-9">
                <input type="text" value="{{$producto->precio}}" id="precio" class="form-control" disabled/>
            </div>
        </div>
        <div class="alert alert-warning">
						<i class="fa fa-info-circle"></i> ¿Esta seguro de eliminar?
				</div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Eliminar</button>
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