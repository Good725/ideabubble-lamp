<div class="modal-dialog" style="width: 90%; width: calc(100% - 20px);max-width: 810px;">
    <form class="modal-content" action="/frontend/contacts3/attendance_bulk_update" method="post" id="bulk_update_form">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="flaticon-remove" aria-hidden="true"></span>
            </button>

            <h4 class="modal-title">Will attend weekly</h4>
        </div>

        <div class="modal-body">
            <div class="row gutters">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-wrap">
                                <label class="form-label">Start Date</label>
                                <label class="icon-wrap">
                                    <input type="text" name="date_from" class="attendance_datepicker_from" />
                                    <span class="text-primary fa icon-calendar" aria-hidden="true"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-wrap">
                                <label class="form-label">End Date</label>
                                <label class="icon-wrap">
                                    <input type="text" name="date_to" class="attendance_datepicker_to" />
                                    <span class="text-primary fa icon-calendar" aria-hidden="true"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <p class="form-label">Days in Week</p>

                            <ul class="list-inline" style="margin: 0;">
                                <?php $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'); ?>

                                <?php foreach ($days as $key => $day): ?>
                                    <li style="padding: 0;">
                                        <label class="checkbox-icon" title="<?= __($day) ?>">
                                            <input type="checkbox" class="bulk-update" name=timetable-bulkupdate-days[]" value="<?= 1 + (int) $key ?>" />
                                            <span class="checkbox-icon-unchecked btn btn-default btn-lg"><?= $day[0] ?></span>
                                            <span class="checkbox-icon-checked btn btn-default btn-lg" style="background-color: #96c511; border-color: #aaa; color: #fff;"><?= $day[0] ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <ul class="list-unstyled">
                        <li>
                            <?= Form::ib_radio(__('All classes for this student'), 'target', 'member', true, array('class' => 'bulk-update bulk-member-selector', 'id' => 'check2')) ?>
                        </li>

                        <li>
                            <?= Form::ib_radio(__('All classes for this family'), 'target', 'family', true, array('class' => 'bulk-update bulk-member-selector', 'id' => 'check3')) ?>
                        </li>
                    </ul>

                    <ul class="list-unstyled">
                        <li>
                            <?= Form::ib_radio(__('Will attend'), 'attending', '1', true, array('class' => 'bulk-update bulk-attending-selector yes', 'id' => 'check4')) ?>
                        </li>

                        <li>
                            <?= Form::ib_radio(__('Will not attend'), 'attending', '0', true, array('class' => 'bulk-update bulk-attending-selector no', 'id' => 'check5')) ?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bulk-update-classes"></div>
        </div>
    </form>
</div>