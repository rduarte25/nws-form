<?php

function nws_form_ajustes() {

    add_menu_page( 'Registros Formulario NWS', 'NWSF Registros', 'administrator', 'nws_form_registros', 'nws_form_registros', 'dashicons-analytics' ,100 );
    add_submenu_page( 'nws_form_registros', 'Ajustes', 'Ajustes', 'administrator', 'nws_form_opciones', 'nws_form_opciones' );

    //Llamar al registro de las opciones de nuestro theme.
    add_action( 'admin_init', 'nws_form_registrar_opciones' );
}

add_action( 'admin_menu', 'nws_form_ajustes' );


function nws_form_registrar_opciones() {
    //Registrar opcines, una por campo.
    register_setting( 'nws_form_opciones_grupo', 'nws_form_site_key' );
    register_setting( 'nws_form_opciones_grupo', 'nws_form_site_secret' );
    register_setting( 'nws_form_opciones_grupo', 'nws_form_redirection_title' );
    register_setting( 'nws_form_opciones_grupo', 'nws_form_redirection_title_error' );
    register_setting( 'nws_form_opciones_email', 'nws_form_from' );
    register_setting( 'nws_form_opciones_email', 'nws_form_bcc' );
    register_setting( 'nws_form_opciones_email', 'nws_form_subject' );
    register_setting( 'nws_form_opciones_email', 'nws_form_message' );

}

function nws_form_opciones() {
?>
    <div class="wrap">
        <h1>Ajustes Network Sparck Simple Form</h1>
        <?php
            if( isset( $_GET['tab'] ) ):
                $active_tab = $_GET['tab'];
            else:
                $active_tab = 'email';
            endif;
        ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=nws_form_opciones&tab=recapcha" class="nav-tab <?php echo $active_tab == 'recapcha' ? 'nav-tab-active' : '' ?> ">Ajustes RECAPCHA</a>
            <a href="?page=nws_form_opciones&tab=email" class="nav-tab <?php echo $active_tab == 'email' ? 'nav-tab-active' : '' ?> ">Ajustes Email</a>
        </h2>
        
        <form action="options.php" method="POST">
            <?php if($active_tab == 'recapcha'):?>
                <?php settings_fields( 'nws_form_opciones_grupo' );?>
                <?php do_settings_sections( 'nws_form_opciones_grupo' );?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Site Key</th>
                        <td>
                            <input type="text" name="nws_form_site_key" value="<?php echo esc_attr( get_option( 'nws_form_site_key' ) );?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Site Secret</th>
                        <td>
                            <input type="text" name="nws_form_site_secret" value="<?php echo esc_attr( get_option( 'nws_form_site_secret' ) );?>">
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Título de la página a redireccionar</th>
                        <td>
                            <input type="text" name="nws_form_redirection_title" value="<?php echo esc_attr( get_option( 'nws_form_redirection_title' ) );?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Título de la página a redireccionar en caso de error</th>
                        <td>
                            <input type="text" name="nws_form_redirection_title_error" value="<?php echo esc_attr( get_option( 'nws_form_redirection_title_error' ) );?>">
                        </td>
                    </tr>
                </table>
            <?php else:?>
                <?php settings_fields( 'nws_form_opciones_email' );?>
                <?php do_settings_sections( 'nws_form_opciones_email' );?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">From del email</th>
                        <td>
                            <input type="text" name="nws_form_from" value="<?php echo esc_attr( get_option( 'nws_form_from' ) );?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Bcc del email</th>
                        <td>
                            <input type="text" name="nws_form_bcc" value="<?php echo esc_attr( get_option( 'nws_form_bcc' ) );?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Asunto del email</th>
                        <td>
                            <input type="text" name="nws_form_subject" value="<?php echo esc_attr( get_option( 'nws_form_subject' ) );?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Mensaje del email</th>
                        <td>
                            <textarea name="nws_form_message" id="nws_form_message" cols="30" rows="10"><?php echo esc_attr( get_option( 'nws_form_message' ) );?></textarea>
                        </td>
                    </tr>
                </table>
            <?php endif;?>
            <?php submit_button();?>
        </form>
    </div>
<?php
}


function nws_form_registros() {

?>
    
    <div class="wrap">
        <?php
            if( isset( $_GET['id'] ) ):
                $id = $_GET['id'];         
        ?>
        <?php 
            global $wpdb;
            
            $dataUser = $wpdb->prefix . 'data_user';
            $query = "SELECT * FROM $dataUser WHERE id=".$id;
            $usuario = $wpdb->get_row( $query, ARRAY_A, 0);
        ?>
        
        <div class="wrap">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th class="manege-column">ID</th>
                        <th class="manege-column">Nombre</th>
                        <th class="manege-column">Apellido</th>
                        <th class="manege-column">Correo</th>
                        <th class="manege-column">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php echo $usuario['id'];?>
                        </td>
                        <td>
                            <?php echo $usuario['nombre'];?>
                        </td>
                        <td>
                            <?php echo $usuario['apellido'];?>
                        </td>
                        <td>
                            <?php echo $usuario['correo'];?>
                        </td>
                        <td>
                            <a id="borrar_registro" href="#" data-data_user="<?php echo $usuario['id']?>"><button class="btn btn-danger">Eliminar</button></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th class="manege-column">ID</th>
                        <th class="manege-column">ID de usuario</th>
                        <th class="manege-column">Fecha del registro de salario</th>
                        <th class="manege-column">Salario</th>
                        <th class="manege-column">Promedio Salarial</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $dataSalary = $wpdb->prefix . 'data_salary';
                        $query = "SELECT * FROM $dataSalary WHERE id_data_user=".$id;
                        $registros = $wpdb->get_results( $query, ARRAY_A );
                        foreach( $registros as $registro ) {
                    ?>  
                        
                        <tr>
                            <td>
                                <?php echo $registro['id'];?>
                            </td>
                            <td>
                                <?php echo $registro['id_data_user'];?>
                            </td>
                            <td>
                                <?php echo $registro['date_salary'];?>
                            </td>
                            <td>
                                <?php echo $registro['change_salary'];?>
                            </td>
                            <td>
                                <?php echo $registro['average_salary'];?>
                            </td>
                        </tr>
                    
                    <?php
                        }                    
                    ?>
                </tbody>
            </table>
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th class="manege-column">ID</th>
                        <th class="manege-column">ID de usuario</th>
                        <th class="manege-column">Determinación de Incremento</th>
                        <th class="manege-column">Salario promedio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $dataSalary = $wpdb->prefix . 'data_calcs';
                        $query = "SELECT * FROM $dataSalary WHERE id_data_user=".$id;
                        $registros = $wpdb->get_results( $query, ARRAY_A );
                        foreach( $registros as $registro ) {
                    ?>  
                        
                        <tr>
                            <td>
                                <?php echo $registro['id'];?>
                            </td>
                            <td>
                                <?php echo $registro['id_data_user'];?>
                            </td>
                            <td>
                                <?php echo $registro['increment_determination'];?>
                            </td>
                            <td>
                                <?php echo $registro['average_salary_total'];?>
                            </td>
                        </tr>
                    
                    <?php
                        }                    
                    ?>
                </tbody>
            </table>
            <div>
                <br>
                <h6>Simulador de Pensión sin cambio en salario y sin cambio en semanas cotizadas.</h6>
                <br>				
            </div>
            <table class="wp-list-table widefat striped">
                <thead>
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
                </thead>
                <tbody>
                    <?php 
                        $dataTableCalcs = $wpdb->prefix . 'data_table_calcs';
                        $dataUser = $wpdb->prefix . 'data_user';
                        $query = "SELECT * FROM $dataTableCalcs INNER JOIN $dataUser ON $dataUser.id = $dataTableCalcs.id_data_user WHERE id_data_user=".$id . " LIMIT 6,6";
                        $registros = $wpdb->get_results( $query, ARRAY_A );
                        foreach( $registros as $registro ) {
                    ?>  
                        
                        <tr>
                            <td>
                                <?php echo $registro['age'];?> años
                            </td>
                            <td>
                                <?php echo $registro['porcentaje'];?>%
                            </td>
                            <td>
                                $ <?php echo $registro['cuantia_anual'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['cuantia_mensual'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['reforma_2005'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['asignacion_familiar'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['asignacion_hijos_menores'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['total_pension_mensual'];?>
                            </td>
                            <td>
                                <?php echo $registro['semanas'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['reforma_2005'];?>
                            </td>
                        </tr>
                    
                    <?php
                        }                
                        $dataDocs = $wpdb->prefix . 'data_docs';
                        $query = "SELECT * FROM $dataDocs WHERE id_data_user=".$id;
                        $registro = $wpdb->get_row( $query, ARRAY_A );                    
                    ?>
                </tbody>
            </table>
            <div>
                <br>
                <h6>Simulador de Pensión sin cambio en salario y cambio en semanas cotizadas.</h6>
                <br>				
            </div>
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th>Rango de edad a recibir la pensión.</th>
                        <th>% de la pensión según edad (ART.171)</th>
                        <th>Cuantía Anual</th>
                        <th>Cuantia mensual</th>
                        <th>Más 11% por reforma 2005</th>
                        <th>Más 15% asignación familiar</th>
                        <th>10% por cada hijo menor de edad</th>
                        <th>Total, de pensión mensual</th>
                        <th>Semanas cotizadas</th>
                        <th>Aguinaldo anual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $dataTableCalcs = $wpdb->prefix . 'data_table_calcs';
                        $query = "SELECT * FROM $dataTableCalcs WHERE id_data_user=".$id. " LIMIT 6";
                        $registros = $wpdb->get_results( $query, ARRAY_A );
                        foreach( $registros as $registro ) {
                    ?>  
                        
                        <tr>
                            <td>
                                <?php echo $registro['age'];?> años
                            </td>
                            <td>
                                <?php echo $registro['porcentaje'];?>%
                            </td>
                            <td>
                                $ <?php echo $registro['cuantia_anual'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['cuantia_mensual'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['reforma_2005'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['asignacion_familiar'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['asignacion_hijos_menores'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['total_pension_mensual'];?>
                            </td>
                            <td>
                                <?php echo $registro['semanas'];?>
                            </td>
                            <td>
                                $ <?php echo $registro['reforma_2005'];?>
                            </td>
                        </tr>
                    
                    <?php
                        }                
                        $dataDocs = $wpdb->prefix . 'data_docs';
                        $query = "SELECT * FROM $dataDocs WHERE id_data_user=".$id;
                        $registro = $wpdb->get_row( $query, ARRAY_A );                    
                    ?>
                </tbody>
            </table>
        <div>
        <a href="<?php echo '../wp-content/plugins/'.plugin_basename(dirname(__DIR__)).'/documents/'. $registro['name_doc']; ?>" target="_blank"><button class='btn btn-primary'><i class='zmdi zmdi-download'></i> Descargar</button></a></div>
        </div>
        <?php else:?>
            <h1>Registros</h1>
            <table id="tblregistro" class="table table-striped table-bordered teble-condensed table-hover table-responsive-sm">
                <thead>
                    <th class="manege-column">ID</th>
                    <th class="manege-column">Nombre</th>
                    <th class="manege-column">Apellido</th>
                    <th class="manege-column">Correo</th>
                    <th class="manege-column">Opciones</th>
                </thead>
                <tbody>

                </tbody>
                <thead>
                    <th class="manege-column">ID</th>
                    <th class="manege-column">Nombre</th>
                    <th class="manege-column">Apellido</th>
                    <th class="manege-column">Correo</th>
                    <th class="manege-column">Opciones</th>
                </thead>
            </table>
        <?php endif;?>
        <div></div>
        
    </div>
    
<?php

}

?>