<div class="col-sm-12">
	<h2>Add Role</h2>

	<form class="form-horizontal" action="<?= URL::Site('admin/settings/add_role') ?>" method="post">

		<?php //This is needed to display any error that might be loaded into the messages queue ?>
		<?= (isset($alert)) ? $alert : '' ?>
		<?php
			if(isset($alert)){
			?>
				<script>
					remove_popbox();
				</script>
			<?php
			}
		?>

		<fieldset>
			<legend>Role Information</legend>

			<div class="form-group">
				<label for="role" class="col-sm-2 control-label">Name</label>

				<div class="col-sm-7">
					<input type="text" id="role" name="role" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="access_type" class="col-sm-2 control-label">Access Type</label>

				<div class="col-sm-7">
					<select class="form-control popinit" data-original-title="Type" rel="popover" data-content="This is the type of access you will have on the CMS." name="access_type" id="access_type">
						<option selected="selected" value="Both">Both</option>
						<option value="Front end">Front end</option>
						<option value="Back end">Back end</option>
					</select>
				</div>
			</div>


			<div class="form-group">
				<label for="description" class="col-sm-2 control-label">Description</label>

				<div class="col-sm-7">
					<textarea rows="3" id="description" name="description"
							  class="form-control"></textarea>
				</div>
			</div>

			<div class="form-actions">
				<button class="btn btn-primary" type="submit">Add Role</button>
			</div>
		</fieldset>

	</form>

</div>
