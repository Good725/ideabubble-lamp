<?php
$form_data        = Kohana::sanitize($_GET);
$paypal_enabled   = (Settings::instance()->get('enable_paypal') == 1 AND Settings::instance()->get('paypal_email')    != '');
$realex_enabled   = (Settings::instance()->get('enable_realex') == 1 AND Settings::instance()->get('realex_username') != '');
$paypal_test_mode = (Settings::instance()->get('paypal_test_mode') == 1);
?>
<?php if (isset($form_data['event_id'])): ?>
	<?php
    $schedule = Model_Schedules::get_one_for_details($form_data['schedule_id']);
    $event = Model_Schedules::get_event_details($form_data['event_id']);
    $booking_item = $event ? $event : $schedule;

	$subtotal = $booking_item['fee_amount'];
	$discount = 0;
	$total = $subtotal;
	$discounts = Model_CourseBookings::get_available_discounts(null, array(array('id' => $booking_item['id'], 'fee' => $schedule['fee_amount'], 'discount' => 0, 'prepay' => 1)));
	if (isset($discounts[0])) {
		$discount = $discounts[0]['discount'];
		$total -= $discount;
	}
	?>

	<?php if ( ! is_null($booking_item)): ?>
		<table class="info_table">
			<caption>Course</caption>
			<thead>
				<tr>
					<th scope="col">Title</th>
					<th scope="col">Location</th>
					<th scope="col">Time</th>
					<th scope="col">Price</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?= $booking_item['course'] ?></td>
					<td><?= $booking_item['location'] ?></td>
					<td><?= date('H:i, D j F Y', strtotime($booking_item['datetime_start'])) ?> &ndash;<br /><?= date('H:i, D j F Y', strtotime($booking_item['datetime_end'])) ?></td>
					<td>&euro;<?= number_format($booking_item['fee_amount'],2) ?></td>
				</tr>
			</tbody>
			<tfoot>
			<?php if ($discount > 0) { ?>
				<tr>
					<th colspan="3" scope="row" style="text-align:right;">Subtotal:</th>
					<td><span class="total_price">&euro;<?= number_format($subtotal,2) ?></span></td>
				</tr>
				<tr>
					<th colspan="3" scope="row" style="text-align:right;">Discount:</th>
					<td><span class="total_price">&euro;<?= number_format($discount,2) ?></span></td>
				</tr>
			<?php } ?>
				<tr>
					<th colspan="3" scope="row" style="text-align:right;">Total:</th>
					<td><span class="total_price">&euro;<?= number_format($total,2) ?></span></td>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>

<?php endif; ?>

<form class="form-horizontal checkout-form" method="post" id="booking_form">
	<input type="hidden" name="subject"       value="New booking">
	<input type="hidden" name="business_name" value="">
	<input type="hidden" name="redirect"      value="payment.html">
	<input type="hidden" name="event"         value="post_contactForm">
	<input type="hidden" name="trigger"       value="booking2">
	<input type="hidden" name="event_id"      value="<?= $event['id'] ?>">
	<input type="hidden" name="price"         value="<?= $booking_item['fee_amount'] ?>">
	<input type="hidden" name="schedule_id"   value="<?= $schedule['id'] ?>" />
	<input type="hidden" name="schedule"      value="<?= $booking_item['schedule'] ?>" />
	<input type="hidden" name="training"      value="<?= $booking_item['schedule'] ?>" />

	<input type="hidden" name="title"         value="Course Booking" />
	<input type="hidden" name="subtotal"      value="<?= $subtotal ?>" />
	<input type="hidden" name="discount"      value="<?= $discount ?>" />
	<input type="hidden" name="amount"        value="<?= $total ?>" />
	<input type="hidden" name="ids"           value="<?= $booking_item['id'] ?>" />
	<input type="hidden" name="custom"        value="" />

	<input type="hidden" name="return_url"    value="<?= Model_Payments::get_thank_you_page(['full_url' => false]) ?>" />
	<input type="hidden" name="cancel_url"    value="<?=URL::site()?>" />

	<?php if ($paypal_enabled AND $realex_enabled): ?>
		<fieldset class="payment_select" id="payment_select">
			<legend>Select your Payment Method</legend>
			<button type="button" data-method="cc"     id="payment_method_cc"     class="payment_method payment_method_cc selected">Credit Card</button>
			<button type="button" data-method="paypal" id="payment_method_paypal" class="payment_method payment_method_paypal">PayPal</button>
		</fieldset>
	<?php endif; ?>

	<fieldset class="contact_details_fieldset">
		<legend>Name &amp; Address</legend>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_first_name">Forename</label>

			<div class="form-controls">
				<input type="text" class="validate[required]" id="booking_form_first_name" name="student_first_name" />
			</div>
		</div>
		
		<div class="form-control-group">
			<label class="form-label" for="booking_form_last_name">Surname</label>

			<div class="form-controls">
				<input type="text" class="validate[required]" id="booking_form_last_name" name="student_last_name" />
			</div>
		</div>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_address1">Address Line 1</label>

			<div class="form-controls">
				<input type="text" id="booking_form_address1" name="student_address1" />
			</div>
		</div>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_address2">Address Line 2</label>

			<div class="form-controls">
				<input type="text" id="booking_form_address2" name="student_address2" />
			</div>
		</div>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_town">Town / City</label>

			<div class="form-controls">
				<input type="text" id="booking_form_town" name="town" />
			</div>
		</div>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_county">County</label>

			<div class="form-controls">
				<select id="booking_form_county" name="county_id">
					<option value="">-- Please Select --</option>
					<?= Model_Cities::get_all_counties_html_options() ?>
				</select>
			</div>
		</div>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_country">Country</label>

			<div class="form-controls">
				<input type="text" id="booking_form_country" name="country" />
			</div>
		</div>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_email">Email</label>

			<div class="form-controls">
				<input type="text" class="validate[required]" id="booking_form_email" name="student_email" />
			</div>
		</div>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_phone">Phone</label>

			<div class="form-controls">
				<input type="text" id="booking_form_phone" name="student_phone" />
			</div>
		</div>

		<div class="form-control-group">
			<label class="form-label" for="booking_form_comments">Comments</label>

			<div class="form-controls">
				<textarea id="booking_form_comments" name="comments"></textarea>
			</div>
		</div>
	</fieldset>

	<?php if ($realex_enabled): ?>
		<fieldset class="credit_card_fieldset" id="credit_card_details">
			<legend>Credit Card Payment Details</legend>

			<div class="form-control-group">
				<label class="form-label" for="booking_form_ccName">Name on card</label>

				<div class="form-controls">
					<input type="text" class="validate[required]" id="booking_form_ccName" name="ccName" />
				</div>
			</div>

			<div class="form-control-group">
				<label class="form-label" for="booking_form_ccType">Card Type</label>

				<div class="form-controls">
					<select class="validate[required]" id="booking_form_ccType" name="ccType">
						<option value="">-- Please Select --</option>
						<option value="visa">Visa</option>
						<option value="mc">MasterCard</option>
					</select>
				</div>
			</div>

			<div class="form-control-group">
				<label class="form-label" for="booking_form_ccNum">Card No.</label>

				<div class="form-controls">
					<input type="text" class="validate[required]" id="booking_form_ccNum" name="ccNum" autocomplete="off" />
				</div>
			</div>

			<div class="form-control-group">
				<div class="form-label">Expiry</div>

				<div class="form-controls">
					<label class="accessible-hide" for="booking_form_ccExpMM">Month</label>
					<select class="validate[required]" id="booking_form_ccExpMM" name="ccExpMM">
						<option value="">-- Month --</option>
						<option value="01">January</option>
						<option value="02">February</option>
						<option value="03">March</option>
						<option value="04">April</option>
						<option value="05">May</option>
						<option value="06">June</option>
						<option value="07">July</option>
						<option value="08">August</option>
						<option value="09">September</option>
						<option value="10">October</option>
						<option value="11">November</option>
						<option value="12">December</option>
					</select>

					<label class="accessible-hide" for="booking_form_ccExpYY">Year</label>
					<select class="validate[required,funcCall[validate_exp_date]]" id="booking_form_ccExpYY" name="ccExpYY">
						<option value="">-- Year --</option>
						<?php for ($i = 0; $i < 15; $i++): ?>
							<option value="<?= date('y') + $i ?>"><?= date('Y') + $i ?></option>
						<?php endfor; ?>
					</select>
				</div>
			</div>

			<div class="form-control-group">
				<label class="form-label" for="booking_form_ccv">CCV No.</label>

				<div class="form-controls">
					<input type="text" class="validate[required]" id="booking_form_ccv" name="ccv" autocomplete="off" />
				</div>
			</div>
		</fieldset>
	<?php endif; ?>

	<fieldset class="checkout_foot_fieldset">
		<p>We use a secure certificate for all our payments.</p>

		<label>
			<label class="cb-wrapper"><input type="checkbox" name="signupCheckbox" /><span></span></label> I would like to sign up to the newsletter.<br />
		</label>

		<label>
			<label class="cb-wrapper"><input type="checkbox" class="validate[required]" id="booking_form_accept" name="accept" /><span></span></label> I accept the terms and conditions.
		</label>

		<div>
			<?php if ($realex_enabled): ?>
				<button type="button" class="booking_button submit-checkout" id="submit-checkout-cc">Pay Now</button>
			<?php endif; ?>
			<?php if ($paypal_enabled): ?>
				<button type="button" class="booking_button submit-checkout submit-checkout-paypal" id="submit-checkout-paypal"<?= ($realex_enabled) ? ' style="display:none;"' : '' ?>>Pay with Paypal</button>
			<?php endif; ?>
		</div>
	</fieldset>

</form>


<div class="blackout" id="payment_process_blackout">
	<div class="payment_processing" id="payment_processing">Please wait while your payment is processed.</div>

	<div class="checkout-modal" id="payment_failed_modal">
		<div class="checkout-modal-head">
			<h4>Payment Unsuccessful</h4>
		</div>
		<div class="checkout-modal-body">
			<p>Error Processing Payment.</p>
			<p>Please review your credit card details.</p>
		</div>
		<div class="checkout-modal-foot">
			<button class="plain_button checkout-modal-dismiss" id="payment_failed_modal_ok">OK</button>
		</div>

	</div>
</div>
<script>
	$('.payment_method').on('click', function()
	{
		$('.payment_method').removeClass('selected');
		$(this).addClass('selected');

		var method         = this.getAttribute('data-method');
		var $cc_details    = $('#credit_card_details').hide();
		var $cc_submit     = $('#submit-checkout-cc').hide();
		var $paypal_submit = $('#submit-checkout-paypal').hide();

		if (method == 'cc')
		{
			$cc_details.show();
			$cc_submit.show();
		}
		else
		{
			$paypal_submit.show();
		}
	});

	$("#submit-checkout-cc").click(function (ev)
	{
		ev.preventDefault();
		var $button       = $(this).prop('disabled', true);
		var $booking_form = $('#booking_form');
		var $blackout     = $('#payment_process_blackout');

		if ($booking_form.validationEngine('validate'))
		{
			if ( ! $blackout.is('visible'))
			{
				$blackout.show(function()
				{
					$('#payment_processing').show();
					var data = $booking_form.serialize();
					$.post('/frontend/courses/ajax_book_and_pay_with_cart/', data, function (data)
					{
						if (data.status == 'success')
						{
							window.location = data.redirect;
						} else {
							$('#payment_failed_modal').show(function()
							{
								document.getElementById('payment_processing').style.display = 'none';
								document.getElementById('submit-checkout-cc').removeAttribute('disabled');
								document.getElementById('payment_failed_modal_ok').focus();
							});
						}
					}, 'json').fail(function()
						{
							$('#payment_failed_modal').show(function()
							{
								document.getElementById('payment_processing').style.display = 'none';
								document.getElementById('submit-checkout-cc').removeAttribute('disabled');
								document.getElementById('payment_failed_modal_ok').focus();
							});
						});

				});
			}
		}
		else
		{
			$blackout.hide();
			$button.prop('disabled', false);
			setTimeout(function()
			{
				$('.formError').fadeOut(function()
				{
					$('.formError').remove();
				})
			}, 5000);
		}
	});
	$('.checkout-modal-dismiss').on('click', function()
	{
		$(this).parents('.checkout-modal').hide();
		$('#payment_process_blackout').hide();
	});

	function validate_exp_date()
	{
		var year  = document.getElementById('booking_form_ccExpYY').value;
		var month = document.getElementById('booking_form_ccExpMM').value;
		var current_year  = new Date().getYear()  % 100;
		var current_month = new Date().getMonth() + 1;

		if ( ! year || ! month)
		{
			// already covered
		}
		else if (year < current_year || (year == current_year && month <= current_month))
		{
			return '* Expiry date has passed';
		}
	}

	$('#submit-checkout-paypal').click(function(ev)
	{
		ev.preventDefault();
		var $booking_form = $('#booking_form');
		var data = $booking_form.serialize();

		if ($booking_form.validationEngine('validate'))
		{
			// $(this).prop('disabled', true);
			$.post('/frontend/courses/ajax_book_and_pay_with_cart_paypal', data, function(data)
			{
				data = JSON.parse(data);
				if (data.status == 0)
				{
					var form = '<form id="paypal_form" method="post" action="https://www.<?= $paypal_test_mode ? 'sandbox.' : '' ?>paypal.com/cgi-bin/webscr">';
					for (var property in data.data)
					{
						form += '<input type="hidden" name="'+property+'" value="'+data.data[property]+'" />';
					}
					form += '</form>';
					$('body').append(form);

					$('#paypal_form').submit();
				}
			});
		}


	});
</script>
