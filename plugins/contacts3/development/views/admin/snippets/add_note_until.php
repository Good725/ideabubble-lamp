<div class="modal fade" id="add_note_until">
    <div class="modal-dialog">
        <form class="modal-content" name="add_note_until_form" id="add_note_until_form" method="post">
            <input type="hidden" name="timeslot_id" />
            <input type="hidden" name="schedule_id" />
            <input type="hidden" name="booking_item_id" />
            <input type="hidden" name="datetime_from" />

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="flaticon-remove" aria-hidden="true"></span>
                </button>

                <h4 class="modal-title">Add/Modify/delete note until</h4>
            </div>

            <div class="modal-body">
                <div class="col-sm-3">
                    <p>Start Date</p>

                    <p>
                        <span class="text-primary icon-calendar" aria-hidden="true"></span>
                        <span class="start-date"></span>
                    </p>

                    <div class="form-wrap">
                        <label class="form-label">Until Date</label>
                        <label class="icon-wrap">
                            <input type="text" class="timetable-notes-datepicker" name="date_to" value="">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </label>
                    </div>
                </div>

                <div class="col-sm-3">
                    <p>Start Time</p>

                    <p>
                        <span class="text-primary icon-clock-o" aria-hidden="true"></span>
                        <span class="start-time"></span>
                    </p>

                    <div class="form-wrap">
                        <label class="form-label">Until Time</label>
                        <label class="icon-wrap">
                            <input type="text" class="timepicker" name="time_to" value="">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                        </label>
                    </div>
                </div>

                <div class="col-sm-6">
                    <ul class="list-unstyled">
                        <li><?= Form::ib_checkbox('This class only',              'filter_scope', 'class',   false, array('id' => 'filter_scope_class'  ), 'right') ?></li>
                        <li><?= Form::ib_checkbox('All classes for this student', 'filter_scope', 'contact', false, array('id' => 'filter_scope_contact'), 'right') ?></li>
                        <li><?= Form::ib_checkbox('All classes for this family',  'filter_scope', 'family',  false, array('id' => 'filter_scope_family' ), 'right') ?></li>
                    </ul>

                    <div class="form-row gutters">
                        <label class="col-sm-4" for="add_note_until_form-attending">Attending</label>

                        <div class="col-sm-4">
                            <select name="attending" class="form-input" id="add_note_until_form-attending">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer form-actions">
                <button type="button" class="btn btn-primary continue" id="add_note_popup-submit">Continue</button>
                <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
