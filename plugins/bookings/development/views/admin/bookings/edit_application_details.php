<form class="form-horizontal" id="fulltime-course-application-edit" name="fulltime-course-application-edit" method="post">
    <input type="hidden" name="application_id" value="<?=$application['id']?>" />

    <?php
    if ($application['interview_status'] != null) {
        $is_interview = 1;
    }
    if (in_array($custom_checkout, ['bc_language', 'sls']) || Kohana::$config->load('config')->fulltime_course_booking_enable) {
        include Kohana::find_file('views', 'application_student_education');
    }
    if ($application['interview_status'] == null || in_array($custom_checkout, ['bc_language', 'sls'])) {
        if (in_array($custom_checkout, ['bc_language'])) {
            include Kohana::find_file('views', 'application_study_routine');
        }
        if (in_array($custom_checkout, ['sls'])) {
            require Kohana::find_file('views', 'application_travel_details');
        }
    ?>

    <input type="hidden" name="contact_id" value="<?=$contact_id?>" />
    <?php
    if (in_array($custom_checkout, ['lsm'])) {
    ?>
        <div class="form-group">
            <div class="col-xs-12">
                <div class="form-group vertically_center">
                    <div class="col-xs-6 col-sm-4">
                        <?= __('Instrument?') ?>
                    </div>

                    <div class="col-xs-6 col-sm-3">
                    <?php
                    $subjects = array('' => '');
                    foreach (Model_Subjects::get_all_subjects() as $subject) {
                        $subjects[$subject['id']] = $subject['name'];
                    }
                    $attributes = array('class' => 'stay_inline', 'style' => 'font-size: .625em;');
                    echo Form::select('application[subject_id]', $subjects, @$application['data']['subject_id'], $attributes);

                    ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <div class="form-group vertically_center">
                    <div class="col-xs-6 col-sm-4">
                        <?= __('Grade') ?>
                    </div>

                    <div class="col-xs-6 col-sm-3">
                        <?php
                        $levels = array('' => '');
                        foreach (Model_Levels::get_all_levels() as $level) {
                            $levels[$level['id']] = $level['level'];
                        }

                        $attributes = array('class' => 'stay_inline', 'style' => 'font-size: .625em;');
                        echo Form::select('application[grade]', $levels, @$application['data']['grade'], $attributes);

                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <?php if (!@Kohana::$config->load('config')->fulltime_course_booking_enable) { ?>
    <div class="">
        <div class="edit_heading edit_heading-nested">
            <div class="edit_heading-left">
                <h2>Application #<?= $application['id'] ?></h2>
            </div>
        </div>

        <div>
            <table class="table" id="application_linked_schedules">
                <thead>
                    <tr>
                        <th scope="col"><?= __('Course')   ?></th>
                        <th scope="col"><?= __('Schedule') ?></th>
                        <th scope="col"><?= __('Actions')  ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($application['schedules'])): ?>
                        <tr>
                            <td>
                                <label class="form-select">
                                    <select class="form-input form-input--select" name="has_course_id">
                                        <option value="" data-fulltime_price="">-- <?= htmlspecialchars(__('Select a course')) ?> --</option>
                                        <?php
                                        if (!empty($application['courses'][0])) {
                                            $selected_course_id = $application['courses'][0]['course_id'];
                                        } else if (!empty($application['extra_data']['has_course_id'])) {
                                            $selected_course_id = $application['extra_data']['has_course_id'];
                                        } else {
                                            $selected_course_id = null;
                                        }
                                        ?>

                                        <?php foreach (Model_Courses::get_all() as $course): ?>
                                            <option
                                                value="<?=$course['id']?>"
                                                <?= $course['id'] == $selected_course_id ? 'selected="selected"' : '' ?>
                                                data-fulltime_price="<?= $course['fulltime_price'] ?>"
                                            ><?= htmlspecialchars($course['title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                            </td>
                            <td>
                                <label class="form-select">
                                    <select class="form-input form-input--select" name="has_schedule_id">
                                        <?php
                                        $schedules = Model_Courses::get_booking_search_term([
                                            'all_time'   => true,
                                            'course_id'  => $selected_course_id,
                                            'ignore_fee' => true
                                        ]);

                                        echo html::optionsFromRows(
                                            'id',
                                            'label',
                                            $schedules,
                                            @$application['extra_data']['has_schedule_id'],
                                            ['value' => '', 'label' => '-- Select schedule --']
                                        );
                                        ?>
                                    </select>
                                </label>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary form-btn update hidden" value="save"><?= __('Save') ?></button>
                                <button type="button" class="btn btn-primary form-btn move" value="move"><?= __('Move') ?></button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($application['schedules'] as $schedule): ?>
                            <tr>
                                <td><?=$schedule['course']?></td>
                                <td><?=$schedule['id']?></td>
                                <td><?=$schedule['name']?></td>
                                <td><input type="checkbox" name="has_schedule[]" value="<?=$schedule['id']?>" <?=in_array($schedule['id'], @$application['assigned_schedules'] ?: array()) ? 'checked="checked"' : ''?> /> </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif; ?>
                    <?php if (in_array($custom_checkout, ['lsm'])) { ?>
                        <tr>
                            <td colspan="4">
                                <table class="table" id="schedule_period_select">
                                    <thead>
                                        <tr>
                                            <th>Period</th>
                                            <th>Trainer</th>
                                            <th>Capacity</th>
                                            <th>Bookings</th>
                                            <th>Assign</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (count($application['assigned_schedules']) > 0) {
                                        $periods = Model_ScheduleEvent::get_periods($application['assigned_schedules']);
                                        foreach ($periods as $period) {
                                            $period_id = $period['period'] . ',' . $period['trainer_id'];
                                            $checked = false;
                                            if (@$application['data']['has_period']) {
                                                $checked = in_array($period_id, $application['data']['has_period']);
                                            }
                                    ?>
                                        <tr>
                                            <td><?=$period['period'] . ' - ' . $period['period_end']?></td>
                                            <td><?=$period['trainer']?></td>
                                            <td><?=$period['max_capacity']?></td>
                                            <td><?=$period['booking_count']?></td>
                                            <td><input type="checkbox" name="application[has_period][]" value="<?=$period_id?>" <?=$checked ? 'checked="checked"' : ''?> /> </td>
                                        </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if (count($transactions) == 0) { ?>
        <div class="form-group">
            <div class="col-xs-12">
                <div class="form-group vertically_center">
                    <div class="col-xs-6 col-sm-4">
                        <?= __('Create Transaction?') ?>
                    </div>

                    <div class="col-xs-6 col-sm-3">
                        <?php
                        $options = array('Yes' => __('Yes'), 'No' => __('No'));
                        $attributes = array('class' => 'stay_inline', 'style' => 'font-size: .625em;');
                        echo Form::btn_options('create_transaction', $options, 'No', false, array(), $attributes);
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 hidden new_transaction_details">
                <div class="form-group vertically_center">
                    <div class="col-xs-6 col-sm-4">
                        <?= __('Transaction Amount?') ?>
                    </div>

                    <div class="col-xs-6 col-sm-3">
                        <?php
                        echo Form::ib_input(null, 'transaction_amount', '')
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

        <?php if (in_array($custom_checkout, ['bc_language', ''])) { ?>
        <?php if (@$application['status_id'] != 'new') { ?>
            <div class="">
                <h3><?= __('Schedule Assignment') ?></h3>

                <div>
                    <table class="table" id="application_linked_schedules">
                        <thead>
                        <tr>
                            <th scope="col"><?= __('Schedule ID') ?></th>
                            <th scope="col"><?= __('Schedule') ?></th>
                            <th scope="col"><?= __('Course') ?></th>
                            <th scope="col"><?= __('Assign') ?></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if (empty($application['ft_schedules'])): ?>
                            <tr>
                                <th colspan="4"><?=__('No schedule has been linked to the course')?></th>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($application['ft_schedules'] as $schedule): ?>
                                <tr>
                                    <td><?=$schedule['id']?></td>
                                    <td><?=$schedule['name']?></td>
                                    <td><?=$schedule['course']?></td>
                                    <td><input type="checkbox" name="has_schedule[]" value="<?=$schedule['id']?>" <?=in_array($schedule['id'], @$application['assigned_schedules'] ?: array()) ? 'checked="checked"' : ''?> /> </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
        <?php } ?>

        <?php if (Kohana::$config->load('config')->fulltime_course_booking_enable) { ?>
            <?php if (@$application['status_id'] == '1' || @$application['status_id'] == 'new' || empty($application['status_id'])) { ?>
        <div class="">
            <h3><?= __('Course Assignment')?></h3>

            <div class="form-group">
                <div class="col-xs-12">
                    <label>Course</label>

                    <select class="form-control" name="fulltime_course_id">
                        <option value="" data-fulltime_price=""><?=__('Select a course')?></option>
                    <?php
                    foreach (Model_Courses::get_all(array('is_fulltime' => 'YES')) as $ftcourse) {
                    ?>
                        <option
                            value="<?= $ftcourse['id'] ?>"
                            data-fulltime_price="<?=$ftcourse['fulltime_price']?>"
                            <?= $ftcourse['id'] == @$application['courses'][0]['course_id'] ? ' selected="selected"' : '' ?>
                        ><?=$ftcourse['title']?></option>
                    <?php
                    }
                    ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-xs-12">
                    <div class="form-group vertically_center">
                        <div class="col-xs-6 col-sm-4">
                            <?= __('Create Transaction?') ?>
                        </div>

                        <div class="col-xs-6 col-sm-3">
                            <?php
                            $options = array('Yes' => __('Yes'), 'No' => __('No'));
                            $attributes = array('class' => 'stay_inline', 'style' => 'font-size: .625em;');
                            echo Form::btn_options('create_ftcourse_transaction', $options, 'No', false, array(), $attributes);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 hidden new_ftcourse_transaction_details">
                    <div class="form-group vertically_center">
                        <div class="col-xs-6 col-sm-4">
                            <?= __('Transaction Amount?') ?>
                        </div>

                        <div class="col-xs-6 col-sm-3">
                            <?php
                            echo Form::ib_input(null, 'ftcourse_transaction_amount', '')
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php } ?>
            <div class="col-sm-12">
                <div class="form-group">
                    <?=Form::ib_checkbox("Send Email", "send_email", "yes")?>
                </div>
            </div>
        <?php } ?>
    <?php
    } else {
    ?>
        <div class="">
            <h3><?= __('Interview Details') ?></h3>

            <div>
                <table class="table dataTable-collapse" id="application_linked_schedules">
                    <thead>
                    <tr>
                        <th scope="col"><?= __('Schedule ID') ?></th>
                        <th scope="col"><?= __('Schedule') ?></th>
                        <th scope="col"><?= __('Course') ?></th>
                        <th scope="col"><?= __('Interview date') ?></th>
                        <th scope="col"><?= __('Status') ?></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if (count($application['schedules']) == 0): ?>
                        <tr>
                            <th colspan="5"><?=__('No schedule has been linked to the course')?></th>
                        </tr>
                        <tr>
                            <td data-label="Schedule ID"></td>
                            <td data-label="Schedule"></td>
                            <td data-label="Course"><?=$schedule['course']?></td>
                            <td data-label="Interview date">
                                <input type="hidden" name="timeslot_id_replace[old]" value="" />
                                <select name="timeslot_id_replace[new]" class="interview_timeslot">
                                    <option value=""><?=__('Set new timeslot')?></option>
                                    <?php
                                    foreach ($application['courses'] as $i => $course)
                                    foreach ($course['timeslots_available'] as $timeslot) {
                                        $timeslot_capacity = $timeslot['max_capacity'];
                                    ?>
                                    <option value="<?=$timeslot['id']?>"><?=$timeslot['schedule'] . ' - ' . $timeslot['datetime_start'] . ' (' . ((int)$timeslot['booking_count']) . ' / ' . $timeslot_capacity . ')'?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td data-label="Status">
                                <select name="interview_status">
                                    <?=html::optionsFromArray(
                                        array(
                                            'Not Scheduled' => __('Not Scheduled'),
                                            'Scheduled' => __('Scheduled'),
                                            'No Follow Up' => __('No Follow Up'),
                                            'No Show' => __('No Show'),
                                            'Waiting List' => __('Waiting List'),
                                            'Interviewed' => __('Interviewed'),
                                            'Accepted' => __('Accepted'),
                                            'Offered' => __('Offered'),
                                            'No Offer' => __('No Offer'),
                                            'Cancelled' => __('Cancelled'),
                                            'On Hold' => __('On Hold')
                                        ),
                                        $schedule['interview_status']
                                    );
                                    ?>
                                </select>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($application['schedules'] as $schedule): ?>
                            <tr>
                                <td data-label="Schedule Id"><?=$schedule['id']?></td>
                                <td data-label="Schedule"><?=$schedule['name']?></td>
                                <td data-label="Course"><?=$schedule['course']?></td>
                                <td data-label="Interview date">
                                    <input type="hidden" name="timeslot_id_replace[old]" value="<?=$schedule['timeslot_id']?>" />
                                    <select name="timeslot_id_replace[new]" class="interview_timeslot">
                                        <option value=""><?=__('Select timeslot')?></option>
                                        <?php
                                        foreach ($application['courses'] as $i => $course)
                                        foreach ($course['all_timeslots'] as $timeslot) {
                                            $timeslot_capacity = $timeslot['max_capacity'] > 0 ? $timeslot['max_capacity'] : $schedule['max_capacity'];
                                        ?>
                                        <option value="<?=$timeslot['id']?>" <?=$timeslot['booking_count'] >= $timeslot_capacity && $schedule['timeslot_id'] != $timeslot['id'] ? ' disabled="disabled"' : ''?> <?=$schedule['timeslot_id'] == $timeslot['id'] ? ' selected="selected"' : ''?>><?=$timeslot['schedule'] . ' - ' . $timeslot['datetime_start'] . ' (' . ((int)$timeslot['bcount']) . ' / ' . $timeslot_capacity . ')'?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td data-label="Status">
                                    <select name="interview_status">
                                    <?=html::optionsFromArray(
                                        array(
                                            'Not Scheduled' => __('Not Scheduled'),
                                            'Scheduled' => __('Scheduled'),
                                            'No Follow Up' => __('No Follow Up'),
                                            'No Show' => __('No Show'),
                                            'Waiting List' => __('Waiting List'),
                                            'Interviewed' => __('Interviewed'),
                                            'Accepted' => __('Accepted'),
                                            'Offered' => __('Offered'),
                                            'No Offer' => __('No Offer'),
                                            'Cancelled' => __('Cancelled'),
                                            'On Hold' => __('On Hold')
                                        ),
                                        $schedule['interview_status']
                                    );
                                    ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="transfer_to_another_course hiddenx">
                            <th colspan="2">Transfer to another course</th>
                            <th>
                                <select name="interview_transfer_to_course_id">
                                    <option value=""></option>
                                    <?=html::optionsFromRows('id', 'title', Model_Courses::get_all_published())?>
                                </select>
                            </th>
                            <th>
                                <input type="hidden" name="interview_transfer_to_schedule_id" value="" />
                                <select name="interview_transfer_to_timeslot_id">
                                    <option value=""></option>
                                </select>
                            </th>
                            <th>
                                <select name="interview_transfer_interview_status">
                                    <option value=""></option>
                                    <?=html::optionsFromArray(
                                        array(
                                            'Not Scheduled' => __('Not Scheduled'),
                                            'Scheduled' => __('Scheduled'),
                                            'No Follow Up' => __('No Follow Up'),
                                            'No Show' => __('No Show'),
                                            'Waiting List' => __('Waiting List'),
                                            'Interviewed' => __('Interviewed'),
                                            'Accepted' => __('Accepted'),
                                            'Offered' => __('Offered'),
                                            'No Offer' => __('No Offer'),
                                            'Cancelled' => __('Cancelled'),
                                            'On Hold' => __('On Hold')
                                        ),
                                        ''
                                    );
                                    ?>
                                </select>
                            </th>
                        </tr>
                        <tr class="send_email hidden"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="2"><?=Form::ib_checkbox("Send Email", "send_email", "yes")?></td> </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php
    }
    ?>

    <?php if (Settings::instance()->get('checkout_customization') == 'ibec') { ?>
    <div class="panel panel-primary" id="fulltime-course-application-edit-panel">
        <div class="panel-heading" data-toggle="collapse" data-target="#fulltime-course-application-edit-panel-body" aria-expanded="false">
            <button type="button" class="button--plain right expanded-invert">
                <span class="icon-angle-up"></span>
            </button>
            <h3 class="panel-title">View form data</h3>
        </div>

        <div class="panel-body collapse" id="fulltime-course-application-edit-panel-body">
            <dl class="dl-horizontal">
                <?php
                $id = DB::select('id')->from(Model_Formbuilder::form_table)->where('form_id', '=', 'accreditation_application')->execute()->as_array();
                $id = $id[0]['id'];
                $form_biulder_object = new Model_Formbuilder($id);
                $form_data = $form_biulder_object->get_form_data($id);
                echo $form_biulder_object->present_form('accreditation_application', 'ul', false);
                ?>
                <?php $application_items = Model_Realexpayments::clean_sensitive_data($application['data']);?>
                <?if (!empty($application_items) && is_array($application_items)):?>
                <!-- <?php foreach ( $application_items as $key => $value): ?>
                    <dt class="mb-0"><strong><?= $key ?></strong></dt>
                    <dd class="mb-0"><?= @htmlspecialchars($value) ?></dd>
                <?php endforeach; ?>
                <?php endif;?>
                -->
            </dl>
            <?php $counties_options = '<option value=""></option>' . Model_Cities::get_all_counties_html_options();
            $countries_options = '<option value=""></option>' .  Model_Country::get_country_as_options();
            $nationalities_options = '<option value=""></option>' .  Model_Country::get_nationalities_as_options();?>

            <script>
                (function($)
                {
                    $.fn.add_alert = function(message, type)
                    {
                        var $alert = $(
                            '<div class="alert'+((type) ? ' alert-'+type : '')+' popup_box">' +
                            '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
                            '</div>');
                        $(this).append($alert);
                    };
                })(jQuery);

                var accreditation_application_data = <?=json_encode($application['data'])?>;
                var countries_options = '<?=$countries_options?>';
                var counties_options = '<?=$counties_options?>';
                var nationalities_options = '<?=$nationalities_options?>';
                var form_action = '<?=$form_data['action']?>';
                for (var input_name in accreditation_application_data) {
                    if (input_name == 'country') {
                        $('select[name="' + input_name + '"]').html(countries_options);
                    }
                    if (input_name == 'county') {
                        $('select[name="' + input_name + '"]').html(counties_options);
                    }
                    if (input_name == 'nationality') {
                        $('select[name="' + input_name + '"]').html(nationalities_options);
                    }
                    $("input[type=hidden][name=\"" + input_name + "\"],input[type=text][name=\"" + input_name + "\"],select[name=\"" + input_name + "\"],textarea[name=\"" + input_name + "\"]").val(accreditation_application_data[input_name]);
                    if (accreditation_application_data[input_name] ) {
                        $("input[type=radio][name=\"" + input_name + "\"][value=\"" + accreditation_application_data[input_name] + "\"]").prop("checked", true);
                    }
                    if (accreditation_application_data[input_name] == 'on') {
                        $("input[type=checkbox][name=\"" + input_name + "\"]").prop("checked", true);
                    }
                    if (accreditation_application_data[input_name]) {
                        if (accreditation_application_data[input_name].length > 0) {
                            $("input[type=text][name=\"" + input_name + "\"],select[name=\"" + input_name + "\"],textarea[name=\"" + input_name + "\"]").closest('label').find('span.form-input--pseudo').addClass('form-input--active');
                            $("input[type=text][name=\"" + input_name + "\"],select[name=\"" + input_name + "\"],textarea[name=\"" + input_name + "\"]").closest('label').addClass('form-input--active');
                        }
                    }
                }
                var $ul_li = $(".dl-horizontal ul > li");
                $($ul_li[$ul_li.length - 1]).remove();
                $ul_li = $(".dl-horizontal ul > li");
                $($ul_li[$ul_li.length - 1]).remove();
                 $('body').ib_initialize_datepickers();
                //$(".dl-horizontal [name=booking_id]").remove();
                $(".dl-horizontal ul").find('div.form-group:last-child').append(
                    '<button ' +
                'type="button" ' +
                'data-toggle="modal" ' +
                'data-target="#application-cancel-confirm-modal" ' +
                'data-booking_id="'  + booking_id +'"> ' +
                    'Cancel</button>');
                $(document).on('click', '#accreditation-submit', function(e){
                    e.preventDefault();

                    var form_validation = $('#fulltime-course-application-edit').validationEngine('validate');
                    if (form_validation) {
                        var data = $("#fulltime-course-application-edit").serialize();
                        console.log(data);
                        $.post(form_action, data , function (result) {
                            if(result.success) {
                                $('.alert_area').add_alert('Application saved.', 'success', {autoscroll: false});
                            } else {
                                $('.alert_area').add_alert('Application was not saved.', 'error', {autoscroll: false});

                            }
                        });
                    }


                });
                $(".dl-horizontal input, .dl-horizontal textarea, .dl-horizontal select").each(function(){
                   //$(this).attr("name", "data[" + $(this).attr("name") + "]");
                });
            </script>

        </div>
    </div>
    <?php } ?>

    <div class="form-action-group form-actions text-center">
        <input type="hidden" name="update" value="save" />
        <?php if ($application['interview_status'] == null) { ?>
            <?php if ($application['status_id'] == 'new') { ?>
                <button type="button" class="btn btn-primary update" onclick="this.form.update.value=this.value;" value="create">Create</button>
            <?php } ?>
            <?php if ($application['status_id'] == 2) { ?>
                <button type="button" class="btn btn-primary update" value="save">Save</button>
            <?php } ?>
        <?php } else { ?>
            <button type="button" class="btn btn-primary update" onclick="this.form.update.value=this.value;" value="save">Save</button>
        <?php } ?>
        <?php if ($application['status_id'] == 1) { ?>
            <?php if ($application['interview_status'] == null) { ?>
            <button type="button" class="btn btn-primary update" onclick="this.form.update.value=this.value;" value="save">Save</button>
            <?php } ?>
            <button type="button" class="btn btn-default update" onclick="this.form.update.value=this.value;" value="approve">Approve</button>
        <?php } ?>
    </div>
</form>

<script>
    if ($("input[name='booking_id']").length > 1)
        $("input[name='booking_id']")[1].remove();
    if ($("input[name='contact_id']").length > 1)
        $("input[name='contact_id']")[1].remove();
        
    $('.checkout-years-completed').find('input').on('change', function()
    {
        var $table = $('#application_year_details_table');
        var $tr = $table.find('tr[data-year_id="' + this.value + '"]');

        var $repeat = $($(this).parents('.checkout-years-completed').data('repeat'));
        var index   = $(this).parents('label').index();
        var $corresponding_repeat = $repeat.find('.btn-group:nth-child(' + (index + 1) + ')');

        if (this.checked) {
            $tr.removeClass("hidden");
            $corresponding_repeat.removeClass('invisible');
        } else {
            $tr.addClass("hidden");
            $corresponding_repeat.addClass('invisible');
        }

        if ($('#application-years-completed').find(':checked').length) {
            $table.removeClass('hidden');
        } else {
            $table.addClass('hidden');
        }
    });

    // Show a subject's levels when the subject is selected
    $(document).on('change', '.profile-education-subject [type="checkbox"]', function() {
        var $levels = $(this).parents('.profile-education-subject_and_level').find('.profile-education-subject_preferences');
        if (this.checked) {
            $levels.removeClass('invisible');
        } else {
            $levels.addClass('invisible')
        }
    });

    $(document).on("change", ".interview_timeslot", function(){
        $(".send_email.hidden").removeClass("hidden");
    });
    $('#checkout-application-cycle').on('change', function()
    {
        // First check if any of the subjects are dependant on the cycle
        // The selective hiding will only take effect if at least one subject is linked to a cycle
        if ($('.profile-education-subject-wrapper[data-cycle]:not([data-cycle=""])').length) {
            var cycle = $(this).val();

            if (cycle) {
                // Show all subjects for the chosen cycle
                $('.profile-education-subject-wrapper').addClass('hidden');
                $('.profile-education-subject-wrapper[data-cycle="'+cycle+'"]').removeClass('hidden');
            } else {
                // Show all subjects when no cycle is selected
                $('.profile-education-subject-wrapper').removeClass('hidden');
            }
        }
    });
</script>