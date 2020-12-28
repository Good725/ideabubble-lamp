<?php
$user            = Auth::instance()->get_user();
$contacts        = Model_Contacts3::get_contact_ids_by_user($user['id']);

if (@$user['id']) {
    $contact = current(Model_Contacts3::get_contact_ids_by_user($user['id']));
    $contact_id = @$contact['id'];
    $contact_details = new Model_Contacts3($contact_id);
    $is_student = $contact_details->has_role('student');
}
if (count($contacts)) {
    $contact = new Model_Contacts3($contacts[0]['id']);
    $family = new Model_Family($contact->get_family_id());
    $family_members = Model_Contacts3::get_family_members($contact->get_family_id());
    $students = array();
    foreach ($family_members as $family_member) {
        if (in_array('student', $family_member['has_roles']) || in_array('mature', $family_member['has_roles'])) {
            $students[] = $family_member;
        }
    }
} else {
    $contact = null;
    $family = null;
    $family_members = array();
    $students = array();
}
$selected_student_id = @$_REQUEST['student_id'];
$application_payment = isset($application_payment) ? $application_payment : false;
?>

<h4>Booking for</h4>

<?php if (($user == false || empty($contact)) && !$application_payment): ?>
    <input type="hidden" name="student_id" value="" />
    <button type="button" class="button button--book" data-toggle="collapse" data-target="#login-overlay"><?= __('Please log in') ?></button>
<?php else:?>
    <?php
    if ($contact) {
        $readonly = $application_payment;
        $selected_id = $readonly ? $contact->get_id() : $selected_student_id;
    } else {
        $readonly = true;
        $selected_id = false;
    }
    ?>

    <?php if ($application_payment == false): ?>
        <?php ob_start(); ?>
            <option value=""></option>

            <?php foreach ($students as $student): ?>
                <option
                    value="<?= $student['id'] ?>"
                    data-year-id="<?= $student['year_id'] ?>"
                    data-year="<?= $student['year'] ?>"
                    <?= ($student['id'] == $selected_id) ? ' selected="selected"' : ''?>
                    >
                    <?= $student['first_name'] . ' ' . $student['last_name'] ?>
                </option>
            <?php endforeach; ?>

            <option value="new" data-new="yes">Add new student</option>
        <?php $options = ob_get_clean(); ?>

        <?php
        $attributes = array('class' => 'validate[required] student_id', 'id' => 'student_id');
        if ($readonly) {
            $attributes['disabled'] = 'disabled';
        }
        echo Form::ib_select(__('Select student'), 'student_id', $options, null, $attributes);
        ?>
    <?php endif; ?>

    <?php if ($readonly): ?>
        <?php // Select lists cannot be made read-only. Instead, we must disable it and put its value in a hidden field. ?>
        <input type="hidden" name="student_id" value="<?= $selected_id ?>" />
    <?php else: ?>
        <script>
            $(document).on("ready", function(){
                $('[name="student_id"]').on("change", function() {
                    // Keep other fields in sync
                    $('select[name="student_id"]').val(this.value);

                    if (this.value == 'new') {
                        window.location.href = '/admin/profile/edit?section=contact&add_student=yes&redirect=' + encodeURIComponent(window.location.href);
                    }
                })
            });
        </script>
    <?php endif; ?>
<?php endif; ?>