<table id="labels_datatable" class="table table-striped dataTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Label</th>
            <th>Plugin</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach($labels as $key=>$label):
    ?>
    <tr data-label_id="<?$label['id'];?>">
        <td><?=$label['id'];?></td>
        <td><?=$label['label'];?></td>
        <td><?=$label['plugin_id'];?></td>
        <td><i class="icon-remove"></i></td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>

<div id="label_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Add Label</h3>
			</div>
			<div class="modal-body">
				<input class="form-control" name="label" id="label"/>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-success" id="add_label">Add Label</button>
			</div>
		</div>
	</div>
</div>