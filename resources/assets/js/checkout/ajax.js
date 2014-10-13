$(function() {
	// Submit the selection form when input values are changed
	$('form#checkout-selection-form input').on('change.ms_checkout', function() {
		var self = $(this);

		if (0 == self.val()) {
			self.closest('tr').find('td.remove a').click();
		} else {
			self.parents('form').submit();
		}
	});

	$('form#checkout-selection-form').on('submit.ms_checkout', function() {
		var self = $(this);

		$.ajax({
			url     : self.attr('action'),
			data    : self.serialize(),
			method  : 'POST',
			dataType: 'html',
			success : function(data) {
				checkoutUpdateTotals(data);
			},
		});

		return false;
	});

	$('form#checkout-selection-form td.remove a').on('click.ms_checkout', function() {
		var self = $(this),
			row  = self.closest('tr');

		$.get(self.attr('href'), function(data) {
			row.trigger('remove.ms_basket');

			if ($('form#checkout-selection-form table tbody tr').length == 0) {
				window.location.href = '/checkout/empty';

				return false;
			}

			if (row.is('visible')) {
				row.fadeOut();
			}

			checkoutUpdateTotals(data);
		});

		return false;
	});

	$('form#delivery-method-form select').on('change', function() {
		var form = $('form#delivery-method-form'),
			submitBtn = form.children('button[type=submit]');

		submitBtn.toggleClass('error', false);
		submitBtn.toggleClass('loading', true);

		$.ajax({
			url     : form.attr('action'),
			data    : form.serialize(),
			method  : 'POST',
			dataType: 'html',
			success : function(data) {
				submitBtn.toggleClass('loading', false);
			},
			error   : function(data) {
				submitBtn.toggleClass('loading', false);
				submitBtn.toggleClass('error', true);
			},
		});
	});
});

function checkoutUpdateTotals(data)
{
	$('[data-checkout-live-update]').each(function() {
		$(this).html($($(this).getPath(), data).html()).trigger('change.ms_basket');
	});
}