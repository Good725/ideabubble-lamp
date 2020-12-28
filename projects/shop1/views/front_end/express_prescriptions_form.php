<?= isset($alert) ? $alert : ''; ?>
<form action="/frontend/formprocessor" method="post" class="form-horizontal" id="express_prescriptions_form">
	<input type="hidden" name="subject"        value="Express Repeat Prescriptions Form" />
	<input type="hidden" name="business_name"  value="<?= Settings::instance()->get('company_title'); ?>" />
	<input type="hidden" name="redirect"       value="thank-you.html" />
	<input type="hidden" name="event"          value="express_repeat_prescriptions" />
	<input type="hidden" name="trigger"        value="custom_form" />
	<input type="hidden" name="email_template" value="express_prescriptions" />

	<div class="control-group">
		<div class="control-label label-required">
			<label for="express_prescription_name">Name</label>
		</div>
		<div class="form-controls">
			<input type="text" class="validate[required]" id="express_prescription_name" name="name" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label label-required">
			<label for="express_prescription_address">Address</label>
		</div>
		<div class="form-controls">
			<textarea class="validate[required]" id="express_prescription_address" name="address"></textarea>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label label-required">
			<label for="express_prescription_mobile">Mobile</label>
		</div>
		<div class="form-controls">
			<input type="text" class="validate[required]" id="express_prescription_mobile" name="mobile" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="express_prescription_email">Email</label>
		</div>
		<div class="form-controls">
			<input type="text" id="express_prescription_email" name="email" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label label-required">
			<label for="express_prescription_pharmacy">Pharmacy</label>
		</div>
		<div class="form-controls">
			<select class="validate[required]" id="express_prescription_pharmacy" name="pharmacy_id">
				<?php $pharmacies = Model_Location::get(NULL, array(array('type','=','Pharmacy'))) ?>
				<option value="">-- Please Select --</option>
				<?php foreach ($pharmacies as $pharmacy): ?>
					<option value="<?= $pharmacy['id'] ?>"><?= $pharmacy['title'] ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="express_prescription_items_needed">Items Needed</label>
		</div>
		<div class="form-controls">
			<select id="express_prescription_items_needed" name="items_needed">
				<option value="">-- Please Select --</option>
				<option value="Repeat all items">Repeat all items</option>
				<option value="Call me to confirm">Call me to confirm</option>
			</select>
		</div>
	</div>

	<div class="control-group">
		<div class="form-controls">
			<?php
			if (Settings::instance()->get('captcha_enabled'))
			{
				require_once ENGINEPATH.'/plugins/formprocessor/development/classes/model/recaptchalib.php';
				echo recaptcha_get_html(Settings::instance()->get('captcha_public_key'));
			}
			?>
		</div>
	</div>

	<div class="control-group">
		<div class="form-controls">
			<button type="submit" class="primary_button" id="express_prescription_submit">Submit</button>
		</div>
	</div>
</form>

<script>
	$('#express_prescriptions_form').on('submit', function(ev)
	{
		ev.preventDefault();
	});

	$('#express_prescription_submit').on('click', function(ev)
	{
		ev.preventDefault();
		var $form = $('#express_prescriptions_form');
		if ($form.validationEngine('validate'))
		{
			$form[0].submit();
		}
	});
</script>