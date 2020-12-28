<div class="theme-form">
    <h3 class="checkout-heading contact-details-heading">
        <span class="fa fa-address-book"></span>
        <?= __('Student profile and address') ?>
    </h3>

    <div class="theme-form-content">
        <div class="theme-form-inner-content">
            <p><?= __('Profile') ?></p>

            <div class="form-group">
                <?php if (!in_array($custom_checkout, ['bcfe', 'bc_language', 'sls'])): ?>
                    <div class="col-sm-2 file_id_container">
                        <label class="image-upload">
                            <span class="image-upload-preview">
                                <img src="" alt="" class="hidden" />
                                <span class="image-upload-placeholder_icon fa fa-user"></span>
                            </span>

                            <input type="file" id="student[photo]" class="sr-only file_select image-upload-input" />
                            <span class="button"><?= __('Upload photo') ?></span>
                        </label>

                        <input type="hidden" name="student[photo_id]" class="form-control file_id" />
                    </div>
                <?php endif; ?>

                <div class="col-sm-<?= !in_array($custom_checkout, ['bcfe', 'bc_language', 'sls']) ? 10 : 12 ?>">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <?= Form::ib_input(__('Date of birth'), 'student[dob]', null, array('id' => 'checkout-profile-student-dob', 'class' => 'datepicker validate[required]')); ?>
                        </div>

                        <div class="col-sm-3">
                            <?php
                            $options = array('' => '', 'M' => __('Male'), 'F' => __('Female'));
                            echo Form::ib_select(__('Gender'), 'student[gender]', $options, '', array('id' => 'checkout-profile-student-gender'));
                            ?>
                        </div>

                        <div class="col-sm-3">
                            <?php
                            $nationality_options = Model_Country::$nationalities;
                            array_unshift($nationality_options, '');
                            $nationality_options= array_combine($nationality_options, $nationality_options);
                            $attributes = array('class' => 'validate[required]', 'id' => 'checkout-profile-student-nationality');
                            echo Form::ib_select(__('Nationality'), 'student[nationality_id]', $nationality_options, null, $attributes);
                            ?>
                        </div>

                        <div class="col-sm-3">
                            <?php
                            $country_options = '<option value=""></option>'.html::optionsFromRows('code', 'name', Model_Residence::get_all_countries(), '');
                            $attributes = array('class' => 'validate[required]', 'id' => 'checkout-profile-student-country_of_birth');
                            echo Form::ib_select(__('Country of birth'), 'student[birth_country_id]', $country_options, null, $attributes);
                            ?>
                        </div>
                    </div>

                    <?php
                    $preferences = Model_Preferences::get_all_preferences();
                    $options_medical = array();
                    if (!empty($preferences)) {
                        foreach ($preferences as $preference) {
                            if ($preference['group'] == 'special') {
                                $options_medical[$preference['id']] = $preference['label'];
                            }
                        }
                    }

                    $has_passport_or_pps = ($custom_checkout != 'sls');
                    ?>

                    <?php if ($options_medical || $has_passport_or_pps): ?>
                        <div class="form-group">
                            <?php if ($has_passport_or_pps): ?>
                                <div class="col-sm-6">
                                    <?php
                                    if ($custom_checkout == 'bc_language') {
                                        $attributes = array('id' => 'checkout-profile-student-passport_number');
                                        echo Form::ib_input(__('Passport No.'), 'student[passport_number]', null, $attributes);
                                    } else {
                                        $attributes = array('class' => 'validate[funcCall[validate_pps]]', 'id' => 'checkout-profile-student-pps');
                                        echo Form::ib_input(__('PPS No.'), 'student[pps]', null, $attributes);
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($options_medical): ?>
                                <div class="col-sm-6">
                                    <?php
                                    $attributes = array(
                                        'id'       => 'checkout-profile-student-preferences-medical',
                                        'multiple' => 'multiple'
                                    );

                                    echo Form::ib_select(__('Medical Conditions'), 'student[preferences_medical][]', $options_medical, null, $attributes);
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <p><?=__('Current Address')?></p>

            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address 1'), 'student[address1]') ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address 2'), 'student[address2]') ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('City'), 'student[town]') ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(__('Postal Code'), 'student[postcode]') ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    <?php
                    $options = html::optionsFromRows('code', 'name', Model_Residence::get_all_countries(), '');
                    echo Form::ib_select(__('Country'), 'student[country]', $options);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Show a subjects levels when the subject is selected
    $(document).on('change', '.profile-education-subject [type="checkbox"]', function() {
        var $levels = $(this).parents('.profile-education-subject_and_level').find('.profile-education-subject_preferences');
        if (this.checked) {
            $levels.removeClass('invisible');
        } else {
            $levels.addClass('invisible')
        }
    });

    $('#checkout-application-cycle').on('change', function()
    {
        // First check if any of the subjects are dependant on the cycle
        // The selective hiding will only take effect if at least one subject is linked to a cycle
        if ($('.profile-education-subject-wrapper[data-cycle]:not([data-cycle=""])').length) {
            var cycle = $(this).val();

            if (cycle) {
                // Show all subjects for the chosen cycle
                $('.profile-education-subject-wrapper').addClass('hidden');
                $('.profile-education-subject-wrapper[data-cycle="'+cycle+'"]').removeClass('hidden');
            } else {
                // Show all subjects when no cycle is selected
                $('.profile-education-subject-wrapper').removeClass('hidden');
            }
        }
    });

    function validate_pps(field, rules, i, options)
    {
        if (field.val().trim() == '' && $('#checkout-profile-student-country_of_birth').val() == 'IE') {
            rules.push('required');
            return '* This field is required for people born in Ireland';
        }
    }
</script>