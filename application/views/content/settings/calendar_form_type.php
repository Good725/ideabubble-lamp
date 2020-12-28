<div id="calendar_add_edit_type" class="col-sm-12">
    <form id="calendar_type_edit_form" class="form-horizontal" action="<?php echo URL::Site('admin/calendars/save_type/') ?>" method="post">
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
        <fieldset>
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">Title</label>

                <div class="col-sm-7">
                    <input type="text" id="title" name="title" class="form-control popinit" rel="popover" value="<?= isset($type->id) ? @$type->title : '';?>" required="required">
                </div>
            </div>
            <input type="hidden" id="id" name="id" value="<?= isset($type->id) ? $type->id : '';?>">

            <? // Publish ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="publish">Publish</label>

                <div class="col-sm-4">
                    <div class="btn-group" data-toggle="buttons">
                        <?php $publish = ($type->publish == '1' OR is_null($type->publish) ); ?>
                        <label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
                            <input type="radio" name="publish" value="1" id="publish_yes"<?= $publish ? ' checked' : '' ?> />Yes
                        </label>
                        <label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
                            <input type="radio" name="publish" value="0" id="publish_no"<?= ( ! $publish) ? ' checked' : '' ?> />No
                        </label>
                    </div>
                </div>
            </div>

        </fieldset>

        <? // Action Buttons ?>
        <input type="hidden" id="save_exit" name="save_exit" value="false" />

        <div class="col-sm-12">
            <div class="well">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="submit" class="btn btn-primary" onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit</button>
                <button type="reset" class="btn" id="type-form-reset">Reset</button>
                <?php if (is_numeric($type->id)) : ?>
                    <a class="btn btn-danger" id="btn_delete" data-item="calendar_types" data-id="<?=$type->id?>">Delete</a>
                <?php endif; ?>
            </div>
        </div>

    </form>
</div>
