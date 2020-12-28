<?php
$mode           = (isset($mode) && strtolower(trim($mode)) == 'popover') ? 'popover' : 'modal';
$id_prefix      = 'timetable-planner-add_slot-'.$mode.'-';
$show_labels    = ($mode == 'modal');
$column_classes = ($mode == 'modal') ? 'col-xs-12 col-sm-8' : 'col-sm-12';
$please_select = ['' => __('Please select')];
$overlay       = ['overlay' => '<span class="form-input-overlay-inner timetables-status-text" data-status="Pending"></span>'];
$search_icon   = ['icon' => '<span class="flip-horizontally"><span class="icon_search"></span></span>', 'arrow_position' => false];
$read_view     = !empty($read_view);
?>


<div class="form-horizontal timetable-planner-slot_form<?= $mode == 'popover' && !$read_view ? ' row gutters' : '' ?>">
    <input type="hidden" name="id" value="" />

    <?php if (!$read_view): ?>
        <div<?= ($mode == 'popover') ? ' class="col-sm-2 right"' : '' ?>>
            <div class="form-group vertically_center">
                <?php if ($mode == 'modal'): ?>
                    <div class="col-xs-2 col-sm-1">
                        <h2><span class="icon-plus-circle" style="font-size: 2em; position: relative; top: .1em;"></span></h2>
                    </div>

                    <div class="col-xs-6 col-sm-9">
                        <h2><?= __('Add slot') ?></h2>
                    </div>
                <?php endif; ?>

                <div class="<?= ($mode == 'modal') ? ' col-xs-4 col-sm-2' : 'col-sm-12' ?>">
                    <div class="timetable-planner-status-dropdown">
                        <?php
                        $statuses = ['Done', 'Booked', 'Pending', 'Conflict', 'Available'];
                        $options = [];
                        foreach ($statuses as $status) {
                            $options[$status] = htmlspecialchars('<span data-status="'.$status.'"></span> <span class="hidden--selected">'.$status.'</span>');
                        }
                        $args = ['multiselect_options' => ['enableHTML' => true]];
                        echo Form::ib_select(null, 'status', $options, 'Booked', ['id' => $id_prefix.'-status'], $args); ?>
                    </div>
                </div>
            </div>

            <?php if ($mode == 'popover'): ?>
                <div class="form-group">
                    <div class="text-center" style="font-size: 1.5em; font-weight: 300;">
                        <span class="icon_group text-primary"></span>
                        <span id="timetable-planner-add_slot-attending">0</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div<?= ($mode == 'popover' && !$read_view) ? ' class="col-sm-10"' : '' ?>>
        <div class="form-group vertically_center">
            <?php if ($show_labels): ?>
                <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-academic_year"><?= __('Academic year') ?></label>
            <?php endif; ?>

            <div class="<?= $column_classes ?>">
                <?php
                $academic_years = Model_AcademicYear::get_all();
                $options = '<option></option>';
                foreach ($academic_years as $academic_year) {
                    $options .= '<option value="' . $academic_year['id'] . '">' . $academic_year['title'] . '</option>';
                }

                $attributes = [
                    'class' => 'ib-combobox',
                    'data-placeholder' => __('Academic year'),
                    'id' => $id_prefix . 'academicyear_id'
                ];
                echo Form::ib_select(null, 'academicyear_id', $options, null, $attributes, $search_icon + $overlay);
                ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <?php if ($show_labels): ?>
                <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-course"><?= __('Course') ?></label>
            <?php endif; ?>

            <div class="<?= $column_classes ?>">
                <input type="hidden" id="<?=$id_prefix?>course_id" name="course_id" value="" />
                <?php
                $options = '';

                $attributes = [
                    'placeholder' => __('Course title'),
                    'id' => $id_prefix.'course'
                ];
                echo Form::ib_input(null, 'course', '', $attributes, $search_icon);
                ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <?php if ($show_labels): ?>
                <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-schedule"><?= __('Schedule') ?></label>
            <?php endif; ?>

            <div class="<?= $column_classes ?>">
                <input type="hidden" id="<?=$id_prefix?>schedule_id" name="schedule_id" value="" />
                <?php
                $options = '';
                $attributes = [
                    'placeholder' => __('Schedule'),
                    'id' => $id_prefix . 'schedule'
                ];
                echo Form::ib_input(null, 'schedule', '', $attributes, $search_icon);
                ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <?php if ($show_labels): ?>
                <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-topic"><?= __('Topic') ?></label>
            <?php endif; ?>

            <div class="<?= $column_classes ?>">
                <input type="hidden" id="<?=$id_prefix?>topic_id" name="topic_id" value="" />
                <?php
                $options = '';
                $attributes = [
                    'placeholder' => __('Add topic'),
                    'id' => $id_prefix.'topic'
                ];
                echo Form::ib_input(null, '', '', $attributes, $search_icon);
                ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <?php if ($show_labels): ?>
                <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-staff"><?= __('Staff') ?></label>
            <?php endif; ?>

            <div class="<?= $column_classes ?>">
                <input type="hidden" id="<?=$id_prefix?>contact_id" name="contact_id" value="" />
                <?php
                $options = '';

                $attributes = [
                    'placeholder' => __('Staff'),
                    'id' => $id_prefix.'contact'
                ];
                echo Form::ib_input(null, 'contact', '', $attributes, $search_icon);
                ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <?php if ($show_labels): ?>
                <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-staff"><?= __('Location') ?></label>
            <?php endif; ?>

            <div class="<?= $column_classes ?>">
                <input type="hidden" id="<?=$id_prefix?>location_id" name="location_id" value="" />
                <?php
                $options = '';

                $attributes = [
                    'placeholder' => __('Location'),
                    'id' => $id_prefix.'location'
                ];
                echo Form::ib_input(null, 'location', '', $attributes, $search_icon);
                ?>
            </div>
        </div>
        <div class="form-group vertically_center">
                <?php if ($show_labels): ?>
                    <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-staff"><?= __('Sub location') ?></label>
                <?php endif; ?>
            <div class="<?= $column_classes ?>">
                <input type="hidden" id="<?= $id_prefix ?>sub_location_id" name="sub_location_id" value=""/>
                <?php
                $options = '';

                $attributes = [
                    'placeholder' => __('Sub location'),
                    'id' => $id_prefix . 'sub_location'
                ];
                echo Form::ib_input(null, 'sub_location', '', $attributes, $search_icon);
                ?>
            </div>
        </div>
        <div class="form-group vertically_center">
            <?php if ($mode == 'modal'): ?>
                <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-date">
                    <label for="<?= $id_prefix ?>-date"><?= __('Date') ?></label> /
                    <label for="<?= $id_prefix ?>-time"><?= __('Time') ?></label>
                </label>
            <?php endif; ?>

            <div class="col-xs-12 <?= ($mode == 'modal') ? 'col-sm-4' : 'col-sm-6' ?>">
                <label class="row no-gutters vertically_center">
                    <span class="col-xs-2 col-sm-3 text-center">
                        <?= IbHelpers::embed_svg('calendar', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                    </span>

                    <span class="col-xs-10 col-sm-9">
                        <?php
                        $hidden_attributes  = ['id' => $id_prefix.'date'];
                        $display_attributes = ['placeholder' => __('Date'), 'id' => $id_prefix.'date-input'];
                        echo Form::ib_datepicker(null, 'date', null, $hidden_attributes, $display_attributes);
                        ?>
                    </span>
                </label>
            </div>
        </div>

        <div class="form-group vertically_center">
            <?php if ($mode == 'modal'): ?>
                <label class="col-xs-auto col-sm-4" for="<?= $id_prefix ?>-date">
                    <label for="<?= $id_prefix ?>-date"><?= __('Time') ?></label>
                </label>
            <?php endif; ?>

            <div class="col-xs-12 <?= ($mode == 'modal') ? 'col-sm-4' : 'col-sm-6' ?>">
                <label class="row no-gutters vertically_center">
                    <span class="col-xs-2 col-sm-3 text-center">
                        <?= IbHelpers::embed_svg('clocks', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                    </span>

                    <span class="col-xs-10 col-sm-9">
                        <?php
                        $attributes = [
                            'placeholder' => __('Start Time'),
                            'id' => $id_prefix.'starts'
                        ];
                        echo Form::ib_input(null, 'starts', '', $attributes);
                        ?>
                    </span>

                </label>
            </div>
            <div class="col-xs-12 <?= ($mode == 'modal') ? 'col-sm-4' : 'col-sm-6' ?>">
                <label class="row no-gutters vertically_center">
                    <span class="col-xs-2 col-sm-3 text-center">
                        <?= IbHelpers::embed_svg('clocks', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                    </span>
                    <span class="col-xs-10 col-sm-9">
                        <?php
                        $attributes = [
                            'placeholder' => __('end Time'),
                            'id' => $id_prefix.'ends'
                        ];
                        echo Form::ib_input(null, 'ends', '', $attributes);
                        ?>
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>
