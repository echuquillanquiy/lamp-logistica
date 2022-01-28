<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>DOCUMENTO</title>
  <style>
    html,body {
        margin:10px;
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
      @if($tienda->imagen!='')
        <p class="text-center">
          <img src="{{url('public/admin/tienda/'.$tienda->imagen)}}" height=80px>
        </p>
      <br>
      @else
        <h1>{{$tienda->nombre}}</h1>
        <h4>REPUESTOS CHINESE</h4>
      @endif
			
			<p class="primero">{{$tienda->descripcion}}</p>
			<p><b>{{$tienda->direccion}}</b></p>
			<p><b>{{$tienda->correo}}</b></p>
			<p><b>{{$tienda->numerotelefono}}</b></p>
		</div>
		<div class="ruc">
			
			<div class="cuadro-ruc" style="text-align:center">
				<h4 style="margin-top:10px;">{{ $tienda->nombre }}</h4>
				<div style="height:40px; background-color: rgb(229, 57, 53)" >
          <h3 style="margin-top:-10px; color:white; padding-top:9px;">TRANSFERENCIA</h3>
        </div> 
				<h4 style="margin-top:-10px;">{{ str_pad($productotransferencia->codigo, 8, "0", STR_PAD_LEFT) }}</h4>
			</div>
		</div>
	</div>
	<div class="logo">
		<img src="{{ url('/public/admin/img/general/baneer-pdf-min.jpg') }}" height=50px>
	</div>
	<div class="direcciones">
		<table cellspacing="0" class="table">
			<tr class="table-tr">
		      <td class="table-tr-td numero" width="100%" colspan=2>DE: {{ $productotransferencia->tienda_origen_nombre }}
						 
 </td>
		  </tr>
        
      <tr class="table-tr">
		      <td class="table-tr-td numero" width="100%" colspan=2>PARA: {{ $productotransferencia->tienda_destino_nombre }}</td>
		  </tr>
			<tr class="table-tr">
          <?php
          $estado = '';
          if($productotransferencia->idestado==1){
              if($productotransferencia->idtiendadestino==usersmaster()->idtienda){
                  $estado = 'Solicitar Productos';
              }else{
                  $estado = 'Enviar Productos';
              }
          }elseif($productotransferencia->idestado==2){
              if($productotransferencia->idtiendadestino==usersmaster()->idtienda){
                  $estado = 'Recepcionar Productos';
              }else{
                  $estado = 'Enviar Productos';
              }
          }elseif($productotransferencia->idestado==3){
              $estado = 'Recepcionado';
          }  
          ?>
        
		      <td class="table-tr-td numero" width="50%" >ESTADO: {{ $estado  }}</td>
		      <td class="table-tr-td numero" width="50%" >MOTIVO: {{$productotransferencia->motivo}}</td>
		  </tr>
		</table>
		<br>
    <table cellspacing="0" class="table" style="float:left;">
			
      <tr class="table-tr">
				<td class="table-tr-td titulo" width="3%">ITEM1</td>
				<td class="table-tr-td titulo" width="50px">CODIGO</td>
				<td class="table-tr-td titulo">NOMBRE</td>
				<td class="table-tr-td titulo">MOTOR</td>
				<td class="table-tr-td titulo">MARCA</td>
				<td class="table-tr-td titulo">MODELO</td>
				<td class="table-tr-td titulo" width="50px">U. MEDIDA</td>
				<td class="table-tr-td titulo" width="30px">CANT.</td>
				<td class="table-tr-td titulo" width="30px">ENV.</td>
				<td class="table-tr-td titulo" width="30px">REC.</td>
			</tr>
      <?php $item = 1 ?>
      @foreach($detalletransferencia as $value)
      <tr class="table-tr">
				<td class="table-tr-td" align="center">{{ $item }}</td>
				<td class="table-tr-td" align="center">{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}}</td>
				<td class="table-tr-td">{{ $value->productonombre }}</td>
        <?php $productomotor = explode('/',$value->productomotor); ?>
				<td class="table-tr-td">{{ count($productomotor)>0?$productomotor[0]:$value->productomotor }}</td>
				<td class="table-tr-td">{{ $value->productomarca }}</td>
        <?php $productomodelo = explode('/',$value->productomodelo); ?>
				<td class="table-tr-td">{{ count($productomodelo)>0?$productomodelo[0]:$value->productomodelo }}</td>
				<td class="table-tr-td">{{ $value->unidadmedidanombre }}</td>
				<td class="table-tr-td">{{ $value->cantidad }}</td>
				<td class="table-tr-td">{{ $value->cantidadenviado }}</td>
				<td class="table-tr-td">{{ $value->cantidadrecepcion }}</td>
			</tr>
          
      <?php $item ++ ?>
      @endforeach
      
    </table>
	</div>
</body>
</html>