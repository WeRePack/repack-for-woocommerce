<?php
/**
 * Template with WeRePack Stats Overview.
 *
 * @package   WeRePack
 * @since     1.2.0
 * @copyright Copyright (c) 2021, WeRePack.org
 * @license   GPL-2.0+
 *
 */
?>
<table class="widefat data-to-send">
	<thead>
	<tr>
		<th colspan="2"><?php esc_html_e( 'Data that will be sent', 'repack-for-woocommerce' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td style="min-width: 200px;"><?php esc_html_e( 'Website URL', 'repack-for-woocommerce' ); ?></td>
		<td><code><?php echo esc_html( $data->site_url ); ?></code></td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'Website Language', 'repack-for-woocommerce' ); ?></td>
		<td><code><?php echo esc_html( $data->site_lang ); ?></code></td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'WeRePack Start', 'repack-for-woocommerce' ); ?></td>
		<td><code><?php echo esc_html( wp_date( get_option( 'date_format' ), $data->repack_start ) ); ?></code></td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'WeRePack Consents', 'repack-for-woocommerce' ); ?></td>
		<td><code><?php echo esc_html( ! empty( $data->repack_counter ) ? $data->repack_counter : __( 'None yet', 'repack-for-woocommerce' ) ); ?></code></td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'WeRePack Consent Ratio', 'repack-for-woocommerce' ); ?></td>
		<td><code><?php echo esc_html( $data->repack_ratio . '%' ); ?></code></td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'WeRePack Coupon', 'repack-for-woocommerce' ); ?></td>
		<?php /* translators: %s: Name of coupon. */ ?>
		<td><code><?php echo esc_html( $data->repack_coupon ? sprintf( __( 'Available (%s)', 'repack-for-woocommerce' ), $data->repack_coupon_code ) : __( 'Not available', 'repack-for-woocommerce' ) ); ?></code></td>
	</tr>
	</tbody>
	<tfoot>
	<tr>
		<th colspan="2">
			<?php
			printf(
			/* translators: %1$s: URL to the server plugin code. %2$s: URL to the stats page. */
				__( 'We believe in complete transparency. You can see the code used on our server <a href="%1$s" target="_blank" rel="nofollow">here</a>, and the results of the statistics we\'re gathering on <a href="%2$s" target="_blank" rel="nofollow">this page</a>.', 'repack-for-woocommerce' ), // phpcs:ignore WordPress.Security.EscapeOutput
				'https://github.com/werepack/repack-telemetry-server/',
				'https://werepack.org/repack-telemetry-statistics/'
			);
			?>
		</th>
	</tr>
	</tfoot>
</table>
