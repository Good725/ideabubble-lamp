<style>
    .edit_profile_wrapper {
        font-weight: 200;
    }

    .edit_profile_wrapper .form-radio-helper {
        font-size: .625em;
        top: -.1875em;
    }

    .edit_profile_wrapper h3 {
        margin-bottom: 1em;
    }
</style>

<div class="edit_profile_wrapper">
<form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=contact" method="post">
    <input type="hidden" name="contact_id" value="<?= $contact_id ?>" />

    <?= $section_switcher ?>

    <?php $section_number = 0; ?>

    <section>
        <h3><?= ++$section_number ?>. <?= __('Photo') ?></h3>

        <?php
        $gravatar_enabled = Settings::instance()->get('gravatar_enabled');
        $use_gravatar = ($gravatar_enabled && ($user['use_gravatar'] == 1 || trim($user['avatar']) == ''));
        ?>

        <div class="form-group gutters">
            <div class="col-xs-6 col-sm-4 col-md-3">
                <div class="profile-cms-avatar-controls hide-for-gravatar<?= $use_gravatar ? ' hidden' : '' ?>">
                    <button type="button" data-accept=".jpg,.jpeg,.png" class="btn-link" id="multi_upload_button" style="border:none;padding:0;" data-onsuccess="set_profile_avatar" data-preset="Avatars">
                        <img src="<?= trim($user['avatar']) ? URL::get_avatar() : URL::get_gravatar('dummy@ideabubble.ie', 156) ?>" id="edit-profile-cms-avatar" style="height: 156px; width: 156px;border-radius:2px;" />
                    </button>

                    <input type="hidden" name="avatar" value="<?= $user['avatar'] ?>" id="edit-profile-avatar-filename" />
                </div>

                <?php if ($gravatar_enabled): ?>
                    <div class="show-for-gravatar<?= !$use_gravatar ? ' hidden' : '' ?>">
                        <img src="<?= URL::get_gravatar($user['email'], 156); ?>" alt="Avatar" style="max-width: 100%;" />
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-xs-6 col-sm-8 col-md-9">
                <?php if ($gravatar_enabled): ?>
                    <?= Form::ib_radio(__('Use Gravatar'), 'use_gravatar', 1, $use_gravatar, array('id' => 'edit-profile-use_gravatar')); ?>

                    <br />

                    <?= Form::ib_radio(__('Upload your photo'), 'use_gravatar', 0, !$use_gravatar, array('id' => 'edit-profile-use_local')); ?>

                    <div class="show-for-gravatar<?= !$use_gravatar ? ' hidden' : '' ?>">
                        <a href="http://en.gravatar.com/emails/" target="_blank"><?= __('Change avatar with Gravatar') ?></a>
                    </div>

                <?php else: ?>
                    <input type="hidden" name="use_gravatar" value="<?= ($user['use_gravatar'] == 1 || trim($user['avatar']) == '') ? 1 : 0 ?>" />
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php $preference_index = 0; ?>

    <section>
        <h3><?= ++$section_number ?>. <?= __('Contact details') ?></h3>

        <div class="form-group">
            <div class="col-sm-6">
                <?= Form::ib_input(__('First Name'), 'name', $user['name'], array('class' => 'validate[required]', 'id' => 'edit_profile_name')) ?>

                <small class="text-default hidden-sm hidden-md hidden-lg hidden-xl"><?= $role->role ?></small>
            </div>

            <div class="col-sm-6">
                <?= Form::ib_input(__('Last Name'), 'surname', $user['surname'], array('class' => 'validate[required]', 'id' => 'edit_profile_surname')) ?>
            </div>
        </div>
        <?php if(Auth::instance()->has_access('user_profile_date_of_birth') || Auth::instance()->has_access('user_profile_nationality')):?>
            <div class="form-group">
                <?php if(Auth::instance()->has_access('user_profile_date_of_birth')):?>
                    <div class="col-sm-6">
                    <?php
                    $value = (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') && isset($contact3)) ? $contact3->get_date_of_birth() : $contact->get_dob();
                    echo Form::ib_datepicker(__('Date of Birth'), 'dob', $value, array(), array('class' => 'form-datepicker dob', 'id' => 'edit_profile_dob'));
                    ?>
                </div>
                <?php endif?>

                <?php if(Auth::instance()->has_access('user_profile_nationality')):?>
                    <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')): ?>
                <div class="col-sm-6">
                    <?php
                    array_unshift($nationalities, ''); // Add blank option
                    $nationalities = array_combine($nationalities, $nationalities);
                    $selected      = isset($contact3) ? $contact3->get_nationality() : '';
                    echo Form::ib_select(__('Nationality'), 'nationality', $nationalities, $selected, array('class' => 'ib-combobox', 'id' => 'nationality'));
                    ?>
                </div>
            <?php endif; ?>
                <?php endif?>
            </div>
        <?php endif?>

        <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')): ?>
        <?php if(Auth::instance()->has_access('user_profile_gender') || Auth::instance()->has_access('user_profile_medical_conditions')):?>
            <div class="form-group">
                <?php if(Auth::instance()->has_access('user_profile_gender')):?>
                <div class="col-sm-6">
                    <?php
                    $options = array('' => '', 'M' => __('Male'), 'F' => __('Female'));
                    echo Form::ib_select(__('Gender'), 'gender', $options, $contact3->get_gender());
                    ?>
                </div>
                <?php endif?>
                <?php if(Auth::instance()->has_access('user_profile_medical_conditions')):?>
                <div class="col-sm-6">
                    <?php
                    $contact_preference_ids = array();
                    foreach ($contact3->get_preferences() as $preference) {
                        $contact_preference_ids[]  = $preference['preference_id'];
                    }

                    $options = array();
                    if (!empty($preferences)) {
                        foreach ($preferences as $preference) {
                            if ($preference['group'] == 'special') {
                                $options[$preference['id']] = $preference['label'];
                            }
                        }
                    }
                    $preference_index = count($options);

                    echo Form::ib_select(__('Medical'), 'preferences[]', $options, $contact_preference_ids, array('multiple' => 'multiple', 'id' => 'preferences_medical'));
                    ?>
                </div>
                <?php endif?>
            </div>
        <?php endif?>
        <?php endif; ?>

        <div class="form-group">
            <div class="col-sm-6">
                <?= Form::ib_input(__('Login Email'), 'email', $user['email'], ['id' => 'edit_profile_email', 'readonly' => 'readonly']); ?>
            </div>

            <div class="col-sm-6">
                <?php
                $country_attributes = array(
                        'class'    => 'mobile-international_code validate[required]',
                        'readonly' => false,
                         'disabled' => false,
                         'id'       => 'mobile-international_code');
                     $country_code_selected = !empty($user['country_dial_code_mobile']) ? $user['country_dial_code_mobile'] : '353';
                     $options = Model_Country::get_dial_code_as_options($country_code_selected);
                     $country_code = Model_Country::get_country_code_by_country_dial_code($country_code_selected);
                     $mobile_codes_array = Model_Country::get_phone_codes_country_code($country_code);
                     $mobile_codes = array('' => '');
                     foreach($mobile_codes_array as $mobile_code) {
                         $mobile_codes[$mobile_code['dial_code']] = $mobile_code['dial_code'];
                     }
                    $code_attributes = array(
                        'class'    => 'mobile-code validate[required]',
                        'readonly' => false,
                        'disabled' => false,
                        'id'       => 'dial_code_mobile',
                );
                $code_selected = isset($user['dial_code_mobile']) ? $user['dial_code_mobile'] : null;
                     ?>
                <div class="col-sm-4" style="padding-left: 0;">
                    <?= Form::ib_select(__('Country'), 'country_dial_code_mobile', $options, $country_code_selected,  $country_attributes)?>
                </div>
                <div class="col-sm-4 dial_code" style="padding-left: 0;">
                    <?= !empty($mobile_codes_array) ?
                    Form::ib_select(__('Code'), 'dial_code_mobile', $mobile_codes, $code_selected, $code_attributes) :
                    Form::ib_input(__('Code'), 'dial_code_mobile', $code_selected, array('id' => 'dial_code_mobile', 'class' => 'validate[required]'))?>
                </div>
                <div class="col-sm-4" style="padding-left: 0; padding-right: 0;">
                    <?= Form::ib_input(__('Mobile'), 'mobile', $user['mobile'], array('id' => 'edit_profile_phone', 'class' => 'validate[required]')); ?>
                </div>
            </div>
        </div>
        <?php if(Settings::instance()->get('two_step_authorization')):?>
        <?php $auth_types = array('None' => __('None'));
        if (Auth::instance()->has_access('user_auth_2step_sms')) {
            $auth_types['SMS']  = __('SMS Code');
        }
        if (Auth::instance()->has_access('user_auth_2step_email')) {
            $auth_types['Email'] = __('Email Code');
        }
        ?>
        <div class="form-group">
            <div class="col-sm-6">
                    <?= Form::ib_select('Two Step Authentication', 'two_step_auth', $auth_types , @$user['two_step_auth'])?>
            </div>
        </div>
        <?php endif?>

    </section>

    <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')): ?>
        <?php foreach ($contact3->get_preferences() as $save_preference): ?>
            <?php
            $add = true;
            foreach ($privileges_preferences as $preference) {
                if ($save_preference['preference_id'] == $preference['id']) {
                    $add = false;
                    break;
                }
            }
            ?>

            <?php if ($add): ?>
                <input type="hidden" name="preferences[<?= $preference_index ?>]" value="<?= $save_preference['preference_id'] ?>" />
                <?php ++$preference_index; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <section>
        <div class="form-action-group" id="ActionMenu">
            <button type="submit" class="btn btn-primary profile_save_btn" data-redirect="save" value="save"><?=__('Save')?></button>
            <button type="submit" class="btn btn-primary profile_save_btn" data-redirect="save_and_exit" value="save_and_exit"><?=__('Save & Exit')?></button>
            <button type="reset" class="btn btn-default"><?=__('Reset')?></button>
            <a href="/admin" class="btn btn-cancel">Cancel</a>
        </div>
    </section>
</form>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="profile-delete-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/profile/delete" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete Profile') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('Are you sure you want to delete your profile? You will not be able to log in. (You can register again.)') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" id="delete-profile-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Go Back') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $("#edit-profile-form").on(
        "submit",
        function(){
            if (!$("#edit-profile-form").validationEngine('validate')) {
                return false;
            }
        }
    );
</script>
<?php
echo View::factory('snippets/modal')->set([
    'id'     => 'profile-data_cleanse-confirm-modal',
    'title'  => __('Confirm data cleanse'),
    'body'   => __('Are you sure you wish to have data related to your account cleansed?'),
    'footer' => '<button type="button" class="btn btn-primary" id="profile-data_cleanse-btn">Yes</button>'.
                '<button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>'
]);
?>
<?php
echo View::factory('snippets/modal')->set([
    'id'     => 'profile-data_cleanse-submitted-modal',
    'title'  => _('Data cleanse request submitted'),
    'body'   => __('Your request has been sent to our Data Privacy Office. Thank you. We will be in touch'),
    'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>'
]);
?>