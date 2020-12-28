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
<form id="propertytype-edit" name="propertytype-edit" class="form-horizontal" method="post" action="/admin/propman/edit_propertytype/<?=@$propertyType['id']?>">
    <input type="hidden" name="id" value="<?=@$propertyType['id']?>" />

	<div class="col-sm-12">
		<div class="form-group">
			<label class="sr-only" for="edit-propertytype-name"><?= __('Enter Property Type') ?></label>
			<div class="col-sm-10">
				<input type="text" class="form-control ib-title-input rdquired" id="edit-propertytype-name" name="name" placeholder="<?= __('Enter Property Type Name') ?>" value="<?=htmlspecialchars(@$propertyType['name'])?>" />
			</div>
			<div class="col-sm-2">
				<label>
					<span class="sr-only"><?= __('Publish') ?></span>
					<input type="hidden" name="published" value="0"  /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
                    <input type="checkbox" name="published" value="1" checked data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="edit-propertytype-bedrooms"><?= __('Number of bedrooms') ?></label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="edit-propertytype-bedrooms" name="bedrooms" placeholder="<?= __('Enter the number of bedrooms') ?>" value="<?=htmlspecialchars(@$propertyType['bedrooms'])?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="edit-propertytype-sleep"><?= __('Max sleep') ?></label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="edit-propertytype-sleep" name="sleep" placeholder="<?= __('Max sleep') ?>" value="<?=htmlspecialchars(@$propertyType['sleep'])?>" />
			</div>
		</div>

        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php
            if (is_numeric(@$propertyType['id'])) {
                ?>
                <button type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#delete-propertytype-modal"><?= __('Delete') ?></button>
                <?php
            }
            ?>
            <a href="/admin/propman/propertytypes" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>

</form>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-propertytype-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_propertytype" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Delete Building Type') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= sprintf(__('Are you sure you want to delete %s?'), $propertyType['name']); ?></p>
            </div>
            <div class="modal-footer">

                <input type="hidden" name="id" value="<?=$propertyType['id']?>" />
                <button type="submit" class="btn btn-danger" id="delete-propertytype-button"><?= __('Delete') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>

            </div>
            </form>
        </div>
    </div>
</div>
