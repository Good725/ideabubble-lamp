<?php
if (@$data) {
	extract($data);
}
?>
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
<div id="list_messages_wrapper" style="clear:both;">
	<table id="list_messages_table" class="table table-striped dataTable list_messages_table">
		<thead>
			<tr>
				<?php foreach ($use_columns as $column): ?>
					<?php if ($column == 'actions'): ?>
						<th scope="col" class="list_messages_table_th_actions"><span class="sr-only">Actions</span></th>
					<?php else: ?>
						<th scope="col" class="list_messages_table_th_<?= $column ?>"><?= ucwords(str_replace('_', ' ', $column)); ?></th>
					<?php endif; ?>
				<?php endforeach; ?>
                <?php if (Settings::instance()->get('messaging_popout_menu')): ?>
                    <th scope="col">View</th>
                <?php endif; ?>
			</tr>
		</thead>
		<thead>
			<tr>
				<?php foreach ($use_columns as $column): ?>
					<th scope="col" class="list_messages_table_search_th_<?= $column ?>">
						<?php if (!in_array($column, array('info', 'last_activity'))) { ?>
						<label for="list_messages_<?= $column ?>" class="sr-only">Search by <?= $column ?></label>
						<input type="text" id="list_messages_<?= $column ?>" class="form-control search_init" name="" placeholder="Search" />
						<?php } ?>
					</th>
				<?php endforeach; ?>
			</tr>
		</thead>
	</table>
</div>
<input type="hidden" id="table_use_columns" value="<?= implode(',', $use_columns) ?>" />
<input type="hidden" id="table_parameters" value="<?= isset($parameters) ? htmlspecialchars(json_encode($parameters)) : '{}' ?>" />

<?php require 'messaging_send_modal.php'; ?>

<!-- No messages selected -->
<div class="modal fade" id="no-messages-selected-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">No Messages selected</h4>
			</div>
			<div class="modal-body">
				<p>You have not selected any messages. To do this click on the relevant checkbox icons in the list of messages.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="delete-messages-modal" tabindex="-1" role="dialog" aria-labelledby="delete-messages-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="delete-messages-modal-label"><?= __('Delete messages') ?></h4>
			</div>
			<div class="modal-body">
				<p>Are you user you would like to delete <span id="delete-message-modal-amount">0</span> message(s).</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="delete-messages-modal-confirm"><?= __('Delete') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>
		</div>
	</div>
</div>
