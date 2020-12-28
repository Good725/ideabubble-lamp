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
		<label for="user_agent" class="control-label col-sm-1">UserAgent</label>
		<div class="col-sm-2"><input class="form-control" type="text" name="user_agent" maxlength="100" /></div>
		<label for="reason" class="control-label col-sm-1">Reason</label>
		<div class="col-sm-2"><input class="form-control" type="text" name="reason" /></div>
		<div class="col-sm-1"><button type="submit" name="add" class="btn add">Add</button></div>
	</div>
</form>
<br />

<table id="ipwatcher_ua_whitelist_table" class="table table-striped dataTable">
    <thead>
        <tr>
            <th scope="col">UserAgent</th>
            <th scope="col">Time</th>
            <th scope="col">Reason</th>
			<th></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function(){
	$('#ipwatcher_ua_whitelist_table').dataTable(
	{
		"bDestroy"        : true,
		"bAutoWidth"      : true,
		"oLanguage"       : { "sInfoFiltered": "" },
		"bProcessing"     : false,
		"bServerSide"     : true,
		"sAjaxSource"     : '/admin/settings/ipwatcher_ajax_get_ua_whitelist_datatable',
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
