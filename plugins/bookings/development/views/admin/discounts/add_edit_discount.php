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

    .edit_discount-image_wrapper .file_previews {
        display: none;
    }

    .edit_discount-image_wrapper .saved-image img {
        max-width: 200px;
    }

</style>

<form class="form-horizontal custom-form-horizontal" id="discount_edit_form" method="post" action="/admin/bookings/save_discount">
    <input type="hidden" name="id" value="<?= $discount->get_id(); ?>"/>
    <input type="hidden" name="redirect" id="redirect" value="save"/>
    <?php if (Settings::instance()->get('courses_discounts_image')):?>
        <div class="form-group edit_discount-image_wrapper">
        <div class="col-sm-12 image-upload-wrapper">
            <div>
                <?= View::factory(
                    'multiple_upload',
                    array(
                        'name'        => 'image_id',
                        'single'      => true,
                        'preset'      => 'courses',
                        'onsuccess'   => 'discount_image_uploaded',
                        'presetmodal' => 'no',
                        'duplicate'   => 0
                    )
                ) ?>
            </div>
        </div>

        <div class="col-sm-12 saved-image" id="edit_discount-image">
            <?php $image = $discount->get_image(); ?>
            <input type="hidden" name="image_id" value="<?= $image['id'] ?>" id="edit_discount-image_media_id" />

            <img id="edit_discount-image_saved" class="saved-image<?= ($image['id']) ? '' : ' hidden' ?>" src="<?= $image['url'] ?>" alt="" title="<?__('Click to edit')?>" />

            <button type="button" class="btn-link saved-image-remove hidden">
                <span class="icon-trash"></span>
            </button>
        </div>
    </div>
    <?php endif?>
    <h2 class="border-title"><span>Details</span></h2>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="title">Discount action type</label>
        <div class="col-sm-10 col-md-4">
            <div class="selectbox">
                <select class="form-control" name="action_type" id="action_type">
                    <option
                        value="1" <?= $discount->get_action_type() == 1 || !$discount->get_action_type() ? "selected=\"selected\"" : "" ?>>
                        Regular Discount
                    </option>
                    <option value="2" <?= $discount->get_action_type() == 2 ? "selected=\"selected\"" : "" ?>>Price
                        Plan
                    </option>

                </select>
            </div>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-2 control-label" for="title">Title</label>
        <div class="col-sm-10">
            <input type="text" class="form-control validate[required]" id="title" name="title"
                   value="<?= $discount->get_title(); ?>">
        </div>
    </div>

    <div class="tab-pane active" id="category_tab">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="summary">Summary</label>
            <div class="col-sm-10">
                <textarea class="form-control validate[required]" id="summary" name="summary"
                          rows="4"><?= $discount->get_summary(); ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="student_years[]">Student Years</label>
            <div class="col-sm-10 col-md-4">
                <select class="form-control multipleselect" name="student_years[]" id="student_years[]" multiple>
                    <?php $student_years = $discount->get_student_years(); ?>
                    <?php foreach (Model_Years::get_all_years() as $syear): ?>
                        <option
                            value="<?= $syear['id']; ?>" <?= (in_array($syear['id'], $student_years)) ? 'selected' : ''; ?>><?= $syear['year']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 col-md-2 control-label" for="discount_member_only">Member Only</label>
            <div class="col-sm-10">
                <div class="btn-group btn-group-slide" data-toggle="buttons">
                    <label class="btn btn-plain <?= ($discount->get_member_only() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($discount->get_member_only() == 1) ? ' checked="checked"' : '' ?> value="1"
                               name="member_only">Yes
                    </label>
                    <label class="btn btn-plain <?= ($discount->get_member_only() != 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($discount->get_member_only() != 1) ? ' checked="checked"' : '' ?> value="0"
                               name="member_only">No
                    </label>
                </div>
            </div>
        </div>

        <?php
        $has_courses = $discount->get_has_courses_details();
        $has_previous_courses = $discount->get_previous_discount_condition_course_details();
        $has_schedules = $discount->get_has_schedules_details();
        $has_previous_schedules = $discount->get_previous_discount_condition_schedule_details();
        $has_previous_categories = $discount->get_previous_discount_condition_category_details();
        ?>

        <h2 class="border-title"><span>Course and Schedule</span></h2>


        <div class="form-group">

            <div class="col-sm-12 col-md-6">
                <table id="has_courses" class="col-sm-12" cellpadding="5">
                    <thead>
                    <tr>
                        <td>
                            <label class="control-label align-left" for="has_courses[0]">Courses</label>
                            <input type="text" class="form-control course name" placeholder="Course"/><input
                                type="hidden" class="course id"/></td>
                        <td><label class="control-label">Action</label>
                            <button type="button" class="btn add">Add</button>
                        </td>
                    </tr>
                    </thead>

                    <tbody><?php
                    foreach ($has_courses as $has_course) {
                        ?>
                        <tr>
                            <td><input type="hidden" name="has_courses[]"
                                       value="<?= $has_course['id'] ?>"/><?= $has_course['title'] ?></td>
                            <td>
                                <button type="button" class="btn btn-outline-danger remove"
                                        onclick="$(this.parentNode.parentNode).remove()">Remove
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?></tbody>
                </table>
            </div>
            <div class="col-sm-12 col-md-6">
                <table id="has_schedules" class="col-sm-12" cellpadding="5">
                    <thead>
                    <tr>
                        <td><label class="control-label" for="has_schedule[0]">Schedules</label>
                            <input type="text" class="form-control schedule name" placeholder="Schedule"/><input
                                type="hidden" class="schedule id"/></td>
                        <td><label class="control-label">Action&nbsp;</label>
                            <button type="button" class="btn add">Add</button>
                        </td>
                    </tr>
                    </thead>

                    <tbody><?php
                    foreach ($has_schedules as $has_schedule) {
                        ?>
                        <tr>
                            <td class="schedule"><input type="hidden" name="has_schedules[]"
                                       value="<?= $has_schedule['id'] ?>"/><?= $has_schedule['name'] ?></td>
                            <td>
                                <button type="button" class="btn btn-outline-danger remove"
                                        onclick="$(this.parentNode.parentNode).remove()">Remove
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?></tbody>


                </table>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="categories[]" style="text-align: left;">Course Category</label>
            <div class="col-sm-2 col-md-4">
                <select class="form-control multipleselect" name="categories[]" id="categories[]" multiple>
                    <?php $array = explode(',', $discount->get_categories()); ?>
                    <?php foreach (Model_Categories::get_all_categories() as $key => $category): ?>
                        <option
                                value="<?= $category['id']; ?>" <?= (in_array($category['id'], $array)) ? 'selected' : ''; ?>><?= $category['category']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <label class="col-sm-2  control-label" for="schedule_type">Schedule Type</label>
            <div class="col-sm-2 col-md-4">
                <div class="selectbox">
                    <select class="form-control" name="schedule_type" id="schedule_type">
                        <?= html::optionsFromArray(array('Prepay' => 'Prepay', 'PAYG' => 'PAYG', 'Prepay,PAYG' => 'BOTH'), $discount->get_schedule_type()) ?>
                    </select>
                </div>
            </div>

        </div>

        <div class="form-group">

            <div class="col-sm-6">
                <label class="control-label" for="course_date_from">Course Date From</label>

                <?php
                $value = $discount->get_course_date_from() ? date::ymd_to_dmy($discount->get_course_date_from()) : '';
                echo Form::ib_input(null, 'course_date_from', $value, ['id' => 'course_date_from'], ['icon' => '<span class="flaticon-calendar-1"></span>']);
                ?>
            </div>


            <div class="col-sm-6">
                <label class="control-label" for="course_date_to">Course Date To</label>

                <?php
                $value = $discount->get_course_date_to() ? date::ymd_to_dmy($discount->get_course_date_to()) : '';
                echo Form::ib_input(null, 'course_date_to', $value, ['id' => 'course_date_to'], ['icon' => '<span class="flaticon-calendar-1"></span>']);
                ?>
            </div>
        </div>

        <div class="form-group">

            <div class="col-sm-6">
                <label class="control-label" for="class_time_from">Class Time From</label>

                <?php
                $value = $discount->get_class_time_from() ? date::ymd_to_dmy($discount->get_class_time_from()) : '';
                echo Form::ib_input(null, 'class_date_from', $value, ['id' => 'class_date_from'], ['icon' => '<span class="flaticon-calendar-1"></span>']);
                ?>
            </div>


            <div class="col-sm-6">
                <label class="control-label" for="class_time_to">Class Time To</label>

                <?php
                $value = $discount->get_class_time_to() ? date::ymd_to_dmy($discount->get_class_time_to()) : '';
                echo Form::ib_input(null, 'class_date_to', $value, ['id' => 'class_date_to'], ['icon' => '<span class="flaticon-calendar-1"></span>']);
                ?>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="amount_type">Discount Amount</label>
            <div class="col-sm-2">
                <div class="selectbox">
                    <select class="form-control validate[required]" name="amount_type">
                        <?= html::optionsFromArray(array('Percent' => 'Percent(%)', 'Fixed' => 'Fixed(euro)', 'Quantity' => 'Quantity'), $discount->get_amount_type()) ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="selectbox">
                    <select class="form-control validate[required]" name="use_limits" id="use_limits">
                        <?php
                        if (count($discount->get_qty_rates()) > 0) {
                            $use_limits = 'QTY';
                        } else if (count($discount->get_daily_rates()) > 0 || count($discount->get_per_day_rates()) > 0) {
                            $use_limits = 'MAX';
                        } else {
                            $use_limits = 'Regular';
                        }
                        ?>
                        <?= html::optionsFromArray(
                            array(
                                'Regular' => 'Regular Discount',
                                'MAX' => 'Daily Max Price List',
                                'QTY' => 'Quantity Limit List'
                            ),
                            $use_limits
                        ) ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="selectbox">
                    <select class="form-control validate[required]" name="apply_to" id="apply_to">
                        <?= html::optionsFromArray(
                            array(
                                'Schedule' => 'For Each Schedule',
                                'Cart' => 'For Cart Total'
                            ),
                            $discount->get_apply_to()
                        ) ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <input type="text" name="amount" id="amount" class="form-control validate[required]"
                       value="<?= $discount->get_amount() ?>" style="<?=count($discount->get_daily_rates()) >= 1 || count($discount->get_per_day_rates()) >= 1 ? 'display:none' : ''?>"/>

                <table class="table" id="qty_rates" style="<?=count($discount->get_qty_rates()) == 0 ? 'display:none' : ''?>">
                    <thead>
                    <tr>
                        <th colspan="5">Quantity Fees</th>
                    </tr>
                    <tr>
                        <th>Min Quantity</th>
                        <th>Max Quantity</th>
                        <th>Total Fee</th>
                        <th><button type="button" class="add">Add</button> </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="template">
                        <td>
                            <input type="text" name="qty_rates[index][min_qty]" value="" size="2" />
                        </td>
                        <td>
                            <input type="text" name="qty_rates[index][max_qty]" value="" size="2" />
                        </td>
                        <td>
                            <input type="text" name="qty_rates[index][amount]" value="" size="4" />
                        </td>
                        <td>
                            <input type="hidden" name="qty_rates[index][id]" value="" />
                            <button type="button" class="remove">Remove</button>
                        </td>
                    </tr>
                    <?php
                    foreach ($discount->get_qty_rates() as $i => $qty_rate) {
                        ?>
                        <tr>
                            <td>
                                <input type="text" name="qty_rates[<?=$i?>][min_qty]" value="<?=$qty_rate['min_qty']?>" size="2" />
                            </td>
                            <td>
                                <input type="text" name="qty_rates[<?=$i?>][max_qty]" value="<?=$qty_rate['max_qty']?>" size="2" />
                            </td>
                            <td>
                                <input type="text" name="qty_rates[<?=$i?>][amount]" value="<?=$qty_rate['amount']?>" size="4" />
                            </td>
                            <td>
                                <input type="hidden" name="qty_rates[<?=$i?>][id]" value="<?=$qty_rate['id']?>" />
                                <button type="button" class="remove">Remove</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>

                <br clear="both" />
                <br />

                <table class="table" id="daily_rates" style="<?=count($discount->get_daily_rates()) == 0 && count($discount->get_per_day_rates()) == 0 ? 'display:none' : ''?>">
                    <thead>
                    <tr>
                        <th colspan="5">Max Daily Fees</th>
                    </tr>
                    <tr>
                        <th>Min Days</th>
                        <th>Max Days</th>
                        <th>Is Consecutive</th>
                        <th>Max Fee</th>
                        <th><button type="button" class="add">Add</button> </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="template">
                            <td>
                                <input type="text" name="daily_rates[index][min_days]" value="" size="2" />
                            </td>
                            <td>
                                <input type="text" name="daily_rates[index][max_days]" value="" size="2" />
                            </td>
                            <td>
                                <input type="checkbox" name="daily_rates[index][is_consecutive]" value="1" />
                            </td>
                            <td>
                                <input type="text" name="daily_rates[index][amount]" value="" size="4" />
                            </td>
                            <td>
                                <input type="hidden" name="daily_rates[index][id]" value="" />
                                <button type="button" class="remove">Remove</button>
                            </td>
                        </tr>
                        <?php
                        foreach ($discount->get_daily_rates() as $i => $daily_rate) {
                        ?>
                            <tr>
                                <td>
                                    <input type="text" name="daily_rates[<?=$i?>][min_days]" value="<?=$daily_rate['min_days']?>" size="2" />
                                </td>
                                <td>
                                    <input type="text" name="daily_rates[<?=$i?>][max_days]" value="<?=$daily_rate['max_days']?>" size="2" />
                                </td>
                                <td>
                                    <input type="checkbox" name="daily_rates[<?=$i?>][is_consecutive]" value="1" <?=$daily_rate['is_consecutive'] == 1 ? 'checked="checked"' : ''?>/>
                                </td>
                                <td>
                                    <input type="text" name="daily_rates[<?=$i?>][amount]" value="<?=$daily_rate['amount']?>" size="4" />
                                </td>
                                <td>
                                    <input type="hidden" name="daily_rates[<?=$i?>][id]" value="<?=$daily_rate['id']?>" />
                                    <button type="button" class="remove">Remove</button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

                <br clear="both" />
                <br />

                <table class="table" id="per_day_rates" style="<?=count($discount->get_daily_rates()) == 0 && count($discount->get_per_day_rates()) == 0 ? 'display:none' : ''?>">
                    <thead>
                    <tr>
                        <th colspan="5">Max Fee Per Day</th>
                    </tr>
                    <tr>
                        <th>Min Classes</th>
                        <th>Max Classes</th>
                        <th>Max Fee</th>
                        <th><button type="button" class="add">Add</button> </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="template">
                        <td>
                            <input type="text" name="per_day_rates[index][min_timeslots]" value="" size="2" />
                        </td>
                        <td>
                            <input type="text" name="per_day_rates[index][max_timeslots]" value="" size="2" />
                        </td>
                        <td>
                            <input type="text" name="per_day_rates[index][amount]" value="" size="4" />
                        </td>
                        <td>
                            <input type="hidden" name="per_day_rates[index][id]" value="" />
                            <button type="button" class="remove">Remove</button>
                        </td>
                    </tr>
                    <?php
                    foreach ($discount->get_per_day_rates() as $i => $per_day_rate) {
                        ?>
                        <tr>
                            <td>
                                <input type="text" name="per_day_rates[<?=$i?>][min_timeslots]" value="<?=$per_day_rate['min_timeslots']?>" size="2" />
                            </td>
                            <td>
                                <input type="text" name="per_day_rates[<?=$i?>][max_timeslots]" value="<?=$per_day_rate['max_timeslots']?>" size="2" />
                            </td>
                            <td>
                                <input type="text" name="per_day_rates[<?=$i?>][amount]" value="<?=$per_day_rate['amount']?>" size="4" />
                            </td>
                            <td>
                                <input type="hidden" name="per_day_rates[<?=$i?>][id]" value="<?=$per_day_rate['id']?>" />
                                <button type="button" class="remove">Remove</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3">Application Type</label>
            <div class="col-sm-3">
                <div class="selectbox">
                    <select class="form-control validate[required]" name="application_type">
                        <?= html::optionsFromArray(array('initial' => 'Apply to initial amount', 'latest' => 'Apply to latest amount'), $discount->get_application_type()) ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3">Application Order</label>
            <div class="col-sm-3">
                <div class="selectbox">
                    <select class="form-control" name="application_order">
                        <?php for ($i=1; $i<=10; $i++):?>
                            <option value="<?=$i?>" <?php if($discount->get_application_order() == $i):?> selected="selected" <?php endif?>> <?=$i?></option>
                        <?php endfor;?>

                    </select>
                </div>
            </div>
        </div>


        <div class="form-group">

            <div class="col-sm-6">
                <label class="control-label" for="from">Cart Total From</label>
                <input type="text" class="form-control" name="from" value="<?= $discount->get_from(); ?>"/>
            </div>
            <div class="col-sm-6">
                <label class="control-label" for="from">Cart Total To</label>
                <input type="text" class="form-control" name="to" value="<?= $discount->get_to(); ?>"/>
            </div>

        </div>


        <h2 class="border-title"><span>Quantity</span></h2>


        <div class="form-group">

            <div class="col-sm-6">
                <label class="control-label" for="item_quantity_type">Item</label>
                <div class="selectbox">
                    <select class="form-control" name="item_quantity_type" id="item_quantity_type">
                        <?= html::optionsFromArray(array('Courses' => 'Courses', 'Classes' => 'Classes'), $discount->get_item_quantity_type()) ?>
                    </select>
                </div>
            </div>

        </div>

        <div class="form-group">

            <div class="col-sm-6">
                <label class="control-label" for="item_quantity_scope">Applies To</label>
                <div class="selectbox">
                    <select class="form-control" name="item_quantity_scope" id="item_quantity_scope">
                        <option value=""></option>
                        <?= html::optionsFromArray(array('Schedule' => 'Course/Class', 'Booking' => 'Booking', 'Contact' => 'Student', 'Family' => 'Family'), $discount->get_item_quantity_scope()) ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label class="control-label" for="min_students_in_family">Min. number of students in family </label>
                <input type="text" class="form-control" id="min_students_in_family" name="min_students_in_family"
                       value="<?= $discount->get_min_students_in_family(); ?>"/>
            </div>
        </div>

        <h2 class="border-title"><span>Limits</span></h2>
        <?php
        $for_contacts = $discount->get_for_contacts_details();
        ?>
        <div class="form-group">
            <div class="col-sm-6">
                <label class="control-label" for="code">Coupon Code</label>
                <input type="text" class="form-control" id="code" name="code" value="<?= $discount->get_code(); ?>"/>
            </div>
            <div class="col-sm-6">
                <label class="control-label" for="contact_id[0]">For Contact</label>
                <input type="text" class="form-control for-contact" id="for_contacts[0]"
                       value="<?= @$for_contacts[0]['fullname']; ?>"/>
                <input type="hidden" id="for_contacts_0" name="for_contacts[0]"
                       value="<?= @$for_contacts[0]['id']; ?>"/>
            </div>

        </div>
        <div class="form-group">
            <div class="col-sm-6">
                <label class="control-label" for="usage_limit">Max Usage Limit</label>
                <input type="text" class="form-control" id="usage_limit" name="usage_limit"
                       value="<?= $discount->get_usage_limit(); ?>"/>
            </div>
            <div class="col-sm-6">
                <label class="control-label" for="max_usage_per">Max Usage Per</label>
                <div class="selectbox">
                    <select class="form-control" name="max_usage_per" id="max_usage_per">
                        <option value=""></option>
                        <?= html::optionsFromArray(array('Cart' => 'Cart', 'Contact' => 'Student', 'Family' => 'Family', 'GLOBAL' => 'GLOBAL'), $discount->get_max_usage_per()) ?>
                    </select>
                </div>
            </div>
        </div>

        <h2 class="border-title"><span>Previous Booking conditions</span></h2>
        <!--#################################################################################-->

        <div class="form-group">

            <div class="col-sm-12 col-md-6">
                <table id="has_previous_courses" class="col-sm-12" cellpadding="5">
                    <thead>
                    <tr>
                        <td>
                            <label class="control-label align-left" for="has_previous_courses[0]">Courses</label>
                            <input type="text" class="form-control course name" placeholder="Course"/><input
                                type="hidden" class="course id"/></td>
                        <td><label class="control-label">Action</label>
                            <button type="button" class="btn add previous">Add</button>
                        </td>
                    </tr>
                    </thead>

                    <tbody><?php
                    foreach ($has_previous_courses as $has_course) {
                        ?>
                        <tr>
                            <td><input type="hidden" name="has_previous_courses[]"
                                       value="<?= $has_course['id'] ?>"/><?= $has_course['title'] ?></td>
                            <td>
                                <button type="button" class="btn btn-outline-danger remove"
                                        onclick="$(this.parentNode.parentNode).remove()">Remove
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?></tbody>
                </table>
            </div>
            <div class="col-sm-12 col-md-6">
                <table id="has_previous_schedules" class="col-sm-12" cellpadding="5">
                    <thead>
                    <tr>
                        <td><label class="control-label" for="has_previous_schedules[0]">Schedules</label>
                            <input type="text" class="form-control schedule name" placeholder="Schedule"/><input
                                type="hidden" class="schedule id"/></td>
                        <td><label class="control-label">Action&nbsp;</label>
                            <button type="button" class="btn add previous">Add</button>
                        </td>
                    </tr>
                    </thead>

                    <tbody><?php
                    foreach ($has_previous_schedules as $has_schedule) {
                        ?>
                        <tr>
                            <td><input type="hidden" name="has_previous_schedules[]"
                                       value="<?= $has_schedule['id'] ?>"/><?= $has_schedule['name'] ?></td>
                            <td>
                                <button type="button" class="btn btn-outline-danger remove"
                                        onclick="$(this.parentNode.parentNode).remove()">Remove
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?></tbody>


                </table>
            </div>
        </div>
        <div class="form-group">


            <div class="col-sm-12 col-md-6">
                <table id="has_previous_category" class="col-sm-12" cellpadding="5">
                    <thead>
                    <tr>
                        <td>
                            <label class="control-label align-left" for="has_previous_category[0]">Category</label>
                            <input type="text" class="form-control category name" placeholder="Category"/><input
                                type="hidden" class="category id"/></td>
                        <td><label class="control-label">Action</label>
                            <button type="button" class="btn add previous">Add</button>
                        </td>
                    </tr>
                    </thead>

                    <tbody><?php
                    foreach ($has_previous_categories as $has_category) {
                        ?>
                        <tr>
                            <td><input type="hidden" name="has_previous_category[]"
                                       value="<?= $has_category['id'] ?>"/><?= $has_category['category'] ?></td>
                            <td>
                                <button type="button" class="btn btn-outline-danger remove"
                                        onclick="$(this.parentNode.parentNode).remove()">Remove
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?></tbody>
                </table>
            </div>
        </div>

            <div class="form-group">

                <div class="col-sm-6">
                    <label class="control-label" for="previous_term_from">Previous Term From</label>

                    <?php
                    $value = $discount->get_previous_term_from(true);
                    echo Form::ib_input(null, 'previous_term_paid_from', $value, ['id' => 'previous_term_from'], ['icon' => '<span class="flaticon-calendar-1"></span>']);
                    ?>
                </div>


                <div class="col-sm-6">
                    <label class="control-label" for="previous_term_to">Previous Term To</label>

                    <?php
                    $value = $discount->get_previous_term_to(true);
                    echo Form::ib_input(null, 'previous_term_paid_to', $value, ['id' => 'previous_term_to'], ['icon' => '<span class="flaticon-calendar-1"></span>']);
                    ?>
                </div>
            </div>
        <!--#################################################################################-->
        <h2 class="border-title"><span>Validation</span></h2>
        <div class="form-group">

            <div class="col-sm-6">
                <label class="control-label" for="valid_from">Valid From</label>

                <?php
                $value = $discount->get_valid_from(true);
                $attributes = ['class' => 'validate[required]', 'id' => 'valid_from'];
                echo Form::ib_input(null, 'valid_from', $value, $attributes, ['icon' => '<span class="flaticon-calendar-1"></span>']);
                ?>
            </div>


            <div class="col-sm-6">
                <label class="control-label" for="valid_to">Valid To</label>

                <?php
                $value = $discount->get_valid_to(true);
                $attributes = ['class' => 'validate[required]', 'id' => 'valid_to'];
                echo Form::ib_input(null, 'valid_to', $value, $attributes, ['icon' => '<span class="flaticon-calendar-1"></span>']);
                ?>
            </div>
        </div>

        <!--
        <div class="form-group">

            <div class="col-sm-6">
                <label class="control-label" for="min_days">Min. days</label>
                <input type="text" class="form-control" id="min_days" name="min_days"
                       value="<?= $discount->get_min_days(); ?>"/>
            </div>


            <div class="col-sm-6">
                <label class="control-label" for="min_days_is_consecutive">Is consecutive</label>
                <input type="checkbox" class="form-control" id="min_days_is_consecutive" name="min_days_is_consecutive"
                       <?= $discount->get_min_days_is_consecutive() == 1 ? 'checked="checked"' : ''; ?> value="1" />
            </div>
        </div>
         -->

        <div class="form-group">

            <div class="col-sm-6">
                <label class="control-label" for="days_of_the_week">Required Days Of Week</label>
                <select class="form-control multipleselect" name="days_of_the_week[]" id="days_of_the_week" multiple>
                    <?=html::optionsFromArray(
                        array(
                            'Monday' => 'Monday',
                            'Tuesday' => 'Tuesday',
                            'Wednesday' => 'Wednesday',
                            'Thursday' => 'Thursday',
                            'Friday' => 'Friday',
                            'Saturday' => 'Saturday',
                            'Sunday' => 'Sunday'
                        ),
                        $discount->get_days_of_the_week()
                    )?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 col-md-2 control-label" for="discount_publish_toggle">Publish</label>
            <div class="col-sm-10">
                <div class="btn-group btn-group-slide" data-toggle="buttons">
                    <label class="btn btn-plain <?= ($discount->get_publish() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($discount->get_publish() == 1) ? ' checked="checked"' : '' ?> value="1"
                               name="publish">Yes
                    </label>
                    <label class="btn btn-plain <?= ($discount->get_publish() != 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($discount->get_publish() != 1) ? ' checked="checked"' : '' ?> value="0"
                               name="publish">No
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 col-md-2 control-label" for="discount_publish_on_web_toggle">Publish On Web</label>
            <div class="col-sm-10">
                <div class="btn-group btn-group-slide" data-toggle="buttons">
                    <label class="btn btn-plain <?= ($discount->get_publish_on_web() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($discount->get_publish_on_web() == 1) ? ' checked="checked"' : '' ?> value="1"
                               name="publish_on_web">Yes
                    </label>
                    <label class="btn btn-plain <?= ($discount->get_publish_on_web() != 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($discount->get_publish_on_web() != 1) ? ' checked="checked"' : '' ?> value="0"
                               name="publish_on_web">No
                    </label>
                </div>
            </div>
        </div>


        <div class="well form-action-group">
            <button type="button" data-action="save" class="save_btn btn  btn-primary">Save</button>
            <button data-action="save_and_exit" class="save_btn btn btn-success">Save &amp; Exit</button>
            <a href="/admin/bookings/list_discounts" id="cancel_button" class="btn btn-cancel">Cancel</a>
        </div>
</form>


<div id="percent_over_100_warning" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
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

<div id="schedule_details_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Schedule Details</h3>
            </div>
            <div class="modal-body">
                <label>Fee Per : </label><span class="fee_per"></span><br />
                <label>Fee : </label><span class="fee"></span><br />
                <br />
                <div id="schedule_details_dates" style="max-height: 500px; overflow: auto;">
                    <table>
                        <thead>
                        <tr>
                            <th scope="col">Order</th>
                            <th scope="col" class="column-date">Day</th>
                            <th scope="col" class="column-date">Date</th>
                            <th scope="col">Price</th>
                            <th scope="col">Start Time</th>
                            <th scope="col">End Time</th>
                            <th scope="col">Trainer <button id="timeslot-trainers-reset" type="button" class="btn btn-default">Reset All</button></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hide</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        var $from = $('#valid_from');
        var $to = $('#valid_to');
        var $previous_from = $('#previous_term_from');
        var $previous_to = $('#previous_term_to');
        var $class_date_from = $('#course_date_from');
        var $class_date_to = $('#course_date_to');
        var $class_time_from = $('#class_time_from');
        var $class_time_to = $('#class_time_to');

        $from.datetimepicker({
            format: 'd-m-Y',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: $to.val() ? $to.val().split('-').reverse().join('/') : false
                })
            },
            timepicker: false
        });

        $to.datetimepicker({
            format: 'd-m-Y',
            onShow: function (ct) {
                this.setOptions({
                    minDate: $from.val() ? $from.val().split('-').reverse().join('/') : false
                })
            },
            timepicker: false
        });


        $previous_to.datetimepicker({
            format: 'd-m-Y',
            onShow: function (ct) {
                this.setOptions({

                })
            },
            timepicker: false
        });


        $previous_from.datetimepicker({
            format: 'd-m-Y',
            onShow: function (ct) {
                this.setOptions({

                })
            },
            timepicker: false
        });

        $class_date_from.datetimepicker({
            format: 'd-m-Y',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: $class_date_to.val() ? $class_date_to.val().split('-').reverse().join('/') : false
                })
            },
            timepicker: false
        });

        $class_date_to.datetimepicker({
            format: 'd-m-Y',
            onShow: function (ct) {
                this.setOptions({
                    minDate: $class_date_from.val() ? $class_date_from.val().split('-').reverse().join('/') : false
                })
            },
            timepicker: false
        });

        $class_time_from.datetimepicker({
            format: 'H:i',
            onShow: function (ct) {

            },
            datepicker: false
        });

        $class_time_to.datetimepicker({
            format: 'H:i',
            onShow: function (ct) {

            },
            datepicker: false
        });
    });

    $("#course_date_from, #course_date_to, #previous_term_from, #previous_term_to, #valid_from, #valid_to").bind("mousewheel", function () {
        if ($.browser.webkit === true) {
            return false;
        }
    });
</script>
