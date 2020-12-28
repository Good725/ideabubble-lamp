
    <div id="timesheet-edit-modal" class="modal fade">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                <span aria-hidden="true">×</span>
            </button>
            <h5 class="modal-title"><span>Submit a request</span></h5>
        </div>
        <div class="modal-body">
            <form action="" class="form-horizontal validate-on-submit" id="timesheets-log_work-form">
                <input type="hidden" id="timesheet-edit_timesheet_id" value="" />
                <div class="form-group vertically_center">
                    <div class="col-xs-2 col-sm-1">
                        <?= IbHelpers::embed_svg('check_round', array('color' => true, 'width' => 48, 'height' => 48)) ?>
                    </div>

                    <div class="col-xs-11 col-sm-10">
                        <strong style="font-weight: 500; margin-left: 1em;">
                            <?= __('Log work') ?>
                        </strong>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div>
                            <div id="header_buttons"></div>
                            <div class="expand-section-tabs">
                                <ul class="nav nav-tabs nav-tabs-contact">
                                    <li class="schedule-select active"><a href="#schedule-select-tab" data-toggle="tab"><?=__('Search Schedule')?></a></li>
                                    <li class="todo-select"><a href="#todos-select-tab" data-toggle="tab"><?=__('Search Task')?></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content px-0">
                            <div class="tab-pane active" id="schedule-select-tab">
                                <input type="hidden" id="log-work-schedule-id" />
                                <?= Form::ib_input(null, null, null, ['id' => 'log-work-schedule-autocomplete', 'placeholder' => 'Search schedules']) ?>
                            </div>
                            <div class="tab-pane" id="todos-select-tab">
                                <input type="hidden" id="log-work-todos-id" />
                                <?= Form::ib_input(null, null, null, ['id' => 'log-work-todos-autocomplete', 'placeholder' => 'Search tasks']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group hidden">
                    <div class="col-sm-12">
                        <label for="timesheets-log_work-schedule-input">
                            <?= __('Search schedule')?>
                        </label>
                    
                        <?php
                        $options    = array('' => '', '1' => 'Human resources', '2' => 'Team meetings', '3' => 'Marketing efforts');
                        $attributes = array(
                            'class' => 'ib-modal-combobox',
                            'id' => 'timesheets-log_work-schedule',
                            'data-combobox-prepend' => '#timesheets-log_work-schedule-filters'
                        );
                        echo Form::ib_select(null, 'schedule_id', $options, null, $attributes);
                        ?>

                        <div class="hidden" id="timesheets-log_work-schedule-filters">
                            <div class="text-center">
                                <?php
                                $options = array(
                                    'recent'   => __('Recent'),
                                    'assigned' => __('Assigned'),
                                    'internal' => __('Internal')
                                );
                                echo Form::btn_options('schedule_filter', $options, 'recent', false, array(), array('class' => 'stay_inline'));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group hidden">
                    <div class="col-sm-12">
                        <p>debug. use these inputs until task/schedule selector is ready</p>
                        <label>Internal todo ID <input type="radio" ng-model="vm.formdata.type" value="internal"></label>
                        <input ng-model="vm.formdata.todo_id"><br/>
                        <label>Course ID <input type="radio" ng-model="vm.formdata.type" value="course"></label>
                        <input ng-model="vm.formdata.schedule_id">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="timesheets-log_work-description"><?= __('Description') ?></label>
                    
                        <?php
                        $attributes = array(
                            'class'    => 'validate[required]',
                            'id'       => 'timesheets-log_work-description',
                        );
                        echo Form::ib_textarea(null, 'description', '', $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6" ib-combobox autocomplete-value="{{vm.formdata.staff.name}}">
                        <label for="timesheets-log_work-person">
                            <?= __('Person')?>
                        </label>
                    
                        <?php
                        $attributes = [
                            'class'       => 'validate[required]',
                            'id'          => 'timesheets-log_work-person',
                        ];
                        if (count($staffs) == 1) {
                            $attributes['disabled'] = 'disabled';
                        }
                        echo Form::ib_select(null, 'contact_id', $staffs, null, $attributes);
                        ?>
                    </div>
                </div>
            
                <?= Form::ib_checkbox(__('Period'), 'period', 1, false, array('id' => 'timesheets-log_work-period')) ?>

                <div class="form-group">
                    <div class="col-xs-11 col-sm-6">
                        <label class="control-label text-left" for="timesheets-log_work-start_date-input"><?= __('Date') ?></label>

                        <label class="row no-gutters vertically_center">
                            <span class="col-xs-10">
                                <?php
                                $hidden_attributes = [
                                    'type' => 'text',
                                    'class' => 'sr-only',
                                    'id' => 'timesheets-log_work-start_date'
                                ];
                                $display_attributes = [
                                    'id' => 'timesheets-log_work-start_date-input'
                                ];
                                echo Form::ib_datepicker(null, 'start_date', date('Y-m-d'), $hidden_attributes, $display_attributes)
                                ?>
                            </span>

                            <span class="col-xs-2 text-center">
                                <?= IbHelpers::embed_svg('calendar', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                            </span>
                        </label>
                    </div>

                    <div class="col-xs-11 col-sm-6 hidden" id="timesheets-log-work-end_date-wrapper-input">
                        <label class="control-label text-left" for="timesheets-log_work-end_date"><?= __('End date') ?></label>

                        <label class="row no-gutters vertically_center">
                            <span class="col-xs-10">
                                <?php
                                $hidden_attributes = [
                                    'type'     => 'text',
                                    'class'    => 'sr-only',
                                    'id'       => 'timesheets-log_work-end_date',
                                ];
                                $display_attributes = [
                                    'id'       => 'timesheets-log_work-end_date-input',
                                ];
                                echo Form::ib_datepicker(null, 'end_date', null, $hidden_attributes, $display_attributes)
                                ?>
                            </span>

                            <span class="col-xs-2 text-center">
                                <?= IbHelpers::embed_svg('calendar', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <label class="control-label text-left worked-label single" for="timesheets-log_work-worked"><?= __('Worked') ?></label>
                        <label class="control-label text-left hidden worked-label multiple" for="timesheets-log_work-worked"><?= __('Worked per day') ?></label>
                    
                        <?php
                        $attributes = [
                            'id'          => 'timesheets-log_work-worked',
                            'placeholder' => 'e.g. 2h 20m',
                            'ng-model'    =>'vm.formdata.duration'
                        ];
                        echo Form::ib_input(null, 'worked', '', $attributes) ?>
                    </div>
                </div>
            </form>

        </div>
        <div class="modal-footer">
            <div class="row gutters vertically_center">
                <div class="col-sm-3 text-left">
                    <div class="log-another">
                        <?= Form::ib_checkbox(__('Log another'), 'log_another', 1, false, array()) ?>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-9 text-center">
                    <button type="button" class="btn btn-primary"><?= __('Log work') ?></button>
                    <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </div>
        </div>
        </div>
        </div>
    </div>
