<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Comprobante</title>
  <style>
    html,body {
        margin:10px;
        margin-top:10px;
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
			margin-top:0px !important;
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
			padding:5px;
      margin-left: -20px;
		}
		.logo > img{
			padding:5px 15px;
		}
		.direcciones{
			width:100%;
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
  <table>
    <tr>
      <td>
        <div class="cabecera">
          <div class="razonsocial">
            <br>
            <?php 
            
            $tienda = DB::table('tienda')
                  ->where('tienda.id',$facturacionboletafactura->idtienda)
                  ->first(); 

            ?>
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
              <h4 style="margin-top:10px;">R.U.C {{ $facturacionboletafactura->emisor_ruc }}</h4>
              <div style="height:40px; background-color: rgb(229, 57, 53)" >
                <h3 style="margin-top:-10px; color:white; padding-top:9px;">
                          @if($facturacionboletafactura->venta_tipodocumento=='03')
                              BOLETA ELECTRÓNICA
                          @elseif($facturacionboletafactura->venta_tipodocumento=='01')
                              FACTURA ELECTRÓNICA
                          @endif
                </h3>
              </div> 
              <h4 style="margin-top:-10px;">{{ $facturacionboletafactura->venta_serie }} - {{ str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT) }}</h4>
            </div>
          </div>
        </div>
        <div class="logo">
          <img src="{{ url('/public/admin/img/general/baneer-pdf-min.jpg') }}" height=50px>
        </div>
        <div class="direcciones">
          <table cellspacing="0" class="table">
            <tr class="table-tr">
                <td class="table-tr-td numero" width="100%" colspan=4>Señor(a): {{ $facturacionboletafactura->cliente_razonsocial }}</td>
            </tr>

            <tr class="table-tr">
                <td class="table-tr-td numero" width="100%" colspan=4>R.U.C. : {{ $facturacionboletafactura->cliente_numerodocumento }}</td>
            </tr>
            <tr class="table-tr">
                <td class="table-tr-td numero" width="100%" colspan=4>DIRECCIÓN: {{ $facturacionboletafactura->cliente_direccion }}, {{ $facturacionboletafactura->cliente_departamento }} - {{ $facturacionboletafactura->cliente_provincia }} - {{ $facturacionboletafactura->cliente_distrito }}  </td>
            </tr>

            <tr class="table-tr">
                <td class="table-tr-td numero" width="30%" >FECHA: {{ date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y H:m:s")  }}</td>
                <td class="table-tr-td numero" width="20%" >MONEDA : {{ $facturacionboletafactura->venta_tipomoneda }}</td>
                <?php 
                      $usuariovendedor = DB::table('venta')
                      ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                      ->where('venta.id',$facturacionboletafactura->idventa)
                      ->first();
                      $numeroplaca = '';
                      if($usuariovendedor==''){
                          $usuariovendedor = DB::table('users')
                            ->where('users.id',$facturacionboletafactura->idusuarioresponsable)
                            ->first();
                      }else{
                          $numeroplaca = $usuariovendedor->numeroplaca;
                      }
              
                  ?>
                <td class="table-tr-td numero" width="30%" >VENDEDOR : {{ $usuariovendedor->nombre }}</td>
                <td class="table-tr-td numero" width="20%" >Nro PLACA : {{ $numeroplaca }}</td>
            </tr>
          </table>
          <br>
          <table cellspacing="0" class="table">

            <tr class="table-tr">
              <td class="table-tr-td titulo" width="3%">ITEM</td>
              <td class="table-tr-td titulo" width="120px">CODIGO</td>
              <td class="table-tr-td titulo">DESCRIPCIÓN</td>
              <td class="table-tr-td titulo" width="50px">U. MEDIDA</td>
              <td class="table-tr-td titulo" width="30px">CANT.</td>
              <td class="table-tr-td titulo" width="50px">P. UNIT.</td>
              <td class="table-tr-td titulo" width="50px">IMPORTE</td>
            </tr>
            <?php $item = 1 ?>
            @foreach($facturacionboletafacturadetalles as $value)
            <tr class="table-tr">
              <td class="table-tr-td" align="center">{{ $item }}</td>
              <td class="table-tr-td" align="center">{{ $value->codigoproducto }}</td>
              <td class="table-tr-td">{{ $value->descripcion }}</td>
              <td class="table-tr-td">{{$value->unidad=='NIU'?'UND':$value->unidad}}</td>
              <td class="table-tr-td">{{ $value->cantidad }}</td>
              <td class="table-tr-td">{{ $value->montopreciounitario }}</td>
              <td class="table-tr-td">{{number_format($value->montopreciounitario*$value->cantidad, 2, '.', '')}}</td>
            </tr>
            <?php $item ++ ?>
            @endforeach

            <?php for($j = $item; $j<=29; $j++){?>
            <tr class="table-tr">
              <td class="table-tr-td" align="center"></td>
              <td class="table-tr-td" align="center"></td>
              <td class="table-tr-td"></td>
              <td class="table-tr-td"></td>
              <td class="table-tr-td"></td>
              <td class="table-tr-td"></td>
              <td class="table-tr-td">&nbsp;</td>
            </tr>
            <?php }?>

            <tr class="table-tr">
              <td class="table-tr-td" colspan="7"><b>SON : {{ $facturacionboletafactura->leyenda_value }} </b></td>
            </tr>
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
            <table cellpadding="0" cellspacing="0"  width="350px"  style="margin-top:-10px;">
              <tr>
                  <td style="width:260px;">
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
                      ?><br>
                    <b>Terminos y condiciones:</b>
                    <?php $terminos = explode('-', $tienda->terminoycondicion)?>
                    <?php $cantidadterminos = count($terminos)?>

                      <?php for($i = 0; $i<$cantidadterminos; $i++){?>

                        <span>- {{ $terminos[$i] }}</span><br>
                    <?php } ?>
                 </td>
                 <td style="padding:0px;">
                   <img src="<?php echo $facturacionboletafactura->venta_qr ?>" width="150px" style="margin-top:0px;">
                 </td>
                 <td style="width:110px;">
                  Autorizado mediante Resolucion de
                  Intendencia: N°032-005
                  Representación Impresa del
                  documento electrónico.
                </td>
              </tr>
            </table>
          </div>
          <table cellspacing="0" class="table2" align="right">
            <tr class="table-tr">
              <td class="table-tr-td titulo" align="center" width="50%">SUBTOTAL</td>
              <td class="table-tr-td" align="center">{{$facturacionboletafactura->venta_valorventa}}</td>
            </tr> 
           <tr class="table-tr">
              <td class="table-tr-td titulo" align="center" width="50%">IGV</td>
              <td class="table-tr-td" align="center">{{$facturacionboletafactura->venta_totalimpuestos}}</td>
            </tr>
            <tr class="table-tr">
              <td class="table-tr-td titulo" align="center" width="50%">TOTAL</td>
              <td class="table-tr-td" align="center">{{$facturacionboletafactura->venta_montoimpuestoventa}}</td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>
        
</body>
</html>