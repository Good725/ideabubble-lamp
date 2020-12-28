<div class="edit_profile_wrapper">
    <input type="hidden" name="contact_id" value="<?= $contact_id ?>"/>
    <input type="hidden" name="org_contact_id" value="<?= $org_contact3->get_id() ?>"/>
    
    <?= $section_switcher ?>

    <section>
        <h3><?= __('Organisation details') ?></h3>

        <div class="form-group">
            <div class="col-sm-6">
                <?= Form::ib_input(__('Name'), 'name', $org_contact3->get_first_name(),
                    array('class' => 'form-input', 'id' => 'edit_profile_name', 'disabled' => 'disabled')) ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
                <?= Form::ib_input(__('Email'), 'email', $org_contact3->get_email(),
                    array('id' => 'edit_profile_email', 'disabled' => 'disabled')); ?>
            </div>

            <div class="col-sm-6">
                <?php
                $mobile_number = '';
                $mobile = $org_contact3->get_mobile(array('components' => true));
                if(!empty($mobile) && is_array($mobile)) {
                    $mobile_number = !empty($mobile['country_code']) ?  '+' . $mobile['country_code'] . ' ' . $mobile['code'] . ' ' . $mobile['number'] : $mobile['number'];
                } elseif(!empty($mobile)) {
                    $mobile_number = $mobile;
                }?>
                <?= Form::ib_input(__('Mobile'), 'mobile', $mobile_number, array(
                    'id' => 'edit_profile_phone',
                    'disabled' => 'disabled'
                )); ?>
            </div>
        </div>
    </section>
    <?php if (Model_Contacts3::get_contact_type($contact3->get_type())['label'] == 'Org rep'): ?>
        <section>
            <h3><?= __('Your organisation members') ?></h3>
            <table class="table" id="organisation_members_table">
                <thead>
                <tr>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                </tr>
                </thead>
            </table>
        </section>
    <?php endif; ?>
</div>
<?php if (Model_Contacts3::get_contact_type($contact3->get_type())['name'] == 'org_rep'): ?>
    <script>
        $(document).ready(function () {
            initTable('#organisation_members_table');
        });

        function initTable(id) {
            var organisation_contact_id = $('input[name="org_contact_id"]').val();
            var ajax_source = `/admin/contacts3/ajax_get_organisation_members_datatable?org_contact_id=${organisation_contact_id}`;
            var settings = {
                "aaSorting": [[1, 'asc']],
            };
            return $(id).ib_serverSideTable(ajax_source, settings);
        }
    </script>
<?php endif; ?>