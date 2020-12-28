<?php //ob_clean();header('content-type: text/plain');print_r(debug_backtrace());exit;
$custom_checkout   = Settings::instance()->get('checkout_customization');
$disable_fields    = isset($disable_fields) ? $disable_fields : false;
$is_interview      = isset($is_interview)   ? $is_interview   : false;
$is_microsite      = isset($is_microsite)   ? $is_microsite   : false;
// Showing the contact data depending on the role or the section is guardian, there's not a student section and guardian's not set
$show_contact_data = (isset($contact) && !($section == 'guardian' && in_array("student", $sections) && !isset($guardian)) &&
        ((!$contact->has_role('student') && $section != 'student')
        || ($section == 'student'))
    || (isset($contact) && $schedule->is_group_booking));
$prefix            = isset($prefix) ? $prefix : '';
$checkoutDetails   = isset($checkoutDetails) ? $checkoutDetails : array();
$country_options = '<option value=""></option>'.html::optionsFromRows('code', 'name', Model_Residence::get_all_countries(), '');
// Give an array of students for only a group booking
$multiple_students = ($schedule->is_group_booking && ($section != 'guardian' || isset($guardian)));

if (isset($contact)) {
    $roles = $contact->get_roles_stubs();

    if (is_numeric($contact->get_id())) { // readonly for existing users/contacts
        if ((isset($guardian) && $section == 'guardian')  || ($section != 'guardian' && count(array_intersect($roles, ['student', 'org_rep'])))) {
            $disable_fields = true;
        }
    }
}

if ($show_contact_data) {
    $emails = $contact->get_contact_notifications('email');
    $email = @$emails[0]['value'];
} else {
    $email = '';
}
?>
<?php if ($section == 'student') { ?>
    <input type="hidden" name="student_id" value="<?=isset($contact) ? $contact->get_id() : '';?>" />
<?php } ?>
<?php if ($section == 'guardian' && isset($contact) && !$contact->has_role('student')) { ?>
    <input type="hidden" name="guardian_id" value="<?=$contact->get_id();?>" />
<?php } ?>

<div class="form-group">
    <div class="col-sm-4">
        <?php
        $value = trim(isset($checkoutDetails['firstname']) ? $checkoutDetails['firstname'] : '');
        $value = ($value == '' && $show_contact_data && $contact->get_first_name() != 'first_name') ? $contact->get_first_name() : $value;
        $id = 'checkout-' . $prefix . 'first_name' . (($multiple_students) ? "_{$student_counter}" : '');
        $name = $prefix . 'first_name' . (($multiple_students) ? '[]' : '');
        $attributes = array(
            'autocomplete'  => 'given-name',
            'class'         => ($disable_fields ? '' : 'validate[required] capitalize'),
            'id'            => $id,
            'readonly'      => $disable_fields,
            'data-saveable' => true
        );
        echo Form::ib_input(__('First name'), $name, $value, $attributes);
        ?>
    </div>

    <div class="col-sm-4">
        <?php
        $value = trim(isset($checkoutDetails['lastname']) ? $checkoutDetails['lastname'] : '');
        $value = ($value == '' && $show_contact_data && $contact->get_last_name() != 'last_name') ? $contact->get_last_name() : $value;
        $id =  'checkout-' . $prefix . 'last_name' . (($multiple_students) ? "_{$student_counter}" : '');
        $name = $prefix . 'last_name' . (($multiple_students) ? '[]' : '');
        $attributes = array(
            'autocomplete'  => 'family-name',
            'class'         => ($disable_fields ? '' : 'validate[required] capitalize'),
            'id'            => $id,
            'readonly'      => $disable_fields,
            'data-saveable' => true
        );
        echo Form::ib_input(__('Last name'), $name, $value, $attributes);
        ?>
    </div>

    <div class="col-sm-4">
        <?php
        $email_field_id = 'checkout-' . $prefix . 'email' . ((isset($contact) && !$contact->is_new_contact()) ? '_' . $contact->get_id() : '') . (($multiple_students) ? "_{$student_counter}" : '');
        $name = $prefix . 'email' . (($multiple_students) ? '[]' : '');
        $value          = isset($checkoutDetails['email']) ? $checkoutDetails['email'] : '';
        if (@$checkoutDetails['email']) {
            $value = $checkoutDetails['email'];
        } else {
            $value          = ($show_contact_data && $contact->get_email()) ? $contact->get_email() : (isset($email) ? $email : $value);
        }

        $email_required = isset($email_required) ? $email_required : true;

        $attributes     = array(
            'autocomplete'  => 'email',
            'class'         => ($disable_fields ? '' : 'validate['.($email_required ? 'required,' : '').'custom[email]]'),
            'id'            => $email_field_id,
            'readonly'      => $disable_fields,
            'data-saveable' => true
        );
        echo Form::ib_input(__('Email'), $name, $value, $attributes);
        ?>
    </div>
</div>

<?php if ($section == 'guardian' && in_array($custom_checkout, ['bcfe', 'bc_language', 'sls'])): ?>
    <div class="form-group">
        <div class="col-sm-6">
            <?= Form::ib_input(__('Address 1'), 'address1') ?>
        </div>

        <div class="col-sm-6">
            <?= Form::ib_input(__('Address 2'), 'address2') ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-6">
            <?= Form::ib_input(__('City'), 'town') ?>
        </div>

        <div class="col-sm-6">
            <?= Form::ib_input(__('Postal Code'), 'postcode') ?>
        </div>
    </div>

    <div class="form-group vertically_center">
        <div class="col-xs-12 col-sm-6">
            <?= Form::ib_select(__('Country'), 'country', $country_options); ?>
        </div>
    </div>
<?php endif; ?>

    <?php
    if ($show_contact_data) {
        $mobile = isset($contact) ? $contact->get_mobile(array('components' => true)) : '';
        $year_id = isset($contact) ? $contact->get_year_id() : '';
    } else {
        $mobile = null;
        $year_id = null;
    }
    if (@$checkoutDetails['telephone']) {
        $mobile = array('code' => substr($checkoutDetails['telephone'], 0, 3), 'number' => substr($checkoutDetails['telephone'], 3));
    }
    if ($key == 0) {
        $mandatory_mobile = (bool) Settings::instance()->get('checkout_mandatory_mobile_number');
    } else {
        $mandatory_mobile = (bool) Settings::instance()->get('checkout_delegate_mandatory_mobile_number');
    }
    ?>

    <input type="hidden" name="mobile" value="<?= isset($mobile['full_number']) ? $mobile['full_number'] : '' ?>" />
    <input type="hidden" name="mobile_dial_code" value="<?= isset($mobile['dial_code']) ? $mobile['dial_code'] : '' ?>" />

    <?php if ($section == 'student' && Settings::instance()->get('checkout_student_year_dropdown')): ?>
        <div class="form-group">
            <div class="col-sm-6">
                <?php
                $years   = Model_Years::get_all_years();
                $options = array('' => '');
                foreach ($years as $year) {
                    $options[$year['id']] = $year['year'];
                }
                $year_id = isset($checkoutDetails['year_id']) ? $checkoutDetails['year_id'] : $year_id;
                $attributes = array(
                    'class' => (count($years) > 0 ? 'validate[required]' : ''),
                    'id'    => 'checkout-year'
                );
                echo Form::ib_select(__('School year'), $prefix.'year_id', $options, $year_id, $attributes);
                ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <div class="col-xs-12 mb-2">
            <label for="checkout-mobile-code">
                <?= (in_array($custom_checkout, ['bcfe', 'bc_language', 'sls']) && $section == 'guardian')
                    ? __('Phone Number')
                    :  __('Mobile Number')
                ?><?= $mandatory_mobile ? '*' : '' ?>
            </label>
        </div>

        <div class="col-xs-6 col-sm-4">
            <?php
            $country_code_selected = !empty($mobile['country_code']) ? $mobile['country_code'] : '353';
            $options = Model_Country::get_dial_code_as_options($country_code_selected);
            $id = 'checkout-' . ($key == 0 ? '' : $prefix) . 'mobile-international_code' . (($multiple_students) ? "_{$student_counter}" : '');
            $name  = ($key == 0 ? '' : $prefix) . 'mobile_international_code' . (($multiple_students) ? '[]' : '');
            $attributes = array(
                'class'    => 'checkout-mobile-international_code '.($mandatory_mobile && !$disable_fields ? 'validate[required]' : ''),
                'disabled' => false,
                'id'       => $id,
            );
            if (($disable_fields && $mobile['number'] && !isset($student_counter))) {
                $attributes['readonly'] = true;
            }
            echo Form::ib_select(__('Country'), $name, $options, $country_code_selected , $attributes)
            ?>
        </div>

        <?php if (in_array($custom_checkout, ['bcfe', 'bc_language', 'sls']) && $section == 'guardian'): ?>
            <?php // Drop this section ?>
        <?php else: ?>
            <div class="col-xs-6 col-sm-4">
                <div class="checkout-mobile-code-wrapper">
                    <?php
                    $country_code_alpha = Model_Country::get_country_code_by_country_dial_code($country_code_selected);
                    $mobile_provider_codes = Model_Country::get_phone_codes_country_code($country_code_alpha);
                    $options = array('' => '');
                    foreach ($mobile_provider_codes as $code) {
                        if (is_array($code)) {
                            $options[$code['dial_code']] = $code['dial_code'];
                        } else {
                            $options[$code] = $code;
                        }
                    }
                    $id = 'checkout-' . ($key == 0 ? '' : $prefix) . 'mobile-code' . (($multiple_students) ? "_{$student_counter}" : '');
                    $name = ($key == 0 ? '' : $prefix) . 'mobile_code' . (($multiple_students) ? '[]' : '');
                    $attributes = array(
                        'class'    => 'checkout-mobile-code '.($mandatory_mobile && !$disable_fields ? 'validate[required]' : ''),
                        'id'       => $id,
                    );
                    if (($disable_fields && $mobile['number'] && !isset($student_counter))) {
                        $attributes['readonly'] = true;
                    }
                    $selected = isset($mobile['code']) ? $mobile['code'] : null;
                    if (!empty($mobile_provider_codes)) {
                        echo Form::ib_select(__('Code'), $name, $options, $selected, $attributes);
                    } else  {
                        echo Form::ib_input(__('Code'), $name, $selected, $attributes);
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-xs-12 col-sm-4">
            <?php
            $value      = isset($mobile['number']) ? $mobile['number'] : '';
            $id = 'checkout-mobile-number' . (($multiple_students) ? "_{$student_counter}" : '');
            $name = ($key == 0 ? '' : $prefix) . 'mobile_number' . (($multiple_students) ? '[]' : '');
            $attributes = array(
                'class'       => $mandatory_mobile && !$disable_fields ? 'validate[required]' : '',
                'readonly'    => ($disable_fields && $value != ''),
                'maxlength'   => 10,
                'type'        => 'tel',
                'id'          => $id
            );
            echo Form::ib_input(__('Number'), $name, $value, $attributes);
            ?>
        </div>

        <?php if ($section == 'guardian' && in_array($custom_checkout, ['bcfe', 'bc_language', 'sls'])): ?>
            <div class="col-sm-4">
                <?= Form::ib_input('Relationship to student', 'relationship_to_student')
                ?>
            </div>
        <?php endif; ?>

    </div>
    <?php if(Settings::instance()->get('how_did_you_hear_enabled') == 1):?>
        <?php if ($section == 'guardian' && isset($contact)): ?>
            <?php
            $attributes = array(
            'class'    => 'checkout-how-did-you-hear',
            'id'       => 'how_did_you_hear_select',
            );?>
            <div class="form-group">
                <div class="col-xs-12 col-sm-4">
                    <label for="how_did_you_hear_select">How did you hear about us?</label>
                    <?= Form::ib_select('', 'how_did_you_hear', html::optionsFromRows('value', 'label', $how_did_you_hear), null, $attributes, array('please_select' => true))?>
                </div>
            </div>
        <?php endif?>
    <?php endif?>

    <?php if ($section == 'student' && $custom_checkout == 'sls'): ?>
        <div class="form-group vertically_center">
            <div class="col-xs-7 col-sm-3">
                <?= __('Dietary requirements'); ?>
            </div>

            <div class="col-xs-5 col-sm-3">
                <?php
                $options = array('1' => __('Yes'), '0' => __('No'));
                $attributes = array('class' => 'stay_inline', 'data-form-show-option' => 1, 'data-target' => '#checkout-dietary_requirements-wrapper');
                echo Form::btn_options('has_dietary_requirement', $options, @$extra_data['has_dietary_requirement'] ?: 0, false, [], $attributes);
                ?>
            </div>
        </div>

        <div class="form-group <?=@$extra_data['has_dietary_requirement'] == 1 ? '' : 'hidden' ?>" id="checkout-dietary_requirements-wrapper">
            <div class="col-sm-12">
                <label for="checkout-dietary_requirements"><?= __('Please mention any dietary requirements.') ?></label>

                <?php
                $attributes = ['id' => 'checkout-dietary_requirements', 'rows' => 3];
                echo Form::ib_textarea(null, 'dietary_requirements', @$extra_data['dietary_requirements'], $attributes);
                ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-7 col-sm-3">
                <?= __('Medical conditions'); ?>
            </div>

            <div class="col-xs-5 col-sm-3">
                <?php
                $options = array('1' => __('Yes'), '0' => __('No'));
                $attributes = array('class' => 'stay_inline', 'data-form-show-option' => 1, 'data-target' => '#checkout-medical_conditions-wrapper');
                echo Form::btn_options('has_medical_conditions', $options, @$extra_data['has_medical_conditions'], false, null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group <?=@$extra_data['has_medical_conditions'] == 1 ? '' : 'hidden' ?>" id="checkout-medical_conditions-wrapper">
            <div class="col-sm-12">
                <label for="checkout-medical_conditions"><?= __('Please specify any existing allergies, intolerances or other medical conditions we should be aware of.') ?></label>

                <?php
                $attributes = ['id' => 'checkout-medical_conditions', 'rows' => 3];
                echo Form::ib_textarea(null, 'medical_conditions', @$extra_data['medical_conditions'], $attributes);
                ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($logged_contact) && $logged_contact->has_role('student') && $section == 'guardian' && @$application_payment == false && Settings::instance()->get('bookings_student_auth_enabled') == 1): ?>
        <div>
            <p><?= __('We need your your guardian to authorize you to complete booking.') ?></p>

            <input type="button" value="Send Authorization Code" class="button button--continue" id="guardian_auth_send" />

            <div class="hidden form-group" id="guardian_auth_sent">
                <p><?= __('Please enter the verification code you received below') ?></p>

                <div class="col-sm-12">
                    <?php
                    $attributes = array(
                        'class' => 'validate[required]',
                        'id'    => 'checkout-guardian-auth-code'
                    );
                    echo Form::ib_input(__('Verification Code'), 'guardian_auth_code', null, $attributes);
                    ?>
                    <input type="hidden" name="guardian_auth_id" value="" />
                </div>
            </div>
        </div>
    <?php endif; ?>


<?php $contact_preference_ids = isset($contact) ? $contact->get_preferences_ids() : array(); ?>

<div class="form-group">
    <?php if ($schedule->is_group_booking == "1"): ?>
        <?php if (empty($is_already_delegate)): ?>
            <div class="col-sm-5">
                <?php
                    $label = __('I will be a delegate on this course');
                    echo Form::ib_checkbox($label, 'org_rep_delegate_confirmation', null, false,
                        array('id' => 'org-rep-delegate-confirmation'));
                ?>
            </div>
        <?php endif; ?>
    <?php endif;
        if (!empty($subscribe_preference)):
            // If the user is already subscribed, do not show this checkbox.
            // This can still be toggled off by editing the user's profile. (Uncheck the "Email Marketing" preference.)
            if (!in_array($subscribe_preference['id'], $contact_preference_ids) && !in_array($custom_checkout,
                    ['bcfe', 'bc_language', 'sls'])): ?>
                <div class="<?= ($schedule->is_group_booking == "1") ? "col-sm-7" : 'col-sm-12' ?>">
                    <?php
                        $label = __('Subscribe to our email newsletter to receive great offers');
                        echo Form::ib_checkbox($label, 'preferences[]', $subscribe_preference['id'], false,
                            array('id' => 'checkout-subscribe'));
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
</div>

