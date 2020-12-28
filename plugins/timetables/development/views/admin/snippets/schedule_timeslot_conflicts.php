<?php
ob_start();
?>
<h2><span class="schedule">Schedule</span> <span class="timeslot hidden"></span></h2>
<p>conflicts with the following timeslots</p>

<form id="schedule_timeslot_conflicts_form">
<table class="table" id="schedule_timeslot_conflicts">
    <thead>
        <tr><th>Date</th><th>Location</th><th>Staff</th><th>Start</th><th>End</th><th>Conflicts</th><th>Select</th></tr>
    </thead>
    <tbody>
        <tr class="hidden">
            <td><?= Form::ib_datepicker(null, null, null, array(), array('class' => 'date')) ?> </td>
            <td><input type="hidden" class="location_id" /> <?=Form::ib_input('', '', NULL, array('class'=> 'location', 'style'=>'min-width:60px;'))?></td>
            <td><input type="hidden" class="trainer_id" /> <?=Form::ib_input('', '', NULL, array('class'=> 'trainer', 'style'=>'min-width:60px;'))?> </td>
            <td><?=Form::ib_input('', '', NULL, array('class'=> 'datetimepicker start', 'style'=>'min-width:60px;'))?></td>
            <td><?=Form::ib_input('', '', NULL, array('class'=> 'datetimepicker end', 'style'=>'min-width:60px;'))?></td>
            <td>
                <table class="table conflicts">
                    <thead><tr><th>Course</th><th>Schedule</th><th>Location</th><th>Staff</th><th>Start</th><th>End</th></tr></thead>
                    <tbody>
                        <tr class="hidden"><td class="course">Course</td><td class="schedule">Schedule</td><td class="location">Location</td><td class="staff">staff</td><td class="conflict_start">Start</td><td class="conflict_end">End</td></tr>
                    </tbody>
                </table>
            </td>
            <td><input type="checkbox" class="update validate[groupRequired[timeslot]]" value="update" /> </td>
        </tr>
    </tbody>
    <tfoot>

    </tfoot>
</table>
</form>
<?php
$schedule_timeslots_conflicts_body = ob_get_clean();
?>

<?php ob_start(); ?>
    <button type="button" class="btn btn-primary" id="timetable-timeslots-conflicts-confirm-modal-trigger" data-toggle="modal" data-target="#timetable-timeslots-conflicts-confirm-modal"><?= __('Update selected slots') ?></button>
    <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $schedule_timeslots_conflicts_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'timetable-timeslots-conflicts-modal')
    ->set('title',  __('Resolve conflicts'))
    ->set('body',   $schedule_timeslots_conflicts_body)
    ->set('footer', $schedule_timeslots_conflicts_footer)
    ->set('size', 'lg');
?>

<?php ob_start(); ?>
    <button type="button" class="btn btn-primary resolve"><?= __('Confirm') ?></button>
    <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $schedule_timeslots_conflicts_footer = ob_get_clean(); ?>


<?php
echo View::factory('snippets/modal')
    ->set('id',     'timetable-timeslots-conflicts-confirm-modal')
    ->set('title',  __('Confirm update'))
    ->set('body',   '<p>Are you sure you want to make changes to a schedule timeslot?</p>')
    ->set('footer', $schedule_timeslots_conflicts_footer)
    ->set('size', 'lg');
?>
