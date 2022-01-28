<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Guia de Remisión</title>
  <style>
    html,body {
        margin:10px;
        margin-top:0px;
        padding:0px;
				font-family:helvetica;
    }
		
		.cabecera{
			width:100%;
			height:190px;
/* 			background-color:green; */
		}
		.razonsocial, .ruc{
			float:left;
		}
		.razonsocial{
/* 			margin-top:5px; */
			width:62%;
			height:190px;
/* 			background-color:blue; */
		}
		.razonsocial > h1{
			margin-bottom:0px !important;
			padding-bottom:0px !important;
/* 			background-color:green; */
			text-align:center;
		}
		
		.razonsocial > h4{
			margin-top:0px !important;
			margin-bottom:17px !important;
/* 			background-color:white; */
			text-align:center;
		}
		.razonsocial > p.primero{
			margin-top:0px !important;
			margin-bottom:0px !important;
/* 			background-color:aqua; */
			font-size:9px;
			text-align:center;
			padding:0px 10px;
			
		}
		.razonsocial > p{
			margin-top:0px !important;
			margin-bottom:0px !important;
/* 			background-color:yellow; */
			font-size:10px;
			text-align:center;
			
		}
		.ruc{
			width:38%;
			height:190px;
/* 			background-color:brown; */
		}
		.ruc > .nro-telefono, .ruc > .nro-celular{
			width:48%;
			padding-top:15px;
/* 			background-color:green; */
			font-size:10px;
			display:inline-block;
			height:61px;
			overflow:hidden;
		}
		.cuadro-ruc{
			width:95%;
			height:125px;
			margin-top:37px;
/* 			background-color:pink; */
      border:solid  rgb(229, 57, 53) 3px:
		}
		.cuadro-ruc > h3{
			height:62px;
			margin-top:0px !important;
			margin-bottom:0px !important;
			line-height:40px !important;
		}
		.cuadro-ruc > h3{
			height:62px;
			margin-top:0px !important;
			margin-bottom:0px !important;
			line-height:10px !important;
		}

		.cuadro-ruc > h3:nth-child(2){
/* 			background-color:#e53935 ; */
/* 			color:white; */
			font-size:18px;
			padding:5px 0px;
			text-align:center;
		}
    
		.logo{
			width:100%;
			height:49px;
/* 			background-color:black; */
			padding:5px;
      margin-left: -20px;
		}
		.logo > img{
			padding:5px 15px;
		}
		.direcciones{
			width:100%;
/* 			background-color:white; */
			font-size:10px;
		}

		.table {
        width:99%;
				margin:auto;
    }
    .table2{
        width:28%;
    }
    .table-tr-th,
    .table-tr-td,{
        border: 1px solid #000000;
        padding:3px;
    }
    .table-tr-th{
        text-align:center;
        background-color:#0AB515;
        color:#fff;
    }
    .table-tr-header {
        padding-top:1px;
        padding-bottom:1px;
    }
		.titulo{
      background-color: rgb(229, 57, 53) ;
			color:white;
			text-align:center;
		}
		.motivo{
			width:99%;
			height:120px;
			margin:10px auto;
			border-radius:8px;
			border:solid 1px #000;
		}
		.motivo > p{
			padding-left:10px;
			font-weight:bold;
		}
		.cuadrado{
			width:20px;
			height:5px;
			border:1px solid #000;
		}
		.dividir{
			width:25%;
			height:40px;
			float:left;
		}
		.dividir > p{
			padding-left:10px;
		}
  </style>
</head>
<body>
	<div class="cabecera">
		<div class="razonsocial">
			<br>
      <?php $tienda = DB::table('tienda')
            ->where('tienda.id',$facturacionguiaremision->idtienda)
            ->first(); ?>
        <p class="text-center">
          <img src="{{url('public/admin/tienda/'.$tienda->imagen)}}" height=80px>
        </p>
        <br>
        <p class="primero">{{$tienda->descripcion}}</p>
        <p><b>{{$tienda->direccion}}</b></p>
        <p><b>{{$tienda->correo}}</b></p>
        <p><b>{{$tienda->numerotelefono}}</b></p>
		</div>
		<div class="ruc">			
			<div class="cuadro-ruc" style="text-align:center">
				<h4 style="margin-top:10px;">R.U.C {{ $facturacionguiaremision->emisor_ruc }}</h4>
				<div style="height:60px; background-color: rgb(229, 57, 53)" >
          <h3 style="margin-top:-15px; color:white; padding-top:9px;">
              GUIA DE REMISIÓN ELECTRÓNICA
          </h3>
        </div>
				<h4 style="margin-top:-10px;">{{ $facturacionguiaremision->guiaremision_serie }} - {{ str_pad($facturacionguiaremision->guiaremision_correlativo, 3, "0", STR_PAD_LEFT) }}</h4>
			</div>
		</div>
	</div>
	<div class="logo">
		<img src="{{ url('/public/admin/img/general/baneer-pdf-min.jpg') }}" height=50px>
	</div>
	<div class="direcciones">
		<table cellspacing="0" class="table">
			<tr class="table-tr">
		      <td class="table-tr-td numero" width="50%" colspan=2><b>RAZÓN SOCIAL</b>: {{ $facturacionguiaremision->despacho_destinatario_razonsocial }}</td>
		      <td class="table-tr-td numero" width="50%"><b>R.U.C.</b>: {{ $facturacionguiaremision->despacho_destinatario_numerodocumento }}</td>
		  </tr>
      <?php
        $ubigeo_partida = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionpartidacodigoubigeo)->first();
      
        $ubigeo_llegada = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionllegadacodigoubigeo)->first();
      
        $codigo_modulo = '';
        if ($facturacionguiaremision->idventa != 0) {
          $venta = DB::table('venta')->whereId($facturacionguiaremision->idventa)->first();
          $venta_facturacion = DB::table('facturacionboletafactura')
            ->join('facturacionboletafacturadetalle','facturacionboletafacturadetalle.idfacturacionboletafactura','facturacionboletafactura.id')
            ->where('facturacionboletafactura.idventa',$facturacionguiaremision->idventa)
            ->orWhere('facturacionboletafacturadetalle.idventa',$facturacionguiaremision->idventa)
            ->limit(1)
            ->first();
          $venta_facturacion_fac = '';
          if($venta_facturacion!=''){
            $venta_facturacion_fac =  ' / '.$venta_facturacion->venta_serie.' - '.$venta_facturacion->venta_correlativo;
          }
          $codigo_modulo = str_pad($venta->codigo, 8, "0", STR_PAD_LEFT).$venta_facturacion_fac;
          $origen = 'V-';
        }else if ($facturacionguiaremision->idfacturacionboletafactura != 0) {
          $facturacion = DB::table('facturacionboletafactura')->whereId($facturacionguiaremision->idfacturacionboletafactura)->first();
          $codigo_modulo =  $facturacion->venta_serie.' - '.$facturacion->venta_correlativo;
          $origen = '';
        }else if ($facturacionguiaremision->idtransferencia != 0){
          $origen = 'T-';
          $transferencia = DB::table('productotransferencia')->whereId($facturacionguiaremision->idtransferencia)->first();
          $codigo_modulo = str_pad($transferencia->codigo, 8, "0", STR_PAD_LEFT);
        }
      ?>  		
      <tr class="table-tr">
		      <td class="table-tr-td numero" width="50%" colspan=3><b>DIRECCIÓN DE PARTIDA:</b> {{ $facturacionguiaremision->envio_direccionpartida }}, {{ strtoupper(str_replace('/', ' - ', $ubigeo_partida->nombre)) }}</td>
		  </tr>     
      <tr class="table-tr">
		      <td class="table-tr-td numero" width="50%" colspan=3><b>DIRECCIÓN DE LLEGADA:</b> {{ $facturacionguiaremision->envio_direccionllegada }}, {{ strtoupper(str_replace('/', ' - ', $ubigeo_llegada->nombre)) }}</td>
		  </tr>
      <tr class="table-tr">
		      <td class="table-tr-td numero" width="100%" colspan=2><b>DOCUMENTOS: {{ $origen }} {{ $codigo_modulo }}</b></td>
          <td class="table-tr-td numero" width="100%" colspan=1><b>FECHA DE TRASLADO:</b> {{ date_format(date_create($facturacionguiaremision->envio_fechatraslado), 'd/m/Y') }}</td>

		  </tr>
      <tr class="table-tr">
		      <td class="table-tr-td numero" width="100%" colspan=3><b>MOTIVO DE TRASLADO:</b> {{ $facturacionguiaremision->envio_descripciontraslado }}</td>
		  </tr>
      <?php
        $chofer = DB::table('users')->where('users.identificacion', $facturacionguiaremision->transporte_choferdocumento)->first();
   
        $nombre_chofer = !is_null($chofer) ? $chofer->nombre.' '.$chofer->apellidos : '';
      ?>
      <tr class="table-tr">
		      <td class="table-tr-td numero" width="100%" colspan=2><b>TRANSPORTISTA:</b> {{ $facturacionguiaremision->transporte_choferdocumento }} - {{ $nombre_chofer }}</td>
          <td class="table-tr-td numero" width="100%" colspan=1>
              <b>Nro PLACA:</b> {{ $facturacionguiaremision->ventanumeroplaca }}
          </td>
		  </tr>
      <tr class="table-tr">
		      <td class="table-tr-td numero" width="100%" colspan=3><b>OBSERVACIÓN:</b> {{ $facturacionguiaremision->despacho_observacion }}</td>
		  </tr>
		</table>
		<br>
    <table cellspacing="0" class="table">
			
      <tr class="table-tr">
				<td class="table-tr-td titulo" width="3%">ITEM</td>
				<td class="table-tr-td titulo" width="120px">CODIGO</td>
				<td class="table-tr-td titulo">DESCRIPCIÓN</td>
				<td class="table-tr-td titulo" width="30px">CANT.</td>
			</tr>
      <?php $item = 1 ?>
      @foreach($facturacionguiaremisiondetalles as $value)
      <tr class="table-tr">
				<td class="table-tr-td" align="center">{{ $item }}</td>
				<td class="table-tr-td" align="center">{{ $value->codigo }}</td>
				<td class="table-tr-td">{{ $value->descripcion }}</td>
				<td class="table-tr-td">{{ $value->cantidad }}</td>
			</tr>
      <?php $item ++ ?>
      @endforeach
      
      <?php for($j = $item; $j<=30; $j++){?>
			<tr class="table-tr">
				<td class="table-tr-td" align="center"></td>
				<td class="table-tr-td" align="center"></td>
				<td class="table-tr-td"></td>
				<td class="table-tr-td">&nbsp;</td>
			</tr>
			<?php }?>      
    </table>
    <br>
	</div>
  <div class="totales">
		<div class="terminos">
      <table cellpadding="0" cellspacing="0"  width="550px">
        <tr>
            <td >
              <b>Cuentas Bancarias:</b>
                <?php 
                  $cuentabancariasoles = DB::table('bancocuentabancaria')
                                ->join('banco','banco.id','bancocuentabancaria.idbanco')
                                ->select(
                                    'bancocuentabancaria.*', 
                                    'banco.nombre as banco'   
                                )
                                ->whereRaw('(banco.id = 1) or (banco.id = 3)')
                                ->get();
                  foreach($cuentabancariasoles as $value){
                     echo "<br>";
                     echo  '- '.$value->banco.' '.$value->nombre.': '.$value->numerocuenta;
                  } 
                ?>
           </td>
           <td>
              <b>Terminos y condiciones:</b>
              <?php $terminos = explode('-', $tienda->terminoycondicion)?>
              <?php $cantidadterminos = count($terminos)?>

                <?php for($i = 0; $i<$cantidadterminos; $i++){?>

                  <span>- {{ $terminos[$i] }}</span><br>
              <?php } ?>
           </td>
        </tr>
      </table>
		</div>
  </div>	
  <style>
    .totales{
      width:99%;
      margin:auto;
      font-size:10px;
			position:relative;
    }
		.terminos{
			top:0;
			position:absolute;
			
		}
  </style>
</body>
</html>