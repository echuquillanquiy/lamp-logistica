<div class="modal-content">
<form action="javascript:;"  id="form-usuario"
          onsubmit="callback({
            route: 'backoffice/trabajador',
            method: 'POST',
            idform: 'form-usuario',
            data:{
                view: 'registrar'
            }
        },
        function(resultado){
            location.reload();                                                                          
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Agregar Trabajador</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Usuarios *</label>
            <div class="col-sm-8">
                <select id="idusuario" class="form-control">
                    <option></option>
                    @foreach($usuarios as $value)
                        <option value="{{ $value->id }}">
                            @if($value->idtipopersona==1)
                                {{ $value->apellidos }}, {{ $value->nombre }}
                            @elseif($value->idtipopersona==3)
                                {{ $value->apellidos }}, {{ $value->nombre }}
                            @else
                                {{ $value->nombre }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" id="botonenviar" class="btn btn-success" onclick="$('#form-usuario').submit();">Guardar Cambios</a>
    </div>
</form>  
</div>            
<script>
    $('#idestado').select2();
    $('#idusuario').select2({
        placeholder: '---Seleccionar usuario---',
        minimumResultsForSearch: -1,
        minimumInputLength: 2,
    });
</script>