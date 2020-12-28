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
<div class=" form-action-group clearfix">
	<div class="left">
	<button type="button" class="btn scan btn-primary">Scan Messages</button>
	<a href="/admin/settings/localisation_importcsv" class="btn">Import CSV</a>
	<button type="button" class="btn clearall btn-danger">Clear All</button>
	</div>
	<a href="/admin/settings/localisation_export/csv" class="btn right">Download as CSV</a>
</div>
<form method="post" id="localisation_messages_form" name="localisation_messages_form" action="/admin/settings/localisation_translations_update">
<table id="localisation_messages_table" class="table table-striped dataTable">
    <thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Default Locale Text</th>
			<?php foreach($languages as $language){ ?>
			<th scope="col"><?=$language['title'] . '(' . $language['code'] . ')'?></th>
			<?php } ?>
		</tr>
    </thead>
    <tbody>
    </tbody>
	<tfoot>
		<tr>
			<th colspan="<?=2 + count($languages)?>">
			<div class="form-action-group text-left"><button class="btn btn-primary" type="submit" name="save" disabled="disabled">Save</button></div>
			</th>
		</tr>
	</tfoot>
</table>
</form>

<script>
$(document).ready(function(){
	$(".btn.scan").on("click", function(){
		var btn = this;
		btn.innerHTML = "scanning...";
		btn.disabled = true;
		$.ajax({url: "/admin/settings/localisation_system_scan", 
			method: "post",
			complete: function(response){
			btn.innerHTML = "scanned";
			window.location.reload();
		}});
	});
	
	$(".btn.import").on("click", function(){
		var btn = this;
		btn.innerHTML = "importing...";
		btn.disabled = true;
		$.ajax({url: "/admin/settings/localisation_system_import", 
			method: "post",
			complete: function(response){
			btn.innerHTML = "imported";
			window.location.reload();
		}});
	});

	$(".btn.clearall").on("click", function(){
		var btn = this;
        if (confirm('<?= __('Are you sure you want to clear all translations?')?>')) {
            btn.innerHTML = "clearing...";
            btn.disabled = true;
            $.ajax({
                url: "/admin/settings/localisation_clearall",
                method: "post",
                complete: function (response) {
                    btn.innerHTML = "cleared";
                    window.location.reload();
                }
            });
        }
	});
	
	var changes = {};
	var tbl = null;
	var xtimeout = null;
	
	$("#localisation_messages_form").on("change", "#localisation_messages_table input", function(e){
		$("[name=save]").prop("disabled", false)[0].innerHTML = "*Save";
		changes[e.target.name] = e.target.value;
	});

	$('#localisation_messages_form').on("submit", function() {
		$("[name=save]").prop("disabled", true)[0].innerHTML = "*Saving...";
		$.ajax({url: this.action,
				method: "post",
				data: {changes: JSON.stringify(changes)},
				success: function(){
					$("[name=save]").prop("disabled", true)[0].innerHTML = "Save";
				},
				complete: function(response){
					
				}});
		return false;
	} );

	tbl = $('#localisation_messages_table').dataTable(
	{
		"bDestroy"        : true,
		"bStateSave"      : true,
		"bAutoWidth"      : true,
		"oLanguage"       : { "sInfoFiltered": "" },
		"bProcessing"     : false,
		"bServerSide"     : true,
		"sAjaxSource"     : '/admin/settings/localisation_ajax_get_translations_datatable',
		"sPaginationType" : "bootstrap",
		"aoColumnDefs": [{
			"aTargets": [1],
			"fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
			{
				$(nTd).parent().attr({'data-id': oData[0]});
				if(xtimeout){
					clearTimeout(xtimeout);
				}
				xtimeout = setTimeout(function(){
					for(var name in changes){
						$("[name='" + name + "']").val(changes[name]);
					}
				}, 0);
			}
		}]
	});
});
</script>
