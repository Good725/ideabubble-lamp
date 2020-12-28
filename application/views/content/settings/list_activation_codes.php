<div class="modal fade" id="add_codes_modal">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3>Add access codes?</h3>
			</div>
			<div class="modal-body">
				<p>Warning: This cannot be undone.</p>
				<h1>Import customer ID codes file</h1>

				<?=Form::open('', array('enctype' => 'multipart/form-data', 'method'=>'POST', 'name'=>'formdata', 'class' => 'form-horizontal')); ?>
				<div class="form-group">
					<?=Form::label('file', '', array('class'=>'col-sm-2 control-label')); ?>
					<div class="col-sm-7">
						<?=Form::file('file', array('required')); ?>
					</div>
				</div>
				<div class="form-group">
					<?=Form::label('group_id', '', array('class'=>'col-sm-2 control-label')); ?>
					<div class="col-sm-7">
						<?=Form::select('group_id', Arr::merge(array('empty'=>'- Choose group -'), $groups), '', array('required', 'class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?=Form::label('role_id', '', array('class'=>'col-sm-2 control-label')); ?>
					<div class="col-sm-7">
						<?=Form::select('role_id', Arr::merge(array('empty'=>'- Choose role -'), $roles), '', array('required', 'class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-actions">
					Import can take few minutes, depends on file size.
				</div>

				<?=Form::close(); ?>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" id="import">Import</a>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>

<a class="btn pull-right" data-toggle="modal" href="#add_codes_modal">Add new activation codes</a>


<p>&nbsp;</p>
<h2 id="activesecurityheading">Activation codes</h2>
<p>&nbsp;</p>

<?php //Debug::vars($_codes); ?>

<?php
    //This is needed to display any error that might be loaded into the messages queue
    if (isset($alert)) echo $alert;
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

<table class="table table-striped" id="activation_codes_list">
    <thead>
    <tr>
        <th>Code</th>
        <th>Group</th>
        <th>Role</th>
        <th>Date added</th>
        <th>Published</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>  
    </thead>
    <tbody>  

    </tbody>
    </table>
