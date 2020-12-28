<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

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

<form class="col-sm-12 form-horizontal" id="form_add_edit_category" name="form_add_edit_category" action="/admin/courses/save_category/" method="post">

    <input type="hidden" id="redirect" name="redirect" />

    <!-- Category -->
    <div class="form-group">
		<div class="col-sm-7">
			<label class="sr-only" for="category"></label>
			<input type="text" class="form-control required" id="category" name="category" placeholder="Enter category name here" value="<?=isset($data['category']) ? $data['category'] : ''?>"/>
		</div>
    </div>

    <!-- Parent -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="parent_id">Parent</label>
        <div class="col-sm-5">
            <select class="form-control" id="parent_id" name="parent_id">
                <option value="" <?=( ! isset($data['parent_id']) OR ($data['parent_id'] == '') ) ? 'selected="selected"' : '' ?>>No Parent</option>

                <? foreach ($categories as $item): ?>
                        <option value="<?=$item['id']?>" <?=( isset($data['parent_id']) AND ($data['parent_id'] == $item['id']) ) ? 'selected="selected"' : '' ?>><?=$item['category']?></option>
                <? endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Description -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="summary">Summary</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="summary" name="summary" value="<?=isset($data['summary']) ? $data['summary'] : ''?>"/>
        </div>
    </div>

    <!-- Information -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="description">Description</label>
        <div class="col-sm-5">
            <textarea class="form-control" id="description" name="description" rows="4"><?=isset($data['description']) ? $data['description'] : ''?></textarea>
        </div>
    </div>

    <!-- Colour -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="course-category-color">Colour</label>
        <div class="col-sm-5">
            <?php
            $value = isset($data['color']) ? $data['color'] : '';
            echo Form::ib_input(null, 'color', $value, ['class' => 'form-input-colorpicker', 'id' => 'course-category-color']);
            ?>
        </div>
    </div>

    <? // Category start and end date ?>
    <div id="date_selection"  style="display: none;">
        <!-- Start End Dates -->
        <div class="form-group">
            <label class="col-sm-2 control-label" for="start_date">Start Date</label>
            <div class="col-sm-2">
                <input type="text" name="start_date"  class="form-control datepicker" id="start_date" value="<?=isset($data['start_date']) ? date('d-m-Y',strtotime($data['start_date'])):'' ;?>"/>
            </div>
            <label class="col-sm-2 control-label" for="end_date">End Date</label>
            <div class="col-sm-2">
                <input type="text" name="end_date" class="form-control datepicker" id="end_date" value="<?=isset($data['end_date']) ? date('d-m-Y',strtotime($data['end_date'])):'' ;?>"/>
            </div>
        </div>
    </div>

    <? // Category start and end Time ?>
    <div id="time_selection" >
        <div class="form-group">
            <label class="col-sm-2 control-label" for="start_time">Start Time</label>
            <div class="col-sm-2">
                <input type="text" name="start_time" id="start_time" class="form-control datetimepicker" value="<?= isset($data['start_time']) ? date('H:i',strtotime($data['start_time'])) : ''; ?>" readonly="readonly">
            </div>
            <label class="col-sm-2 control-label" for="end_time">End Time</label>
            <div class="col-sm-2">
                <input type="text" name="end_time" id="end_time" class="form-control datetimepicker" value="<?= isset($data['end_time']) ? date('H:i',strtotime($data['end_time'])) : ''; ?>" readonly="readonly">
            </div>
        </div>
    </div>

    <!-- Tutorial -->
    <div class="form-group">
        <div class="col-sm-2 control-label">PAYG (Default)</div>
        <div class="col-sm-5">
            <div class="btn-group" data-toggle="buttons">
				<?php $grinds_tutorial = ((isset($data['grinds_tutorial'])) AND ($data['grinds_tutorial'] == '1')); ?>
				<label class="btn btn-plain<?= $grinds_tutorial ? ' active' : '' ?>">
					<input type="radio" name="grinds_tutorial" value="1" id="grinds_tutorial_yes"<?= $grinds_tutorial ? ' checked="checked"' : '' ?> />Yes
				</label>
				<label class="btn btn-plain<?= ( ! $grinds_tutorial) ? ' active' : '' ?>">
					<input type="radio" name="grinds_tutorial" value="0" id="grinds_tutorial_no"<?= ( ! $grinds_tutorial) ? ' checked="checked"' : '' ?> />No
				</label>
            </div>
        </div>
    </div>

    <!-- Publish -->
    <div class="form-group">
        <div class="col-sm-2 control-label">Publish</div>
        <div class="col-sm-5">
			<div class="btn-group" data-toggle="buttons">
				<?php $publish = ( ! isset($data['publish']) OR $data['publish'] == '1'); ?>
				<label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
					<input type="radio" name="publish" value="1" id="publish_yes"<?= $publish ? ' checked="checked"' : '' ?> />Yes
				</label>
				<label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
					<input type="radio" name="publish" value="0" id="publish_no"<?= ( ! $publish) ? ' checked="checked"' : '' ?> />No
				</label>
			</div>
        </div>
    </div>
    <!-- Image -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="file_id">Image</label>
        <div class="col-sm-5">
            <select class="form-control" id="file_id" name="file_id">
                <?php if ( ! isset($data['file_id']) OR ($data['file_id'] == '') ): ?>
                    <option value="">-- Select Document --</option>
                <?php endif; ?>

                <?php foreach ($documents as $item): ?>
                    <option value="<?=$item['filename']?>" <?=( isset($data['file_id']) AND ($data['file_id'] == $item['filename']) ) ? 'selected="selected"' : '' ?>><?=$item['filename']?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="edit_course-checkout_alert">Checkout Alert</label>

        <div class="col-sm-5">
            <p style="font-weight: 200; margin-top: .45em;">Text to appear in the cart when the user books a course of this category.</p>
            <textarea
                class="form-control ckeditor"
                id="edit_course-checkout_alert"
                name="checkout_alert"
                rows="4"><?= isset($data['checkout_alert']) ? $data['checkout_alert'] : ''?></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-2 control-label">Display in Calendar</div>

        <div class="col-sm-5">
            <div
                class="btn-group popinit"
                data-content="If a calendar is displaying on the front end, use this option to toggle if courses in this category appear in the calendar. (Requires the &quot;<a href=&quot;/admin/settings?group=Courses#courses_in_calendar&quot; target=&quot;_blank&quot;>Courses in Calendar</a>&quot; setting to be &quot;on&quot;)"
                data-original-title="Display in Calendar"
                data-trigger="focus hover"
                data-toggle="buttons"
                rel="popover"
                >
                <?php $display_in_calendar = ( ! isset($data['display_in_calendar']) || $data['display_in_calendar'] == '1'); ?>

                <label class="btn btn-default<?= $display_in_calendar ? ' active' : '' ?>">
                    <input type="radio" name="display_in_calendar" value="1"<?= $display_in_calendar ? ' checked="checked"' : '' ?> />Yes
                </label>

                <label class="btn btn-default<?= ( ! $display_in_calendar) ? ' active' : '' ?>">
                    <input type="radio" name="display_in_calendar" value="0"<?= ( ! $display_in_calendar) ? ' checked="checked"' : '' ?> />No
                </label>
            </div>
        </div>
    </div>

    <!-- Order By -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="order">Order Priority</label>
        <div class="col-sm-5">
            <select class="form-control" id="order" name="order">
                <?php for ($i = 0; $i < 11; $i ++): ?>
                    <option <?=(@$data['order'] == $i) ? 'selected="true"' : ''?> value"<?= $i; ?>"><?= $i; ?></option>
               <?php endfor; ?>
            </select>
        </div>
    </div>

    <!-- Category Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>

    <div class="well">
        <button type="button" class="btn btn-primary save_button" data-redirect="save">Save</button>
        <button type="button" class="btn btn-primary save_button" data-redirect="save_and_exit">Save &amp; Exit</button>
        <button type="reset" class="btn">Reset</button>
        <?php if (isset($data['id'])) : ?>
			<a href="#" class="btn btn-danger" id="btn_delete" data-id="<?=$data['id']?>">Delete</a>
        <?php endif; ?>
    </div>
</form>
<?php if (isset($data['id'])) : ?>
	<div class="modal fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Warning!</h3>
				</div>
				<div class="modal-body">
					<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected category.
						<br>All items like subcategories, courses will be also deleted!</p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" data-id="0" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
