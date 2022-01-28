<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>ORDEN DE COMPRA</title>
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
		}
		.razonsocial, .ruc{
			float:left;
		}
		.razonsocial{
			width:62%;
			height:190px;
		}
		.razonsocial > h1{
			margin-bottom:0px !important;
			padding-bottom:0px !important;
			text-align:center;
		}
		
		.razonsocial > h4{
			margin-top:0px !important;
			margin-bottom:17px !important;
			text-align:center;
		}
		.razonsocial > p.primero{
			margin-top:-10px !important;
			margin-bottom:0px !important;
			font-size:9px;
			text-align:center;
			padding:0px 10px;
			
		}
		.razonsocial > p{
			margin-top:0px !important;
			margin-bottom:0px !important;
			font-size:10px;
			text-align:center;
			
		}
		.ruc{
			width:38%;
			height:190px;
		}
		.ruc > .nro-telefono, .ruc > .nro-celular{
			width:48%;
			padding-top:15px;
			font-size:10px;
			display:inline-block;
			height:61px;
			overflow:hidden;
		}
		.cuadro-ruc{
			width:95%;
			height:125px;
			margin-top:37px;
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
            ->where('tienda.id',$compra->idtienda)
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
				<h4 style="margin-top:10px;">{{$tienda->nombre}}</h4>
				<div style="height:40px; background-color: rgb(229, 57, 53)" >
          <h3 style="margin-top:-10px; color:white; padding-top:9px;">ORDEN DE COMPRA</h3>
        </div> 
				<h4 style="margin-top:-10px;">{{ str_pad($compra->codigo, 8, "0", STR_PAD_LEFT) }}</h4>
			</div>
		</div>
	</div>
	<div class="logo">
		<img src="{{ url('/public/admin/img/general/baneer-pdf-min.jpg') }}" height=50px>
	</div>
	<div class="direcciones">
      <table cellspacing="0" class="table">
          <tr class="table-tr">
              <td class="table-tr-td numero" width="100%" colspan=4>Proveedor: {{ $compra->proveedornombre }}</td>
          </tr>
          <tr class="table-tr">
              <td class="table-tr-td numero" width="100%" colspan=4>RUC/DNI: {{ $compra->proveedoridentificacion }}</td>
          </tr>
          <tr class="table-tr">
              <td class="table-tr-td numero" width="100%" colspan=4>DIRECCIÓN: {{ $ubigeocliente->departamento }} - {{ $ubigeocliente->provincia }} - {{ $ubigeocliente->distrito }}  </td>
          </tr>
          <tr class="table-tr">
              <td class="table-tr-td numero" width="50%" colspan=2>COMPROBANTE : {{ $compra->nombreComprobante }}</td>
              <td class="table-tr-td numero" width="50%" colspan=2>SERIE - CORRELATIVO : {{ $compra->seriecorrelativo }}</td>
          </tr>
          <tr class="table-tr">
              <td class="table-tr-td numero" width="20%" >FECHA : {{ date_format(date_create($compra->fecharegistro),"d/m/Y H:m:s")  }}</td>
              <td class="table-tr-td numero" width="20%" >MONEDA :  {{ $moneda->nombre }}</td>
              <td class="table-tr-td numero" width="30%" >RESPONSABLE : {{ $responsable->nombre }}</td>
              <td class="table-tr-td numero" width="30%" >FORMA DE PAGO : {{ $formapago->nombre}}</td>
          </tr>
      </table>
		<br>
    <table cellspacing="0" class="table">
			
      <tr class="table-tr">
				<td class="table-tr-td titulo" width="3%">ITEM</td>
				<td class="table-tr-td titulo" width="120px">CODIGO</td>
				<td class="table-tr-td titulo">DESCRIPCIÓN</td>
				<td class="table-tr-td titulo" width="30px">CANT.</td>
				<td class="table-tr-td titulo" width="50px">P. UNIT.</td>
				<td class="table-tr-td titulo" width="50px">IMPORTE</td>
			</tr>
      <?php $item = 1 ?>
      <?php $sumatotal = 0 ?>
      @foreach($compradetalle as $value)
          <?php $montoimporte = $value->cantidad*$value->preciounitario ?>
          <?php 
            $sumatotal += $montoimporte;
          ?>
          <tr class="table-tr">
            <td class="table-tr-td" align="center">{{ $item }}</td>
            <td class="table-tr-td" align="center">{{ $value->codigoimpresion }}</td>
            <td class="table-tr-td">{{ $value->nombreproducto }}</td>
            <td class="table-tr-td">{{ $value->cantidad }}</td>
            <td class="table-tr-td">{{ $value->preciounitario }}</td>
            <td class="table-tr-td">{{ number_format($montoimporte, 2, ',', '') }}</td>
          </tr>
          <?php $item ++ ?>
      @endforeach
      <?php for($j = $item; $j<=6; $j++){?>
			<tr class="table-tr">
				<td class="table-tr-td" align="center"></td>
				<td class="table-tr-td" align="center"></td>
				<td class="table-tr-td"></td>
				<td class="table-tr-td"></td>
				<td class="table-tr-td"></td>
				<td class="table-tr-td">&nbsp;</td>
			</tr>
			<?php }?>
      
    </table>
    <br>

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
  <div class="totales">
		<div class="terminos">
      <table cellpadding="0" cellspacing="0"  width="550px">
        <tr>
            <td>
           </td>
           <td>
           </td>
        </tr>
      </table>
		</div>
    <table cellspacing="0" class="table2" align="right">
      <tr class="table-tr">
				<td class="table-tr-td titulo" align="center" width="50%">TOTAL</td>
				<td class="table-tr-td" align="center">{{ number_format($sumatotal,2) }}</td>
			</tr>
    </table>
  </div>
</body>
</html>