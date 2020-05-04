(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {
		// Add celebrate animation container to checkbox
		const $span = '<span class="celebrate-repack"></span>';
		$('input[name="shipping_repack"]').parent().append($span);

		// Update cart if checkbox changes
		$( "form.checkout" ).on( "change", "input#shipping_repack", function() {
			let data = {
				action: 'update_order_review',
				security: wc_checkout_params.update_order_review_nonce,
				post_data: $( 'form.checkout' ).serialize()
			};


			$.post( repack.ajax_url, data, function()
			{

				$( 'body' ).trigger( 'update_checkout' );

			});
		});
	});


})( jQuery );
