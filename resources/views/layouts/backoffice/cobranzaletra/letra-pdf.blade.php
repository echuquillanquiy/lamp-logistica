<?php $usersmaster = usersmaster() ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>LETRA</title>
  <style>
    html, body {
      margin:       10px;
      margin-top:   10px;
      padding:      0px;
      font-family:  helvetica;
      font-size:    10px;
    }    
    .text-vertical{
      position:absolute;
      top:100px;
      left:-95px;
      width: 330px;
      height: 140px;
      transform: rotate(-90deg);
      background-color: green;
      font-size: 8px;
      padding:5px;
/*       background-color: blue; */
/*       transform: rotate(-90deg); */
    }
    .contenido{
      position:absolute;
      top:-5px;
      left:155px;
      width: 600px;
      height: 330px;
      padding:5px;
      background-color: red;
    }
    /*.text-invertido{
      font-size: 8px;
    }*/
    .tabla{
      border: 1px solid #000;
      border-radius: 10px;
      border-spacing: 0px;
      padding: 0px;
    }
    .tabla2{
      border: 1px solid #000;
      border-top: 0px;
      border-right: 0px;
      border-radius: 10px;
      border-spacing: 0px;
      padding: 0px;
    }
    .tabla2 tbody tr td{
      border-top: 1px solid #000;
      border-right: 1px solid #000;
    }
    .top-left{
      border-top-left-radius: 8px;
    }
    .top-right{
      border-top-right-radius: 8px;
    }
    .bottom-left{
      border-bottom-left-radius: 8px;
    }
    .bottom-right{
      border-bottom-right-radius: 8px;
    }
    .titulo-tabla{
      font-size: 9px;
      text-align: center;
    }
    .size-cont{
      font-size: 8px;
    }
    .text-left{
      text-align: left;
    }
    .text-center{
      text-align: center;
    }
    .descripcion{
      font-size: 7px:
    }    
  </style>
</head>
<body>

  
<!-- EJEMPLO DOS -->
  <div class="text-vertical">
      MUNDO 
      CLAUSULAS ESPECIALES:
      <br>
      1.- En caso de mora, esta Letra de Cambio generará las tasas de interés compensatorio y moratorio más altas que la ley lo permita a su último tenedor.
      <br>
      2.- El plazo de su vencimiento podrá ser prorrogado por el Tenedor, por el plazo que éste señale, sin que sea necesario la intervención del obligado principal ni de los solidarios.
      <br>
      3.- Esta letra de Cambio no requiere ser protestada por falta de pago.
      <br>
      4.- Su importe, debe ser pagado sólo en la misma moneda que expresa este título valor.
  </div>
  <div class="contenido">
    <table width="100%">
      <tbody>
        <tr>
          <td colspan="2">
            <table class="tabla" width="100%">
              <tbody>
                <tr>
                  <td width="50px">
                    <center>
                      <img src="{{url('public/admin/tienda/'.$usersmaster->tiendalogo)}}" height="30px">
                    </center>
                  </td>
                  <td width="300px">
                    <div>
                      <center>
                        <strong><em>{{ $usersmaster->tiendanombre }}</em></strong> <br>
                        <div class="descripcion">
                          {{$usersmaster->tiendadireccion}}<br>
                          {{$usersmaster->tiendacorreo}} - {{$usersmaster->tiendanumerotelefono}}<br>
                          {{$usersmaster->tiendadescripcion}}
                        </div>                        
                      </center>
                    </div>                    
                  </td>
                  <td width="50px">
                    <div class="text-center">
                        <strong><em> D.O.I. 20562831020</em></strong>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>

        <tr>  
          <td colspan="2">
            <table class="tabla2" width="100%">
              <tbody>
                <tr>
                  <td class="titulo-tabla top-left"><b>NUMERO DE LETRA</b></td>
                  <td class="titulo-tabla"><b>REF DEL GIRADOR</b></td>
                  <td class="titulo-tabla"><b>FECHA DE GIRO</b></td>
                  <td class="titulo-tabla"><b>LUGAR DE GIRO</b></td>
                  <td class="titulo-tabla"><b>FECHA DE VENCIMIENTO</b></td>
                  <td class="titulo-tabla top-right"><b>MONEDA E IMPORTE</b></td>
                </tr>
                <tr>
                  <td class="text-center bottom-left" rowspan="2" style="color:red;">0689</td>
                  <td class="text-center" rowspan="2">F02-1335-1724</td>
                  <td class="titulo-tabla"><b>DIA / MES / AÑO</b></td>
                  <td class="text-center" rowspan="2">LIMA</td>
                  <td class="titulo-tabla"><b>DIA / MES / AÑO</b></td>
                  <td class="text-center bottom-right" rowspan="2">S/. 1018.75</td>
                </tr>
                <tr>
                  <td class="text-center">24/02/2020</td>
                  <td class="text-center">23/03/2020</td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <spam class="size-cont">
              Por esta LETRA DE CAMBIO, se servirán(n) pagar incondicionalmente a la Orden de: <strong><em>{{ $usersmaster->tiendanombre }}.</em></strong>            
            </spam>
          </td>
        </tr>

        <tr>
          <td colspan="2"> 
            <spam class="size-cont">
              La cantidad de:
            </spam>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <table class="tabla" width="100%">
              <tbody>
                <tr>
                  <td style="padding:5px;">
                    MIL DIECIOCHO CON 75/100 SOLES.
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>

        <tr>        
          <td colspan="2"> 
            <spam class="size-cont">
              En el siguiente lugar de pago, o con cargo en la cuenta corriente del Banco: 
            </spam>
          </td>
        </tr>

        <tr>
          <td width="330px">
            <table class="tabla" width="100%">
              <tbody>
                <tr>
                  <td class="titulo-tabla text-left" style="padding:5px 5px 0px 5px;" colspan="2"><b>Aceptante:</b> QUISPE SULCA DIANA CAROLINA</td>
                </tr>
                <tr>
                  <td class="titulo-tabla text-left" style="padding:0px 5px 0px 5px;" colspan="2"><b>Domicilio:</b> JR. LAMBAYEQUE Nº 628 - SAN RAMON<</td>
                </tr>
                <tr>
                  <td class="titulo-tabla text-left" style="padding:0px 5px 0px 5px;" colspan="2"><b>Localidad:</b> PUNO</td>
                </tr>
                <tr>
                  <td class="titulo-tabla text-left" style="padding:0px 5px 5px 5px;"><b>RUC/DNI:</b> 10455688448</td>
                  <td class="titulo-tabla text-left" style="padding:0px 5px 5px 5px;"><b>Teléfono:</b> 950777429</td>
                </tr>
              </tbody>
            </table>
          </td>
          <td>
            <table class="tabla2" width="100%">
              <tbody>
                <tr>
                  <td colspan="4" class="top-left top-right size-cont">Importe a debitar en la siguiente cuenta  del Banco que se indica</td>
                </tr>
                <tr>
                  <td class="titulo-tabla" width="30px"><b>BANCO</b></td>
                  <td class="titulo-tabla" width="30px"><b>OFICINA</b></td>
                  <td class="titulo-tabla" width="50px"><b>NUMERO DE CUENTA</b></td>
                  <td class="titulo-tabla" width="30px"><b>D.C.</b></td>
                </tr>
                <tr>
                  <td class="bottom-left">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td class="bottom-right">&nbsp;</td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>

        <tr>
          <td>
            <table class="tabla" width="100%">
              <tbody>
                <tr>
                  <th class="titulo-tabla text-left" style="padding:5px;"><b>Aval Permanente:</b></th>
                  <td colspan=3>VALDIVIA SANTI EDILER</td>
                </tr>
                <tr>
                  <th class="titulo-tabla text-left" style="padding:5px;"><b>Domicilio:</b></th>
                  <td colspan=3>JR. LAMBAYEQUE Nº 475 - SAN RAMON</td>
                </tr>
                <tr>
                  <th class="titulo-tabla text-left" style="padding:5px;"><b>Localidad:</b></th>
                  <td>PUNO</td>                  
                  <th class="titulo-tabla text-left">Teléfono:</th>
                  <td>950777429</td>
                </tr>
                <tr>
                  <th class="titulo-tabla text-left" style="padding:5px;"><b>D.O.I:</b></th>
                  <td>1042178637</td>
                  <th class="titulo-tabla text-left">Firma:</th>
                  <td>---------</td>
                </tr>
              </tbody>
            </table>          
          </td>
          <td>
            <table class="tabla" width="100%">
              <tbody>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td> _____________________________________ </td>
                </tr>
                <tr>
                  <td class="size-cont">Nombre del Representante (s) Legal (es)</td>
                </tr>
                <tr>
                  <td class="size-cont">D.O.I.: 20562831020</td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>    
  </div>
  
</body>
</html>