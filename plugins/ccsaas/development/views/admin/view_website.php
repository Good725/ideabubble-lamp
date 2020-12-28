<div class="alert_area"><?= isset($alert) ? $alert : IbHelpers::get_messages() ?></div>
<div class="">
<?php
$form = new IbForm('ccsaas-host-form', '/api/ccsaas/create_website', 'post', ['layout' => 'vertical']);
$form->data_object = $data;
echo $form->start(['title' => false]);

//$form->id = $data['id'];
echo $form->hidden('id');
echo $form->input('Hostname', 'hostname', $data->hostname, ['placeholder' => 'Hostname']);
if (Settings::instance()->get('ccsaas_mode') == Model_Ccsaas::CENTRAL) {
    echo $form->combobox('Server', 'branch_server_id', $servers, $data->branch_server_id);
}
echo $form->combobox('Project Folder', 'project_folder', $project_folders, $data->project_folder);
echo $form->datepicker('Starts', 'starts', $data->starts);
echo $form->datepicker('Expires', 'expires', $data->expires);
?>
<div class="form-group">
    <div class="col-sm-12">
    <?php echo Form::ib_checkbox_switch('Is Trial', 'is_trial', 1, @$data->is_trial == 1)?>
    </div>
</div>
<?php
echo $form->end();
?>
</div>