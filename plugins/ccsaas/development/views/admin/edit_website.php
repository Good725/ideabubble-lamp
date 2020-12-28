<div class="alert_area"><?= isset($alert) ? $alert : IbHelpers::get_messages() ?></div>
<div class="">
<?php
$form = new IbForm('ccsaas-host-form', '/api/ccsaas/create_website', 'post', ['layout' => 'vertical']);
$form->data_object = $data;
echo $form->start(['title' => false]);

//$form->id = $data['id'];
echo $form->hidden('id');
echo $form->hidden('contact_id');
echo $form->input('Contact', null, trim($contact->get_first_name() . ' ' . $contact->get_last_name()), array('id' => 'ccsaas-contact'));
echo $form->input('Hostname', 'hostname', $data->hostname, ['placeholder' => 'Hostname']);
if (Settings::instance()->get('ccsaas_mode') == Model_Ccsaas::CENTRAL) {
    echo $form->combobox('Server', 'branch_server_id', $servers, $data->branch_server_id);
}
echo $form->combobox('Project Folder', 'project_folder', $project_folders, $data->project_folder ?: 'shop1');
echo $form->combobox('Theme', 'cms_skin', Model_Settings::cms_skin_options(null, true), $data->project_folder ?: 'wine');
echo $form->datepicker('Starts', 'starts', $data->starts);
echo $form->datepicker('Expires', 'expires', $data->expires);
?>
<div class="form-group">
    <div class="col-sm-12">
    <?php echo Form::ib_checkbox_switch('Is Trial', 'is_trial', 1, @$data->is_trial == 1)?>
    </div>
</div>
<?php
$form->delete_permission = 'ccsaas_edit';
$form->delete_url = '/api/ccsaas/delete_website';
echo $form->action_buttons();

echo $form->end();
?>
</div>