<form id="add_profile_student_form" class="new-profile-form validate-on-submit" action="/frontend/contacts3/save_new_profile" method="POST">
    <input type="hidden" value="<?= $family_id ?>" name="family_id"/>
	<input type="hidden" value="student" name="role"/>
    <input type="hidden" name="redirect" value="<?=html::chars(@$redirect)?>" />

    <h3 class="text-primary">Student Contact</h3>

    <div class="form-row gutters">
        <div class="col-sm-6">
            <?= Form::ib_input(__('First name'), 'first_name', null, array('class' => 'validate[required]')); ?>
        </div>

        <div class="col-sm-6">
            <?= Form::ib_input(__('Last name'), 'last_name', null, array('class' => 'validate[required]')); ?>
        </div>
    </div>

    <div class="form-row gutters">
        <div class="col-sm-6">
            <?= Form::ib_input(__('Date of Birth'), 'date_of_birth', null, array('autocomplete' => 'off', 'class' => 'date-of-birth validate[required]')); ?>
        </div>

        <div class="col-sm-6">
            <?php
            $attributes = array(
                'class' => 'validate[custom[irishMobileLength],custom[irishMobilePrefix],custom[onlyNumberSp]]',
                'id' => 'profile_mobile_number'
            );
            echo Form::ib_input(__('Mobile'), 'mobile', null, $attributes);
            ?>
        </div>
    </div>

    <div class="db-main-address">
        <h3 class="text-primary">Account</h3>

        <div class="form-row gutters">
            <div class="col-sm-6">
                <?= Form::ib_checkbox(__('Add Family Member Login'), 'login', '1', false, array('id' => 'db-login')) ?>
            </div>

            <div class="col-sm-6" id="account_block" style="display: none;">
                <?php
                $attributes = array('type' => 'email', 'id' => 'add_profile_email');
                echo Form::ib_input(__('E-mail'), 'email', null, $attributes);
                ?>
            </div>
        </div>
    </div>

    <div class="db-main-address">
        <h3 class="text-primary">Main Address</h3>

        <div class="form-row">
            <?= Form::ib_checkbox(__('Use Family Address'), null, null, true, array('id' => 'db-u-add-st')); ?>
        </div>

        <div class="form-row gutters">
            <div class="col-sm-6">
                <?= Form::ib_input(__('Address line 1'), 'address1'); ?>
            </div>

            <div class="col-sm-6">
                <?= Form::ib_input(__('Address line 2'), 'address2'); ?>
            </div>
        </div>

        <div class="form-row gutters">
            <div class="col-sm-6">
                <?= Form::ib_input(__('Address line 3'), 'address3'); ?>
            </div>

            <div class="col-sm-6">
                <?= Form::ib_input(__('City'), 'town'); ?>
            </div>
        </div>

        <div class="form-row gutters">
            <div class="col-sm-6">
                <?php
                $options = array('' => '');
                foreach ($counties as $county) {
                    $options[$county['id']] = $county['name'];
                }
                echo Form::ib_select(__('County'), 'county', $options);
                ?>
            </div>
        </div>
    </div>

    <div class="noti-cntct-pf">
        <h3 class="text-primary">Contact Preferences</h3>

        <?php if (!empty($preferences['contact'])): ?>
            <ul class="list-unstyled">
                <?php foreach ($preferences['contact'] as $preference): ?>
                    <li>
                        <?php
                        $attributes = array('id' => 'contact_preference_'.$preference['stub']);
                        if ($preference['required']) $attributes['class'] = 'validate[required]';
                        echo Form::ib_checkbox($preference['label'], 'preferences[]', $preference['id'], false, $attributes);
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div id="student-info" class="full_colm clear stu-info noti-cntct-pf" style="display: block;">
        <h1>Add student educational information</h1>

        <div class="row gutters">
            <div class="col-sm-6">
                <h3 class="text-primary">Current Educational Details</h3>

                <div class="form-row">
                    <?php
                    $options = array('' => '');
                    foreach($academic_years as $academic_year) $options[$academic_year['id']] = $academic_year['title'];
                    echo Form::ib_select(__('Academic Year'), 'academic_year_id', $options);
                    ?>
                </div>

                <div class="form-row">
                    <?php
                    $options = array('' => '');
                    foreach ($schools as $school) {
                        $options[$school['id']] = $school['name'].(( ! empty($school['address1'])) ? ' - '.$school['address1'] : '');
                    }
                    $options[''] = 'Not On List';
                    echo Form::Ib_select('School', 'school_id', $options, null, array('id' => 'contact_school_id'));
                    ?>
                </div>

                <div class="form-row">
                    <?php
                    $options = array('' => '');
                    foreach ($years as $year) $options[$year['id']] = $year['year'];
                    echo Form::ib_select(__('School Year'), 'year_id', $options, null, array('id' => 'contact_year_id', 'class' => 'validate[required]'));
                    ?>
                </div>

                <div class="form-row">
                    <?php
                    $options = array();
                    foreach ($subjects as $subject) {
                        $options[$subject['id']] = $subject['name'];
                    }
                    echo Form::ib_select(__('Subjects studied'), 'subject_preferences[]', $options, null, array('multiple' => 'multiple'));
                    ?>
                </div>
            </div>

            <div class="col-sm-6">
                <h3 class="text-primary">Student Considerations</h3>

                <div class="form-row">
                    <?php
                    $options = array();
                    if (!empty($preferences['special'])) {
                        foreach ($preferences['special'] as $preference) {
                            $options[$preference['id']] = $preference['label'];
                        }
                    }
                    echo Form::ib_select(__('Special Preferences'), 'preferences[]', $options, null, array('multiple' => 'multiple'));
                    ?>
                </div>

                <div class="form-row">
                    <?php
                    $options = array();
                    foreach($course_types as $course_type) {
                        $options[$course_type['id']] = $course_type['category'];
                    }
                    echo Form::ib_select(__('College course they want'), 'course_type_preferences[]', $options, null, array('multiple' => 'multiple'))
                    ?>
                </div>

                <div class="form-row">
                    <?= Form::Ib_input(__('Points required'), 'points_required'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary db-save-btn">Save</button>
        <a class="btn-cancel" href="/admin/contacts3/profile">Cancel</a>
    </div>
</form>

<script>
    $('.form-select--multiple select').multiselect();
</script>
