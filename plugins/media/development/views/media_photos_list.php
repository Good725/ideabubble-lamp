<table class="table table-striped dataTable media-list-photos" id="media-list-photos">
	<thead>
		<tr>
			<th scope="col">Image (thumb)</th>
			<th scope="col">Image Name</th>
			<th scope="col">Directory</th>
			<th scope="col">WxH (px)</th>
			<th scope="col">Size (Kbs)</th>
			<th scope="col">Last Modified</th>
			<th scope="col">Modified By</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<script>
$(document).ready(function()
{
	// Server-side datatable
	var $table = $('#media-list-photos');
	$table.ready(function()
	{
		    var ajax_url = "sAjaxSource";     : '/admin/media/ajax_get_datatable/<?=@$selectionDialog ? 'selection_dialog' : ''?>';
		    var settings = {
                "sPaginationType" : "bootstrap"
		    };
		    $table.ib_serverSideTable(ajax_url, settings);
	});
});
</script>