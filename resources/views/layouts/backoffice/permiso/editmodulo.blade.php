<div class="modal-content">
<form action="javascript:;" 
          onsubmit="selectestadomodulo(this)"> 
    <div class="modal-header">
        <h4 class="modal-title">{{ $permiso->description }} / Módulos</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
          <table class="table table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="10px"></th>
                <th colspan="5">Nombre</th>
                <th width="10px"></th>
              </tr>
            </thead>  
            <tbody>
              @foreach($modulos as $value)
              <?php $countrolesmodulos = DB::table('rolesmodulo')->where('idmodulo',$value->id)->count();?>
              <?php $countmodulos = DB::table('modulo')->where('idmodulo',$value->id)->count();?>
                <tr>
                  <td>{{ $value->orden }}</td>
                  <td width="10px"><i class="{{ $value->icono }}"></i></td>
                  <td colspan="4">{{ $value->nombre }}</td>
                  <td class="with-checkbox">
												<div class="checkbox checkbox-css">
                          <?php $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$value->id)->limit(1)->first(); ?>
                          <input type="checkbox" class="idpermiso" id="idpermiso{{ $value->id }}" value="{{ $value->id }}" <?php echo $rolesmodulo!='' ? 'checked':'' ?>>
													<label for="idpermiso{{ $value->id }}">&nbsp;</label>
												</div>
									</td>
                </tr>
                <?php
                $submodulos = DB::table('modulo')
                  ->where('idmodulo',$value->id)
                  ->where('idestado',1)
                  ->orderBy('orden','asc')
                  ->get();
                ?>
                @foreach($submodulos as $subvalue)
                <?php $countrolesmodulos = DB::table('rolesmodulo')->where('idmodulo',$subvalue->id)->count();?>
                <?php $countmodulos = DB::table('modulo')->where('idmodulo',$subvalue->id)->count();?>
                <tr>
                  <td></td>
                  <td></td>
                  <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}</td>
                  <td colspan="3">{{ $subvalue->nombre }}</td>
                  <td class="with-checkbox">
												<div class="checkbox checkbox-css">
                          <?php $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$subvalue->id)->limit(1)->first(); ?>
                          <input type="checkbox" class="idpermiso" id="idpermiso{{ $value->id }}{{ $subvalue->id }}" value="{{ $subvalue->id }}" <?php echo $rolesmodulo!='' ? 'checked':'' ?>>
													<label for="idpermiso{{ $value->id }}{{ $subvalue->id }}">&nbsp;</label>
												</div>
									</td>
                </tr>
                <?php
                $subsubmodulos = DB::table('modulo')
                  ->where('idmodulo',$subvalue->id)
                  ->orderBy('orden','asc')
                  ->where('idestado',1)
                  ->get();
                ?>
                @foreach($subsubmodulos as $subsubvalue)
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}</td>
                  <td colspan="2">{{ $subsubvalue->nombre }}</td>
                  <td class="with-checkbox">
												<div class="checkbox checkbox-css">
                          <?php $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$subsubvalue->id)->limit(1)->first(); ?>
                          <input type="checkbox" class="idpermiso" id="idpermiso{{ $value->id }}{{ $subvalue->id }}{{ $subsubvalue->id }}" value="{{ $subsubvalue->id }}" <?php echo $rolesmodulo!='' ? 'checked':'' ?>>
                          <label for="idpermiso{{ $value->id }}{{ $subvalue->id }}{{ $subsubvalue->id }}">&nbsp;</label>
												</div>
									</td>
                </tr>
                @endforeach
                @endforeach
              @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>
<script>
function selectestadomodulo(pthis){
    var idmodulos = '';
    $('.idpermiso[type=checkbox]:checked').each(function() {
        idmodulos = idmodulos+','+$(this).val();
    });
    callback({
        route: 'backoffice/permiso/{{ $permiso->id }}',
        method: 'PUT',
        data: {
            'view' : 'editarmodulo',
            'idmodulos' : idmodulos
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/permiso') }}';                                                                            
    },pthis)
}

</script>