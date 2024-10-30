<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 * 
 * @link                https://www.lytex.com.br
 * @since               1.1.0
 * @package             Lytex_Enrolment_Form
 * 
 * 
 * @wordpress-plugin
 * Plugin Name:         Lytex Pagamentos
 * Description:         Adicione facilmente opções de pagamento do Lytex à sua loja do WooCommerce.
 * Version:             2.0.5
 * Author:              Lytex
 * Author URI:          https://Lytex.com.br
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         Lytex-pagamentos-para-woocommerce
 * Domain Path:         /languages
 * Requires PHP:        7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lytex Payments
 */

add_action('plugins_loaded', 'woocommerce_myplugin', 0);
function woocommerce_myplugin(){
    if (!class_exists('WC_Payment_Gateway'))
        return; // if the WC payment gateway class

    include_once dirname( __FILE__ ) . '/class-lytex-payments.php';
    include_once dirname( __FILE__ ) . '/billet-class-gateway.php';
    include_once dirname( __FILE__ ) . '/card-class-gateway.php';
    include_once dirname( __FILE__ ) . '/includes/function.php';
    define( 'LYTEX_PAYMENTS_MAIN_FILE', __FILE__ );
    define( 'LYTEX_PAYMENTS_VERSION', '1.0.2' );
    define('LYTEX_MAIN_PATH', dirname( __FILE__ ));
}

add_filter( 'woocommerce_payment_gateways', 'lytex_add_gateway_class' );
function lytex_add_gateway_class( $ly_gateways ) {
    $ly_gateways[] = 'WC_lytex_Gateway'; // o nome da sua classe está aqui
    $ly_gateways[] = 'Billet_Lytex_Gateway'; // o nome da sua classe está aqui
    $ly_gateways[] = 'Card_lytex_Gateway'; // o nome da sua classe está aqui
    return $ly_gateways;
}



function declare_cart_checkout_blocks_compatibility() {
    // Check if the required class exists
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        // Declare compatibility for 'cart_checkout_blocks'
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
}
// Hook the custom function to the 'before_woocommerce_init' action
add_action('before_woocommerce_init', 'declare_cart_checkout_blocks_compatibility');

// Hook the custom function to the 'woocommerce_blocks_loaded' action
add_action( 'woocommerce_blocks_loaded', 'oawoo_register_order_approval_payment_method_type' );

/**
 * Custom function to register a payment method type

 */
function oawoo_register_order_approval_payment_method_type() {
    // Check if the required class exists
    if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
        return;
    }

    // Include the custom Blocks Checkout class
    require_once plugin_dir_path(__FILE__) . 'class-block.php';

    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
            // Register an instance of My_Custom_Gateway_Blocks
            $payment_method_registry->register( new Pix_Lytex_Blocks );
        }
    );

}
