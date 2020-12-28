<?php $search_icon = ['icon' => '<span class="flip-horizontally"><span class="icon_search"></span></span>'] ?>
<?php ob_start(); ?>
    <form class="form-horizontal" id="edit-interview-form">
        <input type="hidden" name="booking_id" id="edit-interview-booking_id"/>

        <div class="form-group">
            <div class="col-sm-12">
                <h2 style="display: flex; align-items: center;">
                    <span class="icon-plus-circle" style="font-size: 2em;"></span>&nbsp;
                    <span><?= __('Edit interview') ?></span>
                </h2>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="edit-interview-academic_period"><?= __('Academic period') ?></label>

            <div class="col-sm-8">
                <?php
                $options = Html::optionsFromRows('id', 'title', $academic_periods, null, ['value' => '', 'label' => __('Please select')]);
                echo Form::ib_select(null, 'academic_period_id', $options, null, ['id' => 'edit-interview-academic_period']);
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="edit-interview-course"><?= __('Course') ?></label>

            <div class="col-sm-8">
                <?php
                $options = html::optionsFromRows('id', 'title', $courses, null, ['value' => '', 'label' => '']);
                $attributes = ['class' => 'ib-combobox', 'id' => 'edit-interview-course', 'data-placeholder' => __('Please select')];
                echo Form::ib_select(null, 'course_id', $options, null, $attributes, $search_icon);
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="edit-interview-schedule"><?= __('Schedule') ?></label>

            <div class="col-sm-8">
                <?php
                $options = ['' => ''];
                foreach ($schedules as $schedule) {
                    $options[$schedule['id']] = '#'.$schedule['id'].' '.$schedule['name'];
                }

                $attributes = ['class' => 'ib-combobox', 'id' => 'edit-interview-schedule', 'data-placeholder' => __('Please select')];
                echo Form::ib_select(null, 'schedule_id', $options, null, $attributes, $search_icon);
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="edit-interview-slot"><?= __('Slot') ?></label>

            <div class="col-sm-8">
                <?= Form::ib_select(null, 'slot_id', ['' => __('Please select')], null, ['id' => 'edit-interview-slot']); ?>
            </div>
        </div>
    </form>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <button type="button" class="btn btn-primary edit-interview-save" data-send_email="0"><?= __('Save') ?></button>
    <button type="button" class="btn btn-default edit-interview-save" data-send_email="1"><?= __('Save and Email') ?></button>
    <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'applications-interviews-edit-modal')
    ->set('title',  __('Edit interview').' - <span id="applications-interviews-edit-modal-name"></span>')
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer);
?>
