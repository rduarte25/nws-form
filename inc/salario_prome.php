<?php

require WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) . '/../vendor/autoload.php';
use Dompdf\Dompdf;

function SpanishDate(){
    $ano = date('Y');
    $mes = date('n');
    $dia = date('d');
    $diasemana = date('w');
    $diassemanaN= array("Domingo","Lunes","Martes","Miércoles",
                    "Jueves","Viernes","Sábado");
    $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
                "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    return $diassemanaN[$diasemana].", $dia de ". $mesesN[$mes] ." de $ano";
}

function nws_form_eliminar() {
    //echo "functiona!";
    if ( isset( $_POST['tipo'] ) ) {
        if( $_POST['tipo'] == 'eliminar' ) {
            //echo "Sí se envió";
            global $wpdb;
            $tabla = $wpdb->prefix . 'data_user';
            $tabla1 = $wpdb->prefix . 'data_salary';
            $tabla2 = $wpdb->prefix . 'data_calcs';
            $tabla3 = $wpdb->prefix . 'data_table_calcs';
            $id_registro = $_POST['id'];   
            $tabla4 = $wpdb->prefix . 'data_docs';
            $query = "SELECT * FROM $tabla4 WHERE id_data_user=".$id_registro;
            $registro = $wpdb->get_row( $query, ARRAY_A );
            $resultado4 = $wpdb->delete( $tabla4, array( 'id_data_user' => $id_registro), array( '%d' ) );
            wp_delete_file($registro['full_path']);
            if( $resultado4 >= 1){    
                $resultado3 = $wpdb->delete( $tabla3, array( 'id_data_user' => $id_registro), array( '%d' ) );
                if( $resultado3 >= 1){
                    $resultado2 = $wpdb->delete( $tabla2, array( 'id_data_user' => $id_registro), array( '%d' ) );
                    if( $resultado2 >= 1){
                        $resultado1 = $wpdb->delete( $tabla1, array( 'id_data_user' => $id_registro), array( '%d' ) );
                        if($resultado1 >= 1){
                            $resultado = $wpdb->delete( $tabla, array( 'id' => $id_registro), array( '%d' ) );
                            if ( $resultado >= 1 ) {
                                $respuesta = array( 
                                    'respuesta' => 1,
                                    'id'        => $id_registro,
                                );
                            } else {
                                $respuesta = array( 
                                    'respuesta' => 'error',
                                );
                            }
                        } else {
                            $respuesta = array( 
                                'respuesta' => 'error',
                            );
                        }
                    }
                }    
            }
        }
    }
    //die(json_encode( $id_registro ));
    die( json_encode( $respuesta ) );
    //die(json_encode( $_POST ));
}

add_action( 'wp_ajax_nws_form_eliminar', 'nws_form_eliminar' );

function nws_form_get_registros(){

    global $wpdb;                        
    $dataUser = $wpdb->prefix . 'data_user';
    $registros = $wpdb->get_results( "SELECT * FROM $dataUser", ARRAY_A );

        $data = array();
        $i = 1;
        foreach ( $registros as $registro ) {
            $data[] = array(
                '0' => $i,
                '1' => $registro['nombre'],
                '2' => $registro['apellido'],
                '3' => $registro['correo'],
                '4' => '<a id="borrar_registro" href="#" data-data_user="'.$registro['id'].'"><button class="btn btn-danger" onclick="eliminar('.$registro['id'].')">Eliminar</button></a><a class="" href="?page=nws_form_registros&id='.$registro['id'].'"><button class="btn btn-primary">Ver</button></a>'
            );
            $i++;
        }

        $results = array (
            'sEcho'=> 1, //Información para el datatables.
            'iTotalRecords'=> count( $data ), //Enviamos el total registro al datatable.
            'iTotalDisplayRecords'=> count( $data ), //Enviamos el total registro a visualizar.
            'aaData'=> $data, //Información para el datatables.
        );

        die( json_encode( $results ) );
        //echo 'desde get registros';
}

add_action( 'wp_ajax_nws_form_get_registros', 'nws_form_get_registros' );

function nws_form_guardar() {
    if ( isset( $_POST['enviar'] )  && $_POST['oculto'] == "1" ) {
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {  
            $captcha = $_POST['g-recaptcha-response'];

            $campos = array(
                'secret'    => get_option( 'nws_form_site_secret' ),
                'response'  => $captcha,
                'remoteip'  => $_SERVER['REMOTE_ADDR'],
            );

            //Iniciar sesión en CURL
            //CURL es utilizado para acceder a servidores remotos.
            $ch = curl_init( 'https://www.google.com/recaptcha/api/siteverify' );

            //configura opciones de curl
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

            curl_setopt( $ch, CURLOPT_TIMEOUT, 15 );

            //genera una cadena codificada para la url
            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $campos ) );

            $respuesta = json_decode( curl_exec( $ch ) );

            if ( $respuesta->success ) {  
                // TO DO realizar calculo de promedio mediante las semanas y no de años
                // calcular promedio por numero de semanas y dividirlo entre las 260 semanas
                // detectar las 260 semanas, calcular semanas cotizadas menos 500      
                global $wpdb;                

                $tabla = $wpdb->prefix . 'data_user';
                $nombre = $_POST['name'];
                $apellido = $_POST['last_name'];
                $birthday = $_POST['birthday'];
                $correo = $_POST['email'];
                $inDemandWeeks = $_POST['in_demand_weeks'];
                $upImss = $_POST['up_imss'];
                $haveChild = $_POST['have_child'];
                $numChild = $_POST['num_child'];
                
                

                $increment_determination = $_POST['increment_determination'];
                
                $average_salary_total = $_POST['average_salary_total'];

                sanitize_text_field( $nombre );
                sanitize_text_field( $apellido );
                sanitize_text_field( $birthday );
                sanitize_text_field( $correo );
                sanitize_text_field( $numChild );

                $factor = $average_salary_total / 88.66;

                $age = date_diff(date_create($birthday), date_create('today'))->y;

                //Primera inerción el la db.
                $datos = array(
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'correo' => $correo,
                    'birthday' => $birthday,
                    'age' => $age,
                    'in_demand_weeks' => $inDemandWeeks,
                    'up_imss' => $upImss,
                    'have_child' => $haveChild,
                    'num_child' => $numChild,
                );
            
                $formato = array(            
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%f',
                    '%s',
                    '%s',
                    '%d',           
                );

                $wpdb->insert( $tabla, $datos, $formato );
                $data_user_id = $wpdb->insert_id;

                //Segunda insersión en la db, con recorrido de datos.
                if(isset($_POST['date_salary']) && isset($_POST['change_salary'])) {
                    $date_salary = $_POST['date_salary'];
                    $change_salary = $_POST['change_salary'];
                    $average_salary = $_POST['average_salary'];

                    $cont=0;
                    $tabla1 = $wpdb->prefix . 'data_salary';
                    $formato1 = array(            
                        '%d',
                        '%s',
                        '%f',
                        '%f',          
                    );               

                    while($cont < count($date_salary)){
                        sanitize_text_field( $date_salary[$cont] );
                        sanitize_text_field( $change_salary[$cont] );
                        sanitize_text_field( $average_salary[$cont] );
                        
                        $datos1 = array(
                            'id_data_user' => $data_user_id,
                            'date_salary' => $date_salary[$cont],
                            'change_salary' => $change_salary[$cont],
                            'average_salary' => $average_salary[$cont],     
                        );
                        $wpdb->insert( $tabla1, $datos1, $formato1 );
                        $cont=$cont+1;
                    }
                }

                //calculo de cuantia
                $cuantia = 0;
                $incremento = 0;
                switch($factor) {
                    case ($factor >= 5.01 && $factor <= 5.25):
                        $cuantia = 15.61;
                        $incremento = 2.377;
                        break;
                    case ($factor >= 5.26 && $factor <= 5.5):
                        $cuantia = 14.88;
                        $incremento = 2.398;
                        break;
                    case ($factor >= 5.51 && $factor <= 5.75):
                        $cuantia = 14.22;
                        $incremento = 2.416;
                        break;
                    case ($factor >= 5.76 && $factor <= 6):
                        $cuantia = 13.62;
                        $incremento = 2.433;
                        break;
                    case ($factor >= 6.01 && $factor <= 999):
                        $cuantia = 13;
                        $incremento = 2.45;
                        break;
                }       

                //Determinación de la edad
                if($age < 60) {
                    $resto = 60 - $age;
                    $birthdayArray = explode('-',$birthday);
                    $todayArray = explode('-',Date('Y-m-d'));
                    $futureBirthday = (int)$todayArray[0] + $resto - 1 . '-' . $birthdayArray[1] . '-' . $birthdayArray[2];
                    $semanasMeta =  (int)(date_diff(date_create('today'), date_create($futureBirthday))->days / 7);
                    $semanas0 = ($inDemandWeeks + $semanasMeta);
                    $semanas1 = $semanas0 + 52;
                    $semanas2 = $semanas1 + 52;
                    $semanas3 = $semanas2 + 52;
                    $semanas4 = $semanas3 + 52;
                    $semanas5 = $semanas4 + 52;
                } else {
                    $semanas0 = $inDemandWeeks;
                    $semanas1 = $semanas0;
                    $semanas2 = $semanas1;
                    $semanas3 = $semanas2;
                    $semanas4 = $semanas3;
                    $semanas5 = $semanas4;
                }

                //Calcaulo del excedente
                $excedente = ($inDemandWeeks + $semanasMeta) - 500;
                $semanasAno = 52;
                $diasAno = 365;
                $mesesAno = 12;
                $numIncrementos = $excedente / $semanasAno;                
                $residuo = $numIncrementos - floor($numIncrementos);
                $factorResidual = $residuo * $semanasAno;                
                
                //calculo del incremento adicional
                $incrementoAdicional = 0;                
                switch($factorResidual) {
                    case ($factorResidual >= 0 && $factorResidual <= 13):
                        $incrementoAdicional = 0;
                        break;
                    case ($factorResidual >= 13.1 && $factorResidual <= 26):
                        $incrementoAdicional = 0.5;
                        break;
                    case ($factorResidual >= 26.1 && $factorResidual <= 999):
                        $incrementoAdicional = 1;
                        break;
                }
                
                $totalIncrementos =  floor($numIncrementos) + $incrementoAdicional;

                //Calculo del importe basico
                $importeBasico = $average_salary_total * $diasAno * ($cuantia / 100);
                $importeIncrementos = $average_salary_total * $diasAno * $incremento * ($totalIncrementos / 100);

                //Pensión con variación a los 60
                $pension = $importeBasico + $importeIncrementos;
                $pension60 = $pension * 75 /100;
                $cuantiaMensual0 = $pension60 / $mesesAno;
                $adicionalOnce0 = $cuantiaMensual0 * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince0 = $adicionalOnce0 * 1.15;
                } else {
                    $adicionalQuince0 = 0;
                }
                $porCadaHijo0 = $adicionalOnce0 * (0.1 * $numChild);
                $totalPensionMensual0 = $adicionalQuince0 + $porCadaHijo0;

                //Pensión con variación a los 61
                $pension61 = $pension * 80 /100;
                $cuantiaMensual1 = $pension61 / $mesesAno;
                $adicionalOnce1 = $cuantiaMensual1 * 1.11;                
                if(isset($haveChild)) {
                    $adicionalQuince1 = $adicionalOnce1 * 1.15;
                } else {
                    $adicionalQuince1 = 0;
                }
                $porCadaHijo1 = $adicionalOnce1 * (0.1 * $numChild);
                $totalPensionMensual1 = $adicionalQuince1 + $porCadaHijo1;               
                
                //Pensión con variación a los 62
                $pension62 = $pension * 85 /100;
                $cuantiaMensual2 = $pension62 / $mesesAno;
                $adicionalOnce2 = $cuantiaMensual2 * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince2 = $adicionalOnce2 * 1.15;
                } else {
                    $adicionalQuince2 = 0;
                }
                $porCadaHijo2 = $adicionalOnce2 * (0.1 * $numChild);
                $totalPensionMensual2 = $adicionalQuince2 + $porCadaHijo2;       

                //Pensión con variación a los 63
                $pension63 = $pension * 90 /100;
                $cuantiaMensual3 = $pension63 / $mesesAno;
                $adicionalOnce3 = $cuantiaMensual3 * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince3 = $adicionalOnce3 * 1.15;
                } else {
                    $adicionalQuince3 = 0;
                }
                $porCadaHijo3 = $adicionalOnce3 * (0.1 * $numChild);
                $totalPensionMensual3 = $adicionalQuince3 + $porCadaHijo3;

                //Pensión con variación a los 64
                $pension64 = $pension * 95 /100;
                $cuantiaMensual4 = $pension64 / $mesesAno;
                $adicionalOnce4 = $cuantiaMensual4 * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince4 = $adicionalOnce4 * 1.15;
                } else {
                    $adicionalQuince4 = 0;
                }
                $porCadaHijo4 = $adicionalOnce4 * (0.1 * $numChild);               
                $totalPensionMensual4 = $adicionalQuince4 + $porCadaHijo4;       

                //Pensión con variación a los 65
                $pension65 = $pension * 100 /100;
                $cuantiaMensual5 = $pension65 / $mesesAno;
                $adicionalOnce5 = $cuantiaMensual5 * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince5 = $adicionalOnce5 * 1.15;
                } else {
                    $adicionalQuince5 = 0;
                }
                $porCadaHijo5 = $adicionalOnce5 * (0.1 * $numChild);
                $totalPensionMensual5 = $adicionalQuince5 + $porCadaHijo5;       

                //Tercera inserción en la db.
                $tabla2 = $wpdb->prefix . 'data_calcs';
                $formato2 = array(            
                    '%d',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                );
                $datos2 = array(
                    'id_data_user' => $data_user_id,
                    'increment_determination' => $increment_determination[$cont],
                    'average_salary_total' => $average_salary_total,
                    'factor' => $factor,
                    'cuantia' => $cuantia,
                    'incremento' => $incremento,
                    'excedente' => $excedente,
                    'num_incremento' => $numIncrementos,
                    'residuo' => $residuo,
                    'factor_residual' => $factorResidual,
                    'incremento_adicional' => $incrementoAdicional,
                    'total_incrementos' => $totalIncrementos,
                    'importe_basico' => $importeBasico,
                    'importe_incrementos' => $importeIncrementos,
                    'semanas_meta' => $semanasMeta,
                    'pension' => $pension,
                );

                $wpdb->insert( $tabla2, $datos2, $formato2 );

                //Cuarta inserción en la db.
                $tabla3 = $wpdb->prefix . 'data_table_calcs';
                $formato3 = array(            
                    '%d',
                    '%d',
                    '%d',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%d',
                );

                $datos3 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 60,
                    'porcentaje' => 75,
                    'cuantia_anual' => $pension60,
                    'cuantia_mensual' => $cuantiaMensual0,
                    'reforma_2005' => $adicionalOnce0,
                    'asignacion_familiar' => $adicionalQuince0,
                    'asignacion_hijos_menores' => $porCadaHijo0,
                    'total_pension_mensual' => $totalPensionMensual0,
                    'semanas' => $semanas0,
                );

                $wpdb->insert( $tabla3, $datos3, $formato3 );

                //Quinta inserción
                $datos4 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 61,
                    'porcentaje' => 80,
                    'cuantia_anual' => $pension61,
                    'cuantia_mensual' => $cuantiaMensual1,
                    'reforma_2005' => $adicionalOnce1,
                    'asignacion_familiar' => $adicionalQuince1,
                    'asignacion_hijos_menores' => $porCadaHijo1,
                    'total_pension_mensual' => $totalPensionMensual1,
                    'semanas' => $semanas1,
                );

                $wpdb->insert( $tabla3, $datos4, $formato3 );

                //Sexta inserción
                $datos5 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 62,
                    'porcentaje' => 85,
                    'cuantia_anual' => $pension62,
                    'cuantia_mensual' => $cuantiaMensual2,
                    'reforma_2005' => $adicionalOnce2,
                    'asignacion_familiar' => $adicionalQuince2,
                    'asignacion_hijos_menores' => $porCadaHijo2,
                    'total_pension_mensual' => $totalPensionMensual2,
                    'semanas' => $semanas2,
                );

                $wpdb->insert( $tabla3, $datos5, $formato3 );

                //Septima inserción
                $datos6 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 63,
                    'porcentaje' => 90,
                    'cuantia_anual' => $pension63,
                    'cuantia_mensual' => $cuantiaMensual3,
                    'reforma_2005' => $adicionalOnce3,
                    'asignacion_familiar' => $adicionalQuince3,
                    'asignacion_hijos_menores' => $porCadaHijo3,
                    'total_pension_mensual' => $totalPensionMensual3,
                    'semanas' => $semanas3
                );

                $wpdb->insert( $tabla3, $datos6, $formato3 );

                //Octava inserción
                $datos7 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 64,
                    'porcentaje' => 95,
                    'cuantia_anual' => $pension64,
                    'cuantia_mensual' => $cuantiaMensual4,
                    'reforma_2005' => $adicionalOnce4,
                    'asignacion_familiar' => $adicionalQuince4,
                    'asignacion_hijos_menores' => $porCadaHijo4,
                    'total_pension_mensual' => $totalPensionMensual4,
                    'semanas' => $semanas4,
                );

                $wpdb->insert( $tabla3, $datos7, $formato3 );
                
                //Novena inserción
                $datos8 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 65,
                    'porcentaje' => 100,
                    'cuantia_anual' => $pension65,
                    'cuantia_mensual' => $cuantiaMensual5,
                    'reforma_2005' => $adicionalOnce5,
                    'asignacion_familiar' => $adicionalQuince5,
                    'asignacion_hijos_menores' => $porCadaHijo5,
                    'total_pension_mensual' => $totalPensionMensual5,
                    'semanas' => $semanas5,
                );
                $wpdb->insert( $tabla3, $datos8, $formato3 );
                
                //Calculo con semanas estaticas
                //Con semanas estaticas
                $semanas0se = $inDemandWeeks;
                $semanas1se = $semanas0se;
                $semanas2se = $semanas1se;
                $semanas3se = $semanas2se;
                $semanas4se = $semanas3se;
                $semanas5se = $semanas4se;

                //Calcaulo del excedente
                $excedente = $inDemandWeeks - 500;
                $semanasAno = 52;
                $diasAno = 365;
                $mesesAno = 12;
                $numIncrementos = $excedente / $semanasAno;                
                $residuo = $numIncrementos - floor($numIncrementos);
                $factorResidual = $residuo * $semanasAno;                
                
                //calculo del incremento adicional
                $incrementoAdicional = 0;                
                switch($factorResidual) {
                    case ($factorResidual >= 0 && $factorResidual <= 13):
                        $incrementoAdicional = 0;
                        break;
                    case ($factorResidual >= 13.1 && $factorResidual <= 26):
                        $incrementoAdicional = 0.5;
                        break;
                    case ($factorResidual >= 26.1 && $factorResidual <= 999):
                        $incrementoAdicional = 1;
                        break;
                }
                
                $totalIncrementos =  floor($numIncrementos) + $incrementoAdicional;

                //Calculo del importe basico
                $importeBasico = $average_salary_total * $diasAno * ($cuantia / 100);
                $importeIncrementos = $average_salary_total * $diasAno * $incremento * ($totalIncrementos / 100);

                //calculo de cuantia de pension anual
                $pension = $importeBasico + $importeIncrementos;

                //pension a los 60
                $pension60se = $pension * (75 /100);
                $cuantiaMensual0se = $pension60se / $mesesAno;
                $adicionalOnce0se = $cuantiaMensual0se * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince0se = $adicionalOnce0se * 1.15;
                } else {
                    $adicionalQuince0se = 0;
                }
                $porCadaHijo0se = $adicionalOnce0se * (0.1 * $numChild);
                $totalPensionMensual0se = $adicionalQuince0se + $porCadaHijo0se;

                //pension a los 61
                $pension61se = $pension * (80 /100);
                $cuantiaMensual1se = $pension61se / $mesesAno;
                $adicionalOnce1se = $cuantiaMensual1se * 1.11;                
                if(isset($haveChild)) {
                    $adicionalQuince1se = $adicionalOnce1se * 1.15;
                } else {
                    $adicionalQuince1se = 0;
                }
                $porCadaHijo1se = $adicionalOnce1se * (0.1 * $numChild);
                $totalPensionMensual1se = $adicionalQuince1se + $porCadaHijo1se;               
                
                //pension a los 62
                $pension62se = $pension * (85 /100);
                $cuantiaMensual2se = $pension62se / $mesesAno;
                $adicionalOnce2se = $cuantiaMensual2se * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince2se = $adicionalOnce2se * 1.15;
                } else {
                    $adicionalQuince2se = 0;
                }
                $porCadaHijo2se = $adicionalOnce2se * (0.1 * $numChild);
                $totalPensionMensual2se = $adicionalQuince2se + $porCadaHijo2se;       

                //pension a los 63
                $pension63se = $pension * (90 /100);
                $cuantiaMensual3se = $pension63se / $mesesAno;
                $adicionalOnce3se = $cuantiaMensual3se * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince3se = $adicionalOnce3se * 1.15;
                } else {
                    $adicionalQuince3se = 0;
                }
                $porCadaHijo3se = $adicionalOnce3se * (0.1 * $numChild);
                $totalPensionMensual3se = $adicionalQuince3se + $porCadaHijo3se;      

                //pension a los 64
                $pension64se = $pension * (95 /100);
                $cuantiaMensual4se = $pension64se / $mesesAno;
                $adicionalOnce4se = $cuantiaMensual4se * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince4se = $adicionalOnce4se * 1.15;
                } else {
                    $adicionalQuince4se = 0;
                }
                $porCadaHijo4se = $adicionalOnce4se * (0.1 * $numChild);                
                $totalPensionMensual4se = $adicionalQuince4se + $porCadaHijo4se;       

                //pension a los 65
                $pension65se = $pension * (100 /100);
                $cuantiaMensual5se = $pension65se / $mesesAno;
                $adicionalOnce5se = $cuantiaMensual5se * 1.11;
                if(isset($haveChild)) {
                    $adicionalQuince5se = $adicionalOnce5se * 1.15;
                } else {
                    $adicionalQuince5se = 0;
                }
                $porCadaHijo5se = $adicionalOnce5se * (0.1 * $numChild);
                $totalPensionMensual5se = $adicionalQuince5se + $porCadaHijo5se;       

                //Cuarta inserción en la db.
                $tabla3 = $wpdb->prefix . 'data_table_calcs';
                $formato3 = array(            
                    '%d',
                    '%d',
                    '%d',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%f',
                    '%d',
                );

                $datos9 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 60,
                    'porcentaje' => 75,
                    'cuantia_anual' => $pension60se,
                    'cuantia_mensual' => $cuantiaMensual0se,
                    'reforma_2005' => $adicionalOnce0se,
                    'asignacion_familiar' => $adicionalQuince0se,
                    'asignacion_hijos_menores' => $porCadaHijo0se,
                    'total_pension_mensual' => $totalPensionMensual0se,
                    'semanas' => $semanas0se,
                );

                $wpdb->insert( $tabla3, $datos9, $formato3 );

                //Quinta inserción
                $datos10 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 61,
                    'porcentaje' => 80,
                    'cuantia_anual' => $pension61se,
                    'cuantia_mensual' => $cuantiaMensual1se,
                    'reforma_2005' => $adicionalOnce1se,
                    'asignacion_familiar' => $adicionalQuince1se,
                    'asignacion_hijos_menores' => $porCadaHijo1se,
                    'total_pension_mensual' => $totalPensionMensual1se,
                    'semanas' => $semanas1se,
                );

                $wpdb->insert( $tabla3, $datos10, $formato3 );

                //Quinta inserción
                $datos11 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 62,
                    'porcentaje' => 85,
                    'cuantia_anual' => $pension62se,
                    'cuantia_mensual' => $cuantiaMensual2se,
                    'reforma_2005' => $adicionalOnce2se,
                    'asignacion_familiar' => $adicionalQuince2se,
                    'asignacion_hijos_menores' => $porCadaHijo2se,
                    'total_pension_mensual' => $totalPensionMensual2se,
                    'semanas' => $semanas2se,
                );

                $wpdb->insert( $tabla3, $datos11, $formato3 );

                //Sexta inserción
                $datos12 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 63,
                    'porcentaje' => 90,
                    'cuantia_anual' => $pension63se,
                    'cuantia_mensual' => $cuantiaMensual3se,
                    'reforma_2005' => $adicionalOnce3se,
                    'asignacion_familiar' => $adicionalQuince3se,
                    'asignacion_hijos_menores' => $porCadaHijo3se,
                    'total_pension_mensual' => $totalPensionMensual3se,
                    'semanas' => $semanas3se
                );

                $wpdb->insert( $tabla3, $datos12, $formato3 );

                //Septima inserción
                $datos13 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 64,
                    'porcentaje' => 95,
                    'cuantia_anual' => $pension64se,
                    'cuantia_mensual' => $cuantiaMensual4se,
                    'reforma_2005' => $adicionalOnce4se,
                    'asignacion_familiar' => $adicionalQuince4se,
                    'asignacion_hijos_menores' => $porCadaHijo4se,
                    'total_pension_mensual' => $totalPensionMensual4se,
                    'semanas' => $semanas4se,
                );

                $wpdb->insert( $tabla3, $datos13, $formato3 );
                
                //Septima inserción
                $datos14 = array(
                    'id_data_user' => $data_user_id,
                    'age' => 65,
                    'porcentaje' => 100,
                    'cuantia_anual' => $pension65se,
                    'cuantia_mensual' => $cuantiaMensual5se,
                    'reforma_2005' => $adicionalOnce5se,
                    'asignacion_familiar' => $adicionalQuince5se,
                    'asignacion_hijos_menores' => $porCadaHijo5se,
                    'total_pension_mensual' => $totalPensionMensual5se,
                    'semanas' => $semanas5se,
                );
                $wpdb->insert( $tabla3, $datos14, $formato3 );

                ?>
                <?php ob_start(); ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                    <meta name="description" content="">
                    <meta name="keyword" content="">
                    <style>
                        .cont-main {
                            padding: 0em;
                            margin: 0em;
                            width: 100%;
                        }
                        .cont-print-single {
                            margin: 0em;
                            padding: 0em;
                            display: block;
                            width: 100%;
                        }

                        .titulos {
                            width: 720px;
                            display: block;
                        }

                    .nombre {
                        text-align: left;
                    }

                    .reporte {
                        text-align: left;
                    }

                        h4 {
                            display: block;
                            text-align: center;
                        }
                        
                        h5 {
                            display: block;
                            text-align: center;
                        }
                        h6 {
                            display: block;
                            text-align: center;
                        }

                        div.cont-table {
                            width: 720px;
                            display: block;			
                        }

                    div.container {
                            width: 720px;
                            display: block;			
                        }

                        table#t01 {
                            width: 100%;
                            border: 1px solid black;
                            border-collapse: collapse;
                            margin-left: 0px;				
                        }

                        tr {
                            border: 1px solid black;
                            border-collapse: collapse;	
                        }

                        td {
                            border: 1px solid black;
                            font-size: .7rem;
                            border-collapse: collapse;
                        }

                        tr {
                            border: 1px solid black;
                            border-collapse: collapse;			
                        }

                        th {
                            border: 1px solid black;
                            font-size: 1rem;
                            border-collapse: collapse;
                        }

                        div.cont-texto {
                            width: 720px;		
                        }
                        p {
                            text-align: justify;
                            font-size: .8rem;
                        }
                        div.cont-firma {
                            display: block;
                            text-align: center;
                        }

                        div.cont-firma-1 {
                            width: 300px;
                            margin-left: 200px;
                            border-top: 2px solid #000;
                            font-size: 18px;
                        }
                    </style>
                    <title>Sistema Simulador de pensión</title>
                </head>
                <body id="body">
                    <div class="container cont-main">
                        <div class="row cont-print-single">
                        <div class="container">
                            <div class="titulos">
                            <h6>Simulador de Pensión sin cambio en salario y sin cambio en semanas cotizadas.</h6>
                            <br>				
                            </div>
                            <div>
                                <h5 class="nombre">Estimado(a) <span><?php echo $nombre . ' ' . $apellido ?></span></h5>	
                                <h6 class="reporte">Fecha de reporte o captura de información en el sistema <span><?php  echo SpanishDate() ?></span></h6>	
                            </div>
                            <div>
                                <span>Conforme a los datos registrados en el sistema con: <?php echo $inDemandWeeks; ?> semanas cotizadas al día <?php echo SpanishDate() ?></span>
                                <br>
                                <span>Considerando el último salario registrado de $<?php echo $average_salary_total; ?> pesos mexicanos</span>
                                <br>
                                <span>Se detalla el cálculo de la pensión considerando que no habrá incremento en las semanas cotizadas a través de un patrón o aportaciones voluntarias con modalidad 40</span>
                                <br>
                                <span>Su edad actual es: <?php echo $age?> años</span>
                                <br>
                                <span>El número de hijos menores de 18 años que tendrá al momento de pensionarse es: <?php echo $numChild ?></span>
                                <br>
                                <span>Considerando que <?php if(isset($haveChild)){echo 'si';} else {echo 'no';}; ?> tiene padres, esposa o hijos al día de hoy</span>
                            </div>
                            
                            <div class="cont-table">
                                <table id="t01">
                                    <tr>
                                        <th>Rango de edad a recibir la pensión.</th>
                                        <th>% de la pensión según edad (ART.171)</th>
                                        <th>Cuantía Anual</th>
                                        <th>Cuantía mensual</th>
                                        <th>Más 11% por reforma 2005</th>
                                        <th>Más 15% asignación familiar</th>
                                        <th>10% por cada hijo menor de edad</th>
                                        <th>Total, de pensión mensual</th>
                                        <th>Semanas cotizadas</th>
                                        <th>Aguinaldo anual</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            60 años
                                        </td>
                                        <td>
                                            75%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension60se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual0se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce0se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince0se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo0se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual0se, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas0se; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce0se, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            61 años
                                        </td>
                                        <td>
                                            80%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension61se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual1se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce1se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince1se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo1se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual1se, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas1se; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce1se, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            62 años
                                        </td>
                                        <td>
                                            85%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension62se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual2se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce2se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince2se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo2se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual2se, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas2se; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce2se, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            63 años
                                        </td>
                                        <td>
                                            90%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension63se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual3se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce3se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince3se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo3se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual3se, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas3se; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce3se, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            64 años
                                        </td>
                                        <td>
                                            95%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension64se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual4se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce4se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince4se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo4se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual4se, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas4se; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce4se, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            65 años
                                        </td>
                                        <td>
                                            100%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension65se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual5se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce5se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince5se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo5se, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual5se, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas5se; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce5se, 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <div class="row cont-texto">
                                <p>Nota: El cálculo se realiza de conformidad a lo establecido en la ley del IMSS 1973 ART. 167 Y 171. En caso que la Ley del IMSS o los factores de cálculo se modifiquen el importe mensual cambiará.</p>			
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="titulos">
                            <h6>Simulador de Pensión sin cambio en salario y cambio en semanas cotizadas.</h6>
                            <br>				
                            </div>
                            <div>
                                <h5 class="nombre">Estimado(a) <span><?php echo $nombre . ' ' . $apellido ?></span></h5>	
                                <h6 class="reporte">Fecha de reporte o captura de información en el sistema <span><?php echo SpanishDate() ?></span></h6>	
                            </div>
                            <div>
                                <span>Conforme a los datos registrados en el sistema con: <?php echo $inDemandWeeks; ?> semanas cotizadas al día <?php echo SpanishDate() ?></span>
                                <br>
                                <span>Considerando el último salario registrado de $<?php echo $average_salary_total; ?> pesos Mexicanos</span>
                                <br>
                                <span>Se detalla el cálculo de la pensión considerando que seguirá incrementando semanas cotizadas a través de un patrón o aportaciones voluntarias con modalidad 40</span>
                                <br>
                                <span>Su edad actual es: <?php echo $age?> años</span>
                                <br>
                                <span>El número de hijos menores de 18 años que tendrá al momento de pensionarse es: <?php echo $numChild ?></span>
                                <br>
                                <span>Considerando que <?php if(isset($haveChild)){echo 'si';} else {echo 'no';}; ?> tiene padres, esposa o hijos al día de hoy</span>
                            </div>
                            <br>
                            <div class="cont-table">
                                <table id="t01">
                                    <tr>
                                        <th>Rango de edad a recibir la pensión.</th>
                                        <th>% de la pensión según edad (ART.171)</th>
                                        <th>Cuantía Anual</th>
                                        <th>Cuantía mensual</th>
                                        <th>Más 11% por reforma 2005</th>
                                        <th>Más 15% asignación familiar</th>
                                        <th>10% por cada hijo menor de edad</th>
                                        <th>Total, de pensión mensual</th>
                                        <th>Semanas cotizadas</th>
                                        <th>Aguinaldo anual</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            60 años
                                        </td>
                                        <td>
                                            75%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension60, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual0, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce0, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince0, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo0, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual0, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas0; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce0, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            61 años
                                        </td>
                                        <td>
                                            80%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension61, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual1, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce1, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince1, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo1, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual1, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas1; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce1, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            62 años
                                        </td>
                                        <td>
                                            85%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension62, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual2, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce2, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince2, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo2, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual2, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas2; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce2, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            63 años
                                        </td>
                                        <td>
                                            90%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension63, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual3, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce3, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince3, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo3, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual3, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas3; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce3, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            64 años
                                        </td>
                                        <td>
                                            95%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension64, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual4, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce4, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince4, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo4, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual4, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas4; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce4, 2); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            65 años
                                        </td>
                                        <td>
                                            100%
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($pension65, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($cuantiaMensual5, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce5, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalQuince5, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($porCadaHijo5, 2); ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($totalPensionMensual5, 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $semanas5; ?>
                                        </td>
                                        <td>
                                            $ <?php echo number_format_i18n($adicionalOnce5, 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="row cont-texto">
                            <p>Nota: El cálculo se realiza de conformidad a lo establecido en la ley del IMSS 1973 ART. 167 Y 171. En caso que la Ley del IMSS o los factores de cálculo se modifiquen el importe mensual cambiará.</p>			
                        </div>
                        <br><br><br><br><br><br><br><br><br><br>
                        <h3>Semanas cotizadas del IMSS con desglose y fechas de movimiento de los últimos 5 años con los salarios registrados en este reporte</h3>
                        <br>
                        <div class="cont-table">
                            <table id="t01">
                                <tr>
                                    <th>Tipo de movimiento</th>
                                    <th>Fecha de movimiento</th>
                                    <th>Salario base</th>
                                </tr>
                                <?php
                                    foreach ($date_salary as $key => $value) {
                                ?>
                                <tr>
                                    <td>
                                        MODIFICACIÓN DE SALARIO
                                    </td>
                                    <td>
                                        <?php echo $value?>
                                    </td>
                                    <td>
                                        <?php  echo $change_salary[$key] ?>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                ?>
                            </table>
                        </div>
                        </div>
                    </div>
                </body>
                </html>       
                <?php
                // instantiate and use the dompdf class
                $html = ob_get_clean();
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);

                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('letter','portrait');

                // Render the HTML as PDF
                $dompdf->render();

                $nameDoc = $data_user_id . '-' . strtotime(date('Y-m-d H:i:s'));

                $full_path = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) . '/../documents/'.$nameDoc.'.pdf';
                
                // Output the generated PDF to Browser
                $pdf_gen = $dompdf->output();

                if(!file_put_contents($full_path, $pdf_gen)){
                //echo 'Not OK!';
                }else{
                //echo 'OK';
                }

                $tabla4 = $wpdb->prefix . 'data_docs';
                $formato4 = array(            
                    '%d',
                    '%s',
                    '%s',         
                );

                $datos9 = array(
                    'id_data_user' => $data_user_id,
                    'full_path' => $full_path,
                    'name_doc' => $nameDoc.'.pdf'   
                );
                $wpdb->insert( $tabla4, $datos9, $formato4 );

                $headers  = "MIME-Version: 1.0 \r\n";
                $headers .= "Content-type: text/html; charset=utf-8 \r\n";
                $headers .= "From: ".get_option( 'nws_form_from' )."\r\n";
                $headers .= "Bcc:".get_option( 'nws_form_bcc' )."\r\n"."X-Mailer: php";

                $to = $correo;
                $subject = get_option( 'nws_form_subject' );
                $message = get_option( 'nws_form_message' );
                $attachments = $full_path;

                wp_mail($to, $subject, $message, $headers, $attachments);

                $url = get_page_by_title(  get_option( 'nws_form_redirection_title' ) );

                wp_redirect( get_permalink( $url->ID ) );      
                exit();
            } else {
                $url = get_page_by_title(  get_option( 'nws_form_redirection_title_error' ) );
                wp_redirect( get_permalink( $url->ID ) );      
                exit();
            }
            
        }

   }

}

add_action( 'init', 'nws_form_guardar' );

?>