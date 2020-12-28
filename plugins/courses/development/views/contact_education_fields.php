<fieldset>
    <legend>Current Educational Details</legend>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="academic_year_id">Academic Year</label>
        <div class="col-sm-9">
            <select class="form-control" id="academic_year_id" name="academic_year_id">
                <option value="">-- Please Select --</option>
                <?php foreach ($data['academic_years'] as $academic_year) { ?>
                    <?php $selected = (isset($data['academic_year_id']) AND $data['academic_year_id'] == $academic_year['id']) ? ' selected="selected"' : '' ?>
                    <option value="<?= $academic_year['id']?>"<?= $selected ?>><?= $academic_year['title']?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <?php /*
	<div class="form-group">
		<label class="col-sm-3 control-label" for="edit-contact-family_role">Family Role</label>
		<div class="col-sm-9">
			<select class="form-control" id="edit-contact-family_role" name="family_role_id">
				<option value=""></option>
			</select>
		</div>
	</div>
    */ ?>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="flexi_student">Flexi Student</label>
        <div class="col-sm-9">
            <?php $is_flexi_student = (isset($data['flexi_student']) AND $data['flexi_student'] == 1); ?>
            <div class="btn-group btn-group-slide" data-toggle="buttons" id="flexi_student">
                <label class="btn btn-default<?= ($is_flexi_student) ? ' active' : '' ?>">
                    <input type="radio"<?= ($is_flexi_student) ? ' checked="checked"' : '' ?> value="1" name="flexi_student" class="flexi_student yes">Yes
                </label>
                <label class="btn btn-default<?= ( ! $is_flexi_student) ? ' active' : '' ?>">
                    <input type="radio"<?= ( ! $is_flexi_student) ? ' checked="checked"' : '' ?> value="0" name="flexi_student" class="flexi_student no">No
                </label>
            </div>
        </div>
    </div>
</fieldset>