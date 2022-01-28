
<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/producto',
                method: 'POST',
                data:{
                    view: 'registrar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/producto') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Código</label>
            <div class="col-sm-9">
              <td class="with-form-control"><input type="text" id="codigoimpresion" class="form-control"/></td>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Nombre *</label>
            <div class="col-sm-9">
               <td class="with-form-control"><input type="text" id="nombreproducto" class="form-control"/></td>
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
            <label class="col-sm-3 col-form-label">P. Mínimo *</label>
            <div class="col-sm-9">
                <input type="text"  placeholder="0.00" id="preciotienda" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">P. Sugerido *</label>
            <div class="col-sm-9">
                <input type="text" placeholder="0.00" id="precio" class="form-control"/>
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
}); 
  
$("#idproductocategoria").select2({
    placeholder: "--  Seleccionar --"
});
  
$("#idproductotalla").select2({
    placeholder: "--  Seleccionar --"
});
</script>