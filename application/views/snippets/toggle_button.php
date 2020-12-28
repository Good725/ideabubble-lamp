<div<?= empty($id) ? '' : ' id="'.$id.'"' ?>>
	<label class="sr-only"<?= empty($on_id) ? '' : ' for="'.$on_id.'"' ?>><?= $label ?></label>
	<input type="hidden"
		   <?= empty($off_id) ? '' : 'id="'.$off_id.'"' ?>
		   name="<?= $name ?>"
		   value="<?= $off_value ?>"
		/><?php // If the checkbox is unticked, this value will get sent to the server  ?>
	<input type="checkbox"
		   <?= empty($on_id) ? '' : 'id="'.$on_id.'"' ?>
		   name="<?= $name ?>"
		   value="<?= $on_value ?>"
		   <?= ($checked) ? 'checked="checked"' : '' ?>
		   data-toggle="toggle"
		   data-onstyle="success"
		   data-on="<?= $label ?>"
		   data-off="<?= $off_label ?>"
		/>
</div>