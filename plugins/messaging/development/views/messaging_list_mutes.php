<div class="alert-area" id="list-messages-alert-area">
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
</div>

<!-- Table -->
<div id="list_mutes_wrapper" style="clear:both;">
	<table id="list_mutes_table" class="table table-striped dataTable list_mutes_table">
		<thead>
			<tr>
				<th><?=__('Sender')?></th>
				<th><?=__('Actions')?></th>
			</tr>
		</thead>
		<thead>
			<tr>
			</tr>
		</thead>
	</table>
</div>

<div class="modal fade" id="delete-mute-modal" tabindex="-1" role="dialog" aria-labelledby="delete-mute-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title""><?= __('Unmute') ?></h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you would like to unmute <span id="delete-mute"></span>.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="delete-mute-modal-confirm"><?= __('Unmute') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function()
{
	var $table = $('#list_mutes_table');

	// Server-side datatable
	$table.ready(function()
	{
	    var ajax_source = '/admin/messaging/mutes_get_datatable';
	    var settings = {
            "bAutoWidth"      : true,
            "sPaginationType" : "bootstrap",
            "fnServerParams"  : function (aoData) {
                aoData.push({"deleted": 0});
            },
            "fnCreatedRow": function(row)
            {
                // Add ID data attribute to the table row
                row.setAttribute('data-id', $(row).find('[data-id]').data('id'));
            }
        };
			$table.ib_serverSideTable(ajax_source, settings);

	});

	// Search by individual columns
	$table.find('.search_init').on('change', function ()
	{
		$table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
	});

	$(document).on("click", "a.unmute", function(){
		var id = $(this).data("id");
		$("#delete-mute-modal #delete-mute").html($(this).parents("tr").find("td")[0].innerHTML);
		$("#delete-mute-modal").modal("show");
	});

	$("#delete-mute-modal-confirm").on("click", function(){
		$.post(
			"/admin/messaging/unmute",
			{
				sender: $("#delete-mute-modal #delete-mute").html()
			},
			function (response) {
				window.location.reload();
			}
		);
	});
});
</script>
