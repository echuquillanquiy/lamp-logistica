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
      margin-left:-95px;
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
      font-size:9px;
      text-align:center;
      font-weight:bold;
      left:30px;
      top:-15px;
      height:20px;
      width:149px;
    }
    .barras{
      padding-left:30px;
      margin-top:10px;
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
  </style>
  <?php //dd($producto); ?>
  <div class="containercodebar">
    <div class="nombre">{{$producto->nombreproducto}}</div>
    <div class="barras">
      <img width="150px" height="60px" 
           src='https://barcode.tec-it.com/barcode.ashx?data={{str_pad($producto->codigoimpresion, 6, "0", STR_PAD_LEFT)}}&code=Code39FullASCII&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=&qunit=Mm&quiet=0'/>
    </div>
  </div>
</body>
</html>

