<?=(isset($alert)) ? $alert : '';?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<form class="form-horizontal" method="post">
	<?php if (is_array($forms)): ?>
		<?php foreach ($forms as $group => $fieldsets): ?>
			<a id="<?php echo $group; ?>"></a>
			<fieldset>
				<legend><?php echo $group; ?></legend>
				<?php if (is_array($fieldsets)): ?>
					<?php foreach ($fieldsets as $fieldset): ?>
						<?= $fieldset; ?>
					<?php endforeach ?>
				<?php endif ?>

			</fieldset>

		<?php endforeach ?>
	<?php endif ?>

	<a id="unavailable"></a>
	<fieldset>
		<legend>Out of office</legend>
		<div class="form-group">
			<label for="is_unavailable" class="col-sm-2 control-label">Unavailable</label>
			<div class="col-sm-4"><input type="checkbox" id="is_unavailable" name="is_unavailable" value="1" <?=@$unavailability != null ? 'checked="checked"' : ''?> /></div>
		</div>
		<div class="form-group">
			<label for="unavailable_from_date" class="col-sm-2 control-label">From Date</label>
			<div class="col-sm-4"><input type="text" class="form-control datetimepicker" id="unavailable_from_date" name="unavailable_from_date" value="<?=@$unavailability['from_date'] ? str_replace('-', '/', date::ymdh_to_dmyh($unavailability['from_date'])) : ''?>" placeholder="Now" <?=@$unavailability == null ? 'disabled="disabled"' : ''?> /></div>
		</div>
		<div class="form-group">
			<label for="unavailable_to_date" class="col-sm-2 control-label">To Date</label>
			<div class="col-sm-4"><input type="text" class="form-control datetimepicker" id="unavailable_to_date" name="unavailable_to_date" value="<?=@$unavailability['to_date'] ? str_replace('-', '/', date::ymdh_to_dmyh($unavailability['to_date'])) : ''?>" placeholder="Forever" <?=@$unavailability == null ? 'disabled="disabled"' : ''?> /></div>
		</div>
		<div class="form-group">
			<label for="unavailable_auto_reply" class="col-sm-2 control-label">Auto Reply</label>
			<div class="col-sm-4"><input type="checkbox" id="unavailable_auto_reply" name="unavailable_auto_reply" value="1" <?=@$unavailability['auto_reply'] == 1 ? 'checked="checked"' : ''?> <?=@$unavailability == null ? 'disabled="disabled"' : ''?> /></div>
		</div>
		<div class="form-group">
			<label for="unavailable_reply_message" class="col-sm-2 control-label">Message</label>
			<div class="col-sm-4"><textarea type="text" class="form-control" id="unavailable_reply_message" name="unavailable_reply_message" <?=@$unavailability == null ? 'disabled="disabled"' : ''?>><?=@$unavailability['reply_message']?></textarea></div>
		</div>
	</fieldset>

	<div class="form-actions">
		<button class="btn btn-primary" type="submit" name="save">Save changes</button>
		<button class="btn">Cancel</button>
	</div>
</form>

<script>
$("#is_unavailable").on("change", function(){
	if (this.checked) {
		$("#unavailable_from_date, #unavailable_to_date, #unavailable_auto_reply, #unavailable_reply_message").prop("disabled", false);
	} else {
		$("#unavailable_from_date, #unavailable_to_date, #unavailable_auto_reply, #unavailable_reply_message").prop("disabled", true);
	}
});

$("#unavailable_from_date, #unavailable_to_date").datetimepicker({
    datepicker : true,
    format: 'd/m/Y H:i',
    formatTime: 'H:i',
    step: 15
});

</script>
