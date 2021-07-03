<?php
/**
 * Plugin Name: Neoworkspark Personal Form
 * Plugin URI:
 * Description: Este plugin es una solución rapida y oportuana para el clente.
 * Version: 1.0.0
 * Author: Ing. Ricardo Frassati y Ing. Rafael Duarte
 * Author URI: 
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('ABSPATH') || die('Access Denied');

class NWSF {
        /**
     * The single instance of the class.
     */
    protected static $_instance = null;
    /**
     * Plugin directory path.
     */
    public $plugin_dir = '';
    /**
     * Plugin directory url.
     */
    public $plugin_url = '';

    /**
   * Main WDFM Instance.
   *
   * Ensures only one instance is loaded or can be loaded.
   *
   * @static
   * @return NWSF - Main instance.
   */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
        self::$_instance = new self();
        }
        return self::$_instance;
    }    

    public function __construct() {
        $this->define_constants();
        require_once( $this->plugin_dir  . '/inc/database.php');
        require_once( $this->plugin_dir . '/inc/options.php' );
        require_once( $this->plugin_dir . '/inc/salario_prome.php' );
        require_once( $this->plugin_dir . '/inc/shortcode.php' );
        $this->add_actions();
    }    
    /**
     * Define Constants.
     */
    private function define_constants() {
        $this->plugin_dir = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));
        $this->plugin_url = plugins_url(plugin_basename(dirname(__FILE__)));
    }

    public function nws_form_agregar_recaptcha() {
        //key site
        //6Lfnq90ZAAAAABHEBKPepAR7kPctSrETYIkq-HYi
        //secret key
        //6Lfnq90ZAAAAAEydQ9SKrtqwmd-znPbJZRCvUXu9
        ?>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php
    }

    
    
    public function nws_form_scripts() {
        wp_register_script( 'scripts', $this->plugin_url . '/js/scripts.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'scripts' );
        wp_register_script( 'bootstrap', $this->plugin_url . '/js/bootstrap.bundle.min.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'bootstrap' );
        wp_enqueue_script( 'sweetalert2js', $this->plugin_url . '/js/sweetalert2.all.min.js', array( 'jquery' ), '9.17.1', true );
        wp_enqueue_script( 'momentjs', $this->plugin_url . '/js/moment.js', array( 'jquery' ), '2.29.1', true );
    }

    public function nws_form_styles() {
        wp_register_style( 'styles', $this->plugin_url . '/css/styles.css');
        wp_enqueue_style( 'styles' );
        wp_register_style( 'bootstrap', $this->plugin_url . '/css/bootstrap.min.css');
        wp_enqueue_style( 'bootstrap' );
        wp_enqueue_style( 'sweetalert2css',$this->plugin_url . '/css/sweetalert2.css' );
    }

    public function nws_form_admin_scripts() {
        wp_register_script( 'jquerydataTables', $this->plugin_url . '/js/jquery.dataTables.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'jquerydataTables' );
        wp_register_script( 'dataTablesbuttons', $this->plugin_url . '/js/dataTables.buttons.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'dataTablesbuttons' );
        wp_register_script( 'dataTablesbootstrap4', $this->plugin_url . '/js/dataTables.bootstrap4.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'dataTablesbootstrap4' );
        wp_register_script( 'jszip', $this->plugin_url . '/js/jszip.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'jszip' );
        wp_register_script( 'pdfmake', $this->plugin_url . '/js/pdfmake.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'pdfmake' );
        wp_register_script( 'vfs_fonts', $this->plugin_url . '/js/vfs_fonts.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'vfs_fonts' );
        wp_register_script( 'buttonshtml5', $this->plugin_url . '/js/buttons.html5.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'buttonshtml5' );
        wp_register_script( 'buttonsbootstrap4', $this->plugin_url . '/js/buttons.bootstrap4.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'buttonsbootstrap4' );
        wp_register_script( 'buttonscolVis', $this->plugin_url . '/js/buttons.colVis.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'buttonscolVis' );
        wp_register_script( 'buttonsflash', $this->plugin_url . '/js/buttons.flash.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'buttonsflash' );
        wp_register_script( 'buttonsprint', $this->plugin_url . '/js/buttons.print.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'buttonsprint' );
        wp_enqueue_style( 'sweetalert2css',$this->plugin_url . '/css/sweetalert2.css' );    
        wp_enqueue_script( 'sweetalert2js', $this->plugin_url . '/js/sweetalert2.all.min.js', array( 'jquery' ), '9.17.1', true );
        wp_register_script( 'bootstrapbundle', $this->plugin_url . '/js/bootstrap.bundle.min.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'bootstrapbundle' );
        
        /* wp_register_script( 'dataTablesdataTables', $this->plugin_url . '/js/dataTables.dataTables.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'dataTablesdataTables' ); */        
        wp_enqueue_script( 'adminjs', $this->plugin_url . '/js/admin-ajax.js', array( 'jquery' ), '1.0', true );
        
        wp_register_style( 'bootstrap', $this->plugin_url . '/css/bootstrap.min.css');
        wp_enqueue_style( 'bootstrap' );
        wp_register_style( 'buttonsbootstrap4', $this->plugin_url . '/css/buttons.bootstrap4.css');
        wp_enqueue_style( 'buttonsbootstrap4' );
        wp_register_style( 'dataTablesbootstrap4', $this->plugin_url . '/css/dataTables.bootstrap4.css');
        wp_enqueue_style( 'dataTablesbootstrap4' );
        wp_register_style( 'flagmin', $this->plugin_url . '/css/flag.min.css');
        wp_enqueue_style( 'flagmin' );
        wp_register_style( 'jquerydataTables', $this->plugin_url . '/css/jquery.dataTables.css');
        wp_enqueue_style( 'jquerydataTables' );
        wp_register_script( 'bootstrapbundle', $this->plugin_url . '/js/bootstrap.bundle.min.js', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'bootstrapbundle' );
    
        //Pasarle la URL de WP Ajax al adminjs
        wp_localize_script( 
            'adminjs',
            'url',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
            )
        );
    }

    private function add_actions() {
        add_action('init', array($this, 'init'), 9);        
        add_action('plugins_loaded', array($this, 'plugins_loaded'), 9);
        add_action( 'wp_head', array($this, 'nws_form_agregar_recaptcha'), 9);
        add_action( 'wp_enqueue_scripts', array($this, 'nws_form_scripts'), 9);
        add_action( 'wp_enqueue_scripts', array($this, 'nws_form_styles'), 9);
        add_action( 'admin_enqueue_scripts', array($this, 'nws_form_admin_scripts'), 9);
        //add_action( 'phpmailer_init',  array($this, 'send_smtp_email'), 9 );
    }
    
    /* function send_smtp_email( $phpmailer ) {
        $phpmailer->isSMTP();
        $phpmailer->Host       = SMTP_HOST;
        $phpmailer->SMTPAuth   = SMTP_AUTH;
        $phpmailer->Port       = SMTP_PORT;
        $phpmailer->SMTPSecure = SMTP_SECURE;
        $phpmailer->Username   = SMTP_USERNAME;
        $phpmailer->Password   = SMTP_PASSWORD;
        $phpmailer->From       = SMTP_FROM;
        $phpmailer->FromName   = SMTP_FROMNAME;
    } */

    /**
    * Wordpress init actions.
    */
    public function init() {
        ob_start();
    }
    /**
     * Plugins loaded actions.
     */
    public function plugins_loaded() {
        if (!function_exists('NWSF')) {
        require_once($this->plugin_dir . '/NWSF.php');
        }
    }
    /**
   * Activate plugin.
   */
    public function form_maker_on_activate() {
        $this->init();
        // Using this insted of flush_rewrite_rule() for better performance with multisite.
        global $wp_rewrite;
        $wp_rewrite->init();
        $wp_rewrite->flush_rules();
    }

    /**
   * Deactivate.
   */
    public function deactivate() {
        // Using this insted of flush_rewrite_rule() for better performance with multisite.
        global $wp_rewrite;
        $wp_rewrite->init();
        $wp_rewrite->flush_rules();
    }   
    
}

/**
 * Main instance of NWSF.
 *
 * @return NWSF The main instance to prevent the need to use globals.
 */
if (!function_exists('NWSFInstance')) {
    function NWSFInstance() {
        return NWSF::instance();
    }
};
NWSFInstance();
?>