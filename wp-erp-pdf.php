<?php
/**
 * Plugin Name: WP ERP - PDF Invoice
 * Plugin URI:  http://wperp.com
 * Description: PDF invoice for WP ERP
 * Version:     1.0.0
 * Author:      weDevs
 * Author URI:  http://wedevs.com
 * Donate link: http://wperp.com
 * License:     GPLv2+
 * Text Domain: erp_pdf
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2018 weDevs (email : support@wperp.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main initiation class
 *
 * @since 1.0.0
 */
class WP_ERP_PDF {

    /**
     * Add-on Version
     *
     * @since 1.0.0
     * @var  string
     */
    public $version = '1.0.0';

    /**
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '5.4.0';


    /**
     * Constructor for the class
     *
     * Sets up all the appropriate hooks and actions
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        // dry check on older PHP versions, if found deactivate itself with an error
        register_activation_hook( __FILE__, array( $this, 'auto_deactivate' ) );

        if ( ! $this->is_supported_php() ) {
            return;
        }

        // Define constants
        $this->define_constants();

        // Include required files
        $this->includes();

        // Initialize the action hooks
        $this->init_hooks();

    }

    /**
     * Initializes the class
     *
     * Checks for an existing instance
     * and if it does't find one, creates it.
     *
     * @since 1.0.0
     *
     * @return object Class instance
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define constants
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function define_constants() {
        define( 'WPERP_PDF_VERSION', $this->version );
        define( 'WPERP_PDF_FILE', __FILE__ );
        define( 'WPERP_PDF_PATH', dirname( WPERP_PDF_FILE ) );
        define( 'WPERP_PDF_INCLUDES', WPERP_PDF_PATH . '/includes' );
        define( 'WPERP_PDF_URL', plugins_url( '', WPERP_PDF_FILE ) );
        define( 'WPERP_PDF_ASSETS', WPERP_PDF_URL . '/assets' );
    }

    /**
     * Include required files
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function includes() {
        require WPERP_PDF_INCLUDES . '/functions.php';
        require WPERP_PDF_INCLUDES . '/class-tfpdf.php';
        require WPERP_PDF_INCLUDES . '/class-pdf-invoicer.php';
    }

    /**
     * Init Hooks
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function init_hooks() {
        // Localize our plugin
        add_action( 'init', [ $this, 'localization_setup' ] );
    }

    /**
     * Initialize plugin for localization
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function localization_setup() {
        load_plugin_textdomain( 'erp_pdf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


    /**
     * Check if the PHP version is supported
     *
     * @return bool
     */
    public function is_supported_php( $min_php = null ) {

        $min_php = $min_php ? $min_php : $this->min_php;

        if ( version_compare( PHP_VERSION, $min_php, '<=' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Show notice about PHP version
     *
     * @return void
     */
    function php_version_notice() {

        if ( $this->is_supported_php() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $error = __( 'Your installed PHP Version is: ', 'erp_pdf' ) . PHP_VERSION . '. ';
        $error .= __( 'The <strong>WP ERP - PDF</strong> plugin requires PHP version <strong>', 'erp_pdf' ) . $this->min_php . __( '</strong> or greater.', 'erp_pdf' );
        ?>
        <div class="error">
            <p><?php printf( $error ); ?></p>
        </div>
        <?php
    }

    /**
     * Bail out if the php version is lower than
     *
     * @return void
     */
    function auto_deactivate() {
        if ( $this->is_supported_php() ) {
            return;
        }

        deactivate_plugins( plugin_basename( __FILE__ ) );

        $error = __( '<h1>An Error Occured</h1>', 'erp_pdf' );
        $error .= __( '<h2>Your installed PHP Version is: ', 'erp_pdf' ) . PHP_VERSION . '</h2>';
        $error .= __( '<p>The <strong>WP ERP - PDF</strong> plugin requires PHP version <strong>', 'erp_pdf' ) . $this->min_php . __( '</strong> or greater', 'erp_pdf' );
        $error .= __( '<p>The version of your PHP is ', 'erp_pdf' ) . '<a href="http://php.net/supported-versions.php" target="_blank"><strong>' . __( 'unsupported and old', 'erp_pdf' ) . '</strong></a>.';
        $error .= __( 'You should update your PHP software or contact your host regarding this matter.</p>', 'erp_pdf' );

        wp_die( $error, __( 'Plugin Activation Error', 'erp_pdf' ), array( 'back_link' => true ) );
    }

}

/**
 * Initialize the plugin
 *
 * @return object
 */
function wp_erp_pdf() {
    return WP_ERP_PDF::init();
}

// kick-off
wp_erp_pdf();
