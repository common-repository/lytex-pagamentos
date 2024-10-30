<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class My_Custom_Gateway_Blocks extends AbstractPaymentMethodType {

    private $gateway;
    protected $name = 'my_custom_gateway';// your payment gateway name

    public function initialize() {
        $this->settings = get_option( 'woocommerce_my_custom_gateway_settings', [] );
        $this->gateway = new My_Custom_Gateway();
    }

    public function is_active() {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {

        wp_register_script(
            'my_custom_gateway-blocks-integration',
            plugin_dir_url(__FILE__) . 'checkout.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            null,
            true
        );
        if( function_exists( 'wp_set_script_translations' ) ) {            
            wp_set_script_translations( 'my_custom_gateway-blocks-integration');
            
        }
        return [ 'my_custom_gateway-blocks-integration' ];
    }

    public function get_payment_method_data() {
        return [
            'title' => $this->gateway->title,
            'description' => $this->gateway->description,
        ];
    }

}

final class Pix_Lytex_Blocks extends AbstractPaymentMethodType {

    private $gateway;
    protected $name = 'lytex';// your payment gateway name

    public function initialize() {
        $this->settings = get_option( 'woocommerce_lytex_settings', [] );
        $this->gateway = new WC_lytex_Gateway();
    }

    public function is_active() {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {

        wp_register_script(
            'lytex-blocks-integration',
            plugin_dir_url(__FILE__) . 'pix-block.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            null,
            true
        );
        if( function_exists( 'wp_set_script_translations' ) ) {
            wp_set_script_translations( 'lytex-blocks-integration');

        }
        return [ 'lytex-blocks-integration' ];
    }

    public function get_payment_method_data() {
        return [
            'title' => $this->gateway->title,
            'description' => $this->gateway->description,
        ];
    }

}

final class Billet_Lytex_Blocks extends AbstractPaymentMethodType {

    private $gateway;
    protected $name = 'billet_lytex_gateway';// your payment gateway name

    public function initialize() {
        $this->settings = get_option( 'woocommerce_lytex_settings', [] );
        $this->gateway = new Billet_Lytex_Gateway();
    }

    public function is_active() {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {

        wp_register_script(
            'billet_lytex_gateway-blocks-integration',
            plugin_dir_url(__FILE__) . 'billet-block.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            null,
            true
        );
        if( function_exists( 'wp_set_script_translations' ) ) {
            wp_set_script_translations( 'billet_lytex_gateway-blocks-integration');

        }
        return [ 'billet_lytex_gateway-blocks-integration' ];
    }

    public function get_payment_method_data() {
        return [
            'title' => $this->gateway->title,
            'description' => $this->gateway->description,
        ];
    }

}
