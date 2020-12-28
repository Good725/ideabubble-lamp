<?= (isset($alert)) ? $alert : ''; ?>
<style>
	.form-control.multipleselect + .btn-group {
		max-width: 100%;
	}
	.form-control.multipleselect + .btn-group button {
		background: #fff;
		border-radius: 4px !important;
		height: 32px;
		max-width: 100%;
	}

	.form-control.multipleselect + .btn-group .multiselect-selected-text {
		float: left;
		width: 100%;
		width: -webkit-calc(100% - 5px);
		width: calc(100% - 5px);
		text-overflow: ellipsis;
		overflow: hidden;
	}

</style>
<form class="col-sm-9 form-horizontal" id="discount_edit_form" method="post" action="<?=URL::site();?>admin/courses/save_discount">
    <input type="hidden" name="id" value="<?=$discount->get_id();?>"/>
    <input type="hidden" name="redirect" id="redirect" value="save"/>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="title">Title</label>
        <div class="col-sm-9">
            <input type="text" class="form-control validate[required]" id="title" name="title" value="<?=$discount->get_title();?>">
        </div>
    </div>

	<div class="tab-pane active" id="category_tab">
		<div class="form-group">
			<label class="col-sm-3 control-label" for="summary">Summary</label>
			<div class="col-sm-9">
				<textarea class="form-control validate[required]" id="summary" name="summary" rows="4"><?=$discount->get_summary();?></textarea>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label" for="categories[]">Course Category</label>
			<div class="col-sm-3">
				<select class="form-control multipleselect" name="categories[]" id="categories[]" multiple>
					<?php $array = explode(',', $discount->get_categories()); ?>
					<?php foreach(Model_Categories::get_all_categories() as $key=>$category): ?>
						<option value="<?=$category['id'];?>" <?=(in_array($category['id'],$array)) ? 'selected' : '';?>><?=$category['category'];?></option>
					<?php endforeach; ?>
				</select>
			</div>

            <label class="col-sm-3 control-label" for="schedule_type">Schedule Type</label>
            <div class="col-sm-3">
                <select class="form-control" name="schedule_type" id="schedule_type">
                    <?=html::optionsFromArray(array('Prepay' => 'Prepay', 'PAYG' => 'PAYG', 'Prepay,PAYG' => 'BOTH'), $discount->get_schedule_type())?>
                </select>
            </div>
		</div>

        <?php
        $has_courses = $discount->get_has_courses_details();
        ?>
        <div class="form-group">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="has_courses[0]">Courses</label>
                <div class="col-sm-9">
                    <table id="has_courses" class="col-sm-12" cellpadding="5">
                        <thead>
                        <tr>
                            <td><input type="text" class="form-control course name" placeholder="Course"/><input type="hidden" class="course id" /></td>
                            <td><span>&nbsp;Action&nbsp;</span> <button type="button" class="btn add">Add</button> </td>
                        </tr>
                        </thead>

                        <tbody><?php
                        foreach ($has_courses as $has_course) {
                            ?>
                            <tr>
                                <td><input type="hidden" name="has_courses[]" value="<?=$has_course['id']?>"/><?=$has_course['title']?></td>
                                <td><button type="button" class="btn btn-outline-danger remove" onclick="$(this.parentNode.parentNode).remove()">Remove</button></td>
                            </tr>
                            <?php
                        }
                        ?></tbody>


                    </table>
                </div>
            </div>
        </div>

        <?php
        $has_schedules = $discount->get_has_schedules_details();
        ?>
        <div class="form-group">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="has_schedule[0]">Schedules</label>
                <div class="col-sm-9">
                    <table id="has_schedules" class="col-sm-12" cellpadding="5">
                        <thead>
                        <tr>
                            <td><input type="text" class="form-control schedule name" placeholder="Schedule"/><input type="hidden" class="schedule id" /></td>
                            <td><span>&nbsp;Action&nbsp;</span> <button type="button" class="btn add">Add</button> </td>
                        </tr>
                        </thead>

                        <tbody><?php
                        foreach ($has_schedules as $has_schedule) {
                            ?>
                            <tr>
                                <td><input type="hidden" name="has_schedules[]" value="<?=$has_schedule['id']?>"/><?=$has_schedule['name']?></td>
                                <td><button type="button" class="btn btn-outline-danger remove" onclick="$(this.parentNode.parentNode).remove()">Remove</button></td>
                            </tr>
                            <?php
                        }
                        ?></tbody>


                    </table>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="amount_type">Discount Amount</label>
            <div class="col-sm-3">
                <select class="form-control validate[required]" name="amount_type">
                    <?=html::optionsFromArray(array('Percent' => 'Percent(%)', 'Fixed' => 'Fixed(euro)', 'Quantity' => 'Quantity'), $discount->get_amount_type())?>
                </select>
            </div>
            <div class="col-sm-3">
                <input type="text" name="amount" class="form-control validate[required]" value="<?=$discount->get_amount()?>" />
            </div>
        </div>

        <div class="form-group">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="from">Cart Total From</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" name="from" value="<?=$discount->get_from();?>" />
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="from">Cart Total To</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="to" value="<?=$discount->get_to();?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="item_quantity_min">Min. quantity</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="item_quantity_min" name="item_quantity_min" value="<?=$discount->get_item_quantity_min();?>" />
                </div>

                <label class="col-sm-3 control-label" for="item_quantity_max">Max. quantity</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="item_quantity_max" name="item_quantity_max" value="<?=$discount->get_item_quantity_max();?>" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="item_quantity_scope">Quantity Scope</label>
                <div class="col-sm-9">
                    <select class="form-control" name="item_quantity_scope" id="item_quantity_scope">
                        <option value=""></option>
                        <?=html::optionsFromArray(array('Booking' => 'Booking', 'Family' => 'Family'),$discount->get_item_quantity_scope())?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="min_students_in_family">Min. number of students in family </label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="min_students_in_family" name="min_students_in_family" value="<?=$discount->get_min_students_in_family();?>" />
                </div>

                <label class="col-sm-3 control-label" for="code">Coupon Code</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="code" name="code" value="<?=$discount->get_code();?>" />
                </div>
            </div>
        </div>

        <?php
        $for_contacts = $discount->get_for_contacts_details();
        ?>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="usage_limit">Max Usage Limit</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="usage_limit" name="usage_limit" value="<?=$discount->get_usage_limit();?>" />
            </div>

            <label class="col-sm-3 control-label" for="contact_id[0]">For Contact</label>
            <div class="col-sm-3">
                <input type="text" class="form-control for-contact" id="for_contacts[0]" value="<?=@$for_contacts[0]['fullname'];?>" />
                <input type="hidden" id="for_contacts_0" name="for_contacts[0]" value="<?=@$for_contacts[0]['id'];?>" />
            </div>
        </div>

    	<div class="form-group">
			<label class="col-sm-3 control-label" for="valid_from">Valid From</label>
			<div class="col-sm-3">
				<input type="text" class="form-control validate[required]" id="valid_from" name="valid_from" value="<?=$discount->get_valid_from(true);?>">
			</div>

            <label class="col-sm-3 control-label" for="valid_to">Valid To</label>
            <div class="col-sm-3">
                <input type="text" class="form-control validate[required]" id="valid_to" name="valid_to" value="<?=$discount->get_valid_to(true);?>">
            </div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label" for="discount_publish_toggle">Publish</label>
			<div class="col-sm-9">

				<div class="btn-group" data-toggle="buttons" id="discount_publish_toggle">
					<label class="btn btn-default<?= ($discount->get_publish() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ($discount->get_publish() == 1) ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
					</label>
					<label class="btn btn-default<?= ($discount->get_publish() != 1) ? ' active' : '' ?>">
						<input type="radio"<?= ($discount->get_publish() != 1) ? ' checked="checked"' : '' ?> value="0" name="publish">No
					</label>
				</div>
			</div>

	</div>

	<div class="well form-action-group">
		<button type="button" data-action="save" class="save_btn btn btn-success">Save</button>
		<button data-action="save_and_exit" class="save_btn btn btn-primary">Save &amp; Exit</button>
		<a href="/admin/bookings/list_discounts" id="cancel_button" class="btn-link">Cancel</a>
	</div>
</form>


<div id="percent_over_100_warning" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Invalid discount amount</h3>
            </div>
            <div class="modal-body">
                <p>You can not have a discount amount more than 100%</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Go back</button>
            </div>
        </div>
    </div>
</div>

<script>
	$(function()
	{
		var $from = $('#valid_from');
		var $to = $('#valid_to');

		$from.datetimepicker({
			format:'d-m-Y',
			onShow:function( ct ){
				this.setOptions({
					maxDate: $to.val()?  $to.val().split('-').reverse().join('/') : false
				})
			},
			timepicker:false
		});

		$to.datetimepicker({
			format:'d-m-Y',
			onShow:function( ct ){
				this.setOptions({
					minDate: $from .val() ? $from.val().split('-').reverse().join('/') : false
				})
			},
			timepicker:false
		});
	});
</script>