<?php
/*
Plugin Name: Lite Button for WooCommerce
Plugin URI: https://github.com/Martiarena/lite-contact-button-for-woocommerce
Description: Lightweight WhatsApp buttons for WooCommerce products and WordPress pages. Modular, fast, and without unnecessary code.
Version: 1.0.0
Author: Raúl Martiarena
Author URI: https://raulmartiarena.com
License: GPL2
Text Domain: lite-contact-button-for-woocommerce
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WLBW_DIR', plugin_dir_path( __FILE__ ) );
define( 'WLBW_URL', plugin_dir_url( __FILE__ ) );


add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {

    $settings_link = '<a href="' . admin_url( 'admin.php?page=wlbw-settings' ) . '">' .
        esc_html__( 'Settings', 'lite-contact-button-for-woocommerce' ) .
    '</a>';

    array_unshift( $links, $settings_link );

    return $links;
});

// Incluir página de configuración
require_once WLBW_DIR . 'admin/settings-page.php';

// Registrar los textos configurados para que Polylang los muestre en Traducciones.
add_action( 'init', function() {
    if ( ! function_exists( 'pll_register_string' ) ) {
        return;
    }

    $settings = get_option( 'wlbw_settings', array() );
    $strings = array(
        'Mensaje del botón flotante' => $settings['floating_message'] ?? '',
        'Mensaje del botón de producto' => $settings['wc_message'] ?? '',
        'Texto del botón de producto' => get_option(
            'wlbw_wc_button_text',
            __( 'Ask via WhatsApp', 'lite-contact-button-for-woocommerce' )
        ),
    );

    foreach ( $strings as $name => $string ) {
        $string = trim( (string) $string );
        if ( '' !== $string ) {
            pll_register_string( $name, $string, 'Lite Button for WooCommerce' );
        }
    }
});

// Cargar módulos según ajustes
$wlbw_options = get_option( 'wlbw_settings', array() );

if ( ! empty( $wlbw_options['enable_wc_single'] ) ) {
    require_once WLBW_DIR . 'modules/wc-single-button.php';
}

if ( ! empty( $wlbw_options['enable_floating'] ) ) {
    require_once WLBW_DIR . 'modules/floating-button.php';
}
