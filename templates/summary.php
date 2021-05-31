<?php
/**
 * Template to render WeRePack Statistics.
 *
 * @package   WeRePack
 * @since     1.1.0
 * @copyright Copyright (c) 2021, WeRePack.org
 * @license   GPL-2.0+
 *
 */
?>
<div class="repack-summary">
	<h2 class="repack-summary-title">
		<?php
		printf(
			/* translators: %s is amount of packaging  */
			esc_html__( 'We saved %s so far!', 'repack-for-woocommerce' ),
			wp_kses_post( $saving->packaging )
		);
		?>
	</h2>
	<p>
		<?php
		printf(
			/* translators: Your site name & WeRePack.org */
			esc_html__( '%1$s supports the %2$s initiative for packaging waste reduction since %3$s.', 'repack-for-woocommerce' ),
			esc_html( get_bloginfo( 'name' ) ),
			'<a href="' . esc_url( $werepack->url ) . '" title="' . esc_attr( $werepack->title ) . '" target="_blank">' . esc_html( $werepack->title ) . '</a>',
			esc_html( $werepack->start )
		);
		?>
		<?php
		printf(
			/* translators: %s is amount of packaging */
			esc_html__( 'So far we were able to reuse %s due to the consent of our clients. Based on the resources a common shipping packaging needs for production, this is equivalent to:', 'repack-for-woocommerce' ),
			wp_kses_post( $saving->packaging )
		);
		?>
	</p>
	<div class="repack-summary-items">
		<div class="repack-summary-logo">
			<a href="<?php echo esc_url( $werepack->url ); ?>" target="_blank" title="<?php echo esc_attr( $werepack->title ); ?>">
				<img src="<?php echo esc_url( $werepack->logo ); ?>" alt="<?php echo esc_attr( $werepack->title ); ?>"/>
			</a>
		</div>
		<div class="repack-summary-item"><?php echo wp_kses_post( $saving->packaging ); ?></div>
		<div class="repack-summary-item"><?php echo wp_kses_post( $saving->co2 ); ?></div>
		<div class="repack-summary-item"><?php echo wp_kses_post( $saving->water ); ?></div>
		<div class="repack-summary-item"><?php echo wp_kses_post( $saving->trees ); ?></div>
	</div>
	<p>
		<?php
		printf(
			/* translators: %s is the counter of the next reused package sent */
			esc_html__( 'You want to contribute, too? During checkout, you can choose to also receive an used but flawless packaging. Just agree and your order will be shipped with the %s reused packaging.', 'repack-for-woocommerce' ),
			esc_html( $saving->next )
		);
		?>
	</p>
</div>
