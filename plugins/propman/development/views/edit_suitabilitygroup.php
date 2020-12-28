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
<form id="suitabilitygroup-edit" name="suitabilitygroup-edit" class="form-horizontal" method="post" action="/admin/propman/edit_suitabilitygroup/<?= $suitabilityGroup['id'] ?>">
    <input type="hidden" name="id" value="<?= $suitabilityGroup['id'] ?>" />

	<div class="col-sm-12">
		<div class="form-group">
			<label class="sr-only" for="edit-suitabilitygroup-name"><?= __('Enter Suitability Group') ?></label>
			<div class="col-sm-10">
				<input type="text" class="form-control ib-title-input required" id="edit-suitabilitygroup-name" name="name" placeholder="<?= __('Enter Suitability Group Name') ?>" value="<?=htmlspecialchars($suitabilityGroup['name'])?>" />
			</div>
			<div class="col-sm-2">
				<label>
					<?php $published = ($suitabilityGroup['published'] !== '0'); ?>
					<span class="sr-only"><?= __('Publish') ?></span>
					<input type="hidden" name="published" value="0"<?= ( ! $published) ? ' checked="checked"' : ''?> /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
					<input type="checkbox" name="published" value="1"<?= $published ? ' checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="edit-propertytype-sort"><?= __('Sort order') ?></label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="edit-propertytype-sort" name="sort" placeholder="<?= __('Enter sort order') ?>" value="<?=htmlspecialchars($suitabilityGroup['sort'])?>" />
			</div>
		</div>

		<h3><?= __('Items within this group') ?></h3>
		<table class="table table-striped" id="edit-suitabilitygroup-types-table">
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
                if ($suitabilityGroup['types'])
                foreach ($suitabilityGroup['types'] as $type):
                ?>
					<tr data-id="<?= $type['id'] ?>">
						<td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
						<td><?= $type['id'] ?></td>
						<td>
							<label class="inline-edit">
								<span class="inline-edit-icon icon-pencil"></span>
								<input name="suitabilityTypeId[]" type="hidden" value="<?=$type['id']?>" />
								<input class="inline-edit-field" name="suitabilityType[]" type="text" value="<?= htmlspecialchars($type['name']) ?>" />
							</label>
						</td>
						<td>
							<button data-id="<?= $type['id'] ?>"
									type="button"
									class="btn-link btn-remove"
									title="<?= __('Remove') ?>"
									data-toggle="modal"
									data-target="#delete-suitability-type-modal">
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
								   id="edit-suitabilitygroup-add-type"
								   name="suitabilityType[]"
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
                            <input name="suitabilityTypeId[]" type="hidden" value="new" />
							<input class="inline-edit-field" type="text" name="suitabilityType[]" />
						</label>
					</td>
					<td>
						<button data-id=""
								type="button"
								class="btn-link btn-remove"
								title="<?= __('Remove') ?>"
								data-toggle="modal"
								data-target="#delete-suitability-type-modal">
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
            <?php
            if (is_numeric($suitabilityGroup['id'])) {
                ?>
                <button type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#delete-suitabilitygroup-modal"><?= __('Delete') ?></button>
                <?php
            }
            ?>
            <a href="/admin/propman/suitabilitygroups" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>

</form>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-suitabilitygroup-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_suitabilitygroup" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Delete Suitability Group') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= sprintf(__('Are you sure you want to delete %s?'), $suitabilityGroup['name']); ?></p>
            </div>
            <div class="modal-footer">

                <input type="hidden" name="id" value="<?= $suitabilityGroup['id'] ?>" />
                <button type="button" class="btn btn-danger" id="delete-suitabilitygroup-button"><?= __('Delete') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>

            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-suitability-type-modal">
	<div class="modal-dialog">
		<div class="modal-content">
            <form action="/admin/propman/delete_suitabilitygroup" method="post" id="delete-suitability-group">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete Suitability Type') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= sprintf(__('Are you sure you want to delete %s?'), '<span class="name"></span>'); ?></p>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="name" value="" />
					<button type="button" class="btn btn-danger" id="delete-suitability-type-button" data-dismiss="modal"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
            </form>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-used-suitabilitygroup-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_used_suitabilitygroup" method="post" id="delete-used-suitability-group">
                <input type="hidden" name="id" value="" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete suitability group') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('This Suitability Group is currently linked to a Property.') ?></p>
                    <p><?= __('Do you wish to continue?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" id="delete-used-suitabilitygroup-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
