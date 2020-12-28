<?php
$input_prefix = (isset($input_prefix)) ? $input_prefix : 'edit-ratecard-';
?>
<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>is_deal"><?= __('Deal') ?></label>
	<div class="col-sm-2">
		<label>
			<span class="sr-only"><?= __('Deal') ?></span>
			<input type="checkbox" id="<?= $input_prefix ?>is_deal" value="1" <?=@$ratecard['is_deal'] ? 'checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('On') ?>" data-off="<?= __('Off') ?>" />
		</label>
	</div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>starts"><?= __('Start Date') ?></label>
	<div class="col-sm-5">
		<div class="input-group">
			<input type="text" class="form-control datepicker" id="<?= $input_prefix ?>starts" name="starts" value="<?=@$ratecard['starts'] ? date::ymd_to_dmy(@$ratecard['starts']) : ''?>" />
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>ends"><?= __('End Date') ?></label>
	<div class="col-sm-5">
		<div class="input-group">
			<input type="text" class="form-control datepicker" id="<?= $input_prefix ?>ends" name="ends" value="<?=@$ratecard['ends'] ? date::ymd_to_dmy(@$ratecard['ends']) : ''?>" />
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>weekly_price"><?= __('Weekly') ?></label>
	<div class="col-sm-5">
		<div class="input-group">
			<span class="input-group-addon">&euro;</span>
			<input type="text" class="form-control" id="<?= $input_prefix ?>weekly_price" name="weekly_price" value="<?=@$ratecard['weekly_price']?>" />
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>short_stay_price"><?= __('Short Stay') ?></label>
	<div class="col-sm-5">
		<div class="input-group">
			<span class="input-group-addon">&euro;</span>
			<input type="text" class="form-control" id="<?= $input_prefix ?>short_stay_price" name="short_stay_price" value="<?=@$ratecard['short_stay_price']?>" />
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>additional_nights_price"><?= __('Additional Nights') ?></label>
	<div class="col-sm-5">
		<div class="input-group">
			<span class="input-group-addon">&euro;</span>
			<input type="text" class="form-control" id="<?= $input_prefix ?>additional_nights_price" name="additional_nights_price" value="<?=@$ratecard['additional_nights_price']?>" />
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>min_stay"><?= __('Min Stay') ?></label>
	<div class="col-sm-5">
		<div class="input-group">
			<input type="text" class="form-control" id="<?= $input_prefix ?>min_stay" name="min_stay" value="<?=@$ratecard['min_stay']?>" />
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>price_type"><?= __('Price type') ?></label>
	<div class="col-sm-5">
		<select class="form-control" id="<?= $input_prefix ?>pricing" name="pricing">
			<option value=""><?= __('-- Please select --') ?></option>
			<option value="Low" <?=@$ratecard['pricing'] == 'Low' ? 'selected="selected"' : ''?>>Low</option>
			<option value="High" <?=@$ratecard['pricing'] == 'High' ? 'selected="selected"' : ''?>>High</option>
		</select>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label" for="edit-<?= $input_prefix ?>arrival-property_type_id"><?= __('Arrival') ?></label>
	<div class="col-sm-5">
		<select class="form-control" id="<?= $input_prefix ?>arrival" name="arrival">
			<option value=""><?= __('-- Please select --') ?></option>
			<?php
			echo HTML::optionsFromArray(
					array(
							'Any' => __('Any'),
							'Monday' => __('Monday'),
							'Tuesday' => __('Tuesday'),
							'Wednesday' => __('Wednesday'),
							'Thursday' => __('Thursday'),
							'Friday' => __('Friday'),
							'Saturday' => __('Saturday'),
							'Sunday' => __('Sunday')
					),
					@$ratecard['arrival']
			);
			?>
		</select>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label" for="<?= $input_prefix ?>discount_price"><?= __('Discount') ?></label>
	<div class="col-sm-5">
		<div class="input-group">
			<span class="input-group-addon">&euro;</span>
			<input type="text" class="form-control" id="<?= $input_prefix ?>discount_price" name="discount" value="<?=@$ratecard['discount']?>" />
		</div>
	</div>
</div>

