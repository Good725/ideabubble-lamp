<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table class="table table-striped dataTable list-screen-table list-ratecards-table" id="list-ratecards-table">
	<thead>
	<tr>
		<th scope="col"><?= __('ID') ?></th>
		<th scope="col"><?= __('Title') ?></th>
		<th scope="col"><?= __('Property type') ?></th>
		<th scope="col"><?= __('Period') ?></th>
		<th scope="col"><?= __('Groups linked') ?></th>
		<th scope="col"><?= __('Created') ?></th>
		<th scope="col"><?= __('Last author') ?></th>
		<th scope="col"><?= __('Updated') ?></th>
		<th scope="col"><?= __('Actions') ?></th>
		<th scope="col"><?= __('Publish') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	// should be a server-side datatable
	foreach ($ratecards as $ratecard)
	{
		include 'includes/list_rate_cards_tr.php';
	}
	?>
	</tbody>
</table>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-ratecard-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="delete-ratecard" method="post" action="/admin/propman/delete_rate_card">
				<input type="hidden" name="id" value="" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete rate card') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this rate card?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" id="delete-ratecard-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-used-ratecard-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-used-ratecard" method="post" action="/admin/propman/delete_used_ratecard">
                <input type="hidden" name="id" value="" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete rate card') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('This Rate Card is currently linked to a Group.') ?></p>
                    <p><?= __('Do you wish to continue?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete-used-ratecard-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
