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
<form id="facilitytype-edit" name="facilitytype-edit" class="form-horizontal" method="post" action="/admin/propman/edit_facilitytype/<?=@$facilityType['id']?>">
    <input type="hidden" name="id" value="<?=@$facilityType['id']?>" />

	<div class="col-sm-12">
		<div class="form-group">
			<label class="sr-only" for="edit-facilitytype-name"><?= __('Enter Facility Type') ?></label>
			<div class="col-sm-10">
				<input type="text" class="form-control ib-title-input required" id="edit-facilitytype-name" name="name" placeholder="<?= __('Enter Facility Type Name') ?>" value="<?=htmlspecialchars(@$facilityType['name'])?>" />
			</div>
			<div class="col-sm-2">
				<label>
					<span class="sr-only"><?= __('Publish') ?></span>
					<input type="hidden" name="published" value="0" <?=!$facilityType['published'] ? 'checked="checked"' : ''?> /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
					<input type="checkbox" name="published" value="1" <?=$facilityType['published'] ? 'checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="edit-facilitytype-group_id"><?= __('Group') ?></label>
			<div class="col-sm-5">
				<select class="form-control" id="edit-facilitytype-group_id" name="facility_group_id" placeholder="<?= __('Select Group') ?>">
					<?=Html::optionsFromRows('id', 'name', $facilityGroups, @$facilityType['facility_group_id'], array('value' => '', 'label' => 'Select Facility Group'));?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="edit-facilitytype-sort"><?= __('Sort order') ?></label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="edit-facilitytype-sort" name="sort" placeholder="<?= __('Enter sort order') ?>" value="<?=htmlspecialchars(@$facilityType['sort'])?>" />
			</div>
		</div>

        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php
            if (is_numeric(@$facilityType['id'])) {
                ?>
                <button type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#delete-facilitytype-modal"><?= __('Delete') ?></button>
                <?php
            }
            ?>
            <a href="/admin/propman/facilitytypes" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>

</form>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-facilitytype-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_facilitytype" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Delete Facility Group') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= sprintf(__('Are you sure you want to delete %s?'), $facilityType['name']); ?></p>
            </div>
            <div class="modal-footer">

                <input type="hidden" name="id" value="<?=$facilityType['id']?>" />
                <button type="submit" class="btn btn-danger" id="delete-facilitytype-button"><?= __('Delete') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>

            </div>
            </form>
        </div>
    </div>
</div>
