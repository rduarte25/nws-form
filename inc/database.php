<?php

function nws_form_database() {

    global $wpdb;
    global $nws_form_dbversion;
    $nws_form_dbversion = '1.0.0';

    $tabla = $wpdb->prefix . 'data_user';
    $tabla1 = $wpdb->prefix . 'data_salary';
    $tabla2 = $wpdb->prefix . 'data_calcs';
    $tabla3 = $wpdb->prefix . 'data_table_calcs';
    $tabla4 = $wpdb->prefix . 'data_docs';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla (
        id mediumint(20) NOT NULL AUTO_INCREMENT,
        nombre varchar(50) NOT NULL,
        apellido varchar(50) NOT NULL,
        birthday datetime NOT NULL,
        age int(4) NOT NULL,
        correo varchar(50) DEFAULT '' NOT NULL,
        in_demand_weeks float(10,3) NOT NULL,
        up_imss varchar(10),
        have_child varchar(10),
        num_child int(4),
        PRIMARY KEY(id)
    ) $charset_collate; ";

    $sql1 = "CREATE TABLE $tabla1 (
        id mediumint(20) NOT NULL AUTO_INCREMENT,
        id_data_user mediumint(20) NOT NULL,
        date_salary datetime NOT NULL,
        change_salary float(10,3),
        average_salary float(10,3),
        PRIMARY KEY(id),
        FOREIGN KEY (id_data_user) REFERENCES $tabla(id)
    ) $charset_collate; ";

    $sql2 = "CREATE TABLE $tabla2 (
        id mediumint(20) NOT NULL AUTO_INCREMENT,
        id_data_user mediumint(20) NOT NULL,
        increment_determination float(10,3) NOT NULL,
        average_salary_total float(10,3) NOT NULL,
        factor float(10,3) NOT NULL,
        cuantia float(10,3) NOT NULL,
        incremento float(10,3) NOT NULL,
        excedente float(10,3) NOT NULL,
        num_incremento float(10,3) NOT NULL,
        residuo float(10,3) NOT NULL,
        factor_residual float(10,3) NOT NULL,
        incremento_adicional float(10,3) NOT NULL,
        total_incrementos float(10,3) NOT NULL,
        importe_basico float(10,3) NOT NULL,
        importe_incrementos float(10,3) NOT NULL,
        semanas_meta float(10,3) NOT NULL,
        pension int(10) NOT NULL,
        PRIMARY KEY(id),
        FOREIGN KEY (id_data_user) REFERENCES $tabla(id)
    ) $charset_collate; ";

    $sql3 = "CREATE TABLE $tabla3 (
        id mediumint(20) NOT NULL AUTO_INCREMENT,
        id_data_user mediumint(20) NOT NULL,
        age int(4) NOT NULL,
        porcentaje int(4) NOT NULL,
        cuantia_anual float(10,3) NOT NULL,
        cuantia_mensual float(10,3) NOT NULL,
        reforma_2005 float(10,3) NOT NULL,
        asignacion_familiar float(10,3),
        asignacion_hijos_menores float(10,3),
        total_pension_mensual float(10,3) NOT NULL,
        semanas int(10) NOT NULL,
        PRIMARY KEY(id),
        FOREIGN KEY (id_data_user) REFERENCES $tabla(id)
    ) $charset_collate; ";

    $sql4 = "CREATE TABLE $tabla4 (
        id mediumint(20) NOT NULL AUTO_INCREMENT,
        id_data_user mediumint(20) NOT NULL,
        full_path varchar(300) NOT NULL,
        name_doc varchar(50) NOT NULL,
        PRIMARY KEY(id),
        FOREIGN KEY (id_data_user) REFERENCES $tabla(id)
    ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    if ( get_site_option( 'nws_form_dbversion' ) != $nws_form_dbversion ) {
        dbDelta( $sql );
        dbDelta( $sql1 );
        dbDelta( $sql2 );
        dbDelta( $sql3 );
        dbDelta( $sql4 );
    }

    add_option( 'nws_form_dbversion', $nws_form_dbversion );

    /*** ACTUALIZAR EN CASO DE SER NECESARIO ***/
    
    $version_actual = get_option( 'nws_form_version' );

    if ( $nws_form_dbversion != $version_actual ) {

        $tabla = $wpdb->prefix . 'data_user';
        $tabla1 = $wpdb->prefix . 'data_salary';
        $tabla2 = $wpdb->prefix . 'data_calcs';
        $tabla3 = $wpdb->prefix . 'data_table_calcs';
        $tabla4 = $wpdb->prefix . 'data_docs';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $tabla (
            id mediumint(20) NOT NULL AUTO_INCREMENT,
            nombre varchar(50) NOT NULL,
            apellido varchar(50) NOT NULL,
            birthday datetime NOT NULL,
            age int(4) NOT NULL,
            correo varchar(50) DEFAULT '' NOT NULL,
            in_demand_weeks float(10,3) NOT NULL,
            up_imss varchar(10),
            have_child varchar(10),
            num_child int(4),
            PRIMARY KEY(id)
        ) $charset_collate; ";

        $sql1 = "CREATE TABLE $tabla1 (
            id mediumint(20) NOT NULL AUTO_INCREMENT,
            id_data_user mediumint(20) NOT NULL,
            date_salary datetime NOT NULL,
            change_salary float(10,3),
            average_salary float(10,3),
            PRIMARY KEY(id),
            FOREIGN KEY (id_data_user) REFERENCES $tabla(id)
        ) $charset_collate; ";

        $sql2 = "CREATE TABLE $tabla2 (
            id mediumint(20) NOT NULL AUTO_INCREMENT,
            id_data_user mediumint(20) NOT NULL,
            increment_determination float(10,3) NOT NULL,
            average_salary_total float(10,3) NOT NULL,
            factor float(10,3) NOT NULL,
            cuantia float(10,3) NOT NULL,
            incremento float(10,3) NOT NULL,
            excedente float(10,3) NOT NULL,
            num_incremento float(10,3) NOT NULL,
            residuo float(10,3) NOT NULL,
            factor_residual float(10,3) NOT NULL,
            incremento_adicional float(10,3) NOT NULL,
            total_incrementos float(10,3) NOT NULL,
            importe_basico float(10,3) NOT NULL,
            importe_incrementos float(10,3) NOT NULL,
            semanas_meta float(10,3) NOT NULL,
            pension int(10) NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY (id_data_user) REFERENCES $tabla(id)
        ) $charset_collate; ";

        $sql3 = "CREATE TABLE $tabla3 (
            id mediumint(20) NOT NULL AUTO_INCREMENT,
            id_data_user mediumint(20) NOT NULL,
            age int(4) NOT NULL,
            porcentaje int(4) NOT NULL,
            cuantia_anual float(10,3) NOT NULL,
            cuantia_mensual float(10,3) NOT NULL,
            reforma_2005 float(10,3) NOT NULL,
            asignacion_familiar float(10,3),
            asignacion_hijos_menores float(10,3),
            total_pension_mensual float(10,3) NOT NULL,
            semanas int(10) NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY (id_data_user) REFERENCES $tabla(id)
        ) $charset_collate; ";

        $sql4 = "CREATE TABLE $tabla4 (
            id mediumint(20) NOT NULL AUTO_INCREMENT,
            id_data_user mediumint(20) NOT NULL,
            full_path varchar(300) NOT NULL,
            name_doc varchar(50) NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY (id_data_user) REFERENCES $tabla(id)
        ) $charset_collate; ";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        if ( get_site_option( 'nws_form_dbversion' ) != $nws_form_dbversion ) {
            dbDelta( $sql );
            dbDelta( $sql1 );
            dbDelta( $sql2 );
            dbDelta( $sql3 );
            dbDelta( $sql4 );
        }
    }
    add_option( 'nws_form_dbversion', $nws_form_dbversion );
}

add_action( 'init', 'nws_form_database' );

function nws_form_dbrevisar() {
    global $nws_form_dbversion;

    if ( get_site_option( 'nws_form_dbversion' ) != $nws_form_dbversion ) {

        nws_form_database();

    }
}

add_action( 'plugins_loaded', 'nws_form_dbrevisar' )



?>