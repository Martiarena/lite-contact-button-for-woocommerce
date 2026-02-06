<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function() {
    $settings = get_option( 'wlbw_settings', array() );

    if ( empty( $settings['enable_floating'] ) ) return;

    $current_post_type = get_post_type();
    if ( empty( $settings['floating_locations'][ $current_post_type ] ) ) return;
    
    wp_enqueue_style( 'wlbw-floating-css', WLBW_URL . 'assets/css/floating-button.css', array(), '1.0.0' );
    wp_enqueue_script( 'wlbw-floating-js', WLBW_URL . 'assets/js/floating-button.js', array(), '1.0.0', true );
});

add_action( 'wp_footer', function() {
    $settings = get_option( 'wlbw_settings', array() );

    if ( empty( $settings['floating_number'] ) ) return;

    $current_post_type = get_post_type();

    if ( empty( $settings['floating_locations'][$current_post_type] ) ) return;

    $number = sanitize_text_field( $settings['floating_number'] );
    $message = sanitize_text_field( $settings['floating_message'] ?? '' );
    
    $url = 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );

    $bottom = isset( $settings['floating_bottom'] ) ? intval( $settings['floating_bottom'] ) : 5;
    $position = $settings['floating_position'] ?? 'right';
    $style  = '--wlbw-bottom-offset: ' . $bottom . '%;';

    if ( $position === 'left' ) {
        $style .= '--wlbw-left-offset: 1.25em; --wlbw-right-offset: auto;';
    } else {
        $style .= '--wlbw-right-offset: 1.25em; --wlbw-left-offset: auto;';
    }

    echo '<a href="' . esc_url( $url ) . '"target="_blank" rel="noopener noreferrer" class="wlbw-floating-btn pulse-anim" style="' . esc_attr( $style ) . '"><img src="' . esc_url( WLBW_URL . 'assets/img/icon.svg' ) . '" alt="' . esc_attr__( 'WhatsApp button', 'lite-contact-button-for-woocommerce' ) . '"></a>';
});
