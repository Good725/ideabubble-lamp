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
<form id="facilitygroup-edit" name="facilitygroup-edit" class="form-horizontal" method="post" action="/admin/propman/edit_facilitygroup/<?= $facilityGroup['id']?>">
    <input type="hidden" name="id" value="<?= $facilityGroup['id'] ?>" />

	<div class="col-sm-12">
		<div class="form-group">
			<label class="sr-only" for="edit-facilitygroup-name"><?= __('Enter Facility Group') ?></label>
			<div class="col-sm-10">
				<input type="text" class="form-control ib-title-input required" id="edit-facilitygroup-name" name="name" placeholder="<?= __('Enter Facility Group Name') ?>" value="<?=htmlspecialchars($facilityGroup['name'])?>" />
			</div>
			<div class="col-sm-2">
				<label>
					<span class="sr-only"><?= __('Publish') ?></span>
					<?php $published = ($facilityGroup['published'] !== '0'); ?>
					<input type="hidden" name="published" value="0"<?= ( ! $published) ? ' checked="checked"' : ''?> /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
					<input type="checkbox" name="published" value="1"<?= $published ? ' checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="edit-propertytype-sort"><?= __('Sort order') ?></label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="edit-propertytype-sort" name="sort" placeholder="<?= __('Enter sort order') ?>" value="<?= htmlspecialchars($facilityGroup['sort']) ?>" />
			</div>
		</div>

		<h3><?= __('Items within this group') ?></h3>
		<table class="table table-striped" id="edit-facility-types-table">
			<thead>
				<tr>
					<th scope="col"><?= __('Order') ?></th>
					<th scope="col"><?= __('ID') ?></th>
					<th scope="col"><?= __('Name') ?></th>
					<th scope="col"><?= __('Remove') ?></th>
				</tr>
			</thead>
			<tbody class="sortable-tbody">
				<?php
				if ($facilityGroup['types'])
				foreach ($facilityGroup['types'] as $type):
				?>
					<tr data-id="<?= $type['id'] ?>">
						<td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
						<td><?= $type['id'] ?></td>
						<td>
							<label class="inline-edit">
								<span class="inline-edit-icon icon-pencil"></span>
								<input type="hidden" name="facilityTypeId[]" value="<?= $type['id'] ?>" />
								<input class="inline-edit-field" type="text" name="facilityType[]" value="<?= htmlspecialchars($type['name']) ?>" />
							</label>
						</td>
						<td>
							<button data-id="<?= $type['id'] ?>"
									type="button"
									class="btn-link btn-remove"
									title="<?= __('Remove') ?>"
									data-toggle="modal"
									data-target="#delete-facility-type-modal">
								<span class="icon-times"></span>
							</button>
						</td>
					</tr>
				<?php
				endforeach;
				?>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td></td>
					<td>
						<label class="inline-edit">
							<span class="inline-edit-icon icon-plus"></span>
							<span class="sr-only"><?= __('Type to add a new item') ?></span>
							<input class="inline-edit-field inline-edit-field-new"
								   id="edit-facilitygroup-add-type"
								   name="facilityType[]"
								   type="text"
								   autocomplete="off"
								   placeholder="<?= __('Type to add a new item...') ?>" />
						</label>
					</td>
					<td></td>
				</tr>
				<tr class="inline-edit-template">
					<td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
					<td></td>
					<td>
						<label class="inline-edit">
							<span class="inline-edit-icon icon-pencil"></span>
                            <input name="facilityTypeId[]" type="hidden" value="new" />
							<input class="inline-edit-field" type="text" name="facilityType[]"  />
						</label>
					</td>
					<td>
						<button data-id=""
								type="button"
								class="btn-link btn-remove"
								title="<?= __('Remove') ?>"
								data-toggle="modal"
								data-target="#delete-facility-type-modal">
							<span class="icon-times"></span>
						</button>
					</td>
				</tr>
			</tfoot>
		</table>

        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php if (is_numeric($facilityGroup['id'])) {
                ?>
                <button type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#delete-facilitygroup-modal"><?= __('Delete') ?></button>
                <?php
            }
            ?>
            <a href="/admin/propman/facilitygroups" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>

</form>

<?php if (is_numeric($facilityGroup['id'])): ?>
	<div class="modal fade" tabindex="-1" role="dialog" id="delete-facilitygroup-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="/admin/propman/delete_facilitygroup" method="post">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><?= __('Delete Facility Group') ?></h4>
					</div>
					<div class="modal-body">
						<p><?= sprintf(__('Are you sure you want to delete %s?'), $facilityGroup['name']); ?></p>
					</div>
					<div class="modal-footer">

						<input type="hidden" name="id" value="<?=$facilityGroup['id']?>" />
						<button type="submit" class="btn btn-danger" id="delete-facilitygroup-button"><?= __('Delete') ?></button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>

					</div>
				</form>
			</div>
		</div>
	</div>
<?php endif; ?>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-facility-type-modal">
	<div class="modal-dialog">
		<div class="modal-content">
            <input type="hidden" name="id" value="" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?= __('Delete Facility Type') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= sprintf(__('Are you sure you want to delete %s?'), '<span class="name"></span>'); ?></p>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="name" value="" />
				<button type="button" class="btn btn-danger" id="delete-facility-type-button" data-dismiss="modal"><?= __('Delete') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-used-facilitygroup-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_used_facilitygroup" method="post" id="delete-facility-group">
                <input type="hidden" name="id" value="" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete a used facility group') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('Please note this Facility is currently linked to a Property.') ?></p>
                    <p><?= __('Do you wish to continue?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" id="delete-used-facilitygroup-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
