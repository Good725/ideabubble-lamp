<?php
ob_start();
$reporter = Auth::instance()->get_contact();
$severity_options = ['' => '-- Please select --'] + array_combine($severities, $severities);

$form = new IbForm('incidents-report-form', '/'.($mode == 'backend' ? 'admin' : 'frontend').'/safety/save_incident', 'post', ['layout' => 'vertical']);
$form->published_field = false;

echo $form->start(['title' => false]);
echo $form->input('Title', 'title', null, ['placeholder' => 'Briefly describe the incident']);
echo $form->hidden('status', 'Pending');
echo $form->hidden('id');
echo $form->combobox('Location', 'location_id', $locations->as_options(['combobox' => true]), null, ['data-placeholder' => 'Select location']);
?>
<div class="form-group mb-0">
    <div class="col-md-6"><?= $form->datepicker('Date of incident', 'date', null, [], ['class' => 'validate[required]', 'placeholder' => 'e.g. 01/Jan/2020']); ?></div>
    <div class="col-md-6"><?= $form->timepicker('Time of incident', 'time', null, ['class'=> 'validate[required]', 'placeholder' => 'e.g. 13:00']); ?></div>
</div>

<?php foreach (['injured_people' => 'Injured people', 'witnesses' => 'Witnesses'] as $type => $label): ?>
    <label><?= htmlentities($label) ?></label>

    <div class="incident-people-wrapper" id="incidents-report-form-<?= $type ?>-wrapper">
        <div class="form-group vertically_center incident-person">
            <div class="col-sm-4"><?= Form::ib_input(null, $type.'[0][first_name]', null, ['placeholder' => 'First name']) ?></div>
            <div class="col-sm-4"><?= Form::ib_input(null, $type.'[0][last_name]',  null, ['placeholder' => 'Last name'])  ?></div>
            <div class="col-sm-3">
                <button type="button" class="btn btn-primary button bg-primary form-btn w-100 incident-person-add" data-type="<?= $type ?>">Add another</button>
            </div>
            <div class="col-sm-1 p-0 text-center">
                <button type="button" class="btn-link button--plain incident-person-remove" title="Remove">
                    <span class="icon-times fa fa-times"></span>
                </button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php
if ($mode == 'backend') {
    echo $form->select('Severity', 'severity', $severity_options);
}
echo $form->textarea('What I observed', 'description');
echo $form->textarea('Action taken', 'action_taken', null, ['placeholder' => 'Describe the action that was taken']);

if ($mode == 'backend') {
    echo $form->ajax_typeselect('Reporter', 'reporter_id', $reporter->id, $reporter->get_name_and_details(), [], [], ['url' => '/admin/contacts3/ajax_get_all_contacts_ui?show_email=1']);
}
?>

<?php if ($mode != 'backend'): ?>
    <h3>Reporter</h3>
    <?php $attributes = $reporter->id ? ['readonly' => 'readonly'] : ['class' => 'validate[required]']; ?>

    <div class="form-group mb-0">
        <div class="col-md-6"><?= $form->input('First name',  'reporter[first_name]', $reporter->first_name, $attributes); ?></div>
        <div class="col-md-6"><?= $form->input('Last name',   'reporter[last_name]',  $reporter->last_name,  $attributes); ?></div>
    </div>

    <div class="form-group mb-0">
        <div class="col-md-6"><?= $form->email('Email',  'reporter[email]',  $reporter->get_notification('email'),  $attributes); ?></div>
        <div class="col-md-6"><?= $form->phone('Mobile', 'reporter[mobile]', $reporter->get_notification('mobile'), ['readonly' => (bool) $reporter->id]); ?></div>
    </div>
<?php endif; ?>

<?php
if ($mode == 'backend') {
    echo $form->textarea('Notes', 'notes');

    echo $form->modal_action_buttons(['buttons' => [
        ['type' => 'button', 'text' => 'Resolve', 'attributes' => ['type' => 'submit', 'name' => 'status', 'value' => 'Resolved', 'id' => 'incidents-report-form-resolve']]
    ]]);
} else {
    echo '<div class="text-center"><button class="button" type="submit">Submit</button></div>';
}

echo $form->end();

$form = ob_get_clean();
?>

<?php
if ($mode == 'backend') {
    echo View::factory('snippets/modal')->set([
        'id'     => 'incidents-report-modal',
        'title'  => 'Incident report',
        'body'   => $form,
    ]);
} else {
    echo $form;
}
?>

<style>
    .incident-person:only-child .incident-person-remove,
    .incident-person:not(:last-child) .incident-person-add { display: none; }

    .form-horizontal .form-group { margin-left: -15px;margin-right: -15px;}

    #incidents-report-form{max-width:800px;}
</style>