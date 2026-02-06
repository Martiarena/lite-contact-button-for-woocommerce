<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function() {
    $settings = get_option( 'wlbw_settings', array() );
    if ( is_singular( 'product' ) || has_shortcode( get_post()->post_content ?? '', 'wlbw_wc_button' ) ) {
        wp_enqueue_style(
            'wlbw-wc-single-css',
            WLBW_URL . 'assets/css/single-button.css',
            array(),
            '1.0'
        );

        wp_enqueue_script(
            'wlbw-wc-single-js',
            WLBW_URL . 'assets/js/single-button.js',
            array( 'jquery' ),
            '1.0',
            true
        );

        wp_localize_script(
            'wlbw-wc-single-js',
            'wlbwSettings',
            array(
                'requireVariation' => ! empty( $settings['require_variation'] ) ? true : false,
            )
        );
    }
});

add_action( 'woocommerce_before_add_to_cart_quantity', function() {

    if ( ! is_product() ) return;

    // Evitar duplicado si usan shortcode (Elementor)
    if ( has_shortcode( get_post()->post_content ?? '', 'wlbw_wc_button' ) ) return;

    echo do_shortcode( '[wlbw_wc_button]' );

});

add_shortcode( 'wlbw_wc_button', function() {
    global $product;
    if ( ! $product instanceof WC_Product ) return '';

    $settings = get_option( 'wlbw_settings', array() );
    $number = isset( $settings['wc_number'] ) ? sanitize_text_field( $settings['wc_number'] ) : '';
    if ( empty( $number ) ) return '';
    $message = isset( $settings['wc_message'] ) ? sanitize_text_field( $settings['wc_message'] ) : __( 'Hello, I am interested in this product:', 'lite-contact-button-for-woocommerce' );
    $button_text = get_option('wlbw_wc_button_text',__( 'Ask via WhatsApp', 'lite-contact-button-for-woocommerce' ));
    $product_name = wp_strip_all_tags( $product->get_name() );
    
    // Construimos la URL inicial (sin variación aún)
    $wa_url = "https://wa.me/{$number}?text=" . rawurlencode("{$message} {$product_name}");

    ob_start();

    $is_variable = $product->is_type( 'variable' );
    $require_variation = ! empty( $settings['require_variation'] );
    $initial_href = ( $is_variable && $require_variation ) ? '#' : esc_url( $wa_url );
    $disabled_class = ( $is_variable && $require_variation ) ? ' disabled' : '';

    ?>
    <div class="wlbw-wc-button-container">
        <a href="<?php echo esc_url( $initial_href ); ?>"
            target="_blank"
            class="wlbw-wc-btn button<?php echo esc_attr( $disabled_class ); ?>"
            data-base-url="<?php echo esc_url( "https://wa.me/{$number}?text=" ); ?>"
            data-message="<?php echo esc_attr( $message ); ?>"
            data-product-name="<?php echo esc_attr( $product_name ); ?>">
            <?php echo esc_html( $button_text ); ?>
        </a>
    </div>
    <?php
    return ob_get_clean();
});
