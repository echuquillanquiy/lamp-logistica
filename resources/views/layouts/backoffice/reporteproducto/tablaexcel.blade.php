<?php $idtienda = isset($_GET['tienda']) ? $_GET['tienda'] : usersmaster()->idtienda ?>
<table style="width:100%">
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="8">
        {{ $titulo }}
      </th>
    </tr>
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cod. venta</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Nombre</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Marca</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Categoria</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Talla</th>
<!--       <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Stock</th> -->
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">U. Medida</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">P. Minimo</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">P. Segurido</th>
    </tr>
  </thead>
  <tbody>
    @foreach($producto as $value)
    <tr>
      <td></td>
      <td style="border: 1px solid black;"> {{ str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) }} </td>
      <td style="border: 1px solid black;"> {{$value->nombreproducto}} </td>
      <td style="border: 1px solid black;"> {{$value->productonombremarca}}</td>
      <td style="border: 1px solid black;"> {{$value->productonombrecategoria}}</td>
      <td style="border: 1px solid black;"> {{$value->productonombretalla}} </td>
<!--       <td style="border: 1px solid black;"> {{stock_producto($idtienda,$value->id)['total']}}</td> -->
      <td style="border: 1px solid black;"> {{$value->productounidadmedida}}</td>
      <td style="border: 1px solid black;"> {{$value->preciotienda}} </td>
      <td style="border: 1px solid black;"> {{$value->precio}} </td>
    </tr>
    @endforeach
  </tbody>
</table>