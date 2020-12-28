<div class="modal fade" id="add_mytime_popup">
    <div class="modal-dialog">
        <form name="add_mytime_form" class="modal-content" id="add_mytime_form" method="post">
            <input type="hidden" name="mytime_id" />

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="flaticon-remove" aria-hidden="true"></span>
                </button>

                <h4 class="modal-title">Add my time</h4>
            </div>

            <div class="modal-body">
                <div class="teacher_only hidden">
                    <p class="form-label">Type</p>
                    <label><input type="radio" id="availability_none" name="availability" value=""/> My Time</label>
                    <label><input type="radio" id="availability_yes" name="availability" value="YES"/> Availability</label>
                </div>

                <div class="teacher_only for_availability hidden">
                    <?php
                    $options = array();
                    foreach ($subjects as $subject) {
                        $options[$subject['id']] = $subject['name'];
                    }
                    ?>
                    <?= Form::ib_select(__('Subjects'), 'subjects', $options, null, array('multiple' => 'multiple', 'timetable-add_time-subjects')) ?>
                </div>

                <div class="form-row not_for_availability">
                    <label class="form-label" for="timetable-add_time-description">Title</label>

                    <input type="text" class="form-input" id="timetable-add_time-description" name="description" />
                </div>

                <div class="form-row">
                    <label class="form-label" for="timetable-add_time-color">Colour</label>

                    <select name="color" class="form-input" id="timetable-add_time-color">
                        <option value="#ff0000" style="background-color: #ff0000;">Red</option>
                        <option value="#00ff00" style="background-color: #00ff00;">Green</option>
                        <option value="#0000ff" style="background-color: #0000ff;">Blue</option>
                        <option value="#ffffff" style="background-color: #ffffff;">White</option>
                    </select>
                </div>

                <fieldset class="mytime_type one selected">
                    <legend>Just One</legend>

                    <input type="hidden" name="start_date" />
                    <input type="hidden" name="end_date" />

                    <div class="mytime_type one">
                        <div class="form-row">
                            <label class="form-label">Start Time</label>
                            <input type="text" class="form-input timepicker" name="start_time" />
                        </div>

                        <div class="form-row">
                            <label class="form-label">End Time</label>
                            <input type="text" class="form-input timepicker" name="end_time" />
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mytime_type daily">
                    <legend>Daily</legend>

                    <div class="mytime_type hidden">
                        <div class="form-row">
                            <label class="form-label">Start Date</label>
                            <input type="text" class="form-input datepicker" name="start_date" />
                        </div>

                        <div class="form-row">
                            <label class="form-label">Start Time</label>
                            <input type="text" class="form-input timepicker" name="start_time" />
                        </div>

                        <div class="form-row">
                            <label class="form-label">End Date</label>
                            <input type="text" class="form-input datepicker" name="end_date" />
                        </div>

                        <div class="form-row">
                            <label class="form-label">End Time</label>
                            <input type="text" class="form-input timepicker" name="end_time" />
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mytime_type weekly">
                    <legend>Weekly</legend>

                    <div class="mytime_type hidden">
                        <div class="form-row">
                            <label class="form-label">Start Time</label>
                            <input type="text" class="form-input timepicker" name="start_time" />
                        </div>

                        <div class="form-row">
                            <label class="form-label">End Time</label>
                            <input type="text" class="form-input timepicker" name="end_time" />
                        </div>

                        <div class="form-row">
                            <p class="form-label">Days in Week</p>

                            <ul class="list-inline" style="margin: 0;">
                                <?php $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'); ?>

                                <?php foreach ($days as $key => $day): ?>
                                    <li style="padding: 0;">
                                        <label class="checkbox-icon" title="<?= __($day) ?>">
                                            <input type="checkbox" class="timetable-add_time_days" name="timetable-add_time_days" value="<?= $key ?>" />
                                            <span class="checkbox-icon-unchecked btn btn-default btn-lg"><?= $day[0] ?></span>
                                            <span class="checkbox-icon-checked btn btn-default btn-lg" style="background-color: #96c511; border-color: #aaa; color: #fff;"><?= $day[0] ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="form-row">
                            <label class="form-label">Start Date</label>
                            <input type="text" class="timetable-notes-datepicker" name="start_date" />
                        </div>

                        <div class="form-row">
                            <label class="form-label">End Date</label>
                            <input type="text" class="timetable-notes-datepicker" name="end_date" />
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="modal-footer form-actions">
                <button type="button" class="btn btn-primary confirm">Confirm</button>
                <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
            </div>

            <div class="modal-body hidden confirm_conflicts">
                <p>There are conflicts with your time. Do you want to continue?</p>

                <div class="slider-wrapper">
                    <ul>
                        <li>
                            <a href="#">
                                <span>Mon 12 Dec</span>
                                <span class="sub-name">Biology</span>
                                8&nbsp;pm <span class="circled_icon icon-exclamation" aria-hidden="true"></span>
                            </a>
                        </li>

                    </ul>
                    <div class="slider_action">
                        <a class="prev_arrow"><span class="icon-angle-left" aria-hidden="true"></span></a>
                        <a class="next_arrow"><span class="icon-angle-right" aria-hidden="true"></span></a>
                    </div>
                </div>
            </div>

            <div class="modal-footer confirm_conflicts hidden">
                <button class="btn btn-primary conflict" type="button"><?= __('Confirm') ?></button>
                <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>