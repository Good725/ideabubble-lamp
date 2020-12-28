<h1>Add User Group</h1>

<div class="col-sm-12">

	<form class="form-horizontal" action="<?php echo URL::Site('admin/settings/manage_roles/') ?>" method="post">

		<?php
		   //This is needed to display any error that might be loaded into the messages queue
		if (isset($alert))
		{
			echo $alert;
		}
		?>
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
			<legend>User Group Information</legend>

			<div class="form-group">
				<label for="name" class="col-sm-2 control-label">Name</label>

				<div class="col-sm-7">
					<input type="text" id="name" name="name" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="description" class="col-sm-2 control-label">Description</label>

				<div class="col-sm-7">
					<textarea rows="3" id="description" name="description"
							  class="form-control"></textarea>
				</div>
			</div>

			<div class="well">
				<button class="btn btn-primary" type="submit">Add User Group</button>
			</div>
		</fieldset>

	</form>

</div>
