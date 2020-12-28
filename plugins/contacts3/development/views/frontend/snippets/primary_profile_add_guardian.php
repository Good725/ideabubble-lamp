<form id="add_profile_guardian_form" class="new-profile-form validate-on-submit" action="/frontend/contacts3/save_new_profile" method="POST">
    <input type="hidden" value="<?= $family_id ?>" name="family_id"/>
	<input type="hidden" value="guardian" name="role"/>
    <input type="hidden" name="redirect" value="<?=html::chars(@$redirect)?>" />


    <h3 class="text-primary">Guardian Contact</h3>

    <div class="form-row gutters">
        <div class="col-sm-6">
            <?php
            $options = array('' => '');
            foreach ($salutations as $salutation) $options[$salutation] = $salutation;
            echo Form::ib_select(__('Title'), 'title', $options);
            ?>
        </div>
    </div>

    <div class="form-row gutters">
        <div class="col-sm-6">
            <?= Form::ib_input(__('First name'), 'first_name', null, array('class' => 'validate[required]')) ?>
        </div>

        <div class="col-sm-6">
            <?= Form::ib_input(__('Last name'), 'last_name', null, array('class' => 'validate[required]')) ?>
        </div>
    </div>

    <div class="form-row gutters">
        <div class="col-sm-6">
            <?= Form::ib_input(__('Date of Birth'), 'date_of_birth', null, array('autocomplete' => 'off', 'class' => 'date-of-birth')) ?>
        </div>

        <div class="col-sm-6">
            <?php
            $attributes = array(
                'class' => 'validate[required,custom[irishMobileLength],custom[irishMobilePrefix],custom[onlyNumberSp]]',
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

    <div class="form-row gutters noti-cntct-pf">
        <div class="col-sm-6">
            <h3 class="text-primary">Type</h3>

            <div class="form-row">
                <?= Form::ib_checkbox(__('I am a student'), 'mature', 1, false, array('id' => 'db-student')) ?>
            </div>
        </div>

        <div class="col-sm-6">
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

            <h3 class="text-primary">Privileges</h3>

            <?php if (!empty($privileges_preferences)): ?>
                <ul class="list-unstyled">
                    <?php foreach ($privileges_preferences as $preference): ?>
                        <li>
                            <?php
                            $name = 'contact_preference['.$preference['stub'].']';
                            $attributes = array('id' => $preference['stub']);
                            if ($preference['required']) $attributes['class'] = 'validate[required]';
                            echo Form::ib_checkbox($preference['label'], $name, null, false, $attributes);
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div id="student-info" class="full_colm clear stu-info noti-cntct-pf" style="display: none;">
        <div class="page-title clear">
            <div class="title-left">
                <h1>Student educational information</h1>
            </div>
        </div>
        <div class="left-sect">
            <div class="contact-dt">
                <h3>Current Educational Details</h3>
            </div>
            <div class="db-form-wrap">
                <label class="lbl">Academic Year</label>
                <div class="select">
                    <select name="academic_year_id">
                        <option value="">Select academic year</option>
                        <?php foreach($academic_years as $academic_year): ?>
                            <option value="<?= $academic_year['id']?>"><?= $academic_year['title']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="db-form-wrap">
                <label class="lbl">School</label>
                <div class="select">
                    <select id="contact_school_id" name="school_id">
                        <option value="">Select school</option>
                        <?php foreach($schools as $school): ?>
                            <option value="<?= $school['id'] ?>">
                                <?= $school['name'].(( ! empty($school['address1'])) ? ' - '.$school['address1'] : '') ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="">Not On List</option>
                    </select>
                </div>
            </div>
            <div class="db-form-wrap">
                <label class="lbl">School Year</label>
                <div class="select">
                    <select id="contact_year_id" name="year_id">
                        <option value="">Select school year</option>
                        <?php foreach($years as $year): ?>
                            <option value="<?= $year['id'] ?>"><?= $year['year'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
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

        <div class="right-sect">
            <div class="contact-dt margin_stu">
                <h3>Student Considerations</h3>
            </div>

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

            <div class="db-form-wrap">
                <label class="lbl">Points required</label>
                <input type="text" placeholder="1">
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
