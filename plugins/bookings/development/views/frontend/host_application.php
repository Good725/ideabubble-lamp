<?php
$site_template = ORM::factory('Engine_Template')->where('stub', '=', Settings::instance()->get('template_folder_path'))->find_undeleted();

if (trim($site_template->header)) {
    eval('?>'.$site_template->header);
} else {
    include Kohana::find_file('template_views', 'header');
}
?>

<div class="container">
    <div class="contact--left row">
        <?php if ($page_data['content']): ?>
            <div><?= $page_data['content'] ?></div>
        <?php endif; ?>

        <form action="/frontend/bookings/submit_host_application" method="post" class="validate-on-submit">
            <section>
                <h2 class="checkout-heading">
                    <span class="fa fa-address-card-o"></span>
                    <?= __('Contact details') ?>
                </h2>

                <div class="theme-form-content">
                    <div class="theme-form-inner-content">
                        <div class="alert alert-info">
                            <div class="row gutters vertically_center">
                                <div class="col-xs-1" style="max-width: 2.25rem;">
                                    <span class="fa fa-info-circle fa-2x"></span>
                                </div>

                                <div class="col-xs-11">
                                    <strong><?= __('Please enter your contact details here.') ?></strong><br />
                                    <?= __('This email and mobile will be used to get in touch regarding any queries.') ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label for="host_application-first_name"><?= __('First name') ?></label>
                                <?= Form::ib_input(null, 'first_name', null, ['class' => 'validate[required]', 'id' => 'host_application-first_name']) ?>
                            </div>

                            <div class="col-sm-6">
                                <label for="host_application-last_name"><?= __('Last name') ?></label>
                                <?= Form::ib_input(null, 'last_name', null, ['class' => 'validate[required]', 'id' => 'host_application-last_name']) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label for="host_application-address_1"><?= __('Address line 1') ?></label>
                                <?= Form::ib_input(null, 'address_1', null, ['class' => 'validate[required]', 'id' => 'host_application-address_1']) ?>
                            </div>

                            <div class="col-sm-6">
                                <label for="host_application-address_2"><?= __('Address line 2') ?></label>
                                <?= Form::ib_input(null, 'address_2', null, ['id' => 'host_application-address_2']) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label for="host_application-city"><?= __('City') ?></label>
                                <?= Form::ib_input(null, 'city', null, ['class' => 'validate[required]', 'id' => 'host_application-city']) ?>
                            </div>

                            <div class="col-sm-6">
                                <label for="host_application-postcode"><?= __('Postal code') ?></label>
                                <?= Form::ib_input(null, 'postcode', null, ['id' => 'host_application-postcode']) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label for="host_application-country"><?= __('Country') ?></label>
                                <?php
                                $options = ['' => __('Please select')];
                                $attributes = ['id' => 'host_application-country'];
                                if (!empty($countries)) {
                                    $countries_name  = array_column($countries, 'name');
                                    array_multisort($countries_name, SORT_ASC, $countries);
                                    foreach ($countries as $country) {
                                        $options[$country['id'].' - '.$country['name']] = $country['name'];
                                    }
                                    $attributes['class'] = 'validate[required]';
                                }

                                echo Form::ib_select(null, 'country', $options, null, $attributes);
                                ?>
                            </div>

                            <div class="col-sm-6">
                                <label for="host_application-occupation"><?= __('Occupation') ?></label>
                                <?= Form::ib_input(null, 'occupation', null, ['id' => 'host_application-occupation']) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label for="host_application-email"><?= __('Email address') ?></label>
                                <?php
                                $attributes = ['class' => 'validate[custom[email]]', 'id' => 'host_application-email'];
                                echo Form::ib_input(null, 'email', null, $attributes);
                                ?>
                            </div>

                            <div class="col-sm-6">
                                <label for="host_application-confirm_email"><?= __('Confirm email address') ?></label>
                                <?php
                                $attributes = ['class' => 'validate[custom[email],equals[host_application-email]]', 'id' => 'host_application-confirm_email'];
                                echo Form::ib_input(null, 'confirm_email', null, ['id' => 'host_application-confirm_email']);
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="host_application-mobile-international_code"><?= __('Phone Number') ?></label>
                            </div>

                            <div class="col-xs-6 col-md-3">
                                <?php
                                $options = Model_Country::get_dial_code_as_options('353');
                                $attributes = array(
                                    'class'    => 'checkout-mobile-international_code ',
                                    'id'       => 'host_application-mobile-international_code',
                                );
                                echo Form::ib_select(__('Country'), 'mobile_international_code', $options, '353', $attributes)
                                ?>
                            </div>

                            <input type="hidden" name="mobile_code" />

                            <div class="col-xs-6 col-md-3">
                                <?php
                                $attributes = [
                                    'type' => 'tel',
                                    'id' => 'host_application-mobile-number'
                                ];
                                echo Form::ib_input(__('Number'), 'mobile_number', null, $attributes);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="checkout-heading">
                    <span class="fa fa-users"></span>
                    <?= __('Host family details') ?>
                </h2>

                <div class="theme-form-content">
                    <div class="theme-form-inner-content clearfix">
                        <h3><?= __('Partner') ?></h3>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label for="host_application-partner_first_name"><?= __('First name') ?></label>

                                <?= Form::ib_input(null, 'partner_first_name', null, ['id' => 'host_application-partner_first_name']); ?>
                            </div>

                            <div class="col-sm-6">
                                <label for="host_application-partner_last_name"><?= __('Last name') ?></label>
                                <?= Form::ib_input(null, 'partner_last_name', null, ['id' => 'host_application-partner_last_name']); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label for="host_application-partner_email"><?= __('Email address') ?></label>

                                <?php
                                $attributes = ['class' => 'validate[custom[email]]', 'id' => 'host_application-partner_email'];
                                echo Form::ib_input(null, 'partner_email', null, $attributes);
                                ?>
                            </div>

                            <div class="col-sm-6">
                                <label for="host_application-partner_phone"><?= __('Phone number') ?></label>

                                <div class="row gutters">
                                    <div class="col-xs-6">
                                        <?php
                                        $options = Model_Country::get_dial_code_as_options('353');
                                        $attributes = array(
                                            'class'    => 'host_application-partner_phone_international_code ',
                                            'id'       => 'host_application-partner_phone_international_code',
                                        );
                                        echo Form::ib_select(__('Country'), 'partner_phone_international_code', $options, '353', $attributes)
                                        ?>

                                    </div>

                                    <div class="col-xs-6">
                                        <?= Form::ib_input(null, 'partner_phone', null, ['id' => 'host_application-partner_phone', 'placeholder' => __('Number')]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3><?= __('Children') ?></h3>

                        <div class="alert alert-info">
                            <div class="row gutters vertically_center">
                                <div class="col-xs-1" style="max-width: 2.25rem;">
                                    <span class="fa fa-info-circle fa-2x"></span>
                                </div>

                                <div class="col-xs-11">
                                    <strong><?= __('Do you have children or anyone else living with you?') ?></strong><br />
                                    <?= __('If so, please provide their name, date of birth and gender.') ?>
                                </div>
                            </div>
                        </div>

                        <div id="host-application-children">

                        </div>

                        <div class="text-right">
                            <button type="button" class="button button--continue" id="host-application-child-add"><?= __('Add another') ?></button>
                        </div>

                        <div class="hidden" id="host_application-child-template">
                            <div class="form-group host_application-child">
                                <div class="col-sm-3">
                                    <label for="host_application-child_index_first_name"><?= __('First name') ?></label>

                                    <?php
                                    $attributes = ['id' => 'host_application-child_index_first_name', 'disabled' => 'disabled'];
                                    echo Form::ib_input(null, 'children[index][first_name]', null, $attributes);
                                    ?>
                                </div>

                                <div class="col-sm-3">
                                    <label for="host_application-child_index_last_name"><?= __('Last name') ?></label>

                                    <?php
                                    $attributes = ['id' => 'host_application-child_index_last_name', 'disabled' => 'disabled'];
                                    echo Form::ib_input(null, 'children[index][last_name]', null, $attributes);
                                    ?>
                                </div>

                                <div class="col-sm-3">
                                    <label for="host_application-child_index_date_of_birth"><?= __('Date of birth') ?></label>

                                    <?php
                                    $attributes = ['class' => 'datepicker dob', 'id' => 'host_application-child_index_date_of_birth', 'disabled' => 'disabled', 'autocomplete' => 'off'];
                                    $args = ['right_icon' => '<span class="fa fa-calendar"></span>'];
                                    echo Form::ib_input(null, 'children[index][date_of_birth]', null, $attributes, $args);
                                    ?>
                                </div>

                                <div class="col-sm-3">
                                    <span><?= __('Gender') ?></span>

                                    <?php
                                    $options = ['male' => __('Male'), 'female' => __('Female')];
                                    echo Form::btn_options('children[index][gender]', $options, null, null, ['disabled' => 'disabled'], ['class' => 'stay_inline']);
                                    ?>
                                </div>

                                <div class="col-xs-12">
                                    <hr class="hidden--tablet hidden--desktop" style="border: solid #ccc; border-width: 1px 0 0;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="checkout-heading">
                    <span class="fa fa-info-circle"></span>
                    <?= __('Other information') ?>
                </h2>

                <div class="theme-form-content">
                    <div class="theme-form-inner-content clearfix">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="host_application-pet_details"><?= htmlspecialchars(__('Do you have any pets? If so, please provide details.')) ?></label>

                                <?= Form::ib_textarea(null, 'pets', '', ['id' => 'host_application-pet_details', 'rows' => 3]); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="host_application-bedroom_details"><?= htmlspecialchars(__('Please provide a brief description of the student\'s bedroom and facilities')) ?></label>

                                <?= Form::ib_textarea(null, 'facilities_description', '', ['id' => 'host_application-bedroom_details', 'rows' => 3]); ?>
                            </div>
                        </div>

                        <h3><?= __('Student profile') ?></h3>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <p><?= htmlspecialchars(__('Please select the student profiles you are happy to host.')) ?></p>
                            </div>

                            <div class="col-md-6">
                                <?php
                                $options = [
                                    'All Ages'      => __('All ages'),
                                    'Under 18'      => __('Under 18'),
                                    'Group Leaders' => __('Group leaders'),
                                    'Smokers'       => __('Smokers'),
                                    'Male'          => __('Male'),
                                    'Female'        => __('Female'),
                                    'Vegetarian'    => __('Vegetarian')
                                ];
                                ?>

                                <div class="form-group">
                                    <?php foreach ($options as $key => $value): ?>
                                        <div class="col-xs-6" style="margin-bottom: 1em;">
                                            <label class="checkbox-icon">
                                                <input type="checkbox" name="allowed_profile_types[]" value="<?= $key ?>" />
                                                <span class="checkbox-icon-unchecked button button--full button--send inverse" style="font-weight: inherit;"><?= htmlspecialchars($value) ?></span>
                                                <span class="checkbox-icon-checked   button button--full button--send"         style="font-weight: inherit;"><?= htmlspecialchars($value) ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach ;?>
                                </div>
                            </div>
                        </div>

                        <h3><?= htmlspecialchars(__('Availability')) ?></h3>

                        <p><?= htmlspecialchars(__('Please select when you are available to host students.')) ?></p>

                        <?php
                        $availability = [
                            'All Year' => __('All year'),
                            'Summer'   => __('Summer'),
                            'Winter'   => __('Winter')
                        ];
                        ?>

                        <div class="form-group">
                            <?php foreach ($availability as $key => $value): ?>
                                <div class="col-xs-4">
                                    <label class="radio-icon">
                                        <input type="radio" name="availability" value="<?= $key ?>" />
                                        <span class="radio-icon-unchecked button button--full button--send inverse" style="font-weight: inherit;"><?= htmlspecialchars($value) ?></span>
                                        <span class="radio-icon-checked   button button--full button--send"         style="font-weight: inherit;"><?= htmlspecialchars($value) ?></span>
                                    </label>
                                </div>
                            <?php endforeach ;?>
                        </div>

                        <h3><?= htmlspecialchars(__('Facilities')) ?></h3>

                        <?php
                        $facilities = [
                            'WI-FI'                   => __('Wi-Fi'),
                            'Computer'               => __('Computer'),
                            'Breakfast Lunch and Dinner' => __('Breakfast, lunch and dinner')
                        ];
                        ?>

                        <?php foreach ($facilities as $key => $value): ?>
                            <label class="checkbox-icon" style="display: inline-block; margin-right: 30px; margin-bottom: 15px;">
                                <input type="checkbox" name="facilities[]" value="<?= $key ?>" />
                                <span class="checkbox-icon-unchecked button button--send inverse" style="font-weight: inherit;"><?= htmlspecialchars($value) ?></span>
                                <span class="checkbox-icon-checked   button button--send"         style="font-weight: inherit;"><?= htmlspecialchars($value) ?></span>
                            </label>
                        <?php endforeach ;?>

                        <h3><?= htmlspecialchars(__('Other')) ?></h3>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="host_application-other_rules"><?= htmlspecialchars(__('Do you have any specific house rules you wish the student to observe? e.g. curfew, guests, etc.')) ?></label>

                                <?= Form::ib_textarea(null, 'rules', '', ['id' => 'host_application-other_rules', 'rows' => 3]) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="host_application-other_rules"><?= htmlspecialchars(__('Any other information that you think might be relevant')) ?></label>

                                <?= Form::ib_textarea(null, 'other', '', ['id' => 'host_application-other_rules', 'rows' => 3]) ?>
                            </div>
                        </div>

                        <?php if (Settings::instance()->get('captcha_enabled')): ?>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php
                                    require_once ENGINEPATH.'/plugins/formprocessor/development/classes/model/recaptchalib.php';
                                    echo recaptcha_get_html(Settings::instance()->get('captcha_public_key'));
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group vertically_center">
                            <div class="col-sm-8">
                                <?php
                                $terms_text = str_replace(
                                    '{{',
                                    '<a href="/terms-and-conditions.html">',
                                    str_replace(
                                        '}}',
                                        '</a>',
                                        htmlspecialchars(__('I have read the {{terms and conditions}} and agree to abide by them.'))
                                    )
                                );

                                echo Form::ib_checkbox($terms_text, 'terms', 1, false, ['class' => 'validate[required]']) ?>
                            </div>

                            <div class="col-xs-12 col-sm-4">
                                <button type="submit" class="button button--full"><?= htmlspecialchars(__('Submit application')) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>
</div>

<style>
    .host_application-child:last-child hr {
        display: none;
    }
</style>

<script>
    $(document).ready(function() {
        $('#host-application-child-add').on('click', function() {
            var children_section = document.getElementById('host-application-children');

            var $clone = $('#host_application-child-template').find('.host_application-child').clone();
            var number_of_children = children_section.getElementsByClassName('host_application-child').length;

            $clone.find(':disabled').prop('disabled', false).removeAttr('disabled');
            $clone.find(':input').each(function() {
                this.name     = this.name.replace('[index]', '[' + number_of_children + ']');
                this.id       = this.id  .replace('_index_', '_' + number_of_children + '_');
                this.disabled = false;
                this.removeAttribute('disabled');
            });

            $clone.find('[for]').each(function() {
                this.setAttribute('for', this.getAttribute('for').replace('_index_', '_' + number_of_children + '_'));
            });

            $(children_section).append($clone);

            if (typeof $.fn.datetimepicker === 'function') {
                $clone.find('.datepicker').datetimepicker({ format: 'd/m/Y', timepicker: false, scrollInput: false });
            }
        }).trigger('click');
    });
</script>

<?php
if (trim($site_template->footer)) {
    eval('?>'.$site_template->footer);
} else {
    include Kohana::find_file('views', 'footer');
}
?>