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
<form method="post" id="localisation_messages_form" name="localisation_messages_form" action="/admin/settings/localisation_importcsv" enctype="multipart/form-data">
    <div class="form-group">
        <label class="col-sm-2 control-label" for="csv"><?= __('Select CSV File') ?></label>
        <div class="col-sm-10">
            <input type="file" name="csv" id="csv" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="csv"><?= __('Override Translations') ?></label>
        <div class="col-sm-10">
            <input type="checkbox" name="override" value="1" />
        </div>
    </div>
    <div class="form-group">
        <button type="submit" name="import" value="import"><?= __('Import')?></button>
    </div>
</form>

