<?php
$monedasoles = DB::table('moneda')->whereId(1)->first();
$monedadolares = DB::table('moneda')->whereId(2)->first();
$efectivosoles = efectivo($idaperturacierre,1);
$efectivodolares = efectivo($idaperturacierre,2);
$count_ingresosdiversos_soles = count($efectivosoles['ingresosdiversos']);
$count_ingresosdiversos_dolares = count($efectivodolares['ingresosdiversos']);
$count_egresosdiversos_soles = count($efectivosoles['egresosdiversos']);
$count_egresosdiversos_dolares = count($efectivodolares['egresosdiversos']);
$count_ventas_soles = count($efectivosoles['ventas']);
$count_ventas_dolares = count($efectivodolares['ventas']);
$count_notadevoluciones_soles = count($efectivosoles['notadevoluciones']);
$count_notadevoluciones_dolares = count($efectivodolares['notadevoluciones']);
$count_cobranzacreditos_soles = count($efectivosoles['cobranzacreditos']);
$count_cobranzacreditos_dolares = count($efectivodolares['cobranzacreditos']);
$count_cobranzaletras_soles = count($efectivosoles['cobranzaletras']);
$count_cobranzaletras_dolares = count($efectivodolares['cobranzaletras']);
$count_compras_soles = count($efectivosoles['compras']);
$count_compras_dolares = count($efectivodolares['compras']);
$count_compradevoluciones_soles = count($efectivosoles['compradevoluciones']);
$count_compradevoluciones_dolares = count($efectivodolares['compradevoluciones']);
$count_pagocreditos_soles = count($efectivosoles['pagocreditos']);
$count_pagocreditos_dolares = count($efectivodolares['pagocreditos']);
$count_pagoletras_soles = count($efectivosoles['pagoletras']);
$count_pagoletras_dolares = count($efectivodolares['pagoletras']);
$count_ingresossaldousers_soles = count($efectivosoles['ingresossaldousers']);
$count_ingresossaldousers_dolares = count($efectivodolares['ingresossaldousers']);
?>
    <div id="accordion" class="card-accordion">
        @if($count_ingresosdiversos_soles>0 or $count_ingresosdiversos_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor" data-toggle="collapse" data-target="#collapseOne">
								  Movimientos - Ingresos
							</div>
							<div id="collapseOne" class="collapse" data-parent="#accordion">
								<div class="card-body">
                  @if($count_ingresosdiversos_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-ingresos-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-ingresos-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-ingresos-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['ingresosdiversos'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{$value->monto}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_ingresosdiversos_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-ingresos-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['ingresosdiversos'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{$value->monto}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_egresosdiversos_soles>0 or $count_egresosdiversos_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor collapsed" data-toggle="collapse" data-target="#collapseTwo">
								  Movimientos - Egresos
							</div>
							<div id="collapseTwo" class="collapse" data-parent="#accordion">
								<div class="card-body">
                  @if($count_egresosdiversos_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-egresos-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-egresos-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-egresos-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['egresosdiversos'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{$value->monto}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_egresosdiversos_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-egresos-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['egresosdiversos'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{$value->monto}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_ventas_soles>0 or $count_ventas_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor collapsed" data-toggle="collapse" data-target="#collapseThree">
								  Ventas
							</div>
							<div id="collapseThree" class="collapse" data-parent="#accordion">
                <div class="card-body">
                  @if($count_ventas_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-ventas-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-ventas-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-ventas-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['ventas'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_ventas_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-ventas-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['ventas'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_notadevoluciones_soles>0 or $count_notadevoluciones_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor" data-toggle="collapse" data-target="#collapseNotadevolucion">
								  Nota de Devolución
							</div>
							<div id="collapseNotadevolucion" class="collapse" data-parent="#accordion">
								<div class="card-body">
                  @if($count_notadevoluciones_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-notadevoluciones-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-notadevoluciones-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-notadevoluciones-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Venta</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['notadevoluciones'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->notadevolucioncodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{$value->total}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_notadevoluciones_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-notadevoluciones-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Venta</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['notadevoluciones'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->notadevolucioncodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{$value->total}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_cobranzacreditos_soles>0 or $count_cobranzacreditos_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor collapsed" data-toggle="collapse" data-target="#collapseFive">
								  Cobranza de Creditos
							</div>
							<div id="collapseFive" class="collapse" data-parent="#accordion">
                <div class="card-body">
                    @if($count_cobranzacreditos_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-cobranzacreditos-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-cobranzacreditos-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-cobranzacreditos-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Venta</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['cobranzacreditos'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_cobranzacreditos_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-cobranzacreditos-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Venta</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['cobranzacreditos'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_cobranzaletras_soles>0 or $count_cobranzaletras_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor collapsed" data-toggle="collapse" data-target="#collapseSix">
								  Cobranza de Letras
							</div>
							<div id="collapseSix" class="collapse" data-parent="#accordion">
								<div class="card-body">
                  @if($count_cobranzaletras_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-cobranzaletras-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-cobranzaletras-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-cobranzaletras-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Venta</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['cobranzaletras'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_cobranzaletras_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-cobranzaletras-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Venta</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['cobranzaletras'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_compras_soles>0 or $count_compras_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor collapsed" data-toggle="collapse" data-target="#collapseFour">
								  Compras
							</div>
							<div id="collapseFour" class="collapse" data-parent="#accordion">
                <div class="card-body">
                  @if($count_compras_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-compras-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-compras-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-compras-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['compras'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_compras_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-compras-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['compras'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_compradevoluciones_soles>0 or $count_compradevoluciones_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor" data-toggle="collapse" data-target="#collapseDevolucioncompra">
								  Devolución de Compras
							</div>
							<div id="collapseDevolucioncompra" class="collapse" data-parent="#accordion">
								<div class="card-body">
                  @if($count_compradevoluciones_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-devolucioncompras-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-devolucioncompras-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-devolucioncompras-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Compra</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['compradevoluciones'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{$value->montorecibido}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_compradevoluciones_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-devolucioncompras-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Compra</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['compradevoluciones'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{$value->montorecibido}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_pagocreditos_soles>0 or $count_pagocreditos_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor collapsed" data-toggle="collapse" data-target="#collapsePagocredito">
								  Pago de Creditos
							</div>
							<div id="collapsePagocredito" class="collapse" data-parent="#accordion">
                <div class="card-body">
                  @if($count_pagocreditos_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-pagocreditos-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-pagocreditos-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-pagocreditos-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Compra</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['pagocreditos'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_pagocreditos_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-pagocreditos-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Compra</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['pagocreditos'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_pagoletras_soles>0 or $count_pagoletras_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor collapsed" data-toggle="collapse" data-target="#collapsePagoletra">
								  Pago de Letras
							</div>
							<div id="collapsePagoletra" class="collapse" data-parent="#accordion">
								<div class="card-body">
                  @if($count_pagoletras_dolares>0)
                  <ul class="nav nav-pills">
                    <li class="nav-items">
                      <a href="#nav-pills-tab-1-pagoletras-soles" data-toggle="tab" class="nav-link active">
                        <span class="d-sm-none">Pills 1</span>
                        <span class="d-sm-block d-none">Soles</span>
                      </a>
                    </li>
                    <li class="nav-items">
                      <a href="#nav-pills-tab-2-pagoletras-dolares" data-toggle="tab" class="nav-link">
                        <span class="d-sm-none">Pills 2</span>
                        <span class="d-sm-block d-none">Dolares</span>
                      </a>
                    </li>
                  </ul>
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-pagoletras-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Compra</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['pagoletras'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_pagoletras_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-pagoletras-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Cod. Compra</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['pagoletras'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
        @if($count_ingresossaldousers_soles>0 or $count_ingresossaldousers_dolares>0)
						<div class="card">
							<div class="card-header bg-black text-white pointer-cursor collapsed" data-toggle="collapse" data-target="#collapseSaldoUusario">
								  Saldo de Usuarios - Ingresos
							</div>
							<div id="collapseSaldoUusario" class="collapse" data-parent="#accordion">
								<div class="card-body">
                  @if($count_ingresossaldousers_dolares>0)
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
                  @endif
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="nav-pills-tab-1-saldousers-soles">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivosoles['ingresossaldousers'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @if($count_ingresossaldousers_dolares>0)
                    <div class="tab-pane fade" id="nav-pills-tab-2-saldousers-dolares">
                      <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th>Código</th>
                                <th>Monto</th>
                                <th>Tipo de Pago</th>
                                <th>Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($efectivodolares['ingresossaldousers'] as $value)
                                <tr>
                                  <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                  <td>{{number_format($value->monto, 2, '.', '')}}</td>
                                  <td>{{$value->tipopagonombre}}</td>
                                  <td>{{$value->fechaconfirmacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    @endif
                  </div>
								</div>
							</div>
						</div>
        @endif
		</div>
        <table class="table table-cierrecaja">
            <thead class="thead-dark">
              <tr>
                <th colspan="3" style="text-align: center;">SUMATORIA TOTAL</th>
              </tr>
            </thead>
            <tbody>
              @if($efectivosoles['total_apertura']>0 or $efectivodolares['total_apertura']>0)
              <tr>
                <td class="td-ingreso">Apertura</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_apertura'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_apertura'] }}</td>
              </tr>
              @endif
              @if(($count_ingresosdiversos_soles>0 or $count_ingresosdiversos_dolares>0)&&($efectivosoles['total_ingresosdiversos_efectivo']>0 or $efectivodolares['total_ingresosdiversos_efectivo']>0))
              <tr>
                <td class="td-ingreso">Movimientos - Ingreso</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosdiversos_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosdiversos_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_ventas_soles>0 or $count_ventas_dolares>0)&&($efectivosoles['total_ventas_efectivo']>0 or $efectivodolares['total_ventas_efectivo']>0))
              <tr>
                <td class="td-ingreso">Ventas</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ventas_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ventas_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_cobranzacreditos_soles>0 or $count_cobranzacreditos_dolares>0)&&($efectivosoles['total_cobranzacreditos_efectivo']>0 or $efectivodolares['total_cobranzacreditos_efectivo']>0))
              <tr>
                <td class="td-ingreso">Cobranza Creditos</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzacreditos_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzacreditos_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_cobranzaletras_soles>0 or $count_cobranzaletras_dolares>0)&&($efectivosoles['total_cobranzaletras_efectivo']>0 or $efectivodolares['total_cobranzaletras_efectivo']>0))
              <tr>
                <td class="td-ingreso">Cobranza Letras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzaletras_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzaletras_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_compradevoluciones_soles>0 or $count_compradevoluciones_dolares>0)&&($efectivosoles['total_compradevoluciones_efectivo']>0 or $efectivodolares['total_compradevoluciones_efectivo']>0))
              <tr>
                <td class="td-ingreso">Devolución de Compras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compradevoluciones_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compradevoluciones_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_ingresossaldousers_soles>0 or $count_ingresossaldousers_dolares>0)&&($efectivosoles['total_ingresossaldousers_efectivo']>0 or $efectivodolares['total_ingresossaldousers_efectivo']>0))
              <tr>
                <td class="td-ingreso">Saldos de Usuarios - Ingreso</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresossaldousers_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresossaldousers_efectivo'] }}</td>
              </tr>
              @endif
              <tr>
                <td class="td-ingreso-total"><b>Tota Ingresos</b></td>
                <td class="td-ingreso-total"><b>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_efectivo_ingresos'] }}</b></td>
                <td class="td-ingreso-total"><b>{{ $monedadolares->simbolo }} {{ $efectivodolares['total_efectivo_ingresos'] }}</b></td>
              </tr>
              @if(($count_egresosdiversos_soles>0 or $count_egresosdiversos_dolares>0)&&($efectivosoles['total_egresosdiversos']>0 or $efectivodolares['total_egresosdiversos']>0))
              <tr>
                <td class="td-egreso">Movimientos - Egresos</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_egresosdiversos'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_egresosdiversos'] }}</td>
              </tr>
              @endif
              @if(($count_compras_soles>0 or $count_compras_dolares>0)&&($efectivosoles['total_compras_efectivo']>0 or $efectivodolares['total_compras_efectivo']>0))
              <tr>
                <td class="td-egreso">Efectivo / Compras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compras_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compras_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_notadevoluciones_soles>0 or $count_notadevoluciones_dolares>0)&&($efectivosoles['total_notadevoluciones_efectivo']>0 or $efectivodolares['total_notadevoluciones_efectivo']>0))
              <tr>
                <td class="td-egreso">Efectivo / Nota de Devolución</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_notadevoluciones_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_notadevoluciones_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_pagocreditos_soles>0 or $count_pagocreditos_dolares>0)&&($efectivosoles['total_pagocreditos_efectivo']>0 or $efectivodolares['total_pagocreditos_efectivo']>0))
              <tr>
                <td class="td-egreso">Efectivo / Pago Creditos</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_pagocreditos_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_pagocreditos_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_pagoletras_soles>0 or $count_pagoletras_dolares>0)&&($efectivosoles['total_pagoletras_efectivo']>0 or $efectivodolares['total_pagoletras_efectivo']>0))
              <tr>
                <td class="td-egreso">Efectivo / Pago Letras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_pagoletras_efectivo'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_pagoletras_efectivo'] }}</td>
              </tr>
              @endif
              @if(($count_compras_soles>0 or $count_compras_dolares>0)&&($efectivosoles['total_compras_deposito']>0 or $efectivodolares['total_compras_deposito']>0))
              <tr>
                <td class="td-egreso">Deposito / Compras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compras_deposito'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compras_deposito'] }}</td>
              </tr>
              @endif
              @if(($count_notadevoluciones_soles>0 or $count_notadevoluciones_dolares>0)&&($efectivosoles['total_notadevoluciones_deposito']>0 or $efectivodolares['total_notadevoluciones_deposito']>0))
              <tr>
                <td class="td-egreso">Deposito / Nota de Devolución</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_notadevoluciones_deposito'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_notadevoluciones_deposito'] }}</td>
              </tr>
              @endif
              @if(($count_pagocreditos_soles>0 or $count_pagocreditos_dolares>0)&&($efectivosoles['total_pagocreditos_deposito']>0 or $efectivodolares['total_pagocreditos_deposito']>0))
              <tr>
                <td class="td-egreso">Deposito / Pago Creditos</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_pagocreditos_deposito'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_pagocreditos_deposito'] }}</td>
              </tr>
              @endif
              @if(($count_pagoletras_soles>0 or $count_pagoletras_dolares>0)&&($efectivosoles['total_pagoletras_deposito']>0 or $efectivodolares['total_pagoletras_deposito']>0))
              <tr>
                <td class="td-egreso">Deposito / Pago Letras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_pagoletras_deposito'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_pagoletras_deposito'] }}</td>
              </tr>
              @endif
              @if(($count_compras_soles>0 or $count_compras_dolares>0)&&($efectivosoles['total_compras_cheque']>0 or $efectivodolares['total_compras_cheque']>0))
              <tr>
                <td class="td-egreso">Cheque / Compras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compras_cheque'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compras_cheque'] }}</td>
              </tr>
              @endif
              @if(($count_notadevoluciones_soles>0 or $count_notadevoluciones_dolares>0)&&($efectivosoles['total_notadevoluciones_cheque']>0 or $efectivodolares['total_notadevoluciones_cheque']>0))
              <tr>
                <td class="td-egreso">Cheque / Nota de Devolución</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_notadevoluciones_cheque'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_notadevoluciones_cheque'] }}</td>
              </tr>
              @endif
              @if(($count_pagocreditos_soles>0 or $count_pagocreditos_dolares>0)&&($efectivosoles['total_pagocreditos_cheque']>0 or $efectivodolares['total_pagocreditos_cheque']>0))
              <tr>
                <td class="td-egreso">Cheque / Pago Creditos</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_pagocreditos_cheque'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_pagocreditos_cheque'] }}</td>
              </tr>
              @endif
              @if(($count_pagoletras_soles>0 or $count_pagoletras_dolares>0)&&($efectivosoles['total_pagoletras_cheque']>0 or $efectivodolares['total_pagoletras_cheque']>0))
              <tr>
                <td class="td-egreso">Cheque / Pago Letras</td>
                <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_pagoletras_cheque'] }}</td>
                <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_pagoletras_cheque'] }}</td>
              </tr>
              @endif
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
              @if($efectivosoles['total_depositocheque_ingresos']>0 or $efectivodolares['total_depositocheque_ingresos']>0)
                @if(($count_ingresosdiversos_soles>0 or $count_ingresosdiversos_dolares>0)&&($efectivosoles['total_ingresosdiversos_deposito']>0 or $efectivodolares['total_ingresosdiversos_deposito']>0))
                <tr>
                  <td class="td-ingreso">Deposito / Movimientos - Ingreso</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosdiversos_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosdiversos_deposito'] }}</td>
                </tr>
                @endif
                @if(($count_ventas_soles>0 or $count_ventas_dolares>0)&&($efectivosoles['total_ventas_deposito']>0 or $efectivodolares['total_ventas_deposito']>0))
                <tr>
                  <td class="td-ingreso">Deposito / Ventas</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ventas_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ventas_deposito'] }}</td>
                </tr>
                @endif
                @if(($count_cobranzacreditos_soles>0 or $count_cobranzacreditos_dolares>0)&&($efectivosoles['total_cobranzacreditos_deposito']>0 or $efectivodolares['total_cobranzacreditos_deposito']>0))
                <tr>
                  <td class="td-ingreso">Deposito / Cobranza Creditos</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzacreditos_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzacreditos_deposito'] }}</td>
                </tr>
                @endif
                @if(($count_cobranzaletras_soles>0 or $count_cobranzaletras_dolares>0)&&($efectivosoles['total_cobranzaletras_deposito']>0 or $efectivodolares['total_cobranzaletras_deposito']>0))
                <tr>
                  <td class="td-ingreso">Deposito / Cobranza Letras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzaletras_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzaletras_deposito'] }}</td>
                </tr>
                @endif
                @if(($count_compradevoluciones_soles>0 or $count_compradevoluciones_dolares>0)&&($efectivosoles['total_compradevoluciones_deposito']>0 or $efectivodolares['total_compradevoluciones_deposito']>0))
                <tr>
                  <td class="td-ingreso">Deposito / Devolución de Compras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compradevoluciones_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compradevoluciones_deposito'] }}</td>
                </tr>
                @endif
                @if(($count_ingresossaldousers_soles>0 or $count_ingresossaldousers_dolares>0)&&($efectivosoles['total_ingresossaldousers_deposito']>0 or $efectivodolares['total_ingresossaldousers_deposito']>0))
                <tr>
                  <td class="td-ingreso">Deposito / Saldos de Usuarios</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresossaldousers_deposito'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresossaldousers_deposito'] }}</td>
                </tr>
                @endif
                @if(($count_ingresosdiversos_soles>0 or $count_ingresosdiversos_dolares>0)&&($efectivosoles['total_ingresosdiversos_cheque']>0 or $efectivodolares['total_ingresosdiversos_cheque']>0))
                <tr>
                  <td class="td-ingreso">Cheque / Movimientos - Ingreso</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresosdiversos_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresosdiversos_cheque'] }}</td>
                </tr>
                @endif
                @if(($count_ventas_soles>0 or $count_ventas_dolares>0)&&($efectivosoles['total_ventas_cheque']>0 or $efectivodolares['total_ventas_cheque']>0))
                <tr>
                  <td class="td-ingreso">Cheque / Ventas</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ventas_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ventas_cheque'] }}</td>
                </tr>
                @endif
                @if(($count_cobranzacreditos_soles>0 or $count_cobranzacreditos_dolares>0)&&($efectivosoles['total_cobranzacreditos_cheque']>0 or $efectivodolares['total_cobranzacreditos_cheque']>0))
                <tr>
                  <td class="td-ingreso">Cheque / Cobranza Creditos</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzacreditos_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzacreditos_cheque'] }}</td>
                </tr>
                @endif
                @if(($count_cobranzaletras_soles>0 or $count_cobranzaletras_dolares>0)&&($efectivosoles['total_cobranzaletras_cheque']>0 or $efectivodolares['total_cobranzaletras_cheque']>0))
                <tr>
                  <td class="td-ingreso">Cheque / Cobranza Letras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_cobranzaletras_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_cobranzaletras_cheque'] }}</td>
                </tr>
                @endif
                @if(($count_compradevoluciones_soles>0 or $count_compradevoluciones_dolares>0)&&($efectivosoles['total_compradevoluciones_cheque']>0 or $efectivodolares['total_compradevoluciones_cheque']>0))
                <tr>
                  <td class="td-ingreso">Cheque / Devolución de Compras</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_compradevoluciones_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_compradevoluciones_cheque'] }}</td>
                </tr>
                @endif
                @if(($count_ingresossaldousers_soles>0 or $count_ingresossaldousers_dolares>0)&&($efectivosoles['total_ingresossaldousers_cheque']>0 or $efectivodolares['total_ingresossaldousers_cheque']>0))
                <tr>
                  <td class="td-ingreso">Cheque / Saldos de Usuarios</td>
                  <td class="td-moneda">{{ $monedasoles->simbolo }} {{ $efectivosoles['total_ingresossaldousers_cheque'] }}</td>
                  <td class="td-moneda">{{ $monedadolares->simbolo }} {{ $efectivodolares['total_ingresossaldousers_cheque'] }}</td>
                </tr>
                @endif
                <tr>
                  <td class="td-ingreso-total"><b>Sub Total</b></td>
                  <td class="td-ingreso-total"><b>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_depositocheque_ingresos'] }}</b></td>
                  <td class="td-ingreso-total"><b>{{ $monedadolares->simbolo }} {{ $efectivodolares['total_depositocheque_ingresos'] }}</b></td>
                </tr>
                <tr>
                  <td class="td-total"><b>Total Venta</b></td>
                  <td class="td-total"><b>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_final'] }}</b></td>
                  <td class="td-total"><b>{{ $monedadolares->simbolo }} {{ $efectivodolares['total_final'] }}</b></td>
                </tr>
              @endif
            </tbody>
          </table>

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