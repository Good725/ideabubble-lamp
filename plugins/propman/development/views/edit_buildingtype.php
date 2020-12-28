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
<form id="buildingtype-edit" name="buildingtype-edit" method="post" action="/admin/propman/edit_buildingtype/<?=@$buildingType['id']?>">
    <input type="hidden" name="id" value="<?=@$buildingType['id']?>" />

    <div class="form-group clearfix">
        <label class="sr-only" for="edit-buildingtype-name"><?= __('Enter Building Type') ?></label>
        <div class="col-sm-10">
            <input type="text" class="form-control ib-title-input required" id="edit-buildingtype-name" name="name" placeholder="<?= __('Enter Building Type Name') ?>" value="<?=htmlspecialchars(@$buildingType['name'])?>" />
        </div>
        <div class="col-sm-2">
            <label>
                <span class="sr-only"><?= __('Publish') ?></span>
                <input type="hidden" name="published" value="0"/><?php // If the checkbox is unticked, this value will get sent to the server  ?>
                <input type="checkbox" name="published" value="1" <?=( ! isset($buildingType['published']) OR $buildingType['published'] == 1) ? 'checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
            </label>
        </div>
    </div>

    <div class="col-sm-12" style="clear: both;">
        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php
            if (is_numeric(@$buildingType['id'])) {
                ?>
                <button type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#delete-buildingtype-modal"><?= __('Delete') ?></button>
                <?php
            }
            ?>
            <a href="/admin/propman/buildingtypes" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>

</form>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-buildingtype-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_buildingtype" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Delete Building Type') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= sprintf(__('Are you sure you want to delete %s?'), $buildingType['name']); ?></p>
            </div>
            <div class="modal-footer">

                <input type="hidden" name="id" value="<?=$buildingType['id']?>" />
                <button type="submit" class="btn btn-danger" id="delete-buildingtype-button"><?= __('Delete') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>

            </div>
            </form>
        </div>
    </div>
</div>
