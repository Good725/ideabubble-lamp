<?php
$is_student = in_array('student', $contact->get_roles_stubs());
$logged_in_user = Auth::instance()->get_user();
?>
<fieldset style="border: none;" <?= !$can_edit  ? 'disabled' : '' ?>>
    <?php if($contact->get_first_name() == 'first_name'): ?>
        <div class="full_colm clear" style="text-align: center;">Please complete your profile below to continue.</div>
    <?php endif; ?>

    <form id="edit_profile_form" class="validate-on-submit" action="" method="POST" >
        <input type="hidden" id="contact_id" name="contact_id" value=""/>
        <input type="hidden" id="mainRole" name="mainRole" value="<?= $contact_role ?? in_array('guardian', $contact->get_roles_stubs()) ? 'Guardian' : 'Student' ?>"/>
        <input type="hidden" value="<?= in_array('guardian', $contact->get_roles_stubs()) ? 'guardian' : (in_array('student', $contact->get_roles_stubs()) ? 'student' : 'mature') ?>" name="role"/>
        <input type="hidden" id="isPrimary" name="isPrimary" value="<?= $contact->get_is_primary() ?>"/>

        <h3 class="text-primary">Contact Details</h3>

        <?php if (in_array('guardian', $contact->get_roles_stubs())): ?>
            <div class="form-row gutters">
                <div class="col-sm-6">
                    <?php
                    $options = array('' => '');
                    foreach ($salutations as $salutation) {
                        $options[$salutation] = $salutation;
                    }
                    echo Form::ib_select(__('Title'), 'title', $options, $contact->get_title(), array('class' => 'validate[required]'));
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-row gutters">
            <div class="col-sm-6">
                <?php
                $value = $contact->get_first_name() != 'first_name' ? $contact->get_first_name() : '';
                echo Form::ib_input(__('First name'), 'first_name', $value, array('class' => 'validate[required]'));
                ?>
            </div>

            <div class="col-sm-6">
                <?php
                $value = $contact->get_last_name() != 'last_name' ? $contact->get_last_name() : '';
                echo Form::ib_input(__('Last name'), 'last_name', $value, array('class' => 'validate[required]'));
                ?>
            </div>
        </div>

        <div class="form-row gutters">
            <div class="col-sm-6">
                <?php
                $value = $contact->get_first_name() != 'first_name' ? $contact->get_date_of_birth() : '';
                echo Form::ib_input(__('Date of Birth'), 'date_of_birth', $value, array('autocomplete' => 'off', 'class' => 'date-of-birth dob'));
                ?>
            </div>

            <div class="col-sm-6">
                <?php
                $attributes = array(
                    'class' => 'validate['.($is_student ? '' : 'required,').'custom[irishMobileLength],custom[irishMobilePrefix],custom[onlyNumberSp]]',
                    'id' => 'profile_mobile_number'
                );
                echo Form::ib_input(__('Mobile'), 'mobile', $contact->get_mobile(), $attributes);
                ?>
            </div>
        </div>

        <?php if ( ! $account_user): ?>
            <div class="db-main-address">
                <h3 class="text-primary">No active login</h3>

                <div class="form-row">
                    <?= Form::ib_checkbox(__('Create Login'), 'login', '1', false, array('id' => 'db-login')) ?>
                </div>
            </div>
        <?php endif; ?>

        <div id="account_block" style="display: <?= $account_user ? 'block' : 'none' ?>">
            <h3 class="text-primary">Login Details</h3>

            <div class="form-row gutters">
                <div class="col-sm-6">
                    <?php
                    $value = !empty($account_user['email']) ? $account_user['email'] : '' ;
                    echo Form::ib_input(__('E-mail'), 'email', $value, array('id' => 'edit_profile_email'));
                    ?>
                </div>
            </div>

            <?php // Users can only change the password of their own account ?>
            <?php if (isset($account_user['id']) && $account_user['id'] == $logged_in_user['id']): ?>
                <div class="form-row gutters">
                    <div class="col-sm-6">
                        <?php
                        $attributes = array('type' => 'password', 'id' => 'profile_password', 'autocomplete' => 'off');
                        $args = array('icon_position' => 'right', 'icon' => '<button type="button" class="btn-link" id="show_profile_pass" data-target="#profile_password"><span class="icon icon-eye" aria-hidden="true"></span></button>');
                        echo Form::ib_input(__('Password'), 'password', null, $attributes, $args);
                        ?>
                    </div>

                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Repeat Password'), 'mpassword', null, array('type' => 'password', 'autocomplete' => 'off')); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="db-main-address">
            <h3 class="text-primary">Main Address</h3>

            <div class="form-row gutters">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address line 1'), 'address1', $contact->address->get_address1() ); ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address line 2'), 'address2', $contact->address->get_address2() ); ?>
                </div>
            </div>

            <div class="form-row gutters">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address line 3'), 'address3', $contact->address->get_address3() ); ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(__('City'), 'town', $contact->address->get_town() ); ?>
                </div>
            </div>

            <div class="form-row gutters">
                <div class="col-sm-6">
                    <?php
                    $options = array('' => '');
                    foreach ($counties as $county) {
                        $options[$county['id']] = $county['name'];
                    }
                    echo Form::ib_select(__('County'), 'county', $options, $contact->address->get_county());
                    ?>
                </div>
            </div>
        </div>

        <div class="row gutters noti-cntct-pf">
            <?php if ($contact->get_is_primary()): ?>
                <div class="col-sm-6">
                    <h3 class="text-primary">When can we contact you? In case of</h3>

                    <?php if ( ! empty($preferences['notification'])): ?>
                        <?php
                        $readonly_fields     = $contact->get('is_primary') ? array('emergency', 'absentee', 'accounts') : array();
                        $pre_selected_fields = $contact->get('is_primary') ? array('emergency', 'absentee', 'accounts', 'marketing_updates') : array();
                        ?>
                        <?php foreach ($preferences['notification'] as $preference): ?>
                            <?php if ( ! $contact->has_role('teacher') && $preference['stub'] == 'time_sheet_alerts'): ?>
                                <?php // Only teachers can see the timesheet alerts preference ?>
                                <input type="hidden" name="preferences[]" value="<?= $preference['id'] ?>" />
                            <?php else: ?>
                                <ul class="list-unstyled">
                                    <?php
                                    $checked    = (in_array($preference['id'], $contact_preference_ids) || in_array($preference['stub'], $pre_selected_fields));
                                    $readonly   = in_array($preference['stub'], $readonly_fields);
                                    $attributes = array('id' => 'contact_preference_'.$preference['stub']);
                                    if ($readonly) {
                                        $attributes['disabled'] = 'disabled';
                                    }
                                    if ($preference['required']) {
                                        $attributes['class'] = 'validate[required]';
                                    }
                                    // "readonly" doesn't work for checkboxes. "disabled" is used instead. If the box is checked, a hidden field is used to ensure the value is sent to the server
                                    if ($checked && $readonly) { ?>
                                        <input type="hidden" name="preferences[]" value="<?= $preference['id'] ?>" />
                                    <?php
                                    }
                                    echo '<li>'.Form::ib_checkbox($preference['label'], 'preferences[]', $preference['id'], $checked, $attributes).'</li>';
                                    ?>
                                </ul>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif ?>
            
            <?php if (!in_array('student', $contact->get_roles_stubs())): ?>
                <div class="col-sm-6">
                    <h3 class="text-primary">Contact Preferences</h3>

                    <?php
                    if (!empty($preferences['contact'])) {
                        echo '<ul class="list-unstyled">';
                        foreach ($preferences['contact'] as $preference) {
                            $is_selected     = in_array($preference['id'], $contact_preference_ids);
                            $is_new_profile  = ($contact->get_first_name() == 'first_name');
                            $selected_on_new = (in_array($preference['stub'],array('emergency', 'absentee', 'accounts')));
                            $checked         = ($is_selected || ($is_new_profile && $selected_on_new));
                            $attributes      = array('id' => 'contact_preference_'.$preference['stub']);
                            if ($preference['required']) {
                                $attributes['class'] = 'validate[required]';
                            }

                            echo '<li>'.Form::ib_checkbox($preference['label'], 'preferences[]', $preference['id'], $checked, $attributes).'</li>';
                        }
                        echo '</ul>';
                    }
                    ?>

                    <?php if (in_array('guardian', $contact->get_roles_stubs())): ?>
                        <h3 class="text-primary">Privileges</h3>
                    <?php endif; ?>

                    <?php
                    if (in_array('guardian', $contact->get_roles_stubs()) && !empty($privileges_preferences)) {
                        echo '<ul class="list-unstyled">';
                        foreach ($privileges_preferences as $preference) {
                            $is_selected = false;
                            if ( ! empty($contact_privileges_preferences)) {
                                foreach ($contact_privileges_preferences as $contact_pri){
                                    if ($contact_pri['preference_id'] == $preference['id']) {
                                        $is_selected = true;
                                    }
                                }
                            }
                            $is_new_profile     = ($contact->get_first_name() == 'first_name');
                            $is_selected_on_new = in_array($preference['stub'], array('db-otr-pf', 'db-cbs-gu', 'db-mn-ads-gu'));
                            $checked         = ($is_selected || ($is_new_profile && $selected_on_new));
                            $name               = 'contact_preference['.$preference['stub'].']';
                            $attributes         = array('id' => $preference['stub']);
                            echo '<li>'.Form::ib_checkbox($preference['label'], $name, null, $checked).'</li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h3 class="text-primary">Type</h3>

            <?php
            $attributes = array();
            $checked = (in_array(2 , $contact->get_roles()) || in_array(3 , $contact->get_roles()));
            if ($checked) $attributes['disabled'] = 'disabled';
            echo Form::ib_checkbox(__('I am a student'), 'mature', 1, $checked, $attributes);
            ?>
        </div>

        <div id="student-info" class="stu-info noti-cntct-pf" <?= in_array(2, $contact->get_roles()) ? 'style="display: block;"' : 'style="display: none;"'?>>
            <h1>Add student educational information</h1>

            <div class="row gutters">
                <div class="col-sm-6">
                    <h3 class="text-primary">Current Educational Details</h3>

                    <div class="form-row">
                        <?php
                        $options = array('' => '');
                        foreach($academic_years as $academic_year) $options[$academic_year['id']] = $academic_year['title'];
                        echo Form::ib_select(__('Academic Year'), 'academic_year_id', $options, $contact->get_academic_year_id());
                        ?>
                    </div>

                    <div class="form-row">
                        <?php
                        $options = array(' ' => '');
                        foreach ($schools as $school) {
                            $options[$school['id']] = $school['name'].(( ! empty($school['address1'])) ? ' - '.$school['address1'] : '');
                        }
                        $options[''] = 'Not On List';
                        echo Form::Ib_select('School', 'school_id', $options, $contact->get_school_id(), array('id' => 'contact_school_id'));
                        ?>
                    </div>

                    <div class="form-row">
                        <?php
                        $options = array('' => '');
                        foreach ($years as $year) $options[$year['id']] = $year['year'];
                        echo Form::ib_select(__('School Year'), 'year_id', $options, $contact->get_year_id(), array('id' => 'contact_year_id'));
                        ?>
                    </div>

                    <div class="form-row">
                        <?php
                        $options = array();
                        foreach ($subjects as $subject) {
                            $options[$subject['id']] = $subject['name'];
                        }
                        echo Form::ib_select(__('Subjects studied'), 'subject_preferences[]', $options, $subject_preferences_ids, array('multiple' => 'multiple'));
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
                        echo Form::ib_select(__('Special Preferences'), 'preferences[]', $options, $contact_preference_ids, array('multiple' => 'multiple'));
                        ?>
                    </div>

                    <div class="form-row">
                        <?php
                        $options = array();
                        foreach($course_types as $course_type) {
                            $options[$course_type['id']] = $course_type['category'];
                        }
                        echo Form::ib_select(__('College course they want'), 'course_type_preferences[]', $options, $course_types_preferences_ids, array('multiple' => 'multiple'))
                        ?>
                    </div>

                    <div class="form-row">
                        <?= Form::Ib_input(__('Points required'), 'points_required', $contact->get_points_required()); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($can_edit): ?>
            <div class="form-actions">
                <button type="button" class="btn btn-primary db-save-btn">Save</button>
            </div>
        <?php endif; ?>
    </form>
</fieldset>
<script>
    $('.form-select--multiple select').multiselect();
</script>

<?php if (!$account_user['email_verified'] && isset($account_user)): ?>
    <script>
        $('#user_created_popup').show();
    </script>

    <div id="user_created_popup" class="sectionOverlay" style="display:none;">
        <div class="overlayer"></div>
        <div class="screenTable">
            <div class="screenCell">
                <div class="sectioninner" style="width: 40%">
                    <div class="popup-header">
                        <span class="popup-title">Email verification required</span>
                        <a class="basic_close popup_close"><span class="fa fa-times" aria-hidden="true"></span></a>
                    </div>

                    <div class="popup-content">
                        <form>
                            <div class="colm" style="text-align: center">
                                Please, verify this family member account.<br/>
                                We sent an e-mail with a verification link to the address you have provided.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
