<div class="col-sm-12 header">
    <?= (isset($alert)) ? $alert : ''; ?>
    <?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
</div>

<div class="col-sm-12">
	<table id="list_corns_table" class="table table-striped dataTable">
		<thead>
			<tr>
				<th scope="col">ID</th>
				<th scope="col">Title</th>
				<th scope="col">Plugin</th>
				<th scope="col">Frequency</th>
				<th scope="col">Time</th>
				<th scope="col">Created</th>
				<th scope="col">Modified</th>
				<th scope="col">Edit</th>
				<th scope="col">Publish</th>
				<th scope="col">Delete</th>
			</tr>
		</thead>
		<tbody>
			<tr data-id="1">
				<td>1</td>
				<td>Nissan Cars Refresh</td>
				<td>Cars</td>
				<td>Daily</td>
				<td>00:00</td>
				<td>2014-01-01 00:00:00</td>
				<td>2014-01-01 00:00:00</td>
				<td><a class="edit_link" href="/admin/settings/add_edit_cron/1"><i class="icon-pencil"></i></a></td>
				<td><i class="icon-ok"></i></td>
				<td><i class="icon-ban-circle"></i></td>
			</tr>
		</tbody>
	</table>
</div>
<!-- Delete modal -->
<div id="delete_cron_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete_cron_modal_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="delete_cron_modal_label">Delete Cron</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this Cron?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#list_corns_table').on('click', 'tbody tr td', function()
    {
        if (this.getElementsByClassName('icon-ban-circle').length > 0)
        {
            $('#delete_cron_modal').modal();
        }
        else if (this.getElementsByClassName('icon-pencil').length == 0)
        {
            this.parentNode.getElementsByClassName('edit_link')[0].click();
        }
    });
</script>
