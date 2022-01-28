@extends('layouts.backoffice.master')
@section('cuerpobackoffice')

    <?php
    $usuario = usersmaster();
    $aperturacierre = aperturacierre($usuario->idtienda,Auth::user()->id); 
    ?>
    @if(isset($aperturacierre['apertura']))    
        @if($aperturacierre['apertura']->idestado==3)
            <?php 
            $monedasoles = DB::table('moneda')->whereId(1)->first();
            $monedadolares = DB::table('moneda')->whereId(2)->first();
            $efectivosoles = efectivo($aperturacierre['apertura']->id,1);
            $efectivodolares = efectivo($aperturacierre['apertura']->id,2); 
            ?>
            <!-- begin row -->
            <div class="row">
              <div class="col-lg-3 col-md-6">
                <div class="widget widget-stats bg-red">
                  <div class="stats-icon"><i class="fa fa-shopping-cart"></i></div>
                  <div class="stats-info">
                    <h4>TOTAL INGRESOS</h4>
                    <p>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_efectivo_ingresos'] }} - {{ $monedadolares->simbolo }} {{ $efectivodolares['total_efectivo_ingresos'] }}</p>	
                  </div>
                  <div class="stats-link">
                    <a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $aperturacierre['apertura']->id }}/edit?view=detallecierre'})">Ver más detalle <i class="fa fa-arrow-alt-circle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6">
                <div class="widget widget-stats bg-orange">
                  <div class="stats-icon"><i class="fa fa-shopping-cart"></i></div>
                  <div class="stats-info">
                    <h4>TOTAL EGRESOS</h4>
                    <p>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_egresos'] }} - {{ $monedadolares->simbolo }} {{ $efectivodolares['total_egresos'] }}</p>	
                  </div>
                  <div class="stats-link">
                    <a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $aperturacierre['apertura']->id }}/edit?view=detallecierre'})">Ver más detalle <i class="fa fa-arrow-alt-circle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6">
                <div class="widget widget-stats bg-grey-darker">
                  <div class="stats-icon"><i class="fa fa-shopping-cart"></i></div>
                  <div class="stats-info">
                    <h4>TOTAL CAJA</h4>
                    <p>{{ $monedasoles->simbolo }} {{ $efectivosoles['total_efectivo'] }} - {{ $monedadolares->simbolo }} {{ $efectivodolares['total_efectivo'] }}</p>	
                  </div>
                  <div class="stats-link">
                    <a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $aperturacierre['apertura']->id }}/edit?view=detallecierre'})">Ver más detalle <i class="fa fa-arrow-alt-circle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6">
                <div class="widget widget-stats bg-black-lighter">
                  <div class="stats-icon"><i class="fa fa-shopping-cart"></i></div>
                  <div class="stats-info">
                    <h4>TOTAL EFECTIVO</h4>
                    <p>{{ $monedasoles->simbolo }} {{ $efectivosoles['total'] }} - {{ $monedadolares->simbolo }} {{ $efectivodolares['total'] }}</p>		
                  </div>
                  <div class="stats-link">
                    <a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $aperturacierre['apertura']->id }}/edit?view=detallecierre'})">Ver más detalle <i class="fa fa-arrow-alt-circle-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
        @endif
    @endif 
<img src="{{ url('public/admin/img/general/portada_1.png') }}" width="100%">
@endsection
@section('subscripts')
@endsection