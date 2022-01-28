<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Código de Barra 25x65</title>
</head>
<body class="horizontal">
  <style>
    body,* {
      margin:0px;
      margin-top:1px;
      margin-left:-1px;
      padding:0px;
      font-family:helvetica;
    }
    .containercodebar{
      margin-left:-90px;
      margin-top:80px;
      transform: rotate(90deg);
      /*background-color:red;*/
      position:absolute;
      top:1;
      width:254px;
      height:90px;
    }
    
    .nombre {
      position:absolute;
      font-size:12px;
      text-align:center;
      font-weight:bold;
      left:35px;
      top:-20px;
    }
    .barras{
      padding-left:30px;
    }
    .modelo{
      font-size:24px;
      text-align:left;
      font-weight:bold;
      margin-left:18px;
      margin-top:-10px;
      width: 100%;
      text-align:center;
      margin-right:15px;
    }
    .modelo2{
      font-size:12px;
      text-align:left;
      font-weight:bold;
      margin-left:18px;
      margin-top:1px;
      width: 100%;
      text-align:center;
      margin-right:15px;
    }
    .fecha{
      position:absolute;
      font-size:9px;
      font-weight:bold;
      right:15px;
      top:53px;
    }
    
    .kit{
      position:absolute;
      font-size:9px;
      font-weight:bold;
      left:20px;
      top:53px;
    }
    
    .kit2{
      position:absolute;
      font-size:9px;
      font-weight:bold;
      left:20px;
      top:60px;
    }
    
    .adicional{
      position:absolute;
      font-size:9px;
      font-weight:bold;
      right:15px;
      top:65px;
    }
    
    .ocultarcodigo{
      width:100px; 
      height:15px; 
      background: #fff; 
      z-index:100; 
      position:absolute;      
      left:85px;
      top: 25px;
    }
  </style>
  @php
      //dd($producto);
  @endphp
  @if ( !is_null($producto) )
      <div class="containercodebar">
          <div class="nombre">{{ $producto->productonombre }}</div>
          <div class="barras">
            <img width="200px" height="40px" 
                 src='https://barcode.tec-it.com/barcode.ashx?data={{str_pad($producto->codigoimpresion, 6, "0", STR_PAD_LEFT)}}&code=Code39FullASCII&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=&qunit=Mm&quiet=0'/>
            <div class="ocultarcodigo">
            </div>
          </div>
          <div class="modelo">{{ str_pad($producto->codigoimpresion, 6, "0", STR_PAD_LEFT) }}</div>
          <div class="modelo2">{{ strtoupper($producto->codigoadicional) }}</div>
          <div class="kit">{{ strtoupper($producto->productounidadmedida) }}</div>
          <div class="fecha">{{ date("d/m/Y") }}</div>
          <div class="adicional">{{ isset($_GET['codigoadicional'])?$_GET['codigoadicional']:''}}</div>
      </div>
  @else
      <div class="containercodebar">
          <div class="nombre">00000000000 000000000000</div>
          <div class="barras">
              <img width="200px" height="40px" 
                   src='https://barcode.tec-it.com/barcode.ashx?data=000000&code=Code39FullASCII&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=&qunit=Mm&quiet=0'/>
              <div class="ocultarcodigo">
              </div>
          </div>
          <div class="modelo">000000</div>
          <div class="modelo2">000</div>
          <div class="kit">000</div>
          <div class="fecha">00/00/0000</div>
          <div class="adicional">{{ isset($_GET['codigoadicional'])?$_GET['codigoadicional']:''}}</div>
      </div>
  @endif
</body>
</html>

