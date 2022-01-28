<?php $idtienda = isset($_GET['tienda']) ? $_GET['tienda'] : usersmaster()->idtienda ?>
<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th>Cod. venta</th>
      <th>Nombre</th>
      <th>Marca</th>
      <th>Categoria</th>
      <th>Talla</th>
<!--       <th>Stock</th> -->
      <th>U. Medida</th>
      <th>P. Minimo </th>
      <th>P. Segurido</th>
    </tr>
  </thead>
  <tbody>
    @foreach($producto as $value)
    <tr>
      <td> {{ str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) }} </td>
      <td> {{$value->nombreproducto}} </td>
<td> {{$value->productonombremarca}} </td>
      <td> {{$value->productonombrecategoria}} </td>
      <td> {{$value->productonombretalla}} </td>
<!--       <td> {{stock_producto($idtienda,$value->id)['total']}}</td> -->
      <td> {{$value->productounidadmedida}}</td>
      <td> {{$value->preciotienda}} </td>
      <td> {{$value->precio}} </td>
    </tr>
    @endforeach
  </tbody>
</table>