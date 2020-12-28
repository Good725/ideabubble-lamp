<?= isset($alert) ? $alert : '' ?>


<form id="register-student-schedule-edit" name="register-student-schedule-edit" class="form-horizontal" method="post" action="/admin/courses/student_schedule_registration/<?=$registration['id']?>">
    <input type="hidden" name="id" value="<?=$registration['id']?>" />

    <div class="col-sm-12">
        <div class="form-group">
            <label class="col-sm-2  control-label" for="edit-schedule"><?= __('Schedule') ?></label>
            <div class="col-sm-5">
                <input type="hidden" name="schedule_id" id="schedule_id" value="<?=$registration['schedule_id']?>" />
                <input type="text" class="form-control ib-title-input validate[required]" id="edit-schedule" placeholder="<?= __('Schedule') ?>" value="<?=$registration['schedule']?>"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2  control-label" for="edit-student"><?= __('Student') ?></label>
            <div class="col-sm-5">
                <input type="hidden" name="contact_id" id="contact_id" value="<?=$registration['contact_id']?>" />
                <input type="text" class="form-control ib-title-input validate[required]" id="edit-student" placeholder="<?= __('Student') ?>" value="<?=$registration['student']?>"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2  control-label" for="status"><?= __('Status') ?></label>
            <div class="col-sm-5">
                <div class="selectbox">
                <select name="status" id="status" class="form-control">
                    <?=html::optionsFromArray(array('Pending' => __('Pending'), 'Registered' => __('Registered'), 'Cancelled' => __('Cancelled')), $registration['status'])?>
                </select>
            </div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="edit-notes"><?= __('Notes') ?></label>
            <div class="col-sm-5">
                <textarea class="form-control" name="notes"><?=$registration['notes']?></textarea>
            </div>
        </div>

        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php
            if (is_numeric($registration['id'])) {
            ?>
            <button type="button" class="btn btn-danger" data-toggle="modal"
                    data-target="#delete-registration-modal"><?= __('Delete') ?></button>
            <?php
            }
            ?>
            <a href="/admin/courses/student_schedule_registrations" class="btn-link"><?= __('Cancel') ?></a>
        </div>
    </div>

</form>

<script>
$(document).on("ready", function(){
    $('#register-student-schedule-edit').on("submit", function (e){
        if (isNaN(parseInt(this.contact_id.value))){
            alert("Please select a student");
            return false;
        }
        if (isNaN(parseInt(this.schedule_id.value))){
            alert("Please select a schedule");
            return false;
        }
    });

    $('#register-student-schedule-edit').validationEngine();

    $("#edit-schedule").autocomplete({
        select: function(e, ui) {
            $('#edit-schedule').val(ui.item.label);
            $('#schedule_id').val(ui.item.value);
            return false;
        },

        source: function(data, callback){
            $.get("/admin/courses/autocomplete_schedules",
                data,
                function(response){
                    callback(response);
                }
            );
        }
    });

    $("#edit-student").autocomplete({
        select: function(e, ui) {
            $('#edit-student').val(ui.item.label);
            $('#contact_id').val(ui.item.value);
            return false;
        },

        source: function(data, callback){
            data.schedule_id_not_registered = $('#schedule_id').val();
            data.list = "student";
            $.get("/admin/courses/autocomplete_contacts",
                data,
                function(response){
                    callback(response);
                }
            );
        }
    });
});
</script>
