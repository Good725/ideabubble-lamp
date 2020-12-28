<?=(isset($alert)) ? $alert : '';?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>

<table id="ipwatcher_log_table" class="table table-striped dataTable">
    <thead>
        <tr>
            <th scope="col">IP</th>
            <th scope="col">User Agent</th>
            <th scope="col">URI</th>
			<th scope="col">Time</th>
            <th scope="col">Host</th>
            <th scope="col">GeopIP</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function(){
	$('#ipwatcher_log_table').dataTable(
	{
		"bDestroy"        : true,
		"bAutoWidth"      : true,
		"oLanguage"       : { "sInfoFiltered": "" },
		"bProcessing"     : false,
		"bServerSide"     : true,
		"sAjaxSource"     : '/admin/settings/ipwatcher_ajax_get_log_datatable',
		"sPaginationType" : "bootstrap",
		'aLengthMenu'     : [10, 25, 50, 100],
		"aoColumnDefs": [{
			"aTargets": [1],
			"fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
			{
				// Add data attribute, with the contact ID to each row
				$(nTd).parent().attr({'data-id': oData[0]});
			}
		}]
	});
});
</script>
