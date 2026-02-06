<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', function() {
    add_menu_page(
        __( 'WhatsApp Buttons', 'lite-contact-button-for-woocommerce' ),
        __( 'WhatsApp Buttons', 'lite-contact-button-for-woocommerce' ),
        'manage_options',
        'wlbw-settings',
        'wlbw_render_settings_page',
        'dashicons-whatsapp',
        80
    );
});

add_action( 'admin_init', function() {
    register_setting( 'wlbw_settings_group', 'wlbw_settings', array( 'sanitize_callback' => 'wlbw_sanitize_settings' ) );
    register_setting( 'wlbw_settings_group', 'wlbw_wc_button_text', array( 'sanitize_callback' => 'sanitize_text_field' ) );
});

function wlbw_sanitize_settings( $input ) {
    $output = array();

    $output['enable_wc_single'] = ! empty( $input['enable_wc_single'] ) ? 1 : 0;
    $output['require_variation'] = ! empty( $input['require_variation'] ) ? 1 : 0;
    $output['enable_floating']  = ! empty( $input['enable_floating'] ) ? 1 : 0;

    $output['wc_number']        = sanitize_text_field( $input['wc_number'] ?? '' );
    $output['wc_message']       = sanitize_text_field( $input['wc_message'] ?? '' );
    $output['floating_number']  = sanitize_text_field( $input['floating_number'] ?? '' );
    $output['floating_message'] = sanitize_text_field( $input['floating_message'] ?? '' );
    $output['floating_position'] = in_array($input['floating_position'] ?? 'right', array( 'left', 'right' ), true) ? $input['floating_position'] : 'right';
    $output['floating_bottom'] = isset( $input['floating_bottom'] ) ? max( 0, min( 50, intval( $input['floating_bottom'] ) ) ) : 3;

    if ( ! empty( $input['floating_locations'] ) && is_array( $input['floating_locations'] ) ) {
        foreach ( $input['floating_locations'] as $key => $value ) {
            $output['floating_locations'][ sanitize_key( $key ) ] = 1;
        }
    }

    return $output;
}

function wlbw_render_settings_page() {
    $settings = get_option( 'wlbw_settings', array() );
    ?>
    <div class="wrap">
        <?php settings_errors(); ?>
        <h1><strong><?php esc_html_e( 'Lite Button for WooCommerce', 'lite-contact-button-for-woocommerce' ); ?></strong></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'wlbw_settings_group' ); ?>

            <h2><?php esc_html_e( 'WooCommerce Single Product Button', 'lite-contact-button-for-woocommerce' ); ?></h2>
            <p style="margin-bottom:15px; color:#555;">
                <?php
                    /* Translators: %s: shortcode example */
                    printf( esc_html__( 'If your product template is built with Elementor, use this shortcode: %s', 'lite-contact-button-for-woocommerce' ), '<code>[wlbw_wc_button]</code>' );
                ?>
            </p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><strong><?php esc_html_e( 'WhatsApp Number', 'lite-contact-button-for-woocommerce' ); ?>:</strong></th>
						<td><input type="text" name="wlbw_settings[wc_number]" placeholder="<?php esc_attr_e( 'e.g. 51999999999', 'lite-contact-button-for-woocommerce' ); ?>" value="<?php echo esc_attr( $settings['wc_number'] ?? '' ); ?>"></td>
					</tr>
                    <tr>
                        <th scope="row"><strong><?php esc_html_e( 'Message', 'lite-contact-button-for-woocommerce' ); ?>:</strong></th>
						<td><input type="text" name="wlbw_settings[wc_message]" placeholder="<?php esc_attr_e( 'Default message', 'lite-contact-button-for-woocommerce' ); ?>" value="<?php echo esc_attr( $settings['wc_message'] ?? '' ); ?>"></td>
					</tr>
                    <tr>
                        <th scope="row"><strong><?php esc_html_e( 'Button Text', 'lite-contact-button-for-woocommerce' ); ?>:</strong></th>
						<td><input type="text" name="wlbw_wc_button_text" placeholder="<?php esc_attr_e( 'Ask via WhatsApp', 'lite-contact-button-for-woocommerce' ); ?>" value="<?php echo esc_attr( get_option( 'wlbw_wc_button_text', __( 'Ask via WhatsApp', 'lite-contact-button-for-woocommerce' ) ) ); ?>"></td>
					</tr>
                    <tr>
                        <th scope="row"></th>
						<td><label><input type="checkbox" name="wlbw_settings[require_variation]" value="1" <?php checked( 1, $settings['require_variation'] ?? 0 ); ?>> <strong><?php esc_html_e( 'Require selecting product variations before enabling WhatsApp button', 'lite-contact-button-for-woocommerce' ); ?></strong></label></td>
					</tr>
                    <tr>
                        <th scope="row"></th>
						<td><label><input type="checkbox" name="wlbw_settings[enable_wc_single]" value="1" <?php checked( 1, $settings['enable_wc_single'] ?? 0 ); ?>> <strong><?php esc_html_e( 'Enable WooCommerce single product button', 'lite-contact-button-for-woocommerce' ); ?></strong></label></td>
					</tr>
                </tbody>
            </table>

            <hr>

            <h2><?php esc_html_e( 'Floating Button', 'lite-contact-button-for-woocommerce' ); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <strong><?php esc_html_e( 'WhatsApp Number', 'lite-contact-button-for-woocommerce' ); ?>:</strong>
                        </th>
						<td><input type="text" name="wlbw_settings[floating_number]" placeholder="<?php esc_attr_e( 'e.g. 51999999999', 'lite-contact-button-for-woocommerce' ); ?>" value="<?php echo esc_attr( $settings['floating_number'] ?? '' ); ?>"></td>
					</tr>
                    <tr>
                        <th scope="row">
                            <strong><?php esc_html_e( 'Message', 'lite-contact-button-for-woocommerce' ); ?>:</strong>
                        </th>
						<td><input type="text" name="wlbw_settings[floating_message]" placeholder="<?php esc_attr_e( 'Default message', 'lite-contact-button-for-woocommerce' ); ?>" value="<?php echo esc_attr( $settings['floating_message'] ?? '' ); ?>"></td>
					</tr>
                    <tr>
                        <th scope="row">
                            <strong><?php esc_html_e( 'Position', 'lite-contact-button-for-woocommerce' ); ?></strong>
                        </th>
                        <td>
                            <label>
                                <input type="radio" name="wlbw_settings[floating_position]" value="right"
                                    <?php checked( $settings['floating_position'] ?? 'right', 'right' ); ?>>
                                <?php esc_html_e( 'Bottom Right', 'lite-contact-button-for-woocommerce' ); ?>
                            </label><br>

                            <label>
                                <input type="radio" name="wlbw_settings[floating_position]" value="left"
                                    <?php checked( $settings['floating_position'] ?? '', 'left' ); ?>>
                                <?php esc_html_e( 'Bottom Left', 'lite-contact-button-for-woocommerce' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <strong><?php esc_html_e( 'Bottom offset (%)', 'lite-contact-button-for-woocommerce' ); ?></strong>
                        </th>
                        <td>
                            <input
                                type="number"
                                min="0"
                                max="50"
                                step="1"
                                name="wlbw_settings[floating_bottom]"
                                value="<?php echo esc_attr( $settings['floating_bottom'] ?? 3 ); ?>"
                            >
                            <p class="description">
                                <?php esc_html_e( 'Distance from bottom in percentage (recommended: 3–10)', 'lite-contact-button-for-woocommerce' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"></th>
						<td><label><input type="checkbox" name="wlbw_settings[enable_floating]" value="1" <?php checked( 1, $settings['enable_floating'] ?? 0 ); ?>> <strong><?php esc_html_e( 'Enable Floating Button', 'lite-contact-button-for-woocommerce' ); ?></strong></label></td>
					</tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Show on:', 'lite-contact-button-for-woocommerce' ); ?></th>
						<td>
                            <?php
                                $post_types = get_post_types( array(
                                    'public'   => true, 
                                    'show_ui'  => true), 
                                'objects' );

                                // Filter out irrelevant post types
                                foreach ( $post_types as $slug => $post_type ) {
                                    // Exclude attachments, Elementor templates, revisions and internal types
                                    if ( in_array( $slug, array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'wp_block', 'elementor_library' ) ) ) {
                                        unset( $post_types[ $slug ] );
                                    }
                                }

                                foreach ( $post_types as $type ) {
                                    $checked = ! empty( $settings['floating_locations'][$type->name] );
                                    echo '<label style="display:block;"><input type="checkbox" name="wlbw_settings[floating_locations][' . esc_attr( $type->name ) . ']" value="1" ' . checked( $checked, true, false ) . '> ' . esc_html( $type->labels->singular_name ) . '</label>';
                                }
                            ?>
                        </td>
					</tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}