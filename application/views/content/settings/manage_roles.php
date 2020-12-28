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
<table id="userroles_table" class="table table-striped">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Role</th>
			<th scope="col">Description</th>
			<th scope="col">Users</th>
			<th scope="col">Edit</th>
			<th scope="col">Publish</th>
			<th scope="col">Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($roles as $role): ?>
			<tr id="user_<?= $role['id'] ?>">
				<td><?= $role['id'] ?></td>
				<td><a href="<?= URL::Site('admin/settings/edit_role/'.$role['id']); ?>"><?= $role['role']; ?></a></td>
				<td><a href="<?= URL::Site('admin/settings/edit_role/'.$role['id']); ?>"><?= $role['description']; ?></a></td>
				<td><?= $role['users'] ?></td>
				<td><a href="<?= URL::Site('admin/settings/edit_role/'.$role['id']); ?>" ><i class="icon-pencil"></i></a></td>
				<td id="publish_<?= $role['id'] ?>"><i class="icon-<?= ($role['publish'] == 0) ? 'ban-circle' : 'ok' ?>" onclick="rolePublish(<?= $role['id'] ?>,<?= $role['publish'] ?>);"></i></td>
				<td><i class="icon-remove-circle" onclick="roleDelete(<?= $role['id'] ?>);"></i></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<script>
	function roleDelete(id){
		var choice = confirm('Are you sure you want to delete this User Group?');
        if (choice)
       {
          $.ajax({
               url  : "<?= URL::Site('admin/settings/delete_role'); ?>/"+id
            }).done(function(result){
				if(result == "This role has been deleted"){
				  $('#user_'+id).remove();
			    }
                alert(result);
            });
       }
    }	
    function rolePublish(id,publish){
          $.ajax({
			   type: "POST",
               url  : "<?= URL::Site('admin/settings/publish_role'); ?>",
               data: { roleid: id, publish: publish } 
            }).done(function(result){
				alert(result);
				if(result=='Role Unpublished Successfully'){
					$('#publish_'+id).html('<i class="icon-ban-circle" onclick="rolePublish('+id+',0)"></i>');
			    }else{
					$('#publish_'+id).html('<i class="icon-ok" onclick="rolePublish('+id+',1)"></i>');
			    }	
                
            });
    }	
</script>
