<?php
$monedasoles = DB::table('moneda')->whereId(1)->first();
$monedadolares = DB::table('moneda')->whereId(2)->first();
$efectivosoles = efectivo($idaperturacierre,1);
$efectivodolares = efectivo($idaperturacierre,2);
?>

<div class="card-body">
  <ul class="nav nav-pills">
    <li class="nav-items">
      <a href="#nav-pills-tab-1-resumen" data-toggle="tab" class="nav-link active">
        <span class="d-sm-none">Pills 1</span>
        <span class="d-sm-block d-none">Resumen</span>
      </a>
    </li>
    <li class="nav-items">
      <a href="#nav-pills-tab-2-detalle" data-toggle="tab" class="nav-link">
        <span class="d-sm-none">Pills 2</span>
        <span class="d-sm-block d-none">Detalle</span>
      </a>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade active show" id="nav-pills-tab-1-resumen">
      <div class="table-responsive">
        <table class="table table-cierrecaja">
            <tbody>
              <tr class="thead-dark">
                <th colspan="3" style="text-align: center;">EFECTIVO</th>
              </tr>
              <tr>
                <td class="td-ingreso">Apertura</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_apertura'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_apertura'] }}</td>
              </tr>
              <tr>
                <td class="td-ingreso">Movimientos</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosdiversos_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosdiversos_efectivo'] }}</td>
              </tr>
              <tr>
                <td class="td-ingreso">Ventas</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ventas_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ventas_efectivo'] }}</td>
              </tr>
              <tr>
                <td class="td-ingreso">Cobranza Creditos</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzacreditos_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzacreditos_efectivo'] }}</td>
              </tr>
              <tr>
                <td class="td-ingreso">Cobranza Letras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzaletras_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzaletras_efectivo'] }}</td>
              </tr>
              <tr>
                <td class="td-ingreso">Devolución de Compras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compradevoluciones_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compradevoluciones_efectivo'] }}</td>
              </tr>
              <tr>
                <td class="td-ingreso">Saldo de Usuario</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosuserssaldo_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosuserssaldo_efectivo'] }}</td>
              </tr>
                <tr class="thead-dark">
                  <th colspan="3" style="text-align: center;">SALDO</th>
                </tr>
                <tr>
                  <td class="td-ingreso">Movimientos</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosdiversos_saldo'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosdiversos_saldo'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Ventas</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ventas_saldo'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ventas_saldo'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Cobranza Creditos</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzacreditos_saldo'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzacreditos_saldo'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Cobranza Letras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzaletras_saldo'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzaletras_saldo'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Devolución de Compras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compradevoluciones_saldo'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compradevoluciones_saldo'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Saldo de Usuario</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosuserssaldo_saldo'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosuserssaldo_saldo'] }}</td>
                </tr>
              <tr>
                <td class="td-ingreso-total"><b>Tota Ingresos</b></td>
                <td class="td-ingreso-total"><b>{{ $monedasoles->simbolo }} {{ number_format($efectivosoles['total_efectivo_ingresos']+$efectivosoles['total_saldo_ingresos'], 2, '.', '') }}</b></td>
                <td class="td-ingreso-total"><b>{{ $monedadolares->simbolo }} {{ number_format($efectivodolares['total_efectivo_ingresos']+$efectivosoles['total_saldo_ingresos'], 2, '.', '') }}</b></td>
              </tr>
              <tr>
                <td class="td-egreso">Movimientos </td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_egresosdiversos'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_egresosdiversos'] }}</td>
              </tr>
              <tr>
                <td class="td-egreso">Compras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compras'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compras'] }}</td>
              </tr>
              <tr>
                <td class="td-egreso">Nota de Devolución</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_notadevoluciones'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_notadevoluciones'] }}</td>
              </tr>
              <tr>
                <td class="td-egreso">Pago Creditos</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_pagocreditos'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_pagocreditos'] }}</td>
              </tr>
              <tr>
                <td class="td-egreso">Pago Letras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_pagoletras'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_pagoletras'] }}</td>
              </tr>
                <tr>
                  <td class="td-egreso">Saldo de Usuario</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_egresosuserssaldo'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_egresosuserssaldo'] }}</td>
                </tr>
              <tr>
                <td class="td-egreso-total"><b>Total Salidas</b></td>
                <td class="td-egreso-total"><b>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_egresos'] }}</b></td>
                <td class="td-egreso-total"><b>{{ $monedadolares->simbolo }} {{ $efectivodolares['total_egresos'] }}</b></td>
              </tr>
              <tr>
                <td class="td-subtotal"><b>Total Efectivo</b></td>
                <td class="td-subtotal"><b>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_efectivo'] }}</b></td>
                <td class="td-subtotal"><b>{{ $monedadolares->simbolo }} {{ $efectivodolares['total_efectivo'] }}</b></td>
              </tr>
              <tr class="thead-dark">
                <th colspan="3" style="text-align: center;">DEPOSITO</th>
              </tr>
                <tr>
                  <td class="td-ingreso">Movimientos</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosdiversos_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosdiversos_deposito'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Ventas</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ventas_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ventas_deposito'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Cobranza Creditos</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzacreditos_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzacreditos_deposito'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Cobranza Letras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzaletras_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzaletras_deposito'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Devolución de Compras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compradevoluciones_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compradevoluciones_deposito'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Saldo de Usuario</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_egresosuserssaldo_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_egresosuserssaldo_deposito'] }}</td>
                </tr>
                <tr class="thead-dark">
                  <th colspan="3" style="text-align: center;">CHEQUE</th>
                </tr>
                <tr>
                  <td class="td-ingreso">Movimientos</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosdiversos_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosdiversos_cheque'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Ventas</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ventas_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ventas_cheque'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Cobranza Creditos</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzacreditos_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzacreditos_cheque'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Cobranza Letras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzaletras_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzaletras_cheque'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Devolución de Compras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compradevoluciones_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compradevoluciones_cheque'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Saldo de Usuario</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_egresosuserssaldo_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_egresosuserssaldo_cheque'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso-total"><b>Sub Total</b></td>
                  <td class="td-ingreso-total"><b>{{ $monedasoles->simbolo }} {{ number_format($efectivosoles['total_deposito_ingresos']+$efectivosoles['total_cheque_ingresos'], 2, '.', '') }}</b></td>
                  <td class="td-ingreso-total"><b>{{ $monedadolares->simbolo }} {{ number_format($efectivodolares['total_deposito_ingresos']+$efectivodolares['total_cheque_ingresos'], 2, '.', '') }}</b></td>
                </tr>
                <tr>
                  <td class="td-total"><b>Total Venta</b></td>
                  <td class="td-total"><b>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_final'] }}</b></td>
                  <td class="td-total"><b>{{ $monedadolares->simbolo }} {{ $efectivodolares['total_final'] }}</b></td>
                </tr>
            </tbody>
          </table>
      </div>
    </div>
    <div class="tab-pane fade" id="nav-pills-tab-2-detalle">
      <ul class="nav nav-pills">
          <li class="nav-items">
            <a href="#nav-pills-tab-1-saldousers-soles" data-toggle="tab" class="nav-link active">
              <span class="d-sm-none">Pills 1</span>
              <span class="d-sm-block d-none">Soles</span>
            </a>
          </li>
          <li class="nav-items">
            <a href="#nav-pills-tab-2-saldousers-dolares" data-toggle="tab" class="nav-link">
              <span class="d-sm-none">Pills 2</span>
              <span class="d-sm-block d-none">Dolares</span>
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade active show" id="nav-pills-tab-1-saldousers-soles">
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="thead-dark">
                      <tr>
                        <th>Módulo</th>
                        <th>Código</th>
                        <th>Monto</th>
                        <th>T. Pago</th>
                        <th>F. de Confirmación</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($efectivosoles['data_reporte'] as $value)
                        <tr>
                          <td>{{$value['modulonombre']}}</td>
                          <td style="height: 40px;">{{$value['codigo']}}</td>
                          <td>{{$value['monto']}}</td>
                          <td>{{$value['tipopagonombre']}}</td>
                          <td>{{$value['fechaconfirmacion']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
          </div>
          <div class="tab-pane fade" id="nav-pills-tab-2-saldousers-dolares">
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="thead-dark">
                      <tr>
                        <th>Módulo</th>
                        <th>Código</th>
                        <th>Monto</th>
                        <th>T. Pago</th>
                        <th>F. de Confirmación</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($efectivodolares['data_reporte'] as $value)
                        <tr>
                          <td>{{$value['modulonombre']}}</td>
                          <td style="height: 40px;">{{$value['codigo']}}</td>
                          <td>{{$value['monto']}}</td>
                          <td>{{$value['tipopagonombre']}}</td>
                          <td>{{$value['fechaconfirmacion']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
          </div>
        </div>
    </div>
  </div>
</div>
    <style>
    .table-cierrecaja > tbody > tr > td {
        padding: 10px !important;
    }
    .td-ingreso {
        background-color: #39a7ff;
        color: #fff;
        width: 50%;
    }
    .td-ingreso-total {
        background-color: #1176c7;
        color: #fff;
    }
    .td-egreso {
        background-color: #ff5939;
        color: #fff;
    }
    .td-egreso-total {
        background-color: #d42200;
        color: #fff;
    }
    .td-subtotal {
        font-size: 20px;
        background-color: #09a50f;
        color: #fff;
    }
    .td-total {
        background-color: #09a50f;
        color: #fff;
    }
    .td-moneda {
        background-color: #f9f9f9;
    }
    </style> 