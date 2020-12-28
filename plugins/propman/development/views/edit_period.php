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
<form id="period-edit" name="period-edit" class="col-sm-12 form-horizontal" method="post" action="/admin/propman/edit_period/<?=@$period['id']?>">
    <input type="hidden" name="id" value="<?=@$period['id']?>" />

    <div class="form-group clearfix">
        <label class="sr-only" for="edit-period-name"><?= __('Enter Period') ?></label>
        <div class="col-sm-10">
            <input type="text" class="form-control ib-title-input required" id="edit-period-name" name="name" placeholder="<?= __('Enter Period Name') ?>" value="<?=htmlspecialchars(@$period['name'])?>" />
        </div>
        <div class="col-sm-2">
            <label>
                <span class="sr-only"><?= __('Publish') ?></span>
				<?php $published = ($period['published'] !== '0') ?>
                <input type="hidden" name="published" value="0" <?= (! $published) ? 'checked="checked"' : ''?> /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
                <input type="checkbox" name="published" value="1" <?= $published ? 'checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="edit-period-starts"><?= __('Start Date') ?></label>
        <div class="col-sm-5">
            <input type="text" class="form-control datepicker" id="edit-period-starts" name="starts" placeholder="<?= __('Enter Start Date') ?>" value="<?=date::ymd_to_dmy(@$period['starts'])?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="edit-period-ends"><?= __('End Date') ?></label>
        <div class="col-sm-5">
            <input type="text" class="form-control datepicker" id="edit-period-ends" name="ends" placeholder="<?= __('Enter End Date') ?>" value="<?=date::ymd_to_dmy(@$period['ends'])?>" />
        </div>
    </div>

    <div class="col-sm-12">
        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php
            if (is_numeric(@$period['id'])) {
                ?>
                <button type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#delete-period-modal"><?= __('Delete') ?></button>
                <?php
            }
            ?>
            <a href="/admin/propman/periods" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>

</form>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-period-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_period" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Delete Period') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= sprintf(__('Are you sure you want to delete %s?'), $period['name']); ?></p>
            </div>
            <div class="modal-footer">

                <input type="hidden" name="id" value="<?=$period['id']?>" />
                <button type="submit" class="btn btn-danger" id="delete-period-button"><?= __('Delete') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>

            </div>
            </form>
        </div>
    </div>
</div>
