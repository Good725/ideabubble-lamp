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

<div class="list_activities_wrapper">
	<?php if (class_exists('Model_Policy') AND class_exists('Model_Note')): ?>
		<div class="header_buttons" style="visibility:hidden;text-align:right;">
			<div class="btn-group" style="display:inline-block;text-align:left;">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Actions<span class="caret"></span></a>
				<ul class="dropdown-menu" style="left:initial;right:0;">
					<li><a href="#" class="add_activity_note">Add Note</a></li>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<style>
		tr.selected.selected.selected td{background:#ccc;}
		.list_activities_table th:nth-child(2){width:180px!important;}
	</style>

	<?php if (isset($server_side) AND $server_side): ?>
		<input type="hidden" id="list_activities_server_side" value="1" />
	<?php endif; ?>

	<table class="table table-striped dataTable list_activities_table" id="list_activities_table">
		<thead>
			<tr>
				<th scope="col">ID</th>
				<th scope="col">Time</th>
				<th scope="col">User</th>
				<th scope="col">Action</th>
				<th scope="col">Item</th>
				<?php if (isset($show_status) AND $show_status): ?>
					<th scope="col">Status</th>
				<?php endif; ?>
				<th scope="col">Item ID</th>
				<?php if ( ! isset($hide_scope) OR ! $hide_scope): ?>
					<th scope="col">Scope ID</th>
				<?php endif; ?>
<!--                --><?php if (class_exists('Model_Messaging')): ?>
                    <th scope="col">Message Detail</th>
<!--                --><?php endif; ?>
				<?php if (class_exists('Model_Policy') AND class_exists('Model_Note')): ?>
					<th scope="col">Notes</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! isset($server_side) OR ! $server_side): ?>
				<?php foreach ($activities as $activity): ?>
					<tr data-id="<?= $activity['id'] ?>">
						<td><?= $activity['id'] ?></td>
						<td><span class="hidden"><?= $activity['timestamp'] ?></span><?= date('d/m/Y H:i:s', strtotime($activity['timestamp'])) ?></td>
						<td><?= $activity['firstname'].' '.$activity['surname'] ?></td>
						<td><?= $activity['action_name'] ?></td>
						<td><?= $activity['item_type_name'].' '.$activity['item_subtype_name'] ?></td>
						<?php if (isset($show_status) AND $show_status): ?>
							<?php if (isset($activity['status_id']) AND isset($activity['status'])): ?>
								<td data-status_id="<?= $activity['status_id'] ?>"><?= $activity['status'] ?></td>
							<?php else: ?>
								<td></td>
							<?php endif; ?>
						<?php endif; ?>
						<td><?= $activity['item_id'] ?></td>
						<?php if ( ! isset($hide_scope) OR ! $hide_scope): ?>
							<td><?= $activity['scope_id'] ?></td>
						<?php endif; ?>
<!--                        --><?php if (class_exists('Model_Messaging')): ?>
                            <td><?= $activity['item_type_name'] == 'Message' ? $activity['sender'] : ''?> </td>
<!--                        --><?php endif; ?>
						<?php if (class_exists('Model_Policy') AND class_exists('Model_Note')): ?>
							<td>
								<div rel="popover" data-placement="left"data-original-title="Notes" class="popinit" data-trigger="focus">
									<a href="#" class="activity-notes-icon" data-id="<?= $activity['id'] ?>" data-item_type="<?= $activity['item_type'] ?>" data-item_id="<?= $activity['item_id'] ?>"></a>
								</div>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
 			<?php endif; ?>
		</tbody>
	</table>

	<?php if (Request::current()->controller() == 'settings'): ?>
		<div class="modal fade add_activity_note_modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<form action="#" method="post" style="margin:0;">
						<input type="hidden" class="activity_note_id" name="link_id" />
						<input type="hidden" class="activity_note_customer_id" name="customer_id" />

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Add Note to Activity</h4>
						</div>

						<div class="modal-body">
							<div class="form-horizontal">
								<div class="form-group">
									<div class="col-sm-2 control-label">
										<label>Note</label>
									</div>
									<div class="col-sm-5">
										<textarea class="form-control" name="notes"></textarea>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-primary save_activity_note_button">Save</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
