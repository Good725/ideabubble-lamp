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

<form method="post" class="form-horizontal">
	<a id="Localisation Settings"></a>
	<fieldset>
		<legend>Localisation Settings</legend>
		<div class="form-group">
			<div class="col-sm-3 control-label">Frontend Localisation</div>
			<div class="col-sm-3">
				<div data-toggle="buttons" id="localisation_content_active" class="btn-group" >
					<label class="btn btn-default <?=Settings::instance()->get('localisation_content_active') == 1 ? 'active' : ''?>">
						<input type="radio" name="localisation_content_active" value="1" <?=Settings::instance()->get('localisation_content_active') == 1 ? 'checked="checked"' : ''?> />
						On</label>
					<label class="btn btn-default <?=Settings::instance()->get('localisation_content_active') == 0 ? 'active' : ''?>">
						<input type="radio" name="localisation_content_active" value="0" <?=Settings::instance()->get('localisation_content_active') == 0 ? 'checked="checked"' : ''?> />
						Off</label>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-4">
				<select name="localisation_content_default_language" class="form-control">
					<?=Model_Localisation::get_languages_list_options(Settings::instance()->get('localisation_content_default_language'))?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-3 control-label">Backend Localisation</div>
			<div class="col-sm-4">
				<div data-toggle="buttons" id="localisation_system_active" class="btn-group" >
					<label class="btn btn-default <?=Settings::instance()->get('localisation_system_active') == 1 ? 'active' : ''?>">
						<input type="radio" name="localisation_system_active" value="1" <?=Settings::instance()->get('localisation_system_active') == 1 ? 'checked="checked"' : ''?> />
						On</label>
					<label class="btn btn-default <?=Settings::instance()->get('localisation_system_active') == 0 ? 'active' : ''?>">
						<input type="radio" name="localisation_system_active" value="0" <?=Settings::instance()->get('localisation_system_active') == 0 ? 'checked="checked"' : ''?> />
						Off</label>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-4">
				<select name="localisation_system_default_language" class="form-control">
					<?=Model_Localisation::get_languages_list_options(Settings::instance()->get('localisation_system_default_language'))?>
				</select>
			</div>
		</div>
		<div class="form-group form-action-group">
			<button class="btn btn-primary" type="submit" name="update">Update Settings</button>
		</div>
	</fieldset>
</form>
