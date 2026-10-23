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

/**
 * Detecta si la ficha actual está construida con Elementor, directamente
 * en el producto o mediante una plantilla Single Product de Theme Builder.
 */
function wlbw_product_uses_elementor_template() {
    if ( ! did_action( 'elementor/loaded' ) || ! is_product() ) {
        return false;
    }

    $product_id = get_queried_object_id();
    if ( $product_id && isset( \Elementor\Plugin::$instance->documents ) ) {
        $document = \Elementor\Plugin::$instance->documents->get( $product_id );
        if ( $document && $document->is_built_with_elementor() ) {
            return true;
        }
    }

    // Elementor Pro Theme Builder: devuelve las plantillas Single que aplican
    // a la consulta actual, incluidas las plantillas de producto.
    if ( class_exists( '\\ElementorPro\\Modules\\ThemeBuilder\\Module' ) ) {
        $theme_builder = \ElementorPro\Modules\ThemeBuilder\Module::instance();
        $conditions_manager = $theme_builder->get_conditions_manager();

        if ( $conditions_manager && $conditions_manager->get_documents_for_location( 'single' ) ) {
            return true;
        }
    }

    return false;
}

// Prioridad 29: se muestra en el resumen, justo antes del formulario de compra
// (prioridad 30), y sigue apareciendo aunque el producto no sea comprable.
add_action( 'woocommerce_single_product_summary', function() {
    if ( ! is_product() ) {
        return;
    }

    $settings = get_option( 'wlbw_settings', array() );
    if ( empty( $settings['enable_wc_single'] ) || wlbw_product_uses_elementor_template() ) {
        return;
    }

    echo do_shortcode( '[wlbw_wc_button]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}, 31 );




add_shortcode( 'wlbw_wc_button', function() {
    global $product;
    if ( ! $product instanceof WC_Product ) return '';

    $settings = get_option( 'wlbw_settings', array() );
    $number = isset( $settings['wc_number'] ) ? sanitize_text_field( $settings['wc_number'] ) : '';
    if ( empty( $number ) ) return '';
    $message = isset( $settings['wc_message'] ) ? sanitize_text_field( $settings['wc_message'] ) : __( 'Hello, I am interested in this product:', 'lite-contact-button-for-woocommerce' );
    $button_text = get_option('wlbw_wc_button_text', __( 'Ask via WhatsApp', 'lite-contact-button-for-woocommerce' ));
    if ( function_exists( 'pll__' ) ) {
        $message = pll__( $message );
        $button_text = pll__( $button_text );
    }
    $product_name = wp_strip_all_tags( $product->get_name() );
    
    // Construimos la URL inicial (sin variación aún)
    $wa_url = "https://wa.me/{$number}?text=" . rawurlencode("{$message} *{$product_name}*");

    ob_start();

    $is_variable = $product->is_type( 'variable' );
    $require_variation = ! empty( $settings['require_variation'] );

    $attribute_labels = array();
    if ( $is_variable ) {
        foreach ( array_keys( $product->get_variation_attributes() ) as $attribute_key ) {
            $attribute_name = preg_replace( '/^attribute_/', '', $attribute_key );
            $attribute_labels[ $attribute_key ] = wc_attribute_label( $attribute_name, $product );
        }
    }

    $initial_href = ( $is_variable && $require_variation ) ? '#' : esc_url( $wa_url );
    $disabled_class = ( $is_variable && $require_variation ) ? ' disabled' : '';

    ?>
    <div class="wlbw-wc-button-container">
        <a href="<?php echo esc_url( $initial_href ); ?>"
            target="_blank"
            class="wlbw-wc-btn button<?php echo esc_attr( $disabled_class ); ?>"
            data-base-url="<?php echo esc_url( "https://wa.me/{$number}?text=" ); ?>"
            data-message="<?php echo esc_attr( $message ); ?>"
            data-product-name="<?php echo esc_attr( $product_name ); ?>"
            data-attribute-labels="<?php echo esc_attr( wp_json_encode( $attribute_labels ) ); ?>">
            <?php echo esc_html( $button_text ); ?>
        </a>
    </div>
    <?php
    return ob_get_clean();
});
