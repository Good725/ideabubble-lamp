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

<form method="post" class="form-horizontal">
	<div class="form-group">
		<label for="ip" class="control-label col-sm-1">IP</label>
		<div class="col-sm-2"><input class="form-control" type="text" name="ip" /></div>
		<label for="reason" class="control-label col-sm-1">Reason</label>
		<div class="col-sm-2"><input class="form-control" type="text" name="reason" /></div>
		<div class="col-sm-1"><button class="btn btn-default add"  type="submit" name="block">Block</button></div>
	</div>
</form>
<br />

<table id="ipwatcher_blacklist_table" class="table table-striped dataTable">
    <thead>
        <tr>
            <th scope="col">IP</th>
            <th scope="col">Time</th>
            <th scope="col">Host</th>
            <th scope="col">GeoIP</th>
            <th scope="col">Reason</th>
			<th></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function(){
	$('#ipwatcher_blacklist_table').dataTable(
	{
		"bDestroy"        : true,
		"bAutoWidth"      : true,
		"oLanguage"       : { "sInfoFiltered": "" },
		"bProcessing"     : false,
		"bServerSide"     : true,
		"sAjaxSource"     : '/admin/settings/ipwatcher_ajax_get_blacklist_datatable',
		"sPaginationType" : "bootstrap",
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
