(function ( $ ) {
	'use strict';

	$(
		function () {
			// Add celebrate animation container to checkbox
			if (repack.options.celebrate) {
				const $span = '<span class="celebrate-repack"></span>';
				$('input[name="shipping_repack"]').parent().append($span);
			}

			//  Initially set coupon
			apply_repack_coupon_code();

			// Update cart if checkbox changes
			$("form.checkout").on(
				"change",
				"input#shipping_repack",
				function () {
					apply_repack_coupon_code();
				}
			);
		}
	);

	function apply_repack_coupon_code()
	{
		let data = {
			action: 'update_order_review',
			security: wc_checkout_params.update_order_review_nonce,
			nonce_token: repack.security,
			post_data: $('form.checkout').serialize()
		};

		$.post(
			repack.ajax_url,
			data,
			function () {
				$('body').trigger('update_checkout');
			}
		);
	}

})(jQuery);
