<div class="modal fade" id="add_weekly_notes_popup">
    <div class="modal-dialog" style="width: calc(100% - 20px); max-width: 810px;">
        <form name="add_weekly_note_form" class="modal-content" id="add_weekly_note_form" method="post">
            <input type="hidden" name="timeslot_id" />
            <input type="hidden" name="schedule_id" />
            <input type="hidden" name="booking_item_id" />
            <input type="hidden" name="datetime_from" />

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="flaticon-remove" aria-hidden="true"></span>
                </button>

                <h4 class="modal-title">Add/modify/delete note weekly</h4>
            </div>

            <div class="modal-body">
                <div class="row gutters">
                    <div class="col-sm-6">
                        <div class="row gutters">
                            <div class="col-sm-6">
                                <p>Start Time</p>

                                <p>
                                    <span class="text-primary icon-clock-o" aria-hidden="true"></span>
                                    <span class="start-time"></span>
                                </p>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-wrap">
                                    <label class="form-label">End Time</label>
                                    <label class="icon-wrap">
                                        <input type="text" class="timepicker" name="time_to" value="">
                                        <span class="text-primary fa icon-clock-o" aria-hidden="true"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div style="margin: 30px 0;">
                            <p>Days in Week</p>

                            <ul class="list-inline" style="margin: 0;">
                                <?php $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'); ?>

                                <?php foreach ($days as $key => $day): ?>
                                    <li style="padding: 0;">
                                        <label class="checkbox-icon" title="<?= __($day) ?>">
                                            <input type="checkbox" name="add-note_days[]" value="<?= $key ?>" />
                                            <span class="checkbox-icon-unchecked btn btn-default btn-lg"><?= $day[0] ?></span>
                                            <span class="checkbox-icon-checked btn btn-default btn-lg" style="background-color: #96c511; border-color: #aaa; color: #fff;"><?= $day[0] ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="row gutters">
                            <div class="col-sm-6">
                                <p>Start Date</p>

                                <p>
                                    <span class="text-primary icon-calendar" aria-hidden="true"></span>
                                    <span class="start-date"></span>
                                </p>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-wrap">
                                    <label class="form-label">End Date</label>

                                    <label class="icon-wrap">
                                        <input type="text" class="timetable-notes-datepicker" name="date_to" />
                                        <span class="text-primary fa icon-calendar" aria-hidden="true"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <ul class="list-unstyled">
                            <?php $options = array('class' => __('This class only'), 'contact' => __('All classes for this student'), 'family' => __('All classes for this family')); ?>
                            <?php foreach ($options as $key => $option): ?>
                                <li>
                                    <?= Form::ib_checkbox($option, 'filter_scope', $key, false, array('id' => 'filter_scope_'.$key.'_w')) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="form-row gutters">
                            <label class="col-sm-3" for="add_weekly_notes-attending"><?= __('Attending') ?></label>
                            <div class="col-sm-4">
                                <select class="form-input" name="attending" id="add_weekly_notes-attending">
                                    <option value="1"><?= __('Yes') ?></option>
                                    <option value="0"><?= __('No') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer form-actions">
                <button type="button" value="Continue" class="btn btn-primary continue" name="continue"><?= __('Continue') ?></button>
                <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
            </div>

            <div class="modal-body weekly_confirm hidden">
                <p>Confirm adding/modifying/deleting notes for these classes</p>

                <div class="slider-wrapper">
                    <ul></ul>

                    <div class="slider_action">
                        <a class="prev_arrow"><span class="icon-angle-left" aria-hidden="true"></span></a>
                        <a class="next_arrow"><span class="icon-angle-right" aria-hidden="true"></span></a>
                    </div>
                </div>

                <label class="form-label" for="add_weekly_note">Note</label>
                <textarea name="note" class="form-input" id="add_weekly_note"></textarea>
            </div>

            <div class="modal-footer form-actions weekly_confirm hidden">
                <button type="button" value="Confirm" class="btn btn-primary confirm">Confirm</button>
                <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
