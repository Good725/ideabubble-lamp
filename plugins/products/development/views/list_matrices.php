<?=(isset($alert)) ? $alert : ''?>

<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table class="table table-striped dataTable" id="matrix_table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Option A</th>
        <th>Option B</th>
        <th>Updated</th>
        <th>Enabled</th>
        <th>Delete</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($matrices AS $key=>$matrix):
    ?>
        <tr data-matrix_id="<?=$matrix['id'];?>">
            <td><a href="/admin/products/add_edit_matrix/<?=$matrix['id'];?>"><?=$matrix['id'];?></a></td>
            <td><a href="/admin/products/add_edit_matrix/<?=$matrix['id'];?>"><?=$matrix['name'];?></a></td>
            <td><a href="/admin/products/add_edit_matrix/<?=$matrix['id'];?>"><?=$matrix['option_1'];?></a></td>
            <td><a href="/admin/products/add_edit_matrix/<?=$matrix['id'];?>"><?=$matrix['option_2'];?></a></td>
            <td><a href="/admin/products/add_edit_matrix/<?=$matrix['id'];?>"><?=$matrix['last_updated'];?></a></td></td>
            <td><span class="enabled"><i class="icon-<?=$matrix['enabled'] == 1 ? 'ok' : 'remove';?>"></i></span></td>
            <td><b class="icon-remove" href="#delete_matrix" role="button" class="btn" data-toggle="modal"></b></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>
<!--Save state of the DataTable Sorting-->
<script>
    $(document).ready(function() {
        $('#matrix_table').dataTable( {
            "bStateSave": true
        } );
    } );
</script>

<div id="delete_matrix" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Delete Matrix</h3>
			</div>
			<div class="modal-body">
				<input type="hidden" id="matrix_delete_modal"/>
				<p>Are you sure you want to delete this Matrix?</p>
				<p>This action cannot be undone.</p>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-danger" id="confirm_delete">Confirm</button>
			</div>
		</div>
	</div>
</div>
