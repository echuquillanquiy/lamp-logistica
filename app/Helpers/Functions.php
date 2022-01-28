<?php
use Peru\Sunat\RucFactory;
use Peru\Jne\DniFactory;

function consultaDniRuc($numeroIdentificacion, $idTipoPersona) {

    if ($idTipoPersona == 1) {
        $factory = new DniFactory();
        $cs      = $factory->create();
        $person  = $cs->get($numeroIdentificacion);
  
        if (!$person) {
            return [
                'resultado' => 'ERROR',
                'mensaje' => 'No existen resultados para este Numero de DNI, ingrese manualemente.'
            ];
            exit();
        }

        return $person;
    }else if ($idTipoPersona == 2) {


        $factory = new RucFactory();
        $cs = $factory->create();

        $company = $cs->get($numeroIdentificacion);

        //$newObjectConsultRuc = new Ruc(new ContextClient(), new RucParser(new HtmlParser()));
        //$company = $newObjectConsultRuc->get($numeroIdentificacion);

        if (!$company) {
          
            // consultar si no existe
            $token_api = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imp1YW5fdGs1QGhvdG1haWwuY29tIn0.9D9_mambkvDzginWXNYZh2nmuf5GNYVqjz13tXR-SNk';
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $numeroIdentificacion,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Referer: http://apis.net.pe/api-ruc',
                    'Authorization: Bearer ' . $token_api
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $company = json_decode($response);
            // consultar si no existe
          
  
          
            if ($response == '') {
                return [
                    'resultado' => 'ERROR',
                    'mensaje' => 'No existen resultados para este Numero de RUC, ingrese manualemente.'
                ];
                exit();
            }
        }    


        $companyUbigeo = $company->departamento.'/'.$company->provincia.'/'.$company->distrito;

        $ubigeo = DB::table('ubigeo')->where('ubigeo.nombre', 'like', '%'.$companyUbigeo.'%')->first();
        
        return [
            'ruc'             => $company->ruc ?? $company->numeroDocumento,
            'razonSocial'     => $company->razonSocial ?? $company->nombre,
            'nombreComercial' => $company->nombreComercial ?? $company->nombre,
            'direccion'       => $company->direccion,
            'departamento'    => $company->departamento,
            'provincia'       => $company->provincia,
            'distrito'        => $company->distrito,
            'idubigeo'        => !is_null($ubigeo) ? $ubigeo->id : '',
            'ubigeo'          => !is_null($ubigeo) ? $ubigeo->nombre : '',
        ];
    }
}

// SISTEMA 
function usersmaster($idusers=''){
    if($idusers==''){
        $idusers = Auth::user()->id;
    }
    $usuario = DB::table('users')
        ->join('role_user','role_user.user_id','users.id')
        ->join('roles','roles.id','role_user.role_id')
        ->join('tienda','tienda.id','role_user.idtienda')
        ->leftJoin('ubigeo','ubigeo.id','tienda.idubigeo')
        ->where('users.id',$idusers)
        ->where('role_user.sesion',1)
        ->select(
            'users.*',
            'roles.id as idpermiso',
            'roles.description as permiso',
            'tienda.id as idtienda',
            'tienda.imagen as tiendalogo',
            'tienda.nombre as tiendanombre',
            'tienda.descripcion as tiendadescripcion',
            'tienda.direccion as tiendadireccion',
            'tienda.correo as tiendacorreo',
            'tienda.numerotelefono as tiendanumerotelefono',
            'tienda.facturador_serie as tiendaserie',
            'tienda.facturador_idestado as idestadosunat',
            'ubigeo.id as idubigeo',
            'ubigeo.codigo as ubigeocodigo',
            'ubigeo.nombre as ubigeonombre',
            'ubigeo.distrito as ubigeodistrito',
            'ubigeo.provincia as ubigeoprovincia',
            'ubigeo.departamento as ubigeodepartamento'
        )
        ->limit(1)
        ->first();
    return $usuario;
}
function aperturacierre($idtienda,$idusersrecepcion){
    $aperturacierre = DB::table('aperturacierre')
        ->join('caja','caja.id','aperturacierre.idcaja')
        ->where('caja.idtienda',$idtienda)
        ->where('aperturacierre.idusersrecepcion',$idusersrecepcion)
        //->where('aperturacierre.idestado',2)
        //->where('aperturacierre.fechaconfirmacion','<>','')
        ->select('aperturacierre.*','caja.id as idcaja','caja.nombre as cajanombre')
        ->orderBy('aperturacierre.id','desc')
        ->limit(1)
        ->first();
  
    $resultado = 'ERROR';
    $mensaje = 'No hay ninguna Caja Aperturada.';
    $idapertura = 0;
    if($aperturacierre!=''){
        if($aperturacierre->idestado==3 && $aperturacierre->idusersrecepcion==Auth::user()->id){
            $resultado = 'CORRECTO';
            $mensaje = 'La Caja esta Aperturada.';
            $idapertura = $aperturacierre->id;
        }else{
            $resultado = 'ERROR';
            $mensaje = 'La Caja debe estar Aperturada.';
        }
    }
  
    return [
        'resultado' => $resultado,
        'mensaje'   => $mensaje,
        'idapertura'  => $idapertura,
        'apertura'  => $aperturacierre
    ];
}
function efectivo($idapertura,$idmoneda){
  
    //---------- INGRESO ----------//
    $apertura = DB::table('aperturacierre')
        ->whereId($idapertura)
        ->limit(1)
        ->first();
    if($idmoneda==1){
        $total_apertura = $apertura->montoasignarsoles;
    }else{
        $total_apertura = $apertura->montoasignardolares;
    }
  
    // Saldo cliente 
    $ingresossaldousers = DB::table('tipopagodetalle')
        ->join('tipopago','tipopago.id','tipopagodetalle.idtipopago')
        ->where('tipopagodetalle.idmoneda',$idmoneda)
        ->where('tipopagodetalle.idaperturacierre',$idapertura)
        ->where('tipopagodetalle.idestado',2)
        ->select(
            'tipopagodetalle.*',
            'tipopago.nombre as tipopagonombre'
        )
        ->orderBy('tipopagodetalle.id','asc')
        ->get();
  
    $total_ingresosdiversos = 0;
    $total_ingresosdiversos_efectivo = 0;
    $total_ingresosdiversos_deposito = 0;
    $total_ingresosdiversos_cheque = 0;
    $total_ingresosdiversos_saldo = 0;
  
    $total_egresosdiversos = 0;
    $total_egresosdiversos_efectivo = 0;
    $total_egresosdiversos_deposito = 0;
    $total_egresosdiversos_cheque = 0;
    $total_egresosdiversos_saldo = 0;
  
    $total_compras = 0;
    $total_compras_efectivo = 0;
    $total_compras_deposito = 0;
    $total_compras_cheque = 0;
    $total_compras_saldo = 0;
  
    $total_compradevoluciones = 0;
    $total_compradevoluciones_efectivo = 0;
    $total_compradevoluciones_deposito = 0;
    $total_compradevoluciones_cheque = 0;
    $total_compradevoluciones_saldo = 0;
  
    $total_pagocreditos = 0;
    $total_pagocreditos_efectivo = 0;
    $total_pagocreditos_deposito = 0;
    $total_pagocreditos_cheque = 0;
    $total_pagocreditos_saldo = 0;
  
    $total_pagoletras = 0;
    $total_pagoletras_efectivo = 0;
    $total_pagoletras_deposito = 0;
    $total_pagoletras_cheque = 0;
    $total_pagoletras_saldo = 0;
  
    $total_ventas = 0;
    $total_ventas_efectivo = 0;
    $total_ventas_deposito = 0;
    $total_ventas_cheque = 0;
    $total_ventas_saldo = 0;
  
    $total_notadevoluciones = 0;
    $total_notadevoluciones_efectivo = 0;
    $total_notadevoluciones_deposito = 0;
    $total_notadevoluciones_cheque = 0;
    $total_notadevoluciones_saldo = 0;
  
    $total_cobranzacreditos = 0;
    $total_cobranzacreditos_efectivo = 0;
    $total_cobranzacreditos_deposito = 0;
    $total_cobranzacreditos_cheque = 0;
    $total_cobranzacreditos_saldo = 0;
  
    $total_cobranzaletras = 0;
    $total_cobranzaletras_efectivo = 0;
    $total_cobranzaletras_deposito = 0;
    $total_cobranzaletras_cheque = 0;
    $total_cobranzaletras_saldo = 0;
  
    $total_ingresosuserssaldo = 0;
    $total_ingresosuserssaldo_efectivo = 0;
    $total_ingresosuserssaldo_deposito = 0;
    $total_ingresosuserssaldo_cheque = 0;
    $total_ingresosuserssaldo_saldo = 0;
  
    $total_egresosuserssaldo = 0;
    $total_egresosuserssaldo_efectivo = 0;
    $total_egresosuserssaldo_deposito = 0;
    $total_egresosuserssaldo_cheque = 0;
    $total_egresosuserssaldo_saldo = 0;
    $data_reporte = [];
  
    foreach($ingresossaldousers as $value){
        $modulo = '';
        $modulonombre = ''; 
        $signo = '';
      
        // Movimientos
        if($value->idmovimiento!=''){
            $modulo = DB::table('movimiento')
                ->whereId($value->idmovimiento)
                ->first();
            $modulonombre = 'Movimientos'; 
          
            if($modulo!=''){
            if($modulo->idtipomovimiento==1){
                if($value->idtipopago==1){
                    $total_ingresosdiversos_efectivo = $total_ingresosdiversos_efectivo+$value->monto;
                }elseif($value->idtipopago==2){
                    $total_ingresosdiversos_deposito = $total_ingresosdiversos_deposito+$value->monto;
                }elseif($value->idtipopago==3){
                    $total_ingresosdiversos_cheque = $total_ingresosdiversos_cheque+$value->monto;
                }elseif($value->idtipopago==4){
                    $total_ingresosdiversos_saldo = $total_ingresosdiversos_saldo+$value->monto;
                }
            }elseif($modulo->idtipomovimiento==2){
                $signo = '-';
                if($value->idtipopago==1){
                    $total_egresosdiversos_efectivo = $total_egresosdiversos_efectivo+$value->monto;
                }elseif($value->idtipopago==2){
                    $total_egresosdiversos_deposito = $total_egresosdiversos_deposito+$value->monto;
                }elseif($value->idtipopago==3){
                    $total_egresosdiversos_cheque = $total_egresosdiversos_cheque+$value->monto;
                }elseif($value->idtipopago==4){
                    $total_egresosdiversos_saldo = $total_egresosdiversos_saldo+$value->monto;
                }
            }    
            }
                
            $total_ingresosdiversos = $total_ingresosdiversos_efectivo+$total_ingresosdiversos_deposito+$total_ingresosdiversos_cheque+$total_ingresosdiversos_saldo;
            $total_egresosdiversos = $total_egresosdiversos_efectivo+$total_egresosdiversos_deposito+$total_egresosdiversos_cheque+$total_egresosdiversos_saldo;
          
        }
        // Compras
        elseif($value->idcompra!=''){
            $modulo = DB::table('compra')
                ->whereId($value->idcompra)
                ->first();
            $modulonombre = 'Compras'; 
            $signo = '-';
          
            if($value->idtipopago==1){
                $total_compras_efectivo = $total_compras_efectivo+$value->monto;
            }elseif($value->idtipopago==2){
                $total_compras_deposito = $total_compras_deposito+$value->monto;
            }elseif($value->idtipopago==3){
                $total_compras_cheque = $total_compras_cheque+$value->monto;
            }elseif($value->idtipopago==4){
                $total_compras_saldo = $total_compras_saldo+$value->monto;
            }
            $total_compras = $total_compras_efectivo+$total_compras_deposito+$total_compras_cheque+$total_compras_saldo;
          
        }
        // Devolucion de Compras
        elseif($value->idcompradevolucion!=''){
            $modulo = DB::table('compradevolucion')
                ->whereId($value->idcompradevolucion)
                ->first();
            $modulonombre = 'Devolucion de Compras'; 
          
            if($value->idtipopago==1){
                $total_compradevoluciones_efectivo = $total_compradevoluciones_efectivo+$value->monto;
            }elseif($value->idtipopago==2){
                $total_compradevoluciones_deposito = $total_compradevoluciones_deposito+$value->monto;
            }elseif($value->idtipopago==3){
                $total_compradevoluciones_cheque = $total_compradevoluciones_cheque+$value->monto;
            }elseif($value->idtipopago==4){
                $total_compradevoluciones_saldo = $total_compradevoluciones_saldo+$value->monto;
            }
            $total_compradevoluciones = $total_compradevoluciones_efectivo+$total_compradevoluciones_deposito+$total_compradevoluciones_cheque+$total_compradevoluciones_saldo;
          
        }
        // Pago Creditos
        elseif($value->idpagocredito!=''){
            $modulo = DB::table('pagocredito')
                ->whereId($value->idpagocredito)
                ->first();
            $modulonombre = 'Pago Creditos'; 
            $signo = '-';
            if($value->idtipopago==1){
                $total_pagocreditos_efectivo = $total_pagocreditos_efectivo+$value->monto;
            }elseif($value->idtipopago==2){
                $total_pagocreditos_deposito = $total_pagocreditos_deposito+$value->monto;
            }elseif($value->idtipopago==3){
                $total_pagocreditos_cheque = $total_pagocreditos_cheque+$value->monto;
            }elseif($value->idtipopago==4){
                $total_pagocreditos_saldo = $total_pagocreditos_saldo+$value->monto;
            }
            $total_pagocreditos = $total_pagocreditos_efectivo+$total_pagocreditos_deposito+$total_pagocreditos_cheque+$total_pagocreditos_saldo;
        }
        // Pago Letras
        elseif($value->idpagoletra!=''){
            $modulo = DB::table('pagoletra')
                ->whereId($value->idpagoletra)
                ->first();
            $modulonombre = 'Pago Letras'; 
            $signo = '-';
            if($value->idtipopago==1){
                $total_pagoletras_efectivo = $total_pagoletras_efectivo+$value->monto;
            }elseif($value->idtipopago==2){
                $total_pagoletras_deposito = $total_pagoletras_deposito+$value->monto;
            }elseif($value->idtipopago==3){
                $total_pagoletras_cheque = $total_pagoletras_cheque+$value->monto;
            }elseif($value->idtipopago==4){
                $total_pagoletras_saldo = $total_pagoletras_saldo+$value->monto;
            }
            $total_pagoletras = $total_pagoletras_efectivo+$total_pagoletras_deposito+$total_pagoletras_cheque+$total_pagoletras_saldo;
        }
        // Ventas
        elseif($value->idventa!=''){
            $modulo = DB::table('venta')
                ->whereId($value->idventa)
                ->first();
            $modulonombre = 'Ventas'; 
          
            if($value->idtipopago==1){
                $total_ventas_efectivo = $total_ventas_efectivo+$value->monto;
            }elseif($value->idtipopago==2){
                $total_ventas_deposito = $total_ventas_deposito+$value->monto;
            }elseif($value->idtipopago==3){
                $total_ventas_cheque = $total_ventas_cheque+$value->monto;
            }elseif($value->idtipopago==4){
                $total_ventas_saldo = $total_ventas_saldo+$value->monto;
            }
            $total_ventas = $total_ventas_efectivo+$total_ventas_deposito+$total_ventas_cheque+$total_ventas_saldo;
          
        }
        // Nota de Devoluciones
        elseif($value->idnotadevolucion!=''){
            $modulo = DB::table('notadevolucion')
                ->whereId($value->idnotadevolucion)
                ->first();
            $modulonombre = 'Nota de Devolución'; 
            $signo = '-';
          
            if($value->idtipopago==1){
                $total_notadevoluciones_efectivo = $total_notadevoluciones_efectivo+$value->monto;
            }elseif($value->idtipopago==2){
                $total_notadevoluciones_deposito = $total_notadevoluciones_deposito+$value->monto;
            }elseif($value->idtipopago==3){
                $total_notadevoluciones_cheque = $total_notadevoluciones_cheque+$value->monto;
            }elseif($value->idtipopago==4){
                $total_notadevoluciones_saldo = $total_notadevoluciones_saldo+$value->monto;
            }
            $total_notadevoluciones = $total_notadevoluciones_efectivo+$total_notadevoluciones_deposito+$total_notadevoluciones_cheque+$total_notadevoluciones_saldo;
        }
        // Cobranza Creditos
        elseif($value->idcobranzacredito!=''){
            $modulo = DB::table('cobranzacredito')
                ->whereId($value->idcobranzacredito)
                ->first();
            $modulonombre = 'Cobranza Creditos'; 
          
            if($value->idtipopago==1){
                $total_cobranzacreditos_efectivo = $total_cobranzacreditos_efectivo+$value->monto;
            }elseif($value->idtipopago==2){
                $total_cobranzacreditos_deposito = $total_cobranzacreditos_deposito+$value->monto;
            }elseif($value->idtipopago==3){
                $total_cobranzacreditos_cheque = $total_cobranzacreditos_cheque+$value->monto;
            }elseif($value->idtipopago==4){
                $total_cobranzacreditos_saldo = $total_cobranzacreditos_saldo+$value->monto;
            }
            $total_cobranzacreditos = $total_cobranzacreditos_efectivo+$total_cobranzacreditos_deposito+$total_cobranzacreditos_cheque+$total_cobranzacreditos_saldo;
        }
        // Cobranza Letras
        elseif($value->idcobranzaletra!=''){
            $modulo = DB::table('cobranzaletra')
                ->whereId($value->idcobranzaletra)
                ->first();
            $modulonombre = 'Cobranza Letras'; 
          
            if($value->idtipopago==1){
                $total_cobranzaletras_efectivo = $total_cobranzaletras_efectivo+$value->monto;
            }elseif($value->idtipopago==2){
                $total_cobranzaletras_deposito = $total_cobranzaletras_deposito+$value->monto;
            }elseif($value->idtipopago==3){
                $total_cobranzaletras_cheque = $total_cobranzaletras_cheque+$value->monto;
            }elseif($value->idtipopago==4){
                $total_cobranzaletras_saldo = $total_cobranzaletras_saldo+$value->monto;
            }
            $total_cobranzaletras = $total_cobranzaletras_efectivo+$total_cobranzaletras_deposito+$total_cobranzaletras_cheque+$total_cobranzaletras_saldo;
        }
        // Saldo de Usuarios
        elseif($value->iduserssaldo!=''){
            $modulo = DB::table('userssaldo')
                ->whereId($value->iduserssaldo)
                ->first();
            $modulonombre = 'Saldo de Usuario'; 
          
            if($modulo!=''){
            if($modulo->idtipomovimiento==1){
                if($value->idtipopago==1){
                    $total_ingresosuserssaldo_efectivo = $total_ingresosuserssaldo_efectivo+$value->monto;
                }elseif($value->idtipopago==2){
                    $total_ingresosuserssaldo_deposito = $total_ingresosuserssaldo_deposito+$value->monto;
                }elseif($value->idtipopago==3){
                    $total_ingresosuserssaldo_cheque = $total_ingresosuserssaldo_cheque+$value->monto;
                }elseif($value->idtipopago==4){
                    $total_ingresosuserssaldo_saldo = $total_ingresosuserssaldo_saldo+$value->monto;
                }
            }elseif($modulo->idtipomovimiento==2){
                $signo = '-';
                if($value->idtipopago==1){
                    $total_egresosuserssaldo_efectivo = $total_egresosuserssaldo_efectivo+$value->monto;
                }elseif($value->idtipopago==2){
                    $total_egresosuserssaldo_deposito = $total_egresosuserssaldo_deposito+$value->monto;
                }elseif($value->idtipopago==3){
                    $total_egresosuserssaldo_cheque = $total_egresosuserssaldo_cheque+$value->monto;
                }elseif($value->idtipopago==4){
                    $total_egresosuserssaldo_saldo = $total_egresosuserssaldo_saldo+$value->monto;
                }
            }    
            }
                
            $total_ingresosuserssaldo = $total_ingresosuserssaldo_efectivo+$total_ingresosuserssaldo_deposito+$total_ingresosuserssaldo_cheque+$total_ingresosuserssaldo_saldo;
            $total_egresosuserssaldo = $total_egresosuserssaldo_efectivo+$total_egresosuserssaldo_deposito+$total_egresosuserssaldo_cheque+$total_egresosuserssaldo_saldo;
          
        }
        
        
        $data_reporte[] = [
            'modulonombre' => $modulonombre,
            'codigo' => $modulo!=''?str_pad($modulo->codigo, 8, "0", STR_PAD_LEFT):'---',
            'monto' => $signo.number_format($value->monto, 2, '.', ''),
            'tipopagonombre' => $value->tipopagonombre,
            'fechaconfirmacion' => $value->fechaconfirmacion
        ];
      
    }
    //---
  
    $total_efectivo_ingresos =  $total_apertura+
                                $total_ventas_efectivo+
                                $total_ingresosdiversos_efectivo+
                                $total_cobranzacreditos_efectivo+
                                $total_cobranzaletras_efectivo+
                                $total_compradevoluciones_efectivo+
                                $total_ingresosuserssaldo_efectivo;
  
    $total_deposito_ingresos =  $total_ventas_deposito+
                                $total_ingresosdiversos_deposito+
                                $total_cobranzacreditos_deposito+
                                $total_cobranzaletras_deposito+
                                $total_compradevoluciones_deposito+
                                $total_ingresosuserssaldo_deposito;
  
    $total_cheque_ingresos =    $total_ventas_cheque+
                                $total_ingresosdiversos_cheque+
                                $total_cobranzacreditos_cheque+
                                $total_cobranzaletras_cheque+
                                $total_compradevoluciones_cheque+
                                $total_ingresosuserssaldo_cheque;
  
    $total_saldo_ingresos =     $total_ventas_saldo+
                                $total_ingresosdiversos_saldo+
                                $total_cobranzacreditos_saldo+
                                $total_cobranzaletras_saldo+
                                $total_compradevoluciones_saldo+
                                $total_ingresosuserssaldo_saldo;
  
    $total_ingresos =           $total_efectivo_ingresos+
                                $total_deposito_ingresos+
                                $total_cheque_ingresos+
                                $total_saldo_ingresos;
  
    //---------- EGRESO ----------//
    $total_efectivo_egresos = $total_compras_efectivo+
                              $total_egresosdiversos_efectivo+
                              $total_pagocreditos_efectivo+
                              $total_pagoletras_efectivo+
                              $total_notadevoluciones_efectivo+
                              $total_egresosuserssaldo_efectivo;
  
    $total_deposito_egresos = $total_compras_deposito+
                              $total_egresosdiversos_deposito+
                              $total_pagocreditos_deposito+
                              $total_notadevoluciones_deposito+
                              $total_pagoletras_deposito+
                              $total_egresosuserssaldo_deposito;
  
    $total_cheque_egresos =   $total_compras_cheque+
                              $total_egresosdiversos_cheque+
                              $total_pagocreditos_cheque+
                              $total_pagoletras_cheque+
                              $total_notadevoluciones_cheque+
                              $total_egresosuserssaldo_cheque;
  
    $total_saldo_egresos =   $total_compras_saldo+
                              $total_egresosdiversos_saldo+
                              $total_pagocreditos_saldo+
                              $total_pagoletras_saldo+
                              $total_notadevoluciones_saldo+
                              $total_egresosuserssaldo_saldo;
  
    $total_egresos =          $total_efectivo_egresos+
                              $total_deposito_egresos+
                              $total_cheque_egresos+
                              $total_saldo_egresos;
    //
    $total_efectivo = $total_efectivo_ingresos+$total_saldo_ingresos-$total_egresos;
    $total = $total_ingresos-$total_egresos;
  
    return [
        'data_reporte' => $data_reporte,
        'total_apertura' => number_format($total_apertura, 2, '.', ''),
        'total_ventas' => number_format($total_ventas, 2, '.', ''),
        'total_ventas_efectivo' => number_format($total_ventas_efectivo, 2, '.', ''),
        'total_ventas_deposito' => number_format($total_ventas_deposito, 2, '.', ''),
        'total_ventas_cheque' => number_format($total_ventas_cheque, 2, '.', ''),
        'total_ventas_saldo' => number_format($total_ventas_saldo, 2, '.', ''),
        'total_ingresosdiversos' => number_format($total_ingresosdiversos, 2, '.', ''),
        'total_ingresosdiversos_efectivo' => number_format($total_ingresosdiversos_efectivo, 2, '.', ''),
        'total_ingresosdiversos_deposito' => number_format($total_ingresosdiversos_deposito, 2, '.', ''),
        'total_ingresosdiversos_cheque' => number_format($total_ingresosdiversos_cheque, 2, '.', ''),
        'total_ingresosdiversos_saldo' => number_format($total_ingresosdiversos_saldo, 2, '.', ''),
        'total_cobranzacreditos' => number_format($total_cobranzacreditos, 2, '.', ''),
        'total_cobranzacreditos_efectivo' => number_format($total_cobranzacreditos_efectivo, 2, '.', ''),
        'total_cobranzacreditos_deposito' => number_format($total_cobranzacreditos_deposito, 2, '.', ''),
        'total_cobranzacreditos_cheque' => number_format($total_cobranzacreditos_cheque, 2, '.', ''),
        'total_cobranzacreditos_saldo' => number_format($total_cobranzacreditos_saldo, 2, '.', ''),
        'total_cobranzaletras' => number_format($total_cobranzaletras, 2, '.', ''),
        'total_cobranzaletras_efectivo' => number_format($total_cobranzaletras_efectivo, 2, '.', ''),
        'total_cobranzaletras_deposito' => number_format($total_cobranzaletras_deposito, 2, '.', ''),
        'total_cobranzaletras_cheque' => number_format($total_cobranzaletras_cheque, 2, '.', ''),
        'total_cobranzaletras_saldo' => number_format($total_cobranzaletras_saldo, 2, '.', ''),
        'total_compradevoluciones' => number_format($total_compradevoluciones, 2, '.', ''),
        'total_compradevoluciones_efectivo' => number_format($total_compradevoluciones_efectivo, 2, '.', ''),
        'total_compradevoluciones_deposito' => number_format($total_compradevoluciones_deposito, 2, '.', ''),
        'total_compradevoluciones_cheque' => number_format($total_compradevoluciones_cheque, 2, '.', ''),
        'total_compradevoluciones_saldo' => number_format($total_compradevoluciones_saldo, 2, '.', ''),
        'total_compras' => number_format($total_compras, 2, '.', ''),
        'total_compras_efectivo' => number_format($total_compras_efectivo, 2, '.', ''),
        'total_compras_deposito' => number_format($total_compras_deposito, 2, '.', ''),
        'total_compras_cheque' => number_format($total_compras_cheque, 2, '.', ''),
        'total_compras_saldo' => number_format($total_compras_saldo, 2, '.', ''),
        'total_egresosdiversos' => number_format($total_egresosdiversos, 2, '.', ''),
        'total_egresosdiversos_efectivo' => number_format($total_egresosdiversos_efectivo, 2, '.', ''),
        'total_egresosdiversos_deposito' => number_format($total_egresosdiversos_deposito, 2, '.', ''),
        'total_egresosdiversos_cheque' => number_format($total_egresosdiversos_cheque, 2, '.', ''),
        'total_egresosdiversos_saldo' => number_format($total_egresosdiversos_saldo, 2, '.', ''),
        'total_pagocreditos' => number_format($total_pagocreditos, 2, '.', ''),
        'total_pagocreditos_efectivo' => number_format($total_pagocreditos_efectivo, 2, '.', ''),
        'total_pagocreditos_deposito' => number_format($total_pagocreditos_deposito, 2, '.', ''),
        'total_pagocreditos_cheque' => number_format($total_pagocreditos_cheque, 2, '.', ''),
        'total_pagocreditos_saldo' => number_format($total_pagocreditos_saldo, 2, '.', ''),
        'total_pagoletras' => number_format($total_pagoletras, 2, '.', ''),
        'total_pagoletras_efectivo' => number_format($total_pagoletras_efectivo, 2, '.', ''),
        'total_pagoletras_deposito' => number_format($total_pagoletras_deposito, 2, '.', ''),
        'total_pagoletras_cheque' => number_format($total_pagoletras_cheque, 2, '.', ''),
        'total_pagoletras_saldo' => number_format($total_pagoletras_saldo, 2, '.', ''),
        'total_notadevoluciones' => number_format($total_notadevoluciones, 2, '.', ''),
        'total_notadevoluciones_efectivo' => number_format($total_notadevoluciones_efectivo, 2, '.', ''),
        'total_notadevoluciones_deposito' => number_format($total_notadevoluciones_deposito, 2, '.', ''),
        'total_notadevoluciones_cheque' => number_format($total_notadevoluciones_cheque, 2, '.', ''),
        'total_notadevoluciones_saldo' => number_format($total_notadevoluciones_saldo, 2, '.', ''),
        'total_ingresosuserssaldo' => number_format($total_ingresosuserssaldo, 2, '.', ''),
        'total_ingresosuserssaldo_efectivo' => number_format($total_ingresosuserssaldo_efectivo, 2, '.', ''),
        'total_ingresosuserssaldo_deposito' => number_format($total_ingresosuserssaldo_deposito, 2, '.', ''),
        'total_ingresosuserssaldo_cheque' => number_format($total_ingresosuserssaldo_cheque, 2, '.', ''),
        'total_ingresosuserssaldo_saldo' => number_format($total_ingresosuserssaldo_saldo, 2, '.', ''),
        'total_egresosuserssaldo' => number_format($total_egresosuserssaldo, 2, '.', ''),
        'total_egresosuserssaldo_efectivo' => number_format($total_egresosuserssaldo_efectivo, 2, '.', ''),
        'total_egresosuserssaldo_deposito' => number_format($total_egresosuserssaldo_deposito, 2, '.', ''),
        'total_egresosuserssaldo_cheque' => number_format($total_egresosuserssaldo_cheque, 2, '.', ''),
        'total_egresosuserssaldo_saldo' => number_format($total_egresosuserssaldo_saldo, 2, '.', ''),
        'total_ingresos' => number_format($total_ingresos, 2, '.', ''),
        'total_egresos' => number_format($total_egresos, 2, '.', ''),
        'total_efectivo_ingresos' => number_format($total_efectivo_ingresos, 2, '.', ''),
        'total_deposito_ingresos' => number_format($total_deposito_ingresos, 2, '.', ''),
        'total_cheque_ingresos' => number_format($total_cheque_ingresos, 2, '.', ''),
        'total_saldo_ingresos' => number_format($total_saldo_ingresos, 2, '.', ''),
        'total_efectivo' => number_format($total_efectivo, 2, '.', ''),
        'total_final' => number_format($total, 2, '.', ''),
        'total' => number_format($total_efectivo, 2, '.', '')
    ];
}
function stock_producto($idtienda,$idproducto){
    // INGRESO
    $compras = DB::table('compradetalle')
        ->join('compra','compra.id','compradetalle.idcompra')
        /*->join('aperturacierre','aperturacierre.id','compra.idaperturacierre')
        ->join('caja','caja.id','aperturacierre.idcaja')*/
        ->where('compradetalle.idproducto',$idproducto)
        ->where('compra.idtienda',$idtienda)
        ->where('compra.idestado',2)
        ->sum('compradetalle.cantidad');
  
    $productotransferenciasenvios = DB::table('productotransferenciadetalle')
        ->join('productotransferencia','productotransferencia.id','productotransferenciadetalle.idproductotransferencia')
        ->where('productotransferenciadetalle.idproducto',$idproducto)
        ->where('productotransferencia.idtiendadestino',$idtienda)
        ->where('productotransferencia.idestadotransferencia',3)
        ->where('productotransferencia.idestado',2)
        ->sum('productotransferenciadetalle.cantidadrecepcion');
  
    $productomovimientosingreso = DB::table('productomovimientodetalle')
        ->join('productomovimiento','productomovimiento.id','productomovimientodetalle.idproductomovimiento')
        ->where('productomovimientodetalle.idproducto',$idproducto)
        ->where('productomovimiento.idtienda',$idtienda)
        ->where('productomovimiento.idestadomovimiento',1)
        ->where('productomovimiento.idestado',2)
        ->sum('productomovimientodetalle.cantidad');
       
    $notadevoluciones = DB::table('notadevoluciondetalle')
        ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
        /*->join('aperturacierre','aperturacierre.id','notadevolucion.idaperturacierre')
        ->join('caja','caja.id','aperturacierre.idcaja')*/
        ->where('notadevoluciondetalle.idproducto',$idproducto)
        ->where('notadevolucion.idtienda',$idtienda)
        ->where('notadevolucion.idestado',2)
        ->sum('notadevoluciondetalle.cantidad');

    $ingresos = $compras+$productotransferenciasenvios+$productomovimientosingreso+$notadevoluciones;

    // EGRESO 
    $ventas = DB::table('ventadetalle')
        ->join('venta','venta.id','ventadetalle.idventa')
        /*->join('aperturacierre','aperturacierre.id','venta.idaperturacierre')
        ->join('caja','caja.id','aperturacierre.idcaja')*/
        ->where('ventadetalle.idproducto',$idproducto)
        ->where('venta.idtienda',$idtienda)
        ->where('venta.idestado',3)
        //->where('venta.idformapago',1)
        ->sum('ventadetalle.cantidad');
    
    $productotransferenciasrecpciones_envio = DB::table('productotransferenciadetalle')
        ->join('productotransferencia','productotransferencia.id','productotransferenciadetalle.idproductotransferencia')
        ->where('productotransferenciadetalle.idproducto',$idproducto)
        ->where('productotransferencia.idtiendaorigen',$idtienda)
        ->where('productotransferencia.idestadotransferencia',2)
        ->where('productotransferencia.idestado',2)
        ->sum('productotransferenciadetalle.cantidadenviado');
  
    $productotransferenciasrecpciones_recepcion = DB::table('productotransferenciadetalle')
        ->join('productotransferencia','productotransferencia.id','productotransferenciadetalle.idproductotransferencia')
        ->where('productotransferenciadetalle.idproducto',$idproducto)
        ->where('productotransferencia.idtiendaorigen',$idtienda)
        ->where('productotransferencia.idestadotransferencia',3)
        ->where('productotransferencia.idestado',2)
        ->sum('productotransferenciadetalle.cantidadrecepcion');
    $productotransferenciasrecpciones = $productotransferenciasrecpciones_envio+$productotransferenciasrecpciones_recepcion;
  
    $productomovimientossalida = DB::table('productomovimientodetalle')
        ->join('productomovimiento','productomovimiento.id','productomovimientodetalle.idproductomovimiento')
        ->where('productomovimientodetalle.idproducto',$idproducto)
        ->where('productomovimiento.idtienda',$idtienda)
        ->where('productomovimiento.idestadomovimiento',2)
        ->where('productomovimiento.idestado',2)
        ->sum('productomovimientodetalle.cantidad');
  
    $compradevoluciones = DB::table('compradevoluciondetalle')
        ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
        /*->join('aperturacierre','aperturacierre.id','compradevolucion.idaperturacierre')
        ->join('caja','caja.id','aperturacierre.idcaja')*/
        ->where('compradevoluciondetalle.idproducto',$idproducto)
        ->where('compradevolucion.idtienda',$idtienda)
        ->where('compradevolucion.idestado',2)
        ->sum('compradevoluciondetalle.cantidad');

    $egresos = $ventas+$productotransferenciasrecpciones+$productomovimientossalida+$compradevoluciones;
  
    $total = $ingresos-$egresos;
  

    $registroproducto = DB::table('registroproducto')
    ->where('registroproducto.idtienda',$idtienda)
    ->where('registroproducto.idproducto',$idproducto)
    ->where('registroproducto.idestado',2)
    ->orderBy('registroproducto.id','desc')
    ->limit(1)
    ->first(); 

    $ultimacantidadunidad = 0;
    if($registroproducto!=''){
        $ultimacantidadunidad = $registroproducto->ultimacantidadunidad;
    }

    return [
        'total' => $total,
        'total_registro' => $ultimacantidadunidad,
    ];
}

function actualizar_stock($modulo,$idmodulo,$idproducto,$cantidad,$idunidadmedida,$por,$idtienda,$signo='Ingreso'){
    // calcular stock según presentacion
    // $productopresentaciones = DB::table('productopresentacion')
    //     ->where('productopresentacion.idproducto',$idproducto)
    //     ->orderBy('productopresentacion.orden','asc')
    //     ->get();              

    $unidadmedidapor1 = 1;
    $unidadmedidapor2 = 1;
    $unidadmedidapor3 = 1;
    $unidadmedidapor4 = 1;
    // foreach($productopresentaciones as $value){
    //     if($value->idunidadmedida==1){
    //         $unidadmedidapor1 = $value->por;
    //     }elseif($value->idunidadmedida==2){
    //         $unidadmedidapor2 = $value->por;
    //     }elseif($value->idunidadmedida==3){
    //         $unidadmedidapor3 = $value->por;
    //     }elseif($value->idunidadmedida==4){
    //         $unidadmedidapor4 = $value->por;
    //     }
    // }
    // fin calcular stock según presentacion

    $cantidadunidad = 1;
    if($idunidadmedida==1){
        $cantidadunidad = $cantidad*$por;
    }elseif($idunidadmedida==2){
        $cantidadunidad = $cantidad*$por*$unidadmedidapor1;
    }elseif($idunidadmedida==3){
        $cantidadunidad = $cantidad*$por*$unidadmedidapor1*$unidadmedidapor2;
    }elseif($idunidadmedida==4){
        $cantidadunidad = $cantidad*$por*$unidadmedidapor1*$unidadmedidapor2*$unidadmedidapor3;
    }

    // Ultima cantidad 
    $registroproducto = DB::table('registroproducto')
        ->where('registroproducto.idtienda',$idtienda)
        ->where('registroproducto.idproducto',$idproducto)
        ->where('registroproducto.idunidadmedida',$idunidadmedida)
        ->where('registroproducto.por',$por)
        ->where('registroproducto.idestado',2)
        ->orderBy('registroproducto.id','desc')
        ->limit(1)
        ->first(); 
  
    $ultimacantidad = $cantidad;
    if($registroproducto!=''){
        if($signo=='Salida'){
            $ultimacantidad = $registroproducto->ultimacantidad-$cantidad;
        }else{
            $ultimacantidad = $registroproducto->ultimacantidad+$cantidad;
        }
    }
    // Ultima cantidad unidad
    $registroproducto = DB::table('registroproducto')
        ->where('registroproducto.idtienda',$idtienda)
        ->where('registroproducto.idproducto',$idproducto)
        ->where('registroproducto.idestado',2)
        ->orderBy('registroproducto.id','desc')
        ->limit(1)
        ->first(); 
  
    $ultimacantidadunidad = $cantidadunidad;
    if($registroproducto!=''){
        if($signo=='Salida'){
            $ultimacantidadunidad = $registroproducto->ultimacantidadunidad-$cantidadunidad;
        }else{
            $ultimacantidadunidad = $registroproducto->ultimacantidadunidad+$cantidadunidad;
        }
    }

    if($signo=='Salida'){
        $cantidadunidad = -$cantidadunidad;
        $cantidad = -$cantidad;
    }
  
    $count_registro = DB::table('registroproducto')
      ->where('idproducto', $idproducto)
      ->where('idtienda', $idtienda)
      ->count();

    if ($count_registro < 1) {
        $stockA = stock_producto($idtienda, $idproducto)['total'];
        DB::table('registroproducto')->insert([
            'fecharegistro' => Carbon\Carbon::now(),
            'fechaconfirmacion' => Carbon\Carbon::now(),
            'ultimacantidadunidad' => $stockA,
            'ultimacantidad' => $stockA,
            'cantidadunidad' => $stockA,
            'cantidad' => $stockA,
            'por' => 1,
            'idunidadmedida' =>1,
            'idproductomovimiento' => 0,
            'idproducto' =>  $idproducto,
            'idtienda' =>  $idtienda,
            'idestado' => 2
        ]);
    }else {
        DB::table('registroproducto')->insert([
            'fecharegistro' => Carbon\Carbon::now(),
            'fechaconfirmacion' => Carbon\Carbon::now(),
            'ultimacantidadunidad' => $ultimacantidadunidad,
            'ultimacantidad' => $ultimacantidad,
            'cantidadunidad' => $cantidadunidad,
            'cantidad' => $cantidad,
            'por' => $por,
            'idunidadmedida' => $idunidadmedida,
            'id'.$modulo => $idmodulo,
            'idproducto' =>  $idproducto,
            'idtienda' =>  $idtienda,
            'idestado' => 2
        ]);
    }
}

function saldousuario($idcliente,$idmoneda){
    $total_saldocliente = DB::table('tipopagodetalle')
        ->where('tipopagodetalle.saldo_cliente',$idcliente)
        ->where('tipopagodetalle.idmoneda',$idmoneda)
        ->where('tipopagodetalle.idestado',2)
        ->sum('tipopagodetalle.monto');
    return [
        'total' => $total_saldocliente
    ];
}
/* ---------------------  Subir Imagen -------------------*/
function uploadfile($text_imagen_eliminar,$text_imagen_anterior,$file_imagen_nueva,$ruta){
    if($text_imagen_anterior!='') {
        $imagen = $text_imagen_anterior;
    }else{
        if($text_imagen_eliminar!=''){
            uploadfile_eliminar($text_imagen_eliminar,$ruta);
        }
        $imagen = '';
        if($file_imagen_nueva!='') {
            if ($file_imagen_nueva->isValid()) {                  
                list($nombre,$ext) = explode(".", $file_imagen_nueva->getClientOriginalName());
                $imagen =  Carbon\Carbon::now()->format('dmYhms').rand(100000, 999999).'.png';
                $file_imagen_nueva->move(getcwd().$ruta, $imagen);
            }
        }
    }
    return $imagen;
}
function uploadfile_eliminar($text_imagen_eliminar,$ruta){
    $rutaimagen = getcwd().$ruta.$text_imagen_eliminar;
    if(file_exists($rutaimagen) && $text_imagen_eliminar!='') {
        unlink($rutaimagen);
    }
}

/* ---------------------------- cargar json productos ------------------------------------*/
function load_json_productos(){
    $productos = DB::table('producto')
                ->join('productocategoria','productocategoria.id','producto.idproductocategoria')
                ->join('productomarca','productomarca.id','producto.idproductomarca')
                ->join('productotalla','productotalla.id','producto.idproductotalla')
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                ->select(
                    'producto.id as id',
                    'producto.id as idproducto',
                    'productounidadmedida.nombre as productounidadmedida',
                    'productocategoria.nombre as productonombrecategoria',
                    'productomarca.nombre as productonombremarca',
                    'productotalla.nombre as productonombretalla',
                    'producto.preciotienda as preciotienda',
                    'producto.precio as precio',
                    'producto.codigoimpresion as codigoimpresion',
                    'producto.nombreproducto as nombreproducto'

                )
                ->orderBy('producto.id','asc')
                ->get();
  
        $json_string = json_encode(
          array(
            /*'draw' => 1,
            'recordsTotal' => 57,
            'recordsFiltered' => 57,*/
            'data' => $productos
          )
        );
        $file = getcwd().'/resources/views/layouts/backoffice/producto/clientes.json';
        file_put_contents($file, $json_string);
}
/*function load_json_productoscatalogo(){
        $productocatalogo = DB::table('productocatalogo')
              //  ->join('productomarca','productomarca.id','productocatalogo.idproductomarca')
                ->select(
                    'productocatalogo.id as id',
                    'productocatalogo.item as item',
                    'productocatalogo.pumptype as pumptype',
                    'productocatalogo.codigobomba as codigobomba',
                    'productocatalogo.codigoalternativo as codigoalternativo',
                    'productocatalogo.elemento as elemento',
                    'productocatalogo.compatibleelemento as compatibleelemento',
                    'productocatalogo.stampadoelemento as stampadoelemento',
                    'productocatalogo.diametroelemento as diametroelemento',
                    'productocatalogo.valvula as valvula',
                    'productocatalogo.motor as motor',
                    'productocatalogo.feedpump as feedpump',
                    'productocatalogo.tipodecilindro as tipodecilindro',
                    'productocatalogo.powerhp as powerhp',
                    'productocatalogo.variador as variador',
                    'productocatalogo.gobernadorbomba as gobernadorbomba',
                    'productocatalogo.oem as oem',
                    'productocatalogo.rpm as rpm',
                    'productocatalogo.marca as marca',
                   // 'productomarca.nombre as productomarca',
                )
                ->orderBy('productocatalogo.id','asc')
                ->get();
  
  
        $json_string = json_encode(
          array(
            'data' => $productocatalogo
          )
        );
        $file = getcwd().'/resources/views/layouts/backoffice/productocatalogo/clientes.json';
        file_put_contents($file, $json_string);
}*/
function consulta_sunat($ruc){
    $sunat = new \Sunat\Sunat();
    $consultaruc = $sunat->search($ruc);
    $direccion = $consultaruc->result->direccion;
    $ubigeo = '<option></option>';
    if($consultaruc->success==true){
        $direc = explode(' - ',$direccion);
        if(count($direc)>1){
            $dbubigeo = DB::table('ubigeo')
                ->where('provincia','LIKE','%'.$direc[1].'%')
                ->where('distrito','LIKE','%'.$direc[2].'%')
                ->limit(1)
                ->first();
            if($dbubigeo!=''){
                $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
                $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
                $ubigeo = '<option value="'.$dbubigeo->id.'">'.$dbubigeo->nombre.'</option>';
                $ubsearch = strtoupper(str_replace($no_permitidas, $permitidas ,$dbubigeo->departamento.' - '.$dbubigeo->provincia.' - '.$dbubigeo->distrito));
                $direccion = str_replace($ubsearch,'',$direccion);
            }
        }
    }
    return [ 
        'consultaruc' => $consultaruc,
        'ubigeo' => $ubigeo,
        'direccion' => $direccion,
    ];
}
/* ---------------------------- FORMA PAGO ------------------------------------*/
function formapago_validar($totalpagar,$request,$rules,$messages,$idtipomovimiento=0){
            // validar apertura de caja
            $idaperturacierre = 0;
            if($idtipomovimiento==1 or $idtipomovimiento==2){
                $aperturacierre = aperturacierre(usersmaster()->idtienda,Auth::user()->id);
                if($aperturacierre['resultado']=='ERROR'){
                    $rules = array_merge($rules,[
                       'error' => 'required',
                    ]);
                    $messages = array_merge($messages,[
                       'error.required' => $aperturacierre['mensaje'],
                    ]);
                }
                $idaperturacierre = $aperturacierre['idapertura'];
            }
            // validar forma pago
            $rules = array_merge($rules,[
                'idformapago' => 'required',
            ]);
            $messages = array_merge($messages,[
                'idformapago.required' => 'La "Forma de Pago" es Obligatorio.',
            ]);
            $montototal = 0;
            $idtipopago = 0;
            $idclientesaldo = 0;
            if($request->input('idformapago')==1){
              
                  //if($totalpagar>0 && $totalpagar!=''){
                      $rules = array_merge($rules,[
                          'seleccionartipopago' => 'required',
                      ]);
                      $messages = array_merge($messages,[
                          'seleccionartipopago.required' => 'Seleccionar tipo de pago.',
                      ]);
                      $servicio = explode('/&/',$request->input('seleccionartipopago'));
                      for($y=1; $y < count($servicio); $y++) {
                        $servic = explode('/,/',$servicio[$y]);
                        $tipopago = explode('/-/',$servic[11]);
                        // Efectivo
                        if($tipopago[1]==1){
                            $efectivo_total = explode('/-/',$servic[0]);
                            if($efectivo_total[1]==''){
                                $rules = array_merge($rules,[
                                    $efectivo_total[0] => 'required',
                                ]);
                                $messages = array_merge($messages,[
                                    $efectivo_total[0].'.required' => 'Ingrese un Monto a Pagar.',
                                ]);
                            }
                            $montototal = $montototal+($efectivo_total[1]!=''?$efectivo_total[1]:0);
                        }
                        // Deposito
                        elseif($tipopago[1]==2){
                            $deposito_banco = explode('/-/',$servic[1]);
                            $deposito_numerocuenta = explode('/-/',$servic[2]);
                            $deposito_fechadeposito = explode('/-/',$servic[3]);
                            $deposito_horadeposito = explode('/-/',$servic[4]);
                            $deposito_numerooperacion = explode('/-/',$servic[12]);
                            $deposito_total = explode('/-/',$servic[5]);
                            if($deposito_banco[1]==''){
                                $rules = array_merge($rules,[
                                    $deposito_banco[0] => 'required',
                                ]);   
                            }
                            if($deposito_numerocuenta[1]==''){
                                $rules = array_merge($rules,[
                                    $deposito_numerocuenta[0] => 'required',
                                ]); 
                            }
                            if($deposito_fechadeposito[1]==''){
                                $rules = array_merge($rules,[
                                    $deposito_fechadeposito[0] => 'required',
                                ]);  
                            }
                            if($deposito_horadeposito[1]==''){
                                $rules = array_merge($rules,[
                                    $deposito_horadeposito[0] => 'required',
                                ]);
                            }
                            if($deposito_numerooperacion[1]==''){
                                $rules = array_merge($rules,[
                                    $deposito_numerooperacion[0] => 'required',
                                    $deposito_total[0] => 'required',
                                ]); 
                            }
                            if($deposito_total[1]==''){
                                $rules = array_merge($rules,[
                                    $deposito_total[0] => 'required',
                                ]);  
                            }
                            $messages = array_merge($messages,[
                                $deposito_banco[0].'.required' => 'Seleccione un Banco.',
                                $deposito_numerocuenta[0].'.required' => 'Registre un numero de cuenta valido.',
                                $deposito_fechadeposito[0].'.required' => 'Seleccione una Fecha de Deposito.',
                                $deposito_horadeposito[0].'.required' => 'Seleccione una Hora de Deposito.',
                                $deposito_numerooperacion[0].'.required' => 'El Número de Operación es obligatorio',
                                $deposito_total[0].'.required' => 'Ingrese un Monto a Pagar.',
                            ]);
                            $montototal = $montototal+($deposito_total[1]!=''?$deposito_total[1]:0);
                        }
                        // Cheque
                        elseif($tipopago[1]==3){
                            $cheque_banco = explode('/-/',$servic[6]);
                            $cheque_fechaemision = explode('/-/',$servic[7]);
                            $cheque_fechavencimiento = explode('/-/',$servic[8]);
                            $cheque_numero = explode('/-/',$servic[9]);
                            $cheque_total = explode('/-/',$servic[10]);

                            if($cheque_banco[1]==''){
                                $rules = array_merge($rules,[
                                    $cheque_banco[0] => 'required',
                                ]);   
                            }
                            if($cheque_fechaemision[1]==''){
                                $rules = array_merge($rules,[
                                    $cheque_fechaemision[0] => 'required',
                                ]);   
                            }
                            if($cheque_fechavencimiento[1]==''){
                                $rules = array_merge($rules,[
                                    $cheque_fechavencimiento[0] => 'required',
                                ]);   
                            }
                            if($cheque_numero[1]==''){
                                $rules = array_merge($rules,[
                                    $cheque_numero[0] => 'required',
                                ]);   
                            }
                            if($cheque_total[1]==''){
                                $rules = array_merge($rules,[
                                    $cheque_total[0] => 'required',
                                ]);   
                            }
                            $messages = array_merge($messages,[
                                $cheque_banco[0].'.required' => 'Seleccione un Banco.',
                                $cheque_fechaemision[0].'.required' => 'Seleccione una Fecha de Emisión.',
                                $cheque_fechavencimiento[0].'.required' => 'Seleccione una Fecha de Vencimiento.',
                                $cheque_numero[0].'.required' => 'El Número de Cheque es obligatorio.',
                                $cheque_total[0].'.required' => 'Ingrese un Monto a Pagar.',
                            ]);
                            $montototal = $montototal+($cheque_total[1]!=''?$cheque_total[1]:0);
                        }
                        // Saldo
                        elseif($tipopago[1]==4){
                            $saldo_cliente = explode('/-/',$servic[13]);
                            $saldo_total = explode('/-/',$servic[14]);

                            if($saldo_cliente[1]==''){
                                $rules = array_merge($rules,[
                                    $saldo_cliente[0] => 'required',
                                ]);   
                            }
                            if($saldo_total[1]==''){
                                $rules = array_merge($rules,[
                                    $saldo_total[0] => 'required',
                                ]);   
                            }
                            $messages = array_merge($messages,[
                                $saldo_cliente[0].'.required' => 'Seleccione el Cliente.',
                                $saldo_total[0].'.required' => 'Ingrese un Monto a Pagar.',
                            ]);

                            $idclientesaldo = $saldo_cliente[1];
                            $montototal = $montototal+($saldo_total[1]!=''?$saldo_total[1]:0);
                        }

                        $idtipopago = $tipopago[1];
                     }
                  //}
              
                  // validar con monto solicitado     
                  if($totalpagar!=''){
                      if($montototal > $totalpagar){
                         $rules = array_merge($rules,[
                             'error' => 'required',
                         ]);
                         $messages = array_merge($messages,[
                             'error.required' => 'El "Total a pagar" es mayor al Total.',
                         ]);
                      }
                      if($montototal < $totalpagar){
                         $rules = array_merge($rules,[
                             'error' => 'required',
                         ]);
                         $messages = array_merge($messages,[
                             'error.required' => 'El "Total a pagar" es menor al Total.',
                         ]);
                      }
                  }
                
                  // validar saldo
                  if($idtipomovimiento==1 or $idtipomovimiento==2){
                      if($idtipopago==4){
                          if($idtipomovimiento==1){
                              $efectivo = saldousuario($idclientesaldo,$request->input('idmoneda'));
                              if($montototal>$efectivo['total']){
                                  $rules = array_merge($rules,[
                                     'error' => 'required',
                                  ]);
                                  $messages = array_merge($messages,[
                                     'error.required' => 'El cliente no tiene suficiente saldo!.'
                                  ]);
                              }
                          }
                      }else{
                          if($idtipomovimiento==2){
                              $efectivo = efectivo($idaperturacierre,$request->input('idmoneda'));
                              if($montototal>$efectivo['total']){
                                  $rules = array_merge($rules,[
                                     'error' => 'required',
                                  ]);
                                  $messages = array_merge($messages,[
                                     'error.required' => 'No hay suficiente saldo en caja!.'.$request->input('idmoneda')
                                  ]);
                              }
                          }
                      }
                  }
            }elseif($request->input('idformapago')==2){
                $montototal = $totalpagar;
                $rules = array_merge($rules,[
                    'creditoiniciopago' => 'required',
                    'creditofrecuencia' => 'required',
                    'creditodias' => 'required',
                    'creditoultimopago' => 'required',
                ]);
                $messages = array_merge($messages,[
                    'creditoiniciopago.required' => 'La "Fecha de inicio" es Obligatorio.',
                    'creditofrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
                    'creditodias.required' => 'Los "Días" son Obligatorio.',
                    'creditoultimopago.required' => 'La "Ultima fecha" es Obligatorio.',
                ]);
            }elseif($request->input('idformapago')==3){
                $montototal = $totalpagar;
                $rules = array_merge($rules,[
                    'letraidgarante' => 'required',
                    'letrafechainicio' => 'required',
                    'letrafrecuencia' => 'required',
                    'letracuota' => 'required|numeric|min:1',
                ]);
                $messages = array_merge($messages,[
                    'letraidgarante.required' => 'El "Aval ó Garante" es Obligatorio.',
                    'letrafechainicio.required' => 'La "Fecha de inicio" es Obligatorio.',
                    'letrafrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
                    'letracuota.required' => 'Las "Cuotas" son Obligatorio.',
                    'letracuota.min' => 'La "Cuota" mínima es 1.',
                ]);
              
    
                    $cuotas = explode('/&/',$request->input('listarcuotasletra'));
                    $montototal = 0;
                    $valid_numletra = [];
                    for($i=1; $i < count($cuotas); $i++) {
                        $cuot = explode('/,/',$cuotas[$i]);
                        $letra_numerounico = explode('/-/',$cuot[1]);
                        $letra_fecha = explode('/-/',$cuot[2]);
                        $letra_monto = explode('/-/',$cuot[3]);
                      
                        if($letra_numerounico[1]==''){
                            $rules = array_merge($rules,[
                                $letra_numerounico[0] => 'required',
                            ]);   
                        }
                        if($letra_fecha[1]==''){
                            $rules = array_merge($rules,[
                                $letra_fecha[0] => 'required',
                            ]);   
                        }
                        if($letra_monto[1]==''){
                            $rules = array_merge($rules,[
                                $letra_monto[0] => 'required',
                            ]);   
                        }
                        $messages = array_merge($messages,[
                            $letra_numerounico[0].'.required' => 'El "N° de Letra" es obligatorio.',
                            $letra_fecha[0].'.required' => 'La "Ultima Fecha" es obligatorio.',
                            $letra_monto[0].'.required' => 'El "Importe" es obligatorio.',
                        ]);
                        if (in_array($letra_numerounico[1], $valid_numletra)) {
                            $rules = array_merge($rules,[
                                'errorsistema' => 'required',
                            ]);
                            $messages = array_merge($messages,[
                                'errorsistema.required' => 'Hay duplicados en el campo "N° de Letra"!!.',
                            ]);
                        }
                        $valid_numletra[] = $letra_numerounico[1];
                    
                        $montototal = $montototal+($letra_monto[1]!=''?$letra_monto[1]:0);
                    } 
              
                    if($montototal > $totalpagar){
                       $rules = array_merge($rules,[
                           'error' => 'required',
                       ]);
                       $messages = array_merge($messages,[
                           'error.required' => 'El "Monto a pagar" es mayor al Total.',
                       ]);
                   }elseif($montototal < $totalpagar){
                       $rules = array_merge($rules,[
                           'error' => 'required',
                       ]);
                       $messages = array_merge($messages,[
                           'error.required' => 'El "Monto a pagar" es menor al Total.',
                       ]);
                   }
              
            }
            return [
                'rules' => $rules,
                'messages' => $messages,
                'idaperturacierre' => $idaperturacierre,
                'total' => $montototal
            ];
}
function formapago_insertar($request,$modulo,$idmodulo){
    $idtipomovimiento = 0;
    if($modulo=='movimiento'){
        $tablemodulo = DB::table('movimiento')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusuario as idusersresponsable',
                    'idestado as idestado',
                    'idtipomovimiento as idtipomovimiento'
                )
                ->first();
        $idtipomovimiento = $tablemodulo->idtipomovimiento;
    }elseif($modulo=='compra'){
        $tablemodulo = DB::table('compra')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusuarioresponsable as idusersresponsable',
                    'idestado as idestado'
                )
                ->first();
        $idtipomovimiento = 2;
    }elseif($modulo=='compradevolucion'){
        $tablemodulo = DB::table('compradevolucion')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusers as idusersresponsable',
                    'idestado as idestado'
                )
                ->first();
        $idtipomovimiento = 1;
    }elseif($modulo=='pagocredito'){
        $tablemodulo = DB::table('pagocredito')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusuario as idusersresponsable',
                    'idestado as idestado'
                )
                ->first();
        $idtipomovimiento = 2;
    }elseif($modulo=='pagoletra'){
        $tablemodulo = DB::table('pagoletra')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusuario as idusersresponsable',
                    'idestado as idestado'
                )
                ->first();
        $idtipomovimiento = 2;
    }elseif($modulo=='venta'){
        $tablemodulo = DB::table('venta')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusuariocajero as idusersresponsable',
                    DB::raw('IF(idestado=3,2,1) as idestado')
                )
                ->first();
        $idtipomovimiento = 1;
    }elseif($modulo=='notadevolucion'){
        $tablemodulo = DB::table('notadevolucion')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusuarioresponsable as idusersresponsable',
                    'idestado as idestado'
                )
                ->first();
        $idtipomovimiento = 2;
    }elseif($modulo=='cobranzacredito'){
        $tablemodulo = DB::table('cobranzacredito')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusuario as idusersresponsable',
                    'idestado as idestado'
                )
                ->first();
        $idtipomovimiento = 1;
    }elseif($modulo=='cobranzaletra'){
        $tablemodulo = DB::table('cobranzaletra')
                ->whereId($idmodulo)
                ->select(
                    'idmoneda as idmoneda',
                    'idaperturacierre as idaperturacierre',
                    'idusuario as idusersresponsable',
                    'idestado as idestado'
                )
                ->first();
        $idtipomovimiento = 1;
    }
  
            if($request->input('idformapago')==1){
                  $servicio = explode('/&/',$request->input('seleccionartipopago'));
                  for($i=1; $i < count($servicio); $i++) {
                      $servic = explode('/,/',$servicio[$i]);
                      $tipopago = explode('/-/',$servic[11]);
                      //----------> Contado Efectivo
                      if($tipopago[1]==1){
                          $efectivo_total = explode('/-/',$servic[0]);
                          
                          if($efectivo_total[1]>0 && $efectivo_total[1]!=''){
                              DB::table('tipopagodetalle')->insertGetId([
                                  'fecharegistro' => Carbon\Carbon::now(),
                                  'fechaconfirmacion' => Carbon\Carbon::now(),
                                  'monto' => $efectivo_total[1],
                                  'deposito_banco' => 0,
                                  'deposito_numerocuenta' => '',
                                  'deposito_fecha' => '',
                                  'deposito_hora' => '',
                                  'deposito_numerooperacion' => '',
                                  'cheque_banco' => 0,
                                  'cheque_emision' => '',
                                  'cheque_vencimiento' => '',
                                  'cheque_numero' => '',
                                  'saldo_cliente' => 0,
                                  'idtipopago' => $tipopago[1],
                                  'idmoneda' => $tablemodulo->idmoneda,
                                  'idaperturacierre' => $tablemodulo->idaperturacierre,
                                  'id'.$modulo => $idmodulo,
                                  'idusersresponsable' => $tablemodulo->idusersresponsable,
                                  'idestado' => $tablemodulo->idestado
                              ]); 
                          }
                          
                      }
                      elseif($tipopago[1]==2){
                          $deposito_banco = explode('/-/',$servic[1]);
                          $deposito_numerocuenta = explode('/-/',$servic[2]);
                          $deposito_fechadeposito = explode('/-/',$servic[3]);
                          $deposito_horadeposito = explode('/-/',$servic[12]);
                          $deposito_numerooperacion = explode('/-/',$servic[4]);
                          $deposito_total = explode('/-/',$servic[5]);
                        
                          if($deposito_total[1]>0 && $deposito_total[1]!=''){
                              DB::table('tipopagodetalle')->insertGetId([
                                  'fecharegistro' => Carbon\Carbon::now(),
                                  'fechaconfirmacion' => Carbon\Carbon::now(),
                                  'monto' => $deposito_total[1],
                                  'deposito_banco' => $deposito_banco[1],
                                  'deposito_numerocuenta' => $deposito_numerocuenta[1],
                                  'deposito_fecha' => $deposito_fechadeposito[1],
                                  'deposito_hora' => $deposito_horadeposito[1],
                                  'deposito_numerooperacion' => $deposito_numerooperacion[1],
                                  'cheque_banco' => 0,
                                  'cheque_emision' => '',
                                  'cheque_vencimiento' => '',
                                  'cheque_numero' => '',
                                  'saldo_cliente' => 0,
                                  'idtipopago' => $tipopago[1],
                                  'idmoneda' => $tablemodulo->idmoneda,
                                  'idaperturacierre' => $tablemodulo->idaperturacierre,
                                  'id'.$modulo => $idmodulo,
                                  'idusersresponsable' => $tablemodulo->idusersresponsable,
                                  'idestado' => $tablemodulo->idestado
                              ]);
                          }  
                      }
                      elseif($tipopago[1]==3){
                          $cheque_banco = explode('/-/',$servic[6]);
                          $cheque_fechaemision = explode('/-/',$servic[7]);
                          $cheque_fechavencimiento = explode('/-/',$servic[8]);
                          $cheque_numero = explode('/-/',$servic[9]);
                          $cheque_total = explode('/-/',$servic[10]);
                        
                          if($cheque_total[1]>0 && $cheque_total[1]!=''){
                              DB::table('tipopagodetalle')->insertGetId([
                                  'fecharegistro' => Carbon\Carbon::now(),
                                  'fechaconfirmacion' => Carbon\Carbon::now(),
                                  'monto' => $cheque_total[1],
                                  'deposito_banco' => 0,
                                  'deposito_numerocuenta' => '',
                                  'deposito_fecha' => '',
                                  'deposito_hora' => '',
                                  'deposito_numerooperacion' => '',
                                  'cheque_banco' => $cheque_banco[1],
                                  'cheque_emision' => $cheque_fechaemision[1],
                                  'cheque_vencimiento' => $cheque_fechavencimiento[1],
                                  'cheque_numero' => $cheque_numero[1],
                                  'saldo_cliente' => 0,
                                  'idtipopago' => $tipopago[1],
                                  'idmoneda' => $tablemodulo->idmoneda,
                                  'idaperturacierre' => $tablemodulo->idaperturacierre,
                                  'id'.$modulo => $idmodulo,
                                  'idusersresponsable' => $tablemodulo->idusersresponsable,
                                  'idestado' => $tablemodulo->idestado
                              ]);
                          }
                              
                      }
                      elseif($tipopago[1]==4){
                          $saldo_cliente = explode('/-/',$servic[13]);
                          $saldo_total = explode('/-/',$servic[14]);
                          if($saldo_total[1]>0 && $saldo_total[1]!=''){
                              DB::table('tipopagodetalle')->insertGetId([
                                  'fecharegistro' => Carbon\Carbon::now(),
                                  'fechaconfirmacion' => Carbon\Carbon::now(),
                                  'monto' => $saldo_total[1],
                                  'deposito_banco' => 0,
                                  'deposito_numerocuenta' => '',
                                  'deposito_fecha' => '',
                                  'deposito_hora' => '',
                                  'deposito_numerooperacion' => '',
                                  'cheque_banco' => 0,
                                  'cheque_emision' => '',
                                  'cheque_vencimiento' => '',
                                  'cheque_numero' => '',
                                  'saldo_cliente' => $saldo_cliente[1],
                                  'idtipopago' => $tipopago[1],
                                  'idmoneda' => $tablemodulo->idmoneda,
                                  'idaperturacierre' => $tablemodulo->idaperturacierre,
                                  'id'.$modulo => $idmodulo,
                                  'idusersresponsable' => $tablemodulo->idusersresponsable,
                                  'idestado' => $tablemodulo->idestado
                              ]);
                            
                              if($tablemodulo->idestado==2){
                                  //registrar saldo cliente
                                  $userssaldo = DB::table('userssaldo')
                                      ->orderBy('userssaldo.codigo','desc')
                                      ->limit(1)
                                      ->first();
                                  $codigo = 1;
                                  if($userssaldo!=''){
                                      $codigo = $userssaldo->codigo+1;
                                  }  
                                  $iduserssaldo = DB::table('userssaldo')->insertGetId([
                                      'fecharegistro' => Carbon\Carbon::now(),
                                      'fechaconfirmacion' => Carbon\Carbon::now(),
                                      'codigo' => $codigo,
                                      'monto' => $saldo_total[1],
                                      'motivo' => $modulo,
                                      'idformapago' => 1,
                                      'idtipomovimiento' => $idtipomovimiento==1?2:1,
                                      'idmoneda' => $tablemodulo->idmoneda,
                                      'idusuarioresponsable' => $tablemodulo->idusersresponsable,
                                      'idaperturacierre' => $tablemodulo->idaperturacierre,
                                      'idestado' => $tablemodulo->idestado
                                  ]);

                                  DB::table('tipopagodetalle')->insertGetId([
                                      'fecharegistro' => Carbon\Carbon::now(),
                                      'fechaconfirmacion' => Carbon\Carbon::now(),
                                      'monto' => $saldo_total[1],
                                      'deposito_banco' => 0,
                                      'deposito_numerocuenta' => '',
                                      'deposito_fecha' => '',
                                      'deposito_hora' => '',
                                      'deposito_numerooperacion' => '',
                                      'cheque_banco' => 0,
                                      'cheque_emision' => '',
                                      'cheque_vencimiento' => '',
                                      'cheque_numero' => '',
                                      'saldo_cliente' => 0,
                                      'idtipopago' => 1,
                                      'idmoneda' => $tablemodulo->idmoneda,
                                      'idaperturacierre' => $tablemodulo->idaperturacierre,
                                      'iduserssaldo' => $iduserssaldo,
                                      'idusersresponsable' => $tablemodulo->idusersresponsable,
                                      'idestado' => $tablemodulo->idestado
                                  ]);
                              }  
                          }
                      }
                  }
            }elseif($request->input('idformapago')==2){
            }elseif($request->input('idformapago')==3){
                    $cuotas = explode('/&/',$request->input('listarcuotasletra'));
                    $valid_numletra = [];
                    for($i=1; $i < count($cuotas); $i++) {
                        $cuot = explode('/,/',$cuotas[$i]);
                        $numero = explode('/-/',$cuot[0]);
                        $letra_numerounico = explode('/-/',$cuot[1]);
                        $letra_fecha = explode('/-/',$cuot[2]);
                        $letra_monto = explode('/-/',$cuot[3]);
                      
                        DB::table('tipopagoletra')->insert([
                            'numero' => $numero[1],
                            'numeroletra' => $letra_numerounico[1],
                            'numerounico' => '',
                            'fecha' => $letra_fecha[1],
                            'monto' => $letra_monto[1],
                            'id'.$modulo => $idmodulo,
                            'idusersresponsable' => $tablemodulo->idusersresponsable,
                            'idestado' => $tablemodulo->idestado
                        ]);
                    } 
            }
}
function tipopago_detalle($modulo,$idmodulo){
    $tipopagodetalles = DB::table('tipopagodetalle')
        ->join('tipopago','tipopago.id','tipopagodetalle.idtipopago')
        ->join('moneda','moneda.id','tipopagodetalle.idmoneda')
        //->leftJoin('bancocuentabancaria','bancocuentabancaria.id','tipopagodetalle.deposito_banco')
        //->leftJoin('banco as bancodeposito','bancodeposito.id','bancocuentabancaria.idbanco')
        //->leftJoin('banco as bancodeposito','bancodeposito.id','tipopagodetalle.deposito_banco')
        ->leftJoin('banco as bancocheque','bancocheque.id','tipopagodetalle.cheque_banco')
        ->leftJoin('users as usuariocliente','usuariocliente.id','tipopagodetalle.saldo_cliente')
        ->where('id'.$modulo,$idmodulo)
        ->select(
              'tipopagodetalle.*',
              'tipopago.nombre as tipopagonombre',
              'moneda.simbolo as monedasimbolo',
              //'bancodeposito.nombre as bancodepositonombre',
              'bancocheque.nombre as bancochequenombre',
              'usuariocliente.identificacion as identificacion',
              DB::raw('IF(usuariocliente.idtipopersona=1,
              CONCAT(usuariocliente.apellidos,", ",usuariocliente.nombre),
              CONCAT(usuariocliente.apellidos)) as cliente')
        )
        ->orderBy('tipopagodetalle.id','asc')
        ->get();
    $detalle = '';
  
    $detalle_array = []; //Variable para devolver datos sin html
    foreach($tipopagodetalles as $tvalue){
      
        if($tvalue->idtipopago==1){
            $detalle = $detalle.'<i class="fa fa-check"></i> '.$tvalue->tipopagonombre.' ('.$tvalue->monedasimbolo.' '.$tvalue->monto.')<br>';
          
            $detalle_array = [
              'tipopagonombre' => $tvalue->tipopagonombre,
              'monedasimbolo' => $tvalue->monedasimbolo,
              'monto' => $tvalue->monto,
              'banco' => '',
              'nrooperacion' => '',
              'fecha' => '',
              'numero' => '',
              'emision' => '',
              'vcto' => '',
              'identificacion' => '',
              'cliente' =>'',
            ];
          
        }elseif($tvalue->idtipopago==2){
          
            
            $bancodepositonombre = ''; 
            if($tvalue->id < 4601){
                $bancodeposito = DB::table('banco')
                    ->where('banco.id',$tvalue->deposito_banco)
                    ->select(
                          'banco.nombre as bancodepositonombre'
                    )
                    ->first();
               $bancodepositonombre = $bancodeposito->bancodepositonombre;
            }else{
                $bancodeposito = DB::table('banco')
                    ->leftJoin('bancocuentabancaria','bancocuentabancaria.idbanco','banco.id')
                    ->where('bancocuentabancaria.id',$tvalue->deposito_banco)
                    ->select(
                          'banco.nombre as bancodepositonombre'
                    )
                    ->first();   
                if($bancodeposito!=''){
                    $bancodepositonombre = $bancodeposito->bancodepositonombre;
                }
            }
          
            $detalle = $detalle.'<i class="fa fa-check"></i> '.$tvalue->tipopagonombre.' ('.$tvalue->monedasimbolo.' '.$tvalue->monto.') - 
            <b>Banco:</b> '.$bancodepositonombre.', 
            <b>N° Ope.:</b> '.$tvalue->deposito_numerooperacion.', 
            <b>Fecha:</b> '.$tvalue->deposito_fecha.' '.$tvalue->deposito_hora.'<br>';
          
            $detalle_array = [
            'tipopagonombre' => $tvalue->tipopagonombre,
              'monedasimbolo' => $tvalue->monedasimbolo,
              'monto' => $tvalue->monto,
              'banco' => $bancodepositonombre,
              'nrooperacion' => $tvalue->deposito_numerooperacion,
              'fecha' => $tvalue->deposito_fecha.' '.$tvalue->deposito_hora,
              'numero' => '',
              'emision' => '',
              'vcto' => '',
              'identificacion' => '',
              'cliente' =>'',
            ];
          
        }elseif($tvalue->idtipopago==3){
                $bancodeposito = DB::table('banco')
                    ->where('banco.id',$tvalue->deposito_banco)
                    ->select(
                          'banco.nombre as bancodepositonombre'
                    )
                    ->first();
          
                $bancodepositonombre = '';
                if($bancodeposito!=''){
                    $bancodepositonombre = $bancodeposito->bancodepositonombre;
                }
          
          
            $detalle = $detalle.'<i class="fa fa-check"></i> '.$tvalue->tipopagonombre.' ('.$tvalue->monedasimbolo.' '.$tvalue->monto.') - 
            <b>Banco:</b> '.$tvalue->bancochequenombre.', 
            <b>Número:</b> '.$tvalue->cheque_numero.', 
            <b>Emisión:</b> '.$tvalue->cheque_emision.', 
            <b>Vcto:</b> '.$tvalue->cheque_vencimiento.' '.$tvalue->deposito_hora.'<br>';
          
           $detalle_array = [
              'tipopagonombre' => $tvalue->tipopagonombre,
              'monedasimbolo' => $tvalue->monedasimbolo,
              'monto' => $tvalue->monto,
              'banco' => $bancodepositonombre,
              'nrooperacion' => $tvalue->deposito_numerooperacion,
              'fecha' => $tvalue->deposito_fecha.' '.$tvalue->deposito_hora,
              'numero' => $tvalue->cheque_numero,
              'emision' => $tvalue->cheque_emision,
              'vcto' => $tvalue->cheque_vencimiento.' '.$tvalue->deposito_hora,
              'identificacion' => '',
              'cliente' =>'',
            ];
          
        }elseif($tvalue->idtipopago==4){
                $bancodeposito = DB::table('banco')
                    ->where('banco.id',$tvalue->deposito_banco)
                    ->select(
                          'banco.nombre as bancodepositonombre'
                    )
                    ->first();
          
                $bancodepositonombre = '';
                if($bancodeposito!=''){
                    $bancodepositonombre = $bancodeposito->bancodepositonombre;
                }
          
          
            $detalle = $detalle.'<i class="fa fa-check"></i> '.$tvalue->tipopagonombre.' ('.$tvalue->monedasimbolo.' '.$tvalue->monto.') - 
            <b>Identificación:</b> '.$tvalue->identificacion.', 
            <b>Cliente:</b> '.$tvalue->cliente.'<br>';
          
            $detalle_array = [
              'tipopagonombre' => $tvalue->tipopagonombre,
              'monedasimbolo' => $tvalue->monedasimbolo,
              'monto' => $tvalue->monto,
              'banco' => $bancodepositonombre,
              'nrooperacion' => $tvalue->deposito_numerooperacion,
              'fecha' => $tvalue->deposito_fecha.' '.$tvalue->deposito_hora,
              'numero' => $tvalue->cheque_numero,
              'emision' => $tvalue->cheque_emision,
              'vcto' => $tvalue->cheque_vencimiento.' '.$tvalue->deposito_hora,
              'identificacion' => $tvalue->identificacion,
              'cliente' => $tvalue->cliente,
            ];
        }
    }
    return [
        'detalle' => $detalle,
        'detalle_array' => $detalle_array
    ];
}
// ---------------------------- FACTURADOR ---------------------------- //
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Document;
use Greenter\Model\Sale\Note;
//use Greenter\Model\Sale\BaseSale;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Summary\SummaryPerception;
use Greenter\Report\Filter\ImageFilter;
use Greenter\Report\Render\QrRender;
// GUIA REMISION
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Response\BillResult;

function facturador($data) {
        // Conexión SUNAT
        $see = new \Greenter\See();
        if($data->facturador_idestado==2){
            $see->setService(SunatEndpoints::FE_PRODUCCION);
            $sunat_usuario = $data->sunat_usuario;
            $sunat_clave = $data->sunat_clave;
            $raiz = 'public/sunat/produccion/';
            $sunat_certificado = url($raiz.'certificado/'.$data->sunat_certificado);
        }else{
            $see->setService(SunatEndpoints::FE_BETA);
            $sunat_usuario = 'MODDATOS';
            $sunat_clave = 'moddatos';
            $raiz = 'public/sunat/beta/';
            $sunat_certificado = url($raiz.'certificado/certificate_demo.pem');
        }
      
        $see->setCertificate(file_get_contents($sunat_certificado));
        $see->setCredentials($data->emisor_ruc.$sunat_usuario, $sunat_clave);

        // Emisor
        $address = new Address();
        $address->setUbigueo($data->emisor_ubigeo)
            ->setDepartamento($data->emisor_departamento)
            ->setProvincia($data->emisor_provincia)
            ->setDistrito($data->emisor_distrito)
            ->setUrbanizacion($data->emisor_urbanizacion)
            ->setDireccion($data->emisor_direccion);

        $company = new Company();
        $company->setRuc($data->emisor_ruc)
            ->setRazonSocial($data->emisor_razonsocial)
            ->setNombreComercial($data->emisor_nombrecomercial)
            ->setAddress($address);

        return [
            'see' => $see,
            'company' => $company,
            'raiz' => $raiz
        ];
}

function facturador_guia($data) {
 $see = new \Greenter\See();
  
 if($data->facturador_idestado == 2){
      $see->setService(SunatEndpoints::GUIA_PRODUCCION);
      $sunat_usuario = $data->sunat_usuario;
      $sunat_clave = $data->sunat_clave;
      $raiz = 'public/sunat/produccion/';
      $sunat_certificado = url($raiz.'certificado/'.$data->sunat_certificado);
  }else{
      $see->setService(SunatEndpoints::GUIA_BETA);
      $sunat_usuario = 'MODDATOS';
      $sunat_clave = 'moddatos';
      $raiz = 'public/sunat/beta/';
      $sunat_certificado = url($raiz.'certificado/certificate_demo.pem');
  }
  
  $see->setCertificate(file_get_contents($sunat_certificado));
  $see->setCredentials($data->emisor_ruc.$sunat_usuario, $sunat_clave);

  // Emisor
  $address = new Address();
  $address->setUbigueo($data->emisor_ubigeo)
      ->setDepartamento($data->emisor_departamento)
      ->setProvincia($data->emisor_provincia)
      ->setDistrito($data->emisor_distrito)
      ->setUrbanizacion($data->emisor_urbanizacion)
      ->setDireccion($data->emisor_direccion);

  $company = new Company();
  $company->setRuc($data->emisor_ruc)
      ->setRazonSocial($data->emisor_razonsocial)
      ->setNombreComercial($data->emisor_nombrecomercial)
      ->setAddress($address);

  return [
      'see' => $see,
      'company' => $company,
      'raiz' => $raiz
  ];
}

function facturador_facturaboleta($idfacturacionboletafactura){
  
        $data = DB::table('facturacionboletafactura')
            ->join('agencia','agencia.id','facturacionboletafactura.idagencia')
            ->join('tienda','tienda.id','facturacionboletafactura.idtienda')
            ->leftJoin('venta','venta.id','facturacionboletafactura.idventa')
            ->where('facturacionboletafactura.id',$idfacturacionboletafactura)
            ->select(
                'facturacionboletafactura.*',
                'venta.codigo as ventacodigo',
                'tienda.facturador_idestado as facturador_idestado',
                'agencia.sunat_usuario as sunat_usuario',
                'agencia.sunat_clave as sunat_clave',
                'agencia.sunat_certificado as sunat_certificado'
            )
            ->first();
  
        $facturador = facturador($data);
       
        // Cliente
        $addresscl = new Address();
        $addresscl->setUbigueo($data->cliente_ubigeo)
            ->setDepartamento($data->cliente_departamento)
            ->setProvincia($data->cliente_provincia)
            ->setDistrito($data->cliente_distrito)
            ->setUrbanizacion($data->cliente_urbanizacion)
            ->setDireccion($data->cliente_direccion);
      
        $client = new Client();
        $client->setTipoDoc($data->cliente_tipodocumento)
            ->setNumDoc($data->cliente_numerodocumento)
            ->setRznSocial($data->cliente_razonsocial)
            ->setAddress($addresscl);
      
        // Venta
        $invoice = (new Invoice())
            ->setUblVersion($data->venta_ublversion)
            ->setTipoOperacion($data->venta_tipooperacion) // Catalog. 51
            ->setTipoDoc($data->venta_tipodocumento)
            ->setSerie($data->venta_serie)
            ->setCorrelativo($data->venta_correlativo)
            ->setFechaEmision(\DateTime::createFromFormat('Y-m-d H:i:s', $data->venta_fechaemision))
            ->setTipoMoneda($data->venta_tipomoneda)
            ->setClient($client)
            ->setMtoOperGravadas($data->venta_montooperaciongravada)
            ->setMtoIGV($data->venta_montoigv)
            ->setTotalImpuestos($data->venta_totalimpuestos)
            ->setValorVenta($data->venta_valorventa)
            ->setSubTotal($data->venta_montoimpuestoventa)
            ->setMtoImpVenta($data->venta_montoimpuestoventa)
            ->setCompany($facturador['company']);

        $datadetalles = DB::table('facturacionboletafacturadetalle')
                ->where('facturacionboletafacturadetalle.idfacturacionboletafactura',$data->id)
                ->orderBy('facturacionboletafacturadetalle.id','asc')
                ->get();
        $item = [];
        foreach($datadetalles as $value){
            $item[] = (new SaleDetail())
                ->setCodProducto($value->codigoproducto)
                ->setUnidad($value->unidad)
                ->setCantidad($value->cantidad)
                ->setDescripcion($value->descripcion)
                ->setMtoBaseIgv($value->montobaseigv)
                ->setPorcentajeIgv($value->porcentajeigv)
                ->setIgv($value->igv)
                ->setTipAfeIgv($value->tipoafectacionigv)
                ->setTotalImpuestos($value->totalimpuestos)
                ->setMtoValorVenta($value->montovalorventa)
                ->setMtoValorUnitario($value->montovalorunitario)
                ->setMtoPrecioUnitario($value->montopreciounitario);
        }
      
        $legend = (new Legend())
            ->setCode($data->leyenda_codigo)
            ->setValue($data->leyenda_value);
      
        $invoice->setDetails($item)
                ->setLegends([$legend]);
      
        // QR
        $crearqr = new QrRender();
        $base64_qr = new ImageFilter();
        $minewqr = $base64_qr->toBase64($crearqr->getImage($invoice));
        // Fin QR

        // Envio SUNAT
        $result = $facturador['see']->send($invoice);
        if ($result->isSuccess()) {
          
            $codigo_cdr          = $result->getCdrResponse()->getCode();
            $note_cdr            = $result->getCdrResponse()->getNotes();
          
            file_put_contents($facturador['raiz'].'boletafactura/'.$invoice->getName().'.xml', $facturador['see']->getFactory()->getLastXml());
            file_put_contents($facturador['raiz'].'boletafactura/'.'R-'.$invoice->getName().'.zip', $result->getCdrZip());
          
            DB::table('facturacionboletafactura')->whereId($idfacturacionboletafactura)->update([
                'estadofacturacion' => $result->getCdrResponse()->getDescription(),
                'codigo_cdr'          => $codigo_cdr,
                'note_cdr'            => $note_cdr,
                'venta_qr' => $minewqr,
                'idestadofacturacion' => 1, // correcto
                'idestadosunat' => $data->facturador_idestado
            ]);
          
            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $result->getCdrResponse()->getDescription(),
                'data' => $data,
                'qr' => $minewqr
            ];
        }else{
            //1033 = El comprobante F005-888 fue informado anteriormente
            //1032 - El comprobante ya esta informado y se encuentra con estado anulado o rechazado
            if($result->getError()->getCode()==1032 or $result->getError()->getCode()==1033){
                DB::table('facturacionboletafactura')->whereId($idfacturacionboletafactura)->update([
                    'estadofacturacion' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                    'venta_qr' => $minewqr,
                    'idestadofacturacion' => 1, // REENVIADO correcto
                    'idestadosunat' => $data->facturador_idestado
                ]);

                return [
                    'resultado' => 'CORRECTO',
                    'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                    'data' => $data,
                    'qr' => $minewqr
                ];
            }else{
                DB::table('facturacionboletafactura')->whereId($idfacturacionboletafactura)->update([
                    'estadofacturacion' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                    'venta_qr' => $minewqr,
                    'idestadofacturacion' => 2, // error
                    'idestadosunat' => $data->facturador_idestado
                ]);

                return [
                    'resultado' => 'ERROR',
                    'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                    'data' => $data,
                    'qr' => $minewqr
                ];
            }
                
        }
}

function facturador_notacredito($idfacturacionnotacredito){
      
        $data = DB::table('facturacionnotacredito')
            ->join('agencia','agencia.id','facturacionnotacredito.idagencia')
            ->join('tienda','tienda.id','facturacionnotacredito.idtienda')
            ->where('facturacionnotacredito.id',$idfacturacionnotacredito)
            ->select(
                'facturacionnotacredito.*',
                'tienda.facturador_idestado as facturador_idestado',
                'agencia.sunat_usuario as sunat_usuario',
                'agencia.sunat_clave as sunat_clave',
                'agencia.sunat_certificado as sunat_certificado'
            )
            ->first();
  
        $facturador = facturador($data);
 
        // Cliente
        $client = new Client();
        $client->setTipoDoc($data->cliente_tipodocumento)
            ->setNumDoc($data->cliente_numerodocumento)
            ->setRznSocial($data->cliente_razonsocial);
      
        // Nota de credito
        $invoice = (new Note())
            ->setUblVersion($data->notacredito_ublversion)
            ->setTipDocAfectado($data->notacredito_tipodocafectado)
            ->setNumDocfectado($data->notacredito_numerodocumentoafectado)
            ->setCodMotivo($data->notacredito_codigomotivo)
            ->setDesMotivo($data->notacredito_descripcionmotivo)
            ->setTipoDoc($data->notacredito_tipodocumento)
            ->setSerie($data->notacredito_serie)
            ->setFechaEmision(\DateTime::createFromFormat('Y-m-d H:i:s', $data->notacredito_fechaemision))
            ->setCorrelativo($data->notacredito_correlativo)
            ->setTipoMoneda($data->notacredito_tipomoneda)
//             ->setGuias($data->notacredito_guias)
            ->setClient($client)
            ->setMtoOperGravadas($data->notacredito_montooperaciongravada)
            ->setMtoOperExoneradas(0.00)
            ->setMtoOperInafectas(0.00)
            ->setMtoIGV($data->notacredito_montoigv)
            ->setTotalImpuestos($data->notacredito_totalimpuestos)
            ->setMtoImpVenta($data->notacredito_montoimpuestoventa)
            ->setCompany($facturador['company']);

        $notacreditodetalles = DB::table('facturacionnotacreditodetalle')
                ->where('facturacionnotacreditodetalle.idfacturacionnotacredito',$data->id)
                ->orderBy('facturacionnotacreditodetalle.id','asc')
                ->get();
        $item = [];
        foreach($notacreditodetalles as $value){
            $item[] = (new SaleDetail())
                ->setCodProducto($value->codigoproducto)
                ->setUnidad($value->unidad)
                ->setCantidad($value->cantidad)
                ->setDescripcion($value->descripcion)
                ->setMtoBaseIgv($value->montobaseigv)
                ->setPorcentajeIgv($value->porcentajeigv) // 18%
                ->setIgv($value->igv)
                ->setTipAfeIgv($value->tipoafectacionigv)
                ->setTotalImpuestos($value->totalimpuestos)
                ->setMtoValorVenta($value->montovalorventa)
                ->setMtoValorUnitario($value->montovalorunitario)
                ->setMtoPrecioUnitario($value->montopreciounitario);
        }

        $legend = (new Legend())
            ->setCode($data->leyenda_codigo)
            ->setValue($data->leyenda_value);
      
        $invoice->setDetails($item)
                ->setLegends([$legend]);
          
        // QR
        $crearqr = new QrRender();
        $base64_qr = new ImageFilter();
        $minewqr = $base64_qr->toBase64($crearqr->getImage($invoice));
        // Fin QR

        // Envio SUNAT
        $result = $facturador['see']->send($invoice);

        if ($result->isSuccess()) {
            file_put_contents($facturador['raiz'].'notacredito/'.$invoice->getName().'.xml', $facturador['see']->getFactory()->getLastXml());
            file_put_contents($facturador['raiz'].'notacredito/'.'R-'.$invoice->getName().'.zip', $result->getCdrZip());
          
            DB::table('facturacionnotacredito')->whereId($idfacturacionnotacredito)->update([
                'estadofacturacion' => $result->getCdrResponse()->getDescription(),
                'notacredito_qr' => $minewqr,
                'idestadofacturacion' => 1, // correcto
                'idestadosunat' => $data->facturador_idestado
            ]);
          
            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $result->getCdrResponse()->getDescription(),
                'data' => $data,
                'qr' => $minewqr
            ];
          }else{
          
            DB::table('facturacionnotacredito')->whereId($idfacturacionnotacredito)->update([
                'estadofacturacion' => facturador_error($result),
                'notacredito_qr' => $minewqr,
                'idestadofacturacion' => 2, // error
                'idestadosunat' => $data->facturador_idestado
            ]);
          
            return [
                'resultado' => 'ERROR',
                'mensaje' => facturador_error($result),
                'data' => $data,
                'qr' => $minewqr
            ];
          }
		}

function facturador_guiaremision($idfacturacionguiaremision) {
  
  $data = DB::table('facturacionguiaremision')
    ->join('agencia','agencia.id','facturacionguiaremision.idagencia') 
    ->join('tienda','tienda.id','facturacionguiaremision.idtienda')
    ->where('facturacionguiaremision.id', $idfacturacionguiaremision)
    ->select(
      'facturacionguiaremision.*',
      'tienda.facturador_idestado as facturador_idestado',
      'agencia.sunat_usuario as sunat_usuario',
      'agencia.sunat_clave as sunat_clave',
      'agencia.sunat_certificado as sunat_certificado'
    )
    ->first();

  $facturador_guia = facturador_guia($data);

  $rel = new Document();
//   $rel->setTipoDoc('02') // Tipo: Numero de Orden de Entrega
//   ->setNroDoc('213123');
  
  $transp = new Transportist();
  $transp->setTipoDoc($data->transporte_tipodocumento)
      ->setNumDoc($data->transporte_numerodocumento)
      ->setRznSocial($data->transporte_razonsocial)
      ->setPlaca($data->transporte_placa)
      ->setChoferTipoDoc($data->transporte_chofertipodocumento)
      ->setChoferDoc($data->transporte_choferdocumento);

  $envio = new Shipment();
  $envio->setCodTraslado($data->envio_codigotraslado) // Cat.20
      ->setDesTraslado($data->envio_descripciontraslado)
      ->setModTraslado($data->envio_modtraslado) // Cat.18
      ->setFecTraslado(date_create($data->envio_fechatraslado))
//       ->setCodPuerto($data->envio_codigopuerto)
//       ->setIndTransbordo($data->envio_indtransbordo)
//       ->setPesoTotal($data->envio_pesototal)
      ->setUndPesoTotal($data->envio_unidadpesototal)
  //    ->setNumBultos(2) // Solo válido para importaciones
//       ->setNumContenedor($data->envio_numerocontenedor)
      ->setLlegada(new Direction($data->envio_direccionllegadacodigoubigeo, $data->envio_direccionllegada))
      ->setPartida(new Direction($data->envio_direccionpartidacodigoubigeo, $data->envio_direccionpartida))
      ->setTransportista($transp);

  $despatch = new Despatch();
  $despatch->setTipoDoc($data->guiaremision_tipodocumento)
      ->setSerie($data->guiaremision_serie)
      ->setCorrelativo($data->guiaremision_correlativo)
      ->setFechaEmision(date_create($data->guiaremision_fechaemision))
      ->setCompany($facturador_guia['company'])
      ->setDestinatario((new Client())
          ->setTipoDoc($data->despacho_destinatario_tipodocumento)
          ->setNumDoc($data->despacho_destinatario_numerodocumento)
          ->setRznSocial($data->despacho_destinatario_razonsocial))
//       ->setTercero((new Client())
//           ->setTipoDoc('6')
//           ->setNumDoc($data->despacho_tercero_numerodocumento)
//           ->setRznSocial($data->despacho_tercero_razonsocial))
      ->setObservacion($data->despacho_observacion)
//       ->setRelDoc($rel)
      ->setEnvio($envio);

  $data_detalle = DB::table('facturacionguiaremisiondetalle')->where('idfacturacionguiaremision', $idfacturacionguiaremision)->get();

  $item = [];
  foreach($data_detalle as $value){
      $item[] = (new DespatchDetail())
          ->setCantidad($value->cantidad)
          ->setUnidad($value->unidad)
          ->setDescripcion($value->descripcion)
          ->setCodigo($value->codigo)
          ->setCodProdSunat($value->codprodsunat);
  }
  // Envio a SUNAT
  $despatch->setDetails($item);
  
  $result = $facturador_guia['see']->send($despatch);

  if ($result->isSuccess()) {
        file_put_contents($facturador_guia['raiz'].'guiaremision/'.$despatch->getName().'.xml', $facturador_guia['see']->getFactory()->getLastXml());
        file_put_contents($facturador_guia['raiz'].'guiaremision/'.'GR-'.$despatch->getName().'.zip', $result->getCdrZip());
    
         DB::table('facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
            'estadofacturacion' => $result->getCdrResponse()->getDescription(),
            'idestadofacturacion' => 1, // correcto
            'idestadosunat' => $data->facturador_idestado
        ]);
    
        return [
            'resultado' => 'CORRECTO',
            'mensaje' => $result->getCdrResponse()->getDescription(),
            'data' => $data,
        ];
  } else {
        //4000 = El documento ya fue presentado anteriormente.
        if($result->getError()->getCode()==4000){
            DB::table('facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
                'estadofacturacion' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                'idestadofacturacion' => 1, // REENEVIADO correcto
                'idestadosunat' => $data->facturador_idestado
            ]);

            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                'data' => $data,
            ];
        }else{
            DB::table('facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
                'estadofacturacion' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                'idestadofacturacion' => 2, // error
                'idestadosunat' => $data->facturador_idestado
            ]);

            return [
                'resultado' => 'ERROR',
                'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                'data' => $data,
            ];
        }
             
  }
} 

function facturador_comunicacionbaja($idfacturacioncomunicacionbaja) {
  
  $data = DB::table('facturacioncomunicacionbaja')
    ->join('agencia','agencia.id','facturacioncomunicacionbaja.idagencia')
    ->join('tienda','tienda.id','facturacioncomunicacionbaja.idtienda')
    ->where('facturacioncomunicacionbaja.id', $idfacturacioncomunicacionbaja)
    ->select(
      'facturacioncomunicacionbaja.*',
      'tienda.facturador_idestado as facturador_idestado',
      'agencia.sunat_usuario as sunat_usuario',
      'agencia.sunat_clave as sunat_clave',
      'agencia.sunat_certificado as sunat_certificado'
    )
    ->first();
  
  $data_detalle = DB::table('facturacioncomunicacionbajadetalle')->where('idfacturacioncomunicacionbaja', $data->id)->get();
  
  $facturador = facturador($data);
  
  $item = [];
  foreach ($data_detalle as $value) {
    $item[] = (new VoidedDetail())
        ->setTipoDoc($value->tipodocumento)
        ->setSerie($value->serie)
        ->setCorrelativo($value->correlativo)
        ->setDesMotivoBaja($value->descripcionmotivobaja);
  }
    
  $voided = new Voided();
  $voided->setCorrelativo($data->comunicacionbaja_correlativo)
      // Fecha Generacion menor que Fecha comunicacion
      ->setFecGeneracion(date_create($data->comunicacionbaja_fechageneracion))
      ->setFecComunicacion(date_create($data->comunicacionbaja_fechacomunicacion))
      ->setCompany($facturador['company'])
      ->setDetails($item);

  $res = $facturador['see']->send($voided);
 
  if ($res->isSuccess()) { 
     $result = $facturador['see']->getStatus($res->getTicket());
     if ($result->isSuccess()) {
          file_put_contents($facturador['raiz'].'comunicacionbaja/'.$voided->getName().'.xml', $facturador['see']->getFactory()->getLastXml());
          file_put_contents($facturador['raiz'].'comunicacionbaja/'.'R-'.$voided->getName().'.zip', $result->getCdrZip());

          DB::table('facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
              'ticket' => $res->getTicket(),
              'estadofacturacion' => $result->getCdrResponse()->getDescription(),
              'idestadofacturacion' => 1, // correcto
              'idestadosunat' => $data->facturador_idestado
          ]);

          return [
              'resultado' => 'CORRECTO',
              'mensaje' => $result->getCdrResponse()->getDescription(),
              'data' => $data,
          ];
      } else {
              DB::table('facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                  'ticket' => $res->getTicket(),
                  'estadofacturacion' => $result->getError()->getMessage(),
                  'idestadofacturacion' => 2, // error
                  'idestadosunat' => $data->facturador_idestado
             ]);

              return [
                  'resultado' => 'ERROR',
                  'mensaje' => $result->getError()->getMessage(),
                  'data' => $data,
              ];
      }
  }else{
        // 0098 = El procesamiento del comprobante aún no ha terminado
        // 0402 = La numeracion o nombre del documento ya ha sido enviado anteriormente'
        if($res->getError()->getCode()=='0402'){
            DB::table('facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                'ticket' => $res->getTicket(),
                'estadofacturacion' => $res->getError()->getMessage(),
                'idestadofacturacion' => 1, // REENVIADO correcto
                'idestadosunat' => $data->facturador_idestado
            ]);

            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $res->getError()->getMessage(),
                'data' => $data,
            ];
        }else{
            DB::table('facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                'ticket' => $res->getTicket(),
                'estadofacturacion' => $res->getError()->getMessage(),
                'idestadofacturacion' => 2, // error
                'idestadosunat' => $data->facturador_idestado
            ]);

            return [
                'resultado' => 'ERROR',
                'mensaje' => $res->getError()->getMessage(),
                'data' => $data,
            ];
        }
            
  }
}

function facturador_resumendiario($idfacturacionresumen) {
  $data = DB::table('facturacionresumen')
    ->join('agencia','agencia.id','facturacionresumen.idagencia')
    ->join('tienda','tienda.id','facturacionresumen.idtienda')
    ->where('facturacionresumen.id', $idfacturacionresumen)
    ->select(
      'facturacionresumen.*',
      'tienda.facturador_idestado as facturador_idestado',
      'agencia.sunat_usuario as sunat_usuario',
      'agencia.sunat_clave as sunat_clave',
      'agencia.sunat_certificado as sunat_certificado'
    )
    ->first();
  
  $data_detalle = DB::table('facturacionresumendetalle')->where('idfacturacionresumen', $data->id)->get();
  
  $facturador = facturador($data);
  
  // Resumen
  $item = [];
  foreach ($data_detalle as $value) {
      $item[] = (new SummaryDetail())
          ->setTipoDoc($value->tipodocumento)
          ->setSerieNro($value->serienumero)
          ->setEstado($value->estado)
          ->setClienteTipo($value->clientetipo)
          ->setClienteNro($value->clientenumero)
          ->setTotal($value->total)
          ->setMtoOperGravadas($value->operacionesgravadas)
          ->setMtoOperInafectas(0)
          ->setMtoOperExoneradas(0)
          ->setMtoOtrosCargos(0)
          ->setMtoIGV($value->montoigv);
  }

  $voided = (new Summary())
        ->setCorrelativo($data->resumen_correlativo)
        ->setFecGeneracion(date_create($data->resumen_fechageneracion))
        ->setFecResumen(date_create($data->resumen_fecharesumen))
        ->setCompany($facturador['company'])
        ->setDetails($item);
  
  $result = $facturador['see']->send($voided);

  if ($result->isSuccess()) {
     $ticket = $result->getTicket();
     $result = $facturador['see']->getStatus($ticket);
      
     if ($result->isSuccess()) { 
        file_put_contents($facturador['raiz'].'resumendiario/'.$voided->getName().'.xml', $facturador['see']->getFactory()->getLastXml());
        file_put_contents($facturador['raiz'].'resumendiario/'.'R-'.$voided->getName().'.zip', $result->getCdrZip());

        DB::table('facturacionresumen')->whereId($idfacturacionresumen)->update([
            'estadofacturacion' => $result->getCdrResponse()->getDescription(),
            'idestadofacturacion' => 1, // correcto
            'idestadosunat' => $data->facturador_idestado
        ]);

        return [
            'resultado' => 'CORRECTO',
            'mensaje' => $result->getCdrResponse()->getDescription(),
            'data' => $data,
        ];
     }else {
       
        DB::table('facturacionresumen')->whereId($idfacturacionresumen)->update([
            'estadofacturacion' => $result->getError()->getMessage(),
            'idestadofacturacion' => 2, // correcto
            'idestadosunat' => $data->facturador_idestado
        ]);

        return [
            'resultado' => 'ERROR',
            'mensaje' => $result->getError()->getCode().$result->getError()->getMessage(),
            'data' => $data,
        ];
     }
     
  }
  
}

function facturador_error($res){
        $mensaje = (array) $res->getError();
        $msj = '';
        foreach($mensaje as $value){
            $msj = $msj.$value.'<br>';
        }
        return $msj;
}

// Obtener navegador
function obtenerBrowser(){
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($user_agent, 'MSIE') !== FALSE)
   return 'Internet explorer';
 elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
   return 'Microsoft Edge';
 elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
    return 'Internet explorer';
 elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
   return "Opera Mini";
 elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
   return "Opera";
 elseif(strpos($user_agent, 'Firefox') !== FALSE)
   return 'Mozilla Firefox';
 elseif(strpos($user_agent, 'Chrome') !== FALSE)
   return 'Google Chrome';
 elseif(strpos($user_agent, 'Safari') !== FALSE)
   return "Safari";
 elseif(strpos($user_agent, 'Konqueror') !== FALSE)
   return "Konqueror";
 elseif(strpos($user_agent, 'iPod') !== FALSE)
   return "iPod";
 elseif(strpos($user_agent, 'iPhone') !== FALSE)
   return "iPhone";
 elseif(strpos($user_agent, 'Android') !== FALSE)
   return "Android";
 else
   return 'No hemos podido detectar su navegador';
}


    //Obtiene la info de la IP del cliente desde geoplugin

    function obtenerUbicacion($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        /*$output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );*/
                        $output = @$ipdat->geoplugin_city.' - '.
                                  @$ipdat->geoplugin_regionName.' - '.
                                  @$ipdat->geoplugin_countryName;
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }
