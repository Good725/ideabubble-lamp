<div class="col-sm-12 header">

	<?php
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

	<h1 class="left">
		Manage User Groups
	</h1>

	<div class="right">
		<a class="btn" href="<?php echo URL::Site('admin/settings/edit_usergroup'); ?>">Add Usergroup</a>
	</div>
</div>

<table class='table table-striped'>
	<thead>
	<tr>
		<th>Usergroup</th>
		<th>Description</th>
	</tr>
	</thead>
	<tbody>
    <?php
        //$usergroups = array( 1=> array('name'=>'The Company1', 'description'=>'Description1'), 2=> array('name'=>'The Company2', 'description'=>'Description2'),);
    ?>

	<?php foreach ($usergroups as $id => $value) { ?>
		<tr>
			<td><a href="<?php echo URL::Site('admin/settings/edit_usergroup/' . $id); ?>"><?php echo $value['name']; ?></a></td>
			<td><a href="<?php echo URL::Site('admin/settings/edit_usergroup/' . $id); ?>"><?php echo $value['description']; ?></a></td>
		</tr>

	<? } ?>

	</tbody>
</table>
