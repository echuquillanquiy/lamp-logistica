<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/trabajador/{{ $usuario->id }}',
            method: 'PUT',
            data: {
                view : 'editpermiso',
                permisos: selectpermisos()
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/trabajador') }}';                                        
        },this)" enctype="multipart/form-data"> 
    <div class="modal-header">
        <h4 class="modal-title">{{ $usuario->nombre }} / Editar Acceso</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Usuario *</label>
            <div class="col-sm-8">
                <input type="text" id="usuario" value="{{ $usuario->usuario }}" class="form-control">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Cambiar Contraseña</label>
            <div class="col-sm-8">
                <input type="text" id="password" class="form-control">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Estado *</label>
            <div class="col-sm-8">
                <select id="idestado" class="form-control">
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                  </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-12">
                <table class="table table-hover" id="tabla-contenido-rol" style="margin-bottom: 10px;">
                    <thead class="thead-dark">
                        <tr>
                            <th>Tienda</th>
                            <th>Permiso</th>
                            <th class="with-btn" width="10px"><a href="javascript:;" onclick="agregar_rol()" class="btn btn-warning width-60 m-r-2"><i class="fa fa-plus"></i></a></th>
                        </tr>
                    </thead>
                    <tbody num="0">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>   

<script>
  
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$usuario->idestado}}).trigger("change");
  
@foreach($role_users as $value)
    agregar_rol('{{$value->idtienda}}','{{$value->role_id}}');
@endforeach
  
function agregar_rol(idtienda=null,idrol=null){
    var num = $("#tabla-contenido-rol tbody").attr('num');
    var html = '<tr id="'+num+'">'+
                  '<td class="with-form-control">'+
                      '<select id="idtienda'+num+'" class="form-control">'+
                          '<option></option>'+
                          @foreach($tiendas as $value)
                              '<option value="{{ $value->id }}" >{{ $value->nombre }}</option>'+
                          @endforeach
                      '</select>'+
                  '</td>'+
                  '<td class="with-form-control">'+
                      '<select id="idrol'+num+'" class="form-control">'+
                          '<option></option>'+
                          @foreach($roles as $value)
                              '<option value="{{ $value->id }}" >{{ $value->description }}</option>'+
                          @endforeach
                      '</select>'+
                  '</td>'+
                  '<td class="with-btn" nowrap="">'+
                      '<a href="javascript:;" onclick="eliminar_rol('+num+')" class="btn btn-danger width-60 m-r-2"><i class="fa fa-times"></i></a>'+
                  '</td>'+
              '</tr>';
    $("#tabla-contenido-rol").append(html);
    $("#tabla-contenido-rol tbody").attr('num',parseInt(num)+1);
    $("#idtienda"+num).select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val(idtienda).trigger("change");
    $("#idrol"+num).select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val(idrol).trigger("change");
}
function eliminar_rol(num){
    $("#tabla-contenido-rol tbody tr#"+num).remove();
}
function selectpermisos(){
    var data = '';
    $("#tabla-contenido-rol tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idtienda = $("#idtienda"+num).val();
        var idrol = $("#idrol"+num).val();
        data = data+'&'+idtienda+','+idrol;
    });
    return data;
}
</script>