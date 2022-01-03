<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://WeRePack.org
 * @since      1.0.0
 *
 * @package    Repack
 * @subpackage Repack/admin/partials
 */

/**
 * Register Settings Page
 */
add_filter(
	'woocommerce_get_sections_shipping',
	function ( $settings_tab ) {
		$settings_tab['werepack'] = __( 'WeRePack Settings', 'repack-for-woocommerce' );
		return $settings_tab;
	}
);

/**
 * Add settings to the WeRePack Settings Page
 */
add_filter(
	'woocommerce_get_settings_shipping',
	function ( $settings, $current_section ) {

		/**
		 * Check the current section is what we want
		 **/
		if ( 'werepack' === $current_section ) {
			return array(
				array(
					'id'   => 'werepack',
					'name' => __( 'Checkout WeRePack Consent', 'repack-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'Adjust the WeRePack Consent Box in the checkout to your needs.', 'repack-for-woocommerce' ),
				),

				// Consent Position
				array(
					'id'      => 'repack_checkout_consent_position',
					'type'    => 'select',
					'name'    => __( 'Consent Position', 'repack-for-woocommerce' ),
					'desc'    => sprintf(
					/* translators: %s: WeRePack website link */
						__( 'Position of the Consent Box in the checkout. You can see a visual overview %s.', 'repack-for-woocommerce' ),
						'<a target="_blank" href="https://www.businessbloomer.com/woocommerce-visual-hook-guide-checkout-page/">here</a>'
					),
					'default' => apply_filters( 'repack_checkout_consent_position', 'woocommerce_after_order_notes' ),
					'options' => array(
						'woocommerce_checkout_before_customer_details' => __( 'Before Customer Details', 'repack-for-woocommerce' ),
						'woocommerce_before_checkout_billing_form' => __( 'Before Billing Form', 'repack-for-woocommerce' ),
						'woocommerce_after_checkout_billing_form' => __( 'After Billing Form', 'repack-for-woocommerce' ),
						'woocommerce_before_checkout_shipping_form' => __( 'Before Shipping Form', 'repack-for-woocommerce' ),
						'woocommerce_after_checkout_shipping_form' => __( 'After Shipping Form', 'repack-for-woocommerce' ),
						'woocommerce_before_order_notes' => __( 'Before Order Notes', 'repack-for-woocommerce' ),
						'woocommerce_after_order_notes'  => __( 'After Order Notes', 'repack-for-woocommerce' ),
						'woocommerce_checkout_after_customer_details' => __( 'After Customer Details', 'repack-for-woocommerce' ),
					),
				),

				// Consent Checkbox Firework Effect
				array(
					'id'      => 'repack_consent_field_firework',
					'type'    => 'checkbox',
					'name'    => __( 'Checkbox Animation', 'repack-for-woocommerce' ),
					'desc'    => __( 'Activate decent firework effect for active checkbox.', 'repack-for-woocommerce' ),
					'default' => wc_bool_to_string( apply_filters( 'repack_consent_field_firework', null ) ),
				),

				// Consent Label
				array(
					'id'          => 'repack_consent_field_label',
					'type'        => 'text',
					'name'        => __( 'Consent Label', 'repack-for-woocommerce' ),
					'desc_tip'    => __( 'Label of the consent checkbox your users will see in the checkout.', 'repack-for-woocommerce' ),
					'placeholder' => wp_strip_all_tags( apply_filters( 'repack_consent_field_label', null ) ),
				),

				// Consent Description
				array(
					'id'          => 'repack_consent_field_description',
					'type'        => 'textarea',
					'name'        => __( 'Consent Description', 'repack-for-woocommerce' ),
					'desc_tip'    => __( 'Description text of the consent checkbox your users will see in the checkout.', 'repack-for-woocommerce' ),
					'placeholder' => wp_strip_all_tags( apply_filters( 'repack_consent_field_description', null ) ),
				),

				/**
				 * WeRePack Coupon
				 */
				array(
					'id'    => 'repack_coupons',
					'type'  => 'repack_section',
					'title' => __( 'WeRePack Coupon', 'repack-for-woocommerce' ),
					'desc'  => __( 'You can optionally add a coupon that is applied to orders with WeRePack consent. This allows you for example to share your packaging savings with your customer.', 'repack-for-woocommerce' ),
				),

				// Coupon Name
				array(
					'id'          => 'repack_coupon_name',
					'type'        => 'text',
					'name'        => __( 'Coupon Name', 'repack-for-woocommerce' ),
					'desc_tip'    => __( 'Name of the coupon to apply to orders with consent.', 'repack-for-woocommerce' ),
					'placeholder' => apply_filters( 'repack_coupon_name', null ),
				),

				array(
					'id'          => 'repack_coupon_applied_notice_text',
					'type'        => 'textarea',
					'name'        => __( 'Coupon Applied Notice', 'repack-for-woocommerce' ),
					'desc_tip'    => __( 'Notification text after consent is given and coupon is applied.', 'repack-for-woocommerce' ),
					'placeholder' => wp_strip_all_tags( apply_filters( 'repack_coupon_applied_notice_text', null ) ),
				),

				array(
					'id'          => 'repack_coupon_removed_notice_text',
					'type'        => 'textarea',
					'name'        => __( 'Coupon Removed Notice', 'repack-for-woocommerce' ),
					'desc_tip'    => __( 'Notification text after consent is removed and coupon is revoked.', 'repack-for-woocommerce' ),
					'placeholder' => wp_strip_all_tags( apply_filters( 'repack_coupon_removed_notice_text', null ) ),
				),

				/**
				 * WeRePack Community
				 */
				array(
					'id'    => 'repack_community',
					'type'  => 'repack_section',
					'title' => __( 'WeRePack Community', 'repack-for-woocommerce' ),
					'desc'  => __( 'We want to win you as a supporter and measure our joint success. To do this, you can share certain data with us in order to be listed in the supporter directory on WeRePack.org. This way, we can measure our positive impact on e-commerce and give you a platform that recognises your commitment to the environment. <br><strong>No sensitive user data is transferred.</strong>', 'repack-for-woocommerce' ),
				),

				array(
					'id'    => 'repack_community_consent',
					'type'  => 'repack_consent',
					'title' => __( 'Community Status', 'repack-for-woocommerce' ),
				),

				// Telemetry Data
				array(
					'id'   => 'repack_community',
					'type' => 'repack_telemetry_data',
				),

				// Manually Trigger Sync
				array(
					'id'   => 'repack_community',
					'type' => 'repack_telemetry_sync_data',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'werepack',
				),
			);
		} else {
			return $settings;
		}
	},
	10,
	2
);

add_action(
	'woocommerce_admin_field_repack_section',
	function ( $value ) {
		echo '</table>' . "\n\n";
		if ( ! empty( $value['title'] ) ) {
			echo '<h3>' . esc_html( $value['title'] ) . '</h3>';
		}
		if ( ! empty( $value['desc'] ) ) {
			echo '<div id="' . esc_attr( sanitize_title( $value['id'] ) ) . '-description">';
			echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
			echo '</div>';
		}
		echo '<table class="form-table">' . "\n\n";
	}
);

add_action(
	'woocommerce_admin_field_repack_telemetry_data',
	function ( $value ) {
		$template_loader = new Repack_Template_Loader();
		$data            = ( new Repack_Telemetry() )->get_data();

		$template_loader
		->set_template_data( $data )
		->get_template_part( 'telemetry-data' );
	}
);

add_action(
	'woocommerce_admin_field_repack_consent',
	function ( $value ) {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<?php if ( ! get_option( 'repack_telemetry_optin' ) ) { ?>
					<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'repack-action', 'telemetry', remove_query_arg( 'repack-consent-notice' ) ) ) ); ?>" class="button button-secondary consent"><?php esc_html_e( 'Yes, I want to join WeRePack.org', 'repack-for-woocommerce' ); ?></a>
				<?php } else { ?>
					<div class="row-actions visible">
						<span class="activate">
							<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'repack-action', 'sync', remove_query_arg( 'repack-consent-notice' ) ) ) ); ?>" class="edit"><?php esc_html_e( 'Sync now with WeRePack.org', 'repack-for-woocommerce' ); ?></a>
							|
						</span>
						<span class="delete">
							<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'repack-action', 'revoke-telemetry', remove_query_arg( 'repack-consent-notice' ) ) ) ); ?>" class="delete"><?php esc_html_e( 'Revoke Consent', 'repack-for-woocommerce' ); ?></a>
						</span>
					</div>
				<?php } ?>
			</td>
		</tr>
		<?php
	}
);
