function raiz() {
  return $('body').attr('url')+'/';
}
function table(param) {
		var route = param['route'].split('/');
		var pagina = param['route'];
		if(route.length>0) {
			 	pagina = route[0];
		}
		var getArray = param['route'].split('?');
    var arrayand = '?';
    if(getArray.length > 1) {
        arrayand = '&';
    }
  
    var table = $(param['idclass']).DataTable({
			processing: true,
      serverSide: true,
			dom: 'tlpi',
			lengthMenu: [[10, 25, 50, 10000], ["10 Filas", "25 Filas", "50 Filas", "Todas las Filas"]],
			ajax: raiz()+'/admin/'+param['route']+arrayand+'pagina='+pagina,
			bSort: false,
			columnDefs: [
				{ className:"mx-td-input","targets":param['btnclass'] },
				{ className:"mx-td-img","targets":param['imgclass'] }
			],
			destroy : true,
			drawCallback: function (settings ) {
         	$('.tablepopover').popover({
             	html: true,
             	trigger: 'manual',
             	placement: 'left',
             	content: function () {
                 	return '<div>'+$(this).attr('text')+'</div>';
             	}
         	})
     	}
		});
    $(param['idclass']+' tfoot th').each(function() {
        var title = $(this).text();
				if(title!='') {
						$(this).html('<input type="text" class="form-control mx-search-table" placeholder="Buscar...">');
				}
    });

    table.columns().every(function() {
        var that = this;
        $('input',this.footer()).on('keyup change',function() {
            if (that.search() !== this.value) {
                that
                    .search( this.value )
                    .draw();
            }
        });
    });
	
		$(param['idclass']).on('click', function(e){
        if($('.tablepopover').length>1){
           	$('.tablepopover').popover('hide');
						$(e.target).popover('toggle');
				}else{
				}
		});
}

function carga(param) {
        removecarga({input:param['input']});
        var inp = param['input'].split('#');
        if (param['color']=='danger') {
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert"><div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle" id="preloader-icon"></i><div id="mx-mensaje-subcarga-icon">'+param['mensaje']+'</div></div></div>');
        }else if (param['color']=='success') {
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert"><div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close"><i class="fa fa-check-circle" id="preloader-icon"></i><div id="mx-mensaje-subcarga-icon">'+param['mensaje']+'</div></div>');
        }else if (param['color']=='warning') {
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert"><div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle" id="preloader-icon"></i><div id="mx-mensaje-subcarga-icon">'+param['mensaje']+'</div></div></div>');
        }else if (param['color']=='info'){
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert"><div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close"><div id="preloader_3"></div><div id="mx-mensaje-subcarga">'+param['mensaje']+'</div></div></div>');
        }else if (param['color']=='default'){
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga"><div class="mx-contenedor-subcarga alert-'+param['color']+'"></div></div>');
        }
}
function removecarga(param) {
        $(param['input']).removeClass('mx-carga');
        var inp = param['input'].split('#');
        $('#mx-subcarga'+inp[1]).remove();
}
function forminput(param) {   
				var formData = new FormData();
				if(param['form']!=undefined) {
						$(param['form']+' input[type=file]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									formData.append($(this).attr('id'), $(this).prop('files')[0]);
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=file]')).each(function() {
											arrayelemet.push($(this).prop('files')[0]);
									});
								 formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=text]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=text]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=hidden]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=hidden]')).each(function() {

											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=password]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=password]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=number]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=number]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=date]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=date]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=time]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=time]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=radio]:checked').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=radio]:checked')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=checkbox]:checked').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=checkbox]:checked')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' select').each(function() {
								var countelemet = $('select#'+$(this).attr('id'));
								if( countelemet.length == 0) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' select#'+$(this).attr('id'))).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' textarea').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' textarea#'+$(this).attr('id'))).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
				}
					 
				$.each(param['data'], function( key, value ) {
            formData.append(key, value);
        });
        return formData;
}
function formerror(param) {
        var errorsHtml= '';
        var i=0
        $('.class-input').removeAttr('style');
        $('.error-input').remove();
        $.each(param['dato'].responseJSON.errors, function( key, value ) {
            $('input#'+key).addClass('class-input').css('border','1px solid #f54708');
            //$('input#'+key).after('<span class="error-input" style="color: #e22d02;float: left;margin-top: -10px;margin-bottom: 10px;width: 100%;text-align: left;">'+value+'</span>');
            $('select#'+key+' + span > span > span').addClass('class-input').css({'border':'1px solid #f54708','border-color':'#f54708 !important'});
            $('select#'+key+' + span > span > span > span > span').css('color','#f54708');
            //$('select#'+key+' + span').after('<span class="error-input" style="color: #e22d02;float: left;margin-top: -10px;margin-bottom: 10px;width: 100%;text-align: left;">'+value+'</span>');
            if (i==0) {
               errorsHtml += value; 
               $('#'+key).focus();
            }
            i=i+1;
        });
        return errorsHtml;
}
function callback(param={},callback,thisp=null) {
    
        $('.class-input').removeAttr('style');
        $('.error-input').remove();
        var pathArray = param['route'].split( '/' );
        var formnew = 'form'+Math.floor((Math.random() * 100) + 1);
  
        if(param['data']==undefined) {
            param['data'] = {};
        }
		
      if(param['idform']!=undefined) {
          formnew = param['idform']
      }
  
		if(thisp!=null){
      $(thisp).attr('id',formnew);
		}
		param['dato'] = forminput({form:'#'+formnew,data:param['data']});
				
        if(param['carga']==undefined) {
                param['carga'] = '#mx-carga';
                var countcarga = $(param['carga']).length;
                if(countcarga==0){
                  var mxcarga = param['carga'].split('#');
                  $('#'+formnew).after('<div id="'+mxcarga[1]+'"></div>');
                  $('#'+formnew).appendTo(param['carga']);
                }
        }
				if(param['method']=='PUT') {
						param['dato'].append('_method','PUT');
				}else if(param['method']=='DELETE') {
						param['dato'].append('_method','DELETE');
				}
	
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: raiz()+param['route'],
            type: 'POST',
            data: param['dato'],
			processData: false,
            contentType: false ,
            beforeSend: function (data) {
               carga({
                    input:param['carga'],
                    color:'info',
                    mensaje:'Procesando información, Espere por favor...'
                }); 
            },
            success:function(respuesta)
            {       
                if (respuesta.resultado=='CORRECTO') {
                    carga({
                        input:param['carga'],
                        color:'success',
                        mensaje:respuesta.mensaje
                    });
                    callback(respuesta);
                }else if (respuesta.resultado=='ERROR') {
                    carga({
                        input:param['carga'],
                        color:'danger',
                        mensaje:respuesta.mensaje
                    });
                }else{
                    callback(respuesta);
                }                    
            },
            error:function(respuesta)
            {
               if(respuesta.responseJSON.message=='The given data was invalid.'){
                  carga({
                      input:param['carga'],
                      color:'danger',
                      mensaje:formerror({dato:respuesta})
                  });
               }else if(respuesta.responseJSON.message=='Your email address is not verified.'){
                  carga({
                        input:param['carga'],
                        color:'success',
                        mensaje:'Ingresando, Espere por favor...'
                  });
                  callback({
                      resultado: 'ERRORCONFIRMEMAIL'
                  });
               }
               
            }
        });
}
function pagina(param) {
        if(param['route']=='') {
            return false;
        }
        if(param['result']==undefined) {
            param['result'] = '#cuerpo';
        }
  
        var pathArray = param['route'].split( '/' );
        var newPathname = "";
        for (i = 0; i < pathArray.length; i++) {
          newPathname = pathArray[i];
        }
        history.replaceState(null, null, window.location.pathname+'?pagina='+newPathname);
        $.ajax({
            url: raiz()+param['route'],
            type:"GET",
            beforeSend: function (data) {
                $(param['result']).html('<div class="mx-alert-load"><img src="'+raiz()+'public/libraries/app/img/loading.gif"></div>');  
            },
            success:function(respuesta){  
                $(param['result']).html(respuesta);
            }
        });
}
function load(result){
    $(result).html('<div style="text-align: center;width: 100%;"><img src="'+raiz()+'public/assets/plugins/app/img/loading.gif"></div>');  
}
	
function confirm(param){
	if(param['resultado']=='CORRECTO'){
		$(param['input']).html('<div class="cont-confirm">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">'+param['mensaje']+'</div></div>'+
                           '<div class="custom-form" style="text-align: center;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="confirm_cerrar(\''+param['cerrarmodal']+'\')">'+
                           '<i class="fa fa-check"></i> Aceptar</button></div>'); 
	}
}
function confirm_cerrar(cerrarmodal){
  $(cerrarmodal+' .close-reg').click();
}

  
function uploadfile(param){
	var imagen = param['image'];
	var style = 'style="'+
					'margin-top:10px;'+
                    'margin-left:10px;'+
					'font-size:18px;'+
					'background-color:#c12e2e;'+
					'padding:2px;'+
					'padding-left:9px;'+
					'padding-right:9px;'+
					'border-radius:15px;'+
					'color:#fff;'+
					'font-weight:bold;'+
					'cursor:pointer;'+
					'position: absolute;'+
					'z-index: 100;"';
	if(imagen!=undefined){
		if(imagen!=''){
			var src = param['ruta']+'/'+param['image'];
			var width = $(param['cont']).width();
	        var height = $(param['cont']).height();
      var imgant = param['input'].split('#');
			$(param['result'])
                      .html('<div '+style+' class="uploadfile-imagen-close" onclick="removeuploadfile(\''+param['result']+'\')">x</div>'+
                          	'<img src="'+src+'" style="max-width:'+width+'px;max-height:'+height+'px;position: relative;z-index: 1;">'+
                      		'<input type="hidden" value="'+param['image']+'" id="'+imgant[1]+'ant">');
		}
	}

    	$(param['input']).change(function(evt) {
            var files = evt.target.files;
            for (var i = 0, f; f = files[i]; i++) {
              if (!f.type.match('image.*')) {
                  continue;
              }
              var reader = new FileReader();
              reader.onload = (function(theFile) {
                  return function(e) {
                  	var width = $(param['cont']).width();
        			var height = $(param['cont']).height();
                    $(param['result'])
                      .html('<div '+style+' class="uploadfile-imagen-close" onclick="removeuploadfile(\''+param['result']+'\')">x</div>'+
                      		'<img src="'+e.target.result+'" style="max-width:'+width+'px;max-height:'+height+'px;position: relative;z-index: 1;">');
                  };
              })(f);
              reader.readAsDataURL(f);
            }
    	});
}
function removeuploadfile(result){
  $(result).html('<input type="hidden" id="imagenant"/>');
}

/* ---------------------------- scripts ----------------------------------------*/
function modal(param) {
        var key = $('.modal').length;
				if(key==0) {
					key = '';
				}
	
				if(param['carga']==undefined) {
						param['carga'] = '#mx-modal-carga';
				}
  
        if(param['size']==undefined) {
						param['size'] = '';
				}
	
				var idcarga = param['carga'].split('#');
				var classcarga = param['carga'].split('.');
				if(idcarga.length>0) {
						var cargamodal = idcarga[1];
				}else if(classcarga.length>0) {
						var cargamodal = classcarga[1];
				}
	
        var size = '';
        if(param['size']=='modal-fullscreen') {
						var size = 'modal-message';
				}else if(param['size']=='modal-mediumscren'){
            //$('#mx-modal'+key+'>.modal-dialog').css('max-width','900px');
        }
  
        $('body')
        .append('<div class="modal '+size+' fade" id="mx-modal'+key+'" style="overflow-y: auto;">'+
            '<div class="modal-dialog '+param['size']+'">'+
                    '<div id="'+cargamodal+'" class="'+cargamodal+'">'+
                    '<div id="mx-modal-cuerpo'+key+'" style="margin-left: 5px;margin-right: 5px;"></div>'+
                    '</div>'+
            '</div> '+
        '</div>');
        $('#mx-modal'+key).modal({backdrop: 'static', keyboard: false});
  				
				
		
        $.ajax({
            url: raiz()+'backoffice/'+param['route'],
            type: 'GET',
            beforeSend: function () {
                carga({
                    input:param['carga'],
                    color:'info',
                    mensaje:'Procesando información, Espere por favor...'
                });                 
            },
            success:function(dato){
                $('#mx-modal-cuerpo'+key).html(dato);
                removecarga({input:param['carga']}); 
            }
        });
        $('#mx-modal'+key).on('hide.bs.modal', function (e) {
          /*$(this).modal('toggle');*/
          $(this).remove();
          $('.modal-backdrop').remove();
        })
}

function removemodal(pthis) {
    var idmodal = $($(pthis).parents('.modal').first()).attr('id');
    console.log(idmodal)
    //$('#'+idmodal).modal('hide');
    $('#'+idmodal).remove();
    $('.modal-backdrop').remove();
}