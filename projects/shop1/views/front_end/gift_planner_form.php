<?php
$date_format = Settings::instance()->get('date_format');
$date_format = ($date_format == '') ? 'd/m/Y' : $date_format; ?>
<form action="/frontend/formprocessor" method="post" class="form-horizontal" id="gift_planner_form">
	<input type="hidden" name="subject"        value="Gift Planner" />
	<input type="hidden" name="business_name"  value="<?= Settings::instance()->get('company_title'); ?>" />
	<input type="hidden" name="redirect"       value="thank-you.html" />
	<input type="hidden" name="event"          value="gift_planner" />
	<input type="hidden" name="trigger"        value="custom_form" />
	<input type="hidden" name="email_template" value="gift_planner" />

	<div class="control-group">
		<div class="control-label label-required">
			<label for="gift_planner_name">Name</label>
		</div>
		<div class="form-controls">
			<input type="text" class="validate[required]" id="gift_planner_name" name="name" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label label-required">
			<label for="gift_planner_address">Address</label>
		</div>
		<div class="form-controls">
			<textarea class="validate[required]" id="gift_planner_address" name="address"></textarea>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label label-required">
			<label for="gift_planner_mobile">Mobile</label>
		</div>
		<div class="form-controls">
			<input type="text" class="validate[required]" id="gift_planner_mobile" name="mobile" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="gift_planner_email">Email</label>
		</div>
		<div class="form-controls">
			<input type="text" id="gift_planner_email" name="email" />
		</div>
	</div>

	<div class="control-group">
		<?php $current_timestamp = strtotime(date($date_format)); ?>
		<div class="control-label">Events</div>
		<div class="form-controls">

			<div class="event_picker">
				<label>
					<input type="checkbox" name="events[]" value="valentines" /> Valentines
				</label>
				<label>
					<?php $year = (strtotime(date('Y').'-02-14') < $current_timestamp) ? date('Y')+1 : date('Y'); ?>
					<input type="text" name="valentines_date" class="datepicker_input" value="<?= date($date_format, strtotime($year.'-02-14')) ?>">
				</label>
			</div>

			<div class="event_picker">
				<label>
					<input type="checkbox" name="events[]" value="mothers_day" /> Mother&apos;s Day
				</label>
				<label>
					<?php $year = (strtotime('-3 week', easter_date(date('Y'))) < $current_timestamp) ? date('Y')+1 : date('Y'); ?>
					<input type="text" name="mothers_day_date" value="<?= date($date_format, strtotime('-3 week', easter_date($year))) ?>" placeholder="Select date..." class="datepicker_input"/>
				</label>
			</div>

			<div class="event_picker">
				<label>
					<input type="checkbox" name="events[]" value="easter" /> Easter
				</label>
				<label>
					<?php $year = (easter_date(date('Y')) < $current_timestamp) ? date('Y')+1 : date('Y'); ?>
					<input type="text" name="easter_date" class="datepicker_input" value="<?= date($date_format, easter_date($year)) ?>" />
				</label>
			</div>

			<div class="event_picker">
				<label>
					<input type="checkbox" name="events[]" value="fathers_day" /> Father&apos;s Day
				</label>
				<label>
					<?php $year = (strtotime('third Sunday of June '.date('Y')) < $current_timestamp) ? date('Y')+1 : date('Y'); ?>
					<input type="text" name="fathers_day_date" value="<?= date($date_format, strtotime('third Sunday of June '.$year)) ?>" class="datepicker_input" />
				</label>
			</div>

			<div class="event_picker">
				<label>
					<input type="checkbox" name="events[]" value="anniversary" /> Anniversary
				</label>
				<label>
					<input type="text" name="anniversary_date" class="datepicker_input" placeholder="Select date...">
				</label>
			</div>

			<div class="event_picker">
				<label>
					<input type="checkbox" name="events[]" value="birthday" /> Birthday
				</label>
				<label>
					<input type="text" name="birthday_date" class="datepicker_input" placeholder="Select date..." />
				</label>
			</div>

			<div class="event_picker">
				<label>
					<input type="checkbox" name="events[]" value="christmas" /> Christmas
				</label>
				<label>
					<?php $year = (strtotime(date('Y').'-12-25') < $current_timestamp) ? date('Y')+1 : date('Y'); ?>
					<input type="text" name="christmas_date" value="<?= date($date_format, strtotime($year.'-12-25')) ?>" class="datepicker_input" placeholder="Date...">
				</label>
			</div>
			Other events. Please specify.
			<div class="event_picker event_picker_custom">
				<label>
					<input type="text" name="custom_events[]" placeholder="Type event name here..." />
					<label>
						<input type="text" name="custom_events_dates[]" class="datepicker_input" placeholder="Date...">
					</label>
				</label>
			</div>

			<div class="event_picker event_picker_custom">
				<label>
					<input type="text" name="custom_events[]" placeholder="Type event name here..." />
					<label>
						<input type="text" name="custom_events_dates[]" class="datepicker_input" placeholder="Date...">
					</label>
				</label>
			</div>

			<div class="event_picker event_picker_custom">
				<label>
					<input type="text" name="custom_events[]" placeholder="Type event name here..." />
					<label>
						<input type="text" name="custom_events_dates[]" class="datepicker_input" placeholder="Date...">
					</label>
				</label>
			</div>
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
			<button type="submit" class="primary_button" id="gift_planner_submit">Submit</button>
		</div>
	</div>
</form>

<script>
	<?php
	switch ($date_format)
	{
		case 'd/m/Y' : $ui_date_format = 'dd/mm/yy'; break;
		case 'm/d/Y' : $ui_date_format = 'mm/dd/yy'; break;
		default      : $ui_date_format = 'yy-mm-dd'; break;
	}
	?>

	$(document).ready(function()
	{
		$('.datepicker_input').datepicker({dateFormat: '<?= $ui_date_format ?>', defaultDate: this.value});
	});


	$('#gift_planner_form').on('submit', function(ev)
	{
		ev.preventDefault();
	});

	$('#gift_planner_submit').on('click', function(ev)
	{
		ev.preventDefault();
		var $form = $('#gift_planner_form');
		if ($form.validationEngine('validate'))
		{
			$form[0].submit();
		}
	});
</script>
