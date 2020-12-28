<div id="dashboard_list_alerts"><?= isset($alert) ? $alert : '' ?>
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

<table class="table table-striped dataTable list_dashboards_table" id="list_dashboards_table">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Name</th>
			<th scope="col">Owner</th>
			<th scope="col">Shared With</th>
			<th scope="col">Created</th>
			<th scope="col">Updated</th>
			<? /* Clone option currently not available
			<th scope="col">Clone</th>
 			*/ ?>
			<th scope="col">View</th>
			<th scope="col">Favourite</th>
			<th scope="col">Delete</th>
		</tr>
	</thead>
	<thead>
		<tr>
			<th scope="col">
				<label for="list_dashboards_search_id" class="sr-only">Search by ID</label>
				<input type="text" id="list_dashboards_search_id" class="form-control search_init" name="" placeholder="Search" />
			</th>

			<th scope="col">
				<label for="list_dashboards_search_name" class="sr-only">Search by name</label>
				<input type="text" id="list_dashboards_search_name" class="form-control search_init" name="" placeholder="Search" />
			</th>

			<th scope="col">
				<label for="list_dashboards_search_owner" class="sr-only">Search by owner</label>
				<input type="text" id="list_dashboards_search_owner" class="form-control search_init" name="" placeholder="Search" />
			</th>

			<th scope="col">
				<label for="list_dashboards_search_shared_with" class="sr-only">Search by who this has been shared with</label>
				<input type="text" id="list_dashboards_search_shared_with" class="form-control search_init" name="" placeholder="Search" />
			</th>

			<th></th>

			<th scope="col"></th>
			<? /* Clone option currently not available
			<td></td>
 			*/ ?>
			<td></td>
			<td></td>
		</tr>
	</thead>
</table>

<div class="modal fade" id="delete_dashboard_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Delete Dashboard</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this dashboard?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="delete_dashboard_button">Delete</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="dashboard-permission-error-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Permission error</h4>
			</div>
			<div class="modal-body">
				<p>You do not have permission to edit other people&#39;s dashboards.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>

<style>
	.list_dashboards_table .search_init{
		width: 100%;
	}
	.list_dashboards_table td:nth-last-child(1),
	.list_dashboards_table td:nth-last-child(2),
	.list_dashboards_table td:nth-last-child(3) {
		text-align: center
	}

	/* Style radio buttons as full/empty stars */
	.star_checkbox {
		position: absolute !important;
		z-index: -9999 !important;
		opacity: 0 !important;
	}
	.star_checkbox + .star_checkbox_icon {
		display: inline-block;
	}
	.star_checkbox + .star_checkbox_icon:before {
		content: '\f006'; /* star-o */
		font-family: FontAwesome;
		display: inline-block;
	}
	.star_checkbox:checked + .star_checkbox_icon:before {
		content: '\f005' /* star */
	}
	.star_checkbox:focus + .star_checkbox_icon {
		outline: 1px dotted #aaa;
	}
</style>
