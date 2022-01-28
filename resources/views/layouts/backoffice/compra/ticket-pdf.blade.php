<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Ticket</title>
  <style>
    html,body {
       	margin: 0px;
			padding: 15px;
			font-size: 10px;
      font-weight: bold;
      font-family: Courier;
    }
	
		.razonsocial, .ruc{
			float:left;
		}
		.razonsocial{
			width:165px;
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
        width:160px;  
        font-size:8px;
        text-align:center;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }
    .table-tr-th,
    .table-tr-td,{
        padding:1px;
    }
    .table-tr-th{
        text-align:center;
        font-size:8px;
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
    .datocomprobante {
        text-align: left;
      }
  </style>
</head>
		<div class="razonsocial">
   <?php $tienda = DB::table('tienda')
            ->where('tienda.id',$compra->idtienda)
            ->first(); ?>
        <p class="text-center">
          <img src="{{url('public/admin/tienda/'.$tienda->imagen)}}" height=40px width= 100px>
        </p>
      <br>
        <p>{{strtoupper($tienda->nombre)}}</p>
        <p >{{$tienda->correo}}</p>
        <p>{{$tienda->numerotelefono}}</p>
      <div class="datocomprobante">
        CÓDIGO: {{ str_pad($compra->codigo, 8, "0", STR_PAD_LEFT) }}<br>
        FECHA: {{ date_format(date_create($compra->fecharegistro),"d/m/Y H:m:s")  }} <br>
        SREÑOR(A):  {{$compra->proveedornombre }}<br>
        DNI/RUC: {{$compra->proveedoridentificacion }}
      </div>
      <table cellspacing="1" >
       <tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      <tr class="table-tr">
				<th style="white-space: nowrap;">CANT.</th>
				<th style="white-space: nowrap;text-align: center;width:60px;">P.UNIT.</th>
				<th style="white-space: nowrap;text-align: center;width:60px;">P.TOTAL</th>
			</tr>
        <tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      <?php $sumatotal = 0 ?>
      @foreach($compradetalle as $value)
      <?php $montoimporte = $value->cantidad*$value->preciounitario ?>
      <?php 
        $sumatotal += $montoimporte;
      ?>
        <tr>
				<td colspan="3" style="text-align: left;"> {{ $value->nombreproducto }}</td>
			</tr>
      <tr class="table-tr">
				<td style="white-space: nowrap;text-align: center;">{{ $value->cantidad }}</td>
				<td style="white-space: nowrap;text-align: center;">{{ $value->preciounitario }}</td>
				<td style="white-space: nowrap;text-align: center;">{{ number_format($montoimporte, 2, ',', '') }}</td>
			</tr>
      @endforeach
        <tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      </table>
      <p style="white-space: nowrap;text-align: center; font-size:12px;">TOTAL S/: {{ number_format($sumatotal,2) }}</p><br>
      <p style="white-space: nowrap;text-align: center; font-size:11px;">¡GRACIAS POR SU COMPRA!</p>
		</div>
</body>
</html>