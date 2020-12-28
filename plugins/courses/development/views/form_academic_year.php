<div class="col-sm-12">
    <?=(isset($alert)) ? $alert : ''?>
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

<form class="col-sm-12 form-horizontal" id="form_add_edit_academic_year" name="form_add_edit_year" 
      action="<?php echo URL::Site('admin/courses/save_academic_year/') ?>" method="post">

    <input type="hidden" id="redirect" name="redirect" />
    <input type="hidden" id="id" name="id" value="<?= $data->id?>" />

    <? // Title ?>
    <div class="form-group">
        <label class="sr-only" for="title">Academic Year</label>
        <div class="col-sm-7">
            <input type="text" class="form-control popinit required validate[required]" rel="popover" id="title" name="title" placeholder="Enter Academic year title" value="<?=$data->title?>"/>
        </div>
    </div>

    <? // Start Date ?>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="start_date">Start Date</label>
        <div class="col-sm-7">
            <input type="text" id="start_date" name="start_date" class="datepicker" value="<?=$data->start_date?>">
        </div>
    </div>

    <? // End Date ?>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="end_date">End Date</label>
        <div class="col-sm-7">
            <input type="text" id="end_date" name="end_date" class="datepicker" value="<?=$data->end_date?>">
        </div>
    </div>

    <? // Status ?>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="status">Status</label>
        <div class="col-sm-7">
            <div class="btn-group" data-toggle="buttons">
                <?php $publish = ( ! $data->status OR $data->status == '0'); ?>
                <label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
                    <input type="radio" name="status" value="0" id="status_pending"<?= $publish ? ' checked' : '' ?> />Pending
                </label>
                <label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
                    <input type="radio" name="status" value="1" id="status_active"<?= ( ! $publish) ? ' checked' : '' ?> />Active
                </label>
            </div>
        </div>
    </div>

    <? // Publish ?>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="publish">Publish</label>
        <div class="col-sm-7">
            <div class="btn-group" data-toggle="buttons">
                <?php $publish = ( ! $data->publish) OR $data->publish == '1'; ?>
                <label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
                    <input type="radio" name="publish" value="1" id="publish_yes"<?= $publish ? ' checked' : '' ?> />Publish
                </label>
                <label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
                    <input type="radio" name="publish" value="0" id="publish_no"<?= ( ! $publish) ? ' checked' : '' ?> />Archive
                </label>
            </div>
        </div>
    </div>

    <? // Action Buttons ?>
    <input type="hidden" id="save_exit" name="save_exit" value="false" />

    <div class="col-sm-12">
        <div class="well">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="submit" class="btn btn-primary" onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit</button>
            <button type="reset" class="btn" id="event-form-reset">Reset</button>
            <?php if (is_numeric($data->id)) : ?>
                <a class="btn btn-danger" id="btn_delete" data-id="<?=$data->id ?>">Delete</a>
            <?php endif; ?>
        </div>
    </div>
    
</form>
<script>
    $(document).on("ready", function() {
        $('#form_add_edit_academic_year').validationEngine();
        $('#start_date').val('');
        $('#end_date').val('');
    });
</script>
