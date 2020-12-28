<?= isset($alert) ? $alert : ''; ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<?php
$is_primary = ($contact->get_id() > 0) ? ( ($contact->get_is_primary()) ) : 1;
$subtype = $contact->get_subtype_id();

$is_staff = false;
foreach ($contact->get_roles_stubs() as $test_role) {
    if (in_array($test_role, array('teacher', 'supervisor', 'admin'))) {
        $is_staff = true;
        break;
    }
}
$is_special_member = false;
if (!empty($contact->get_tags())) {
    foreach ($contact->get_tags() as $tag) {
        if ($tag->get_name() == 'special_member') {
            $is_special_member = true;
        }
    }
}
$is_flexi_student = $contact->get_is_flexi_student() ;
$contact_preference_ids = $contact_course_type_ids = $contact_subject_ids = $contact_course_subject_ids = array();
$required_preferences = array('emergency', 'absentee', 'accounts');
if($contact->get_id() === null && !empty(Settings::instance()->get('contact_default_preferences'))) {
    $contact_preference_ids = Settings::instance()->get('contact_default_preferences');
} else {
    foreach ($contact->get_preferences()             as $preference) $contact_preference_ids[]  = $preference['preference_id'];
}
foreach ($contact->get_course_type_preferences() as $preference) $contact_course_type_ids[] = $preference['course_type_id'];
foreach ($contact->get_subject_preferences()     as $preference) $contact_subject_ids[]     = $preference['subject_id'];
foreach ($contact->get_courses_subject_preferences() as $preference ) $contact_course_subject_ids[] = $preference['course_subject_id'];
//$categories_selected = FALSE;
//$subjects_selected = FALSE;
$contact_type = Model_Contacts3::get_contact_type($contact->get_type() )['label'] ?? '';

?>
<script>
    window.booked_locations = <?=json_encode(@$booked_locations)?>;
    window.contact_phone_number = <?=json_encode(@$contact_phone_number)?>;
    window.tx_balances = <?=json_encode(@$tx_balances)?>;
    window.accountsiq_id = <?=json_encode($accountsiq_id)?>;
</script>
<form id="add_edit_contact" class="form-horizontal educate-form add_edit_contact clearfix" action="/admin/contacts3/save_contact" method="POST">
    <input type="hidden" id="contact_action"     name="action"     value="" />
    <input type="hidden" id="contact_new_family" name="new_family" value="0" />
    <input type="hidden" id="contact_id"         name="id"         value="<?=$contact->get_id();?>"/>
    <input type="hidden" id="contact_is_currently_primary" value="<?= ($is_primary) ? 1 : 0 ?>" />
    <div class="row gutters">
        <div class="col-sm-4">
            <h3 class="border-title">Contact profile</h3>
            <?php if ($contact->is_new_contact()): ?>
                <div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="contact_type " class="control-label">New Contact</label>
                    
                            <?php ob_start(); ?>
                            <?php foreach ($contact_types as $type): ?>
                                <?php
                                if ($contact->get_type() == $type['contact_type_id']) {
                                    $contact_type = $type['label'];
                                }
                                ?>
                                <option value="<?= $type['contact_type_id'] ?>"
                                        data-name="<?= $type['name'] ?>" <?= $contact->get_type() == $type['contact_type_id'] ? ' selected="selected"' : '' ?>><?= $type['label'] ?></option>
                            <?php endforeach ?>
                            <?php $options = ob_get_clean(); ?>
                    
                            <?= Form::ib_select(null, 'type', $options, null, ['id' => 'contact_type']); ?>
                        </div>
                    </div>

                    <!-- no longer used as kes-3803 -->
                    <div class="form-group hidden">
                        <div class="col-sm-12">
                            <label for="contact_find" class="control-label">Find</label>
                            <input class="form-input" id="contact_find" type="text"
                                   placeholder="enter mobile / full name / last name"/>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="contact_type" class="control-label">Contact type</label>
                
                        <?php ob_start(); ?>
                        <option value="">Please select</option>
                        <?php foreach ($contact_types as $type): ?>
                            <?php
                            if ($contact->get_type() == $type['contact_type_id']) {
                                $contact_type = $type['label'];
                            }
                            ?>
                            <option value="<?= $type['contact_type_id'] ?>"
                                    data-name="<?= $type['name'] ?>" <?= $contact->get_type() == $type['contact_type_id'] ? ' selected="selected"' : '' ?>><?= $type['label'] ?></option>
                        <?php endforeach ?>
                        <?php $options = ob_get_clean(); ?>
                
                        <?php
                        echo Form::ib_select(null, 'type', $options, null,
                            [
                                'id' => 'contact_type',
                                'style' => ''
                            ]);
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div>
                <h3 class="border-title">Labels</h3>

                <label for="contact-tag-selector">Select an existing or type a new label</label>

                <div class="row gutters gutters--narrow mb-2">
                    <div class="col-sm-9">
                        <?php
                        $attributes = [
                            'class' => 'ib-combobox',
                            'id' =>'contact-tag-selector',
                            'data-placeholder' => 'Enter tag...'
                        ];
                        $args = ['allow_new' => true];
                        $options = '<option></option>'.Model_Contacts3_Tag::get_all()->as_options(['name_column' => 'label', 'please_select' => false]);
                        echo Form::ib_select(null, 'add_tag', $options, null, $attributes, $args);
                        ?>
                    </div>

                    <div class="col-sm-3">
                        <button type="button" class="btn btn-default form-btn" id="contact-tag-add">Add</button>
                    </div>
                </div>

                <div id="contact-tags-list">
                    <?php foreach ($contact->get_tags() as $tag): ?>
                        <span class="btn bg-primary text-white contact-tag mr-1 mb-2">
                            <input type="hidden" class="contact-tag-id" name="contact_tags[][id]" value="<?= $tag->get_id() ?>" />
                            <input type="hidden" class="contact-tag-id" name="contact_tags[][label]" value="<?= $tag->get_label() ?>" />

                            #<span class="contact-tag-title-text"><?= htmlspecialchars($tag->get_label()) ?></span>

                            <button type="button" class="button--plain contact-tag-remove">
                                <span class="icon_close"></span>
                            </button>
                        </span>
                    <?php endforeach; ?>

                    <span class="btn bg-primary text-white contact-tag hidden mr-1 mb-2" id="contact-tag-template">
                        <input type="hidden" class="contact-tag-id"    name="contact_tags[][id]"    disabled="disabled" />
                        <input type="hidden" class="contact-tag-title" name="contact_tags[][label]" disabled="disabled" />

                        #<span class="contact-tag-title-text"></span>

                        <button type="button" class="button--plain contact-tag-remove">
                            <span class="icon_close"></span>
                        </button>
                    </span>
                </div>
            </div>

            <div class="toggleable-block type-organisation type-department">
                <h3 class="border-title">General information</h3>

                <div class="form-group organisation">
                    <div class="col-sm-12">
                        <label class="control-label" for="linked_organisation">Organisation</label>
                        <?php
                        $linked_organisation_id = '';
                        $linked_organisation = '';
                            foreach ($contact->get_contact_relations_details(array('contact_type' => 'organisation')) as $contact_relation) {
                                $linked_organisation_id = $contact_relation['parent_id'];
                                $linked_organisation = $contact_relation['name'];
                            }
                        ?>
                        <input type="hidden" name="linked_organisation_id" id="linked_organisation_id"
                               value="<?= $linked_organisation_id ?>"/>
                
                        <?= Form::ib_input(null, null, $linked_organisation, ['id' => 'linked_organisation']); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_first_name">Name</label>
                
                        <?= Form::ib_input(null, 'first_name', $contact->get_first_name(),
                            ['class' => 'validate[required] enforce_ucfirst', 'id' => 'organisation_first_name']); ?>
                    </div>
                </div>
                <?php if (Settings::instance()->get('display_sub_contact_types') == true): ?>
                    <div class="form-group type-subtype-prop organisation_contact"
                         style="<?= ($contact_type != "organisation") ? "display:none;" : "" ?>">
                        <div class="col-sm-12">
                            <label for="contact_subtype-prop" class="control-label">Organisation subtype</label>
                
                            <?php ob_start(); ?>
                            <option value="0" selected="selected" data-name="General">General</option>
                            <?php if (isset($contact_subtypes)): ?>
                                <?php foreach ($contact_subtypes as $sub_type): ?>
                                    <option value="<?= $sub_type['id'] ?>"
                                            data-name="<?= $sub_type['subtype'] ?>" <?= ($subtype == $sub_type['id']) ? 'selected' : '' ?>>
                                        <?= $sub_type['subtype'] ?></option>
                                <?php endforeach ?>
                            <?php endif; ?>
                            <?php $options = ob_get_clean(); ?>
                
                            <?= Form::ib_select(null, 'subtype_id', $options, null, ['id' => 'contact_subtype']); ?>
                        </div>

                    </div>
                <?php endif;
                if (Settings::instance()->get('display_organisation_industries') == true): ?>
                <div class="form-group org-industries organisation_contact"
                     style="<?= ($contact_type != "organisation") ? "display:none;" : "" ?>">
                    <div class="col-sm-12">
                        <label for="organisation_industry_id" class="control-label">Organisation industries</label>
            
                        <?php ob_start(); ?>
                        <?php foreach ($organisation_industries as $organisation_industry): ?>
                            <option value="<?= $organisation_industry['id'] ?>"
                                    data-name="<?= $organisation_industry['name'] ?>"
                                <?= (isset($organisation) && $organisation->get_organisation_industry_id() == $organisation_industry['id']) ? 'selected' : '' ?>>
                                <?= $organisation_industry['label'] ?></option>
                        <?php endforeach ?>
                        <?php $options = ob_get_clean(); ?>
            
                        <?= Form::ib_select(null, 'organisation_industry_id', $options, null,
                            ['id' => 'organisation_industry_id']); ?>
                    </div>

                </div>
                <?php endif; ?>
                <div class="form-group org-size organisation_contact"
                     style="<?= ($contact_type != "organisation") ? "display:none;" : "" ?>">
                    <div class="col-sm-12">
                        <label for="organisation_size_id" class="control-label">Organisation size</label>
            
                        <?php ob_start(); ?>
                            <?php foreach ($organisation_sizes as $organisation_size): ?>
                                <option value="<?= $organisation_size['id'] ?>"
                                        data-name="<?= $organisation_size['name'] ?>"
                                    <?= (isset($organisation) && $organisation->get_organisation_size_id() == $organisation_size['id']) ? 'selected' : '' ?>>
                                    <?= $organisation_size['label'] ?></option>
                            <?php endforeach ?>
                        <?php $options = ob_get_clean(); ?>
            
                        <?= Form::ib_select(null, 'organisation_size_id', $options, null,
                            ['id' => 'organisation_size_id']); ?>
                    </div>

                </div>

                <div class="form-group org-primary_biller organisation_contact">
                    <div class="col-sm-12">
                        <label class="control-label" for="linked_organisation">Primary biller</label>
                        <?php
                        $primary_biller = null;
                        if ($organisation) {
                            $primary_biller = $organisation->get_primary_biller();
                        }
                        ?>
                        <input type="hidden" name="primary_biller_id" id="primary_biller_id"
                               value="<?= $primary_biller ? $primary_biller->get_id() : "" ?>"/>
            
                        <?= Form::ib_input(null, null,  ($primary_biller ? $primary_biller->get_id() . " - " . $primary_biller->get_contact_name() : ""),
                            ['id' => 'primary_biller']); ?>
                    </div>
                </div>
                <div id="member_information_section" class="toggleable-block">
                    <h3 class="border-title">Special Membership</h3>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="control-label">Special Membership</div>
                            <?php $membership_api_control = Settings::instance()->get('organisation_api_control_membership');?>
                            <?php if($membership_api_control):?><small>Readonly. Controlled by External API</small><?php endif?>

                            <div class="btn-group btn-group-slide" data-toggle="<?php if($membership_api_control):?>off<?php else:?>buttons<?php endif?>" id="special_member">
                                <label class="btn btn-plain<?=($is_special_member) ? ' active' : '' ?>" >
                                    <input type="radio"<?= ($is_special_member) ? ' checked="checked"' : '' ?> value="1" name="special_member" id="special_member_yes" <?php if ($membership_api_control):?> disabled="disabled" <?php endif?>>Yes
                                </label>
                                <label class="btn btn-plain<?=(!$is_special_member ) ? ' active' : '' ?>">
                                    <input type="radio"<?=(!$is_special_member) ? ' checked="checked"' : '' ?> value="0" name="special_member" id="special_member_no" <?php if ($membership_api_control):?> disabled="disabled" <?php endif?>>No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="toggleable-block type-general type-billed">
                <h3 class="border-title">Personal information</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_title">Title</label>
                    </div>

                    <div class="col-sm-12">
                        <?php
                        $salutations = Model_Contacts3::temporary_salutation_dropdown();
                        $options = ['' => 'Please select'] + array_combine($salutations, $salutations);
                        echo Form::ib_select(null, 'title', $options, $contact->get_title(), ['id' => 'title']);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_first_name">First name</label>

                        <?php
                        $attributes = ['class' => 'validate[required] enforce_ucfirst', 'id' => 'contact_first_name'];
                        echo Form::ib_input(null, 'first_name', $contact->get_first_name(), $attributes);
                        ?>

                        <label><input type="checkbox" checked="checked" class="enforce_ucfirst_toggle" data-input="contact_first_name" /> Autocapitalise</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_last_name">Last name</label>

                        <?php
                        $attributes = ['class' => 'validate[required] enforce_ucfirst', 'id' => 'contact_last_name'];
                        echo Form::ib_input(null, 'last_name', $contact->get_last_name(), $attributes);
                        ?>

                        <label><input type="checkbox" class="enforce_ucfirst_toggle" checked="checked" data-input="contact_last_name" /> Autocapitalise</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_date_of_birth">Date of Birth</label>

                        <?php
                        $attributes = [
                            'id' => 'contact_date_of_birth',
                            'autocomplete' => 'off',
                            'data-date_format' => 'd/m/Y',
                            'data-date-end-date' => '0d',
                            'placeholder' => 'e.g. 01/06/1967',
                            'class' => 'form-datepicker dob validate[funcCall[validate_dob]]',
                        ];

                        echo Form::ib_datepicker(null, 'date_of_birth', $contact->get_date_of_birth(), array(), $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="nationality">Nationality</label>

                        <?php
                        $nationalities = Model_Country::$nationalities;
                        $nationalities = array_combine($nationalities, $nationalities);
                        $options = html::optionsFromArray($nationalities, $contact->get_nationality(), ['value' => '', 'label' => '']);
                        $attributes = ['id' => 'nationality', 'class' => 'ib-combobox', 'data-placeholder' => 'Please select'];
                        echo Form::ib_select(null, 'nationality', $options, null, $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="control-label">Gender</div>

                        <div class="btn-group" data-toggle="buttons" id="gender">
                            <label class="btn btn-default<?= ($contact->get_gender() == 'M') ? ' active' : '' ?>">
                                <input type="radio"<?= ($contact->get_gender() == 'M') ? ' checked="checked"' : '' ?> value="M" name="gender" id="contact_gender_m">Male
                            </label>
                            <label class="btn btn-default<?= ($contact->get_gender() == 'F') ? ' active' : '' ?>">
                                <input type="radio"<?= ($contact->get_gender() == 'F') ? ' checked="checked"' : '' ?> value="F" name="gender" id="contact_gender_f">Female
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div id="contact_pps_details" class="toggleable-block">
                <h3 class="border-title">PPS number</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_pps_number">PPS number</label>

                        <?php
                        $attributes = ['class' => 'enforce_ucfirst', 'id' => 'contact_pps_number', 'placeholder' => 'e.g. 12345G'];
                        echo Form::ib_input(null, 'pps_number', $contact->get_pps_number(), $attributes);
                        ?>
                    </div>
                </div>
            </div>
            <?php if (Settings::instance()->get('contacts_create_family') == 1):?>

            <div id="family_information_section">
                    <h3 class="border-title">Family profile</h3>

                    <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_family">Family</label>

                        <?php
                        echo Form::ib_input(null, 'contact_family', $family->get_family_name(), ['class' => 'enforce_ucfirst', 'id' => 'contact_family', 'placeholder' => 'Type to select']);
                        ?>

                        <input type="hidden" id="contact_family_id" name="family_id" value="<?= $family->get_id() ?>" />
                        <input type="hidden" id="contact_family_primary_contact_id" name="family_primary_contact_id" value="<?=$family->get_primary_contact_id();?>" />
                    </div>
                </div>

                    <div class="form-group">
                    <div class="col-sm-12 contact-type-general">
                        <label class="control-label contact-type-general" for="contact_role_id_1">Family Role</label>

                        <?php ob_start(); ?>
                            <?php $selected_roles = $contact->get_roles(); ?>
                            <?php foreach ($contact->get_all_roles(1) as $role): ?>
                                <option value="<?= $role['id'] ?>" data-name="<?= $role['stub'] ?>"<?= in_array($role['id'], $selected_roles) ? ' selected="selected"' : '' ?>><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        <?php $options = ob_get_clean(); ?>

                        <?php
                        echo Form::ib_select(null, 'role_id[]', $options, null, ['multiple' => 'multiple', 'id' => 'contact_role_id_1', 'class' => 'multiple_select contact_role_id']);
                        ?>
                    </div>
                </div>

                    <div id="primary_contact_div" class="form-group hide">
                        <div class="col-sm-12">
                        <div class="control-label">Primary Contact</div>

                        <div class="btn-group" data-toggle="buttons" id="contact_is_primary">
                            <label class="btn btn-default<?= ($is_primary) ? ' active' : '' ?>">
                                <input type="radio"<?= ($is_primary) ? ' checked="checked"' : '' ?> value="1" name="is_primary" id="contact_is_primary_yes">Yes
                            </label>
                            <label class="btn btn-default<?= ( ! $is_primary) ? ' active' : '' ?>">
                                <input type="radio"<?= ( ! $is_primary) ? ' checked="checked"' : '' ?> value="0" name="is_primary" id="contact_is_primary_no">No
                            </label>
                        </div>
                    </div>
                    </div>
                </div>
            <?php endif?>
            <?php $membership_api_control = Settings::instance()->get('organisation_api_control_membership'); ?>

            <div id="member_information_section" <?php if($contact_type == 'Organisation'):?>class="hidden"<?php endif?>>
                <h3 class="border-title">Special Membership</h3>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="control-label">Special Membership</div>
                        <input type="hidden" name="organisation_api_control_membership" id="organisation_api_control_membership" value="<?=$membership_api_control?>"/>
                        <?php if($membership_api_control):?><small>Readonly. Controlled by External API</small><?php endif?>
                        <div class="btn-group btn-group-slide" data-toggle="<?php if ($membership_api_control):?>off<?php else:?>buttons<?php endif?>" id="special_member">
                        <label class="btn btn-plain<?=($is_special_member) ? ' active' : '' ?>" >
                            <input type="radio"<?= ($is_special_member) ? ' checked="checked"' : '' ?> value="1" name="special_member" id="special_member_yes" <?php if ($membership_api_control):?> disabled="disabled" <?php endif?>>Yes
                        </label>
                        <label class="btn btn-plain<?=(!$is_special_member ) ? ' active' : '' ?>">
                            <input type="radio"<?=(!$is_special_member) ? ' checked="checked"' : '' ?> value="0" name="special_member" id="special_member_yes" <?php if ($membership_api_control):?> disabled="disabled" <?php endif?>>No
                        </label>
                    </div>
                </div>
                </div>
            </div>
                <div id="staff_information_section" class="toggleable-block">
                <h3 class="border-title">Staff profile</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="control-label">Staff member</div>

                        <div class="btn-group btn-group-slide" data-toggle="buttons" id="staff_member">
                            <label class="btn btn-plain<?=($is_staff) ? ' active' : '' ?>">
                                <input type="radio"<?= ($is_staff) ? ' checked="checked"' : '' ?> value="1" name="staff_member" id="staff_member_yes">Yes
                            </label>
                            <label class="btn btn-plain<?=(!$is_staff) ? ' active' : '' ?>">
                                <input type="radio"<?=(!$is_staff) ? ' checked="checked"' : '' ?> value="0" name="staff_member" id="staff_member_no">No
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="staff_role"<?=$is_staff ? '' : 'style="display: none"';?>>
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_role_id_2">Staff Role</label>

                        <?php ob_start(); ?>
                            <?php $selected_roles = $contact->get_roles(); ?>
                            <?php foreach ($contact->get_all_roles(2) as $role): ?>
                                <option value="<?= $role['id'] ?>" data-name="<?= $role['stub'] ?>"<?= in_array($role['id'], $selected_roles) ? ' selected="selected"' : '' ?>><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        <?php $options = ob_get_clean(); ?>

                        <?php
                        $attributes = ['multiple' => 'multiple', 'class' => 'multiple_select contact_role_id', 'id' => 'contact_role_id_2'];
                        echo Form::ib_select(null, 'role_id[]', $options, null, $attributes);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="job_title">Job title</label>
                        <input id="job_title" class="form-input" type="text" name="job_title" value="<?= $contact->get_job_title() ?? '' ?>"/>
                    </div>
                </div>
                <?php if (Settings::instance()->get('engine_enable_org_register') == 1): ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label" for="job_function_id">Job function</label>
                            <?php $options = array();
                                $options['0'] = 'N/A'; ?>
                                <?php foreach ($job_functions as $job_function):
                                    $options[$job_function['id']] = $job_function['label']; ?>
                                <?php endforeach;
                                $selected = (!empty($contact->get_job_function_id())) ? $contact->get_job_function_id() : '0'; ?>
                            <?= Form::ib_select(null, 'job_function_id', $options, $selected, ['id' => 'job_function_id']); ?>
                        </div>
                    </div>
                <? endif; ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="linked_department">Department</label>
                        <?php
                        $linked_department_id = '';
                        $linked_department = '';
                        $linked_department_role = '';
                        foreach ($contact->get_contact_relations_details(array('contact_type' => 'Department')) as $contact_relation) {
                            $linked_department_id = $contact_relation['parent_id'];
                            $linked_department = $contact_relation['name'];
                            $linked_department_role = $contact_relation['role'];
                        }
                        
                        ?>
                        <input type="hidden" name="linked_department_id" id="linked_department_id" value="<?=$linked_department_id?>" />
                        <input id="linked_department" class="form-input" type="text" value="<?=$linked_department?>"/>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="linked_department_role">Department role</label>
                        <?php
                        $options = html::optionsFromArray(array('staff' => 'Staff', 'manager' => 'Manager'), @$linked_department_role) ;
                        echo Form::ib_select(null, 'linked_department_role', $options, null, ['id' => 'linked_department_role']);
                        ?>
                    </div>
                </div>
            </div>
                <?php if (Auth::instance()->has_access('contacts3_billing')): ?>
                <div id="billing_section" class="toggleable-block type-all type-billing">
                    <h3 class="border-title">Billing</h3>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label" for="hourly_rate">Hourly Rate</label>
                            <input id="hourly_rate" class="form-control" name="hourly_rate" type="text"
                                    value="<?= $contact->get_hourly_rate() ?? "0.00" ?>"/>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
                <div id="other_section" class="toggleable-block">
                    <div class="form-group">
                        <label class="col-sm-12 control-label" for="is_inactive">Status</label>

                        <div class="col-sm-12">
                            <div class="btn-group btn-group-slide" data-toggle="buttons" id="staff_member">
                                <label class="btn btn-plain<?= ($contact->get_is_inactive() == 1) ? ' active' : '' ?>">
                                    <input type="radio" name="is_inactive" value="1"<?= ($contact->get_is_inactive() == 1) ? ' checked="checked"' : '' ?> id="is_inactive_yes">Inactive
                                </label>
                                <label class="btn btn-plain<?= ($contact->get_is_inactive() != 1) ? ' active' : '' ?>">
                                    <input type="radio" name="is_inactive" value="0"<?= ($contact->get_is_inactive() != 1) ? ' checked="checked"' : '' ?> id="is_inactive_no">Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        <div class="col-sm-4">
            <div class="family_sharable_section" id="contact_address_information_section">
                <h3 class="border-title">Address information</h3>
                <ul class="address_tabs nav nav-tabs">
                    <li class="
                        <?php if($contact_type == 'Organisation'):?>
                            hidden
                            <?php else:?>
                            active
                            <?php endif;?>">
                        <a href="#address_personal_tab" data-toggle="tab" aria-expanded="true">Personal</a>
                    </li>
                    <li class="<?php if($contact_type == 'Organisation'):?>active<?php endif;?>">
                        <a href="#address_billing_tab" data-toggle="tab" aria-expanded="true">Billing</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane
                        <?php if($contact_type == 'Organisation'):?>
                            hidden
                            <?php else:?>
                            active
                            <?php endif;?>" id="address_personal_tab">
                        <?php
                        echo View::factory('address_fields')
                            ->set('id_prefix', 'contact_')
                            ->set('address_1_field', 'address1')
                            ->set('address_2_field', 'address2')
                            ->set('address_3_field', 'address3')
                            ->set('city_field', false)
                            ->set('country', $residence->get_country())
                            ->set('address1', $residence->get_address1())
                            ->set('address2', $residence->get_address2())
                            ->set('address3', $residence->get_address3())
                            ->set('town', $residence->get_town())
                            ->set('county', $residence->get_county())
                            ->set('postcode', $residence->get_postcode())
                            ->set('coordinates', $residence->get_coordinates());
                        ?>
                    </div>
                    <div class="tab-pane <?php if($contact_type == 'Organisation'):?>active<?php endif;?>" id="address_billing_tab">
                        <?php
                        echo View::factory('address_fields')
                            ->set('id_prefix', 'contact2_')
                            ->set('country_field', 'billing_address[country]')
                            ->set('address_1_field', 'billing_address[address1]')
                            ->set('address_2_field', 'billing_address[address2]')
                            ->set('address_3_field', 'billing_address[address3]')
                            ->set('town_field', 'billing_address[town]')
                            ->set('county_field', 'billing_address[county]')
                            ->set('postcode_field', 'billing_address[postcode]')
                            ->set('coordinates_field', 'billing_address[coordinates]')
                            ->set('city_field', false)
                            ->set('country', $billing_residence->get_country())
                            ->set('address1', $billing_residence->get_address1())
                            ->set('address2', $billing_residence->get_address2())
                            ->set('address3', $billing_residence->get_address3())
                            ->set('town', $billing_residence->get_town())
                            ->set('county', $billing_residence->get_county())
                            ->set('postcode', $billing_residence->get_postcode())
                            ->set('coordinates', $billing_residence->get_coordinates());
                        ?>
                    </div>
                </div>
                

            </div>
        </div>

        <div class="col-sm-4">
            <div class="family_sharable_section" id="contact_contact_information_section">
                <?php
                if (!is_null($contact->get_id())) {
                    $notification_group_id = $contact->get_notifications_group_id();
                }
                else if (!is_null($family->get_id())) {
                    $notification_group_id = $family->get_notifications_group_id() ;
                }
                else {
                    $notification_group_id = '' ;
                }
                $use_family_notifications = ($notification_group_id == $family->get_notifications_group_id() AND $notification_group_id != '');
                ?>
                <h3 class="border-title">Contact Information</h3>

                <div class="add_contact_type mb-3">
                    <?php
                    $has_email  = false;
                    $has_mobile = false;
                    foreach ($notifications as $notification) {
                        if ($notification['type_stub'] == 'email')  $has_email  = true;
                        if ($notification['type_stub'] == 'mobile') $has_mobile = true;
                    }
                    if (!$has_email) {
                        $notifications[] = ['id' => 'new', 'value' => @$login_details['email'], 'type_id' => 1, 'type_text' => 'Email', 'type_stub' => 'email' ];
                    }
                    ?>

                    <div class="contact_types_list">
                        <?php
                        foreach ($notifications as $notification) {
                            include 'snippets/add_edit_contact_method.php';
                        }
                        ?>
                    </div>

                    <label style="display:none">
                        <input class="form-input" type="text" id="contact_notifications_group_id" name="notifications_group_id" value="<?= $contact->get_notifications_group_id() ?>" style="display:none;"/>
                    </label>

                    <label class="input_group">
                        <select class="form-input select_type" style="height: 2.5rem; width:85px; font-size:13px!important;">
                            <optgroup label="Type">
                                <?php foreach ($notification_types as $notification_type): ?>
                                    <option value="<?= $notification_type['id'] ?>" data-stub="<?= $notification_type['stub'] ?>"
                                        <?= $notification_type['stub'] == 'email' ? ' selected="selected"' : '' ?>>
                                        <?= $notification_type['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <input type="hidden" id="notification_type_new" class="notification_new" name="tmp_contact_id" value="new">
                        <input name="contactdetail_id[new]" type="hidden" value="new">
                        <input type="hidden" id="notification_type_new" name="contactdetail_value[new][notification_id]" value="1">

                        <input class="form-input border-left rounded-0 enter_value validate[funcCall[validate_contact_enter_value]]" name="tmp_contact_enter_value" type="text" id="contact_enter_value" style="width:155px;width:calc(100% - 80px);" />

                        <span class="input_group-icon">
                            <button type="button" class="btn btn-link text-decoration-none submit_item" style="height: 2rem;">Add</button>
                        </span>
                    </label>
                </div>

                <?php if ($display_invite_button): ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="hidden" name="send_invite" id="send_invite" value="0" />
                            <button type="button" class="btn btn-primary btn-lg send_invite" id="contact-email-invite">Send login invitation</button>
                        </div>
                    </div>
                <?php elseif ($invitation && $invitation['status'] != 'Accepted'): ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <p><b><?=__('Invitation status:') . $invitation['status']?></b></p>
                        </div>
                    </div>
                <?php elseif($contact->get_linked_user_id() !== "0"): ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <a class="btn btn-primary btn-lg" href="/admin/usermanagement/user/<?= $contact->get_linked_user_id(); ?>">View contact's user</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div id="contact_preferences" class="<?=Settings::instance()->get('contacts3_display_contact_preferences') == 0 ? 'hidden' : ''?>">
                <h3 class="border-title">Contact preferences</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <ul>
                            <?php foreach ($preferences as $preference): ?>
                                <?php if ($preference['group'] == 'contact'): ?>
                                    <li>
                                        <input id="contact_preference_<?= $preference['stub'] ?>"  type="checkbox"
                                               name="preferences[]" value="<?= $preference['id'] ?>" data-name="<?= $preference['stub'] ?>"
                                            <?= (in_array($preference['id'], $contact_preference_ids)) ? ' checked="checked"' : '' ?> />
                                        <label for="contact_preference_<?= $preference['stub'] ?>"><?= $preference['label'] ?></label>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="special_preferences_section" class="toggleable-block">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label">Medical conditions</label>

                        <?php
                        $options = array();
                        if (!empty($preferences)) {
                            foreach ($preferences as $preference) {
                                if ($preference['group'] == 'special') {
                                    $options[$preference['id']] = $preference['label'];
                                }
                            }
                        }
                        echo Form::ib_select(null, 'preferences[]', $options, $contact_preference_ids, array('class' => 'multiple_select', 'multiple' => 'multiple'));
                        ?>
                    </div>
                </div>
            </div>

            <div id="notification_preferences" class="toggleable-block <?=Settings::instance()->get('contacts3_display_marketing_preferences') == 0 ? 'hidden' : ''?>">
                <h3 class="border-title">Notification preferences</h3>
                <div class="form-group">
                    <div class="col-sm-12">
                        <ul>
                            <?php foreach ($preferences as $preference): ?>
                                <?php if ($preference['group'] == 'notification'): ?>
                                    <li class="contact_preferences contact_preference_<?= $preference['stub'] ;?>_item">
                                        <input id="contact_preference_<?= $preference['stub'] ?>"  type="checkbox"
                                               name="preferences[<?= $preference['id'] ?>]" value="<?= $preference['id'] ?>" data-name="<?= $preference['stub'] ?>"
                                            <?= (in_array($preference['stub'], $required_preferences)) ? 'class="validate[funcCall[validate_preferences]]"' : '' ?>
                                            <?= (in_array($preference['id'], $contact_preference_ids)
                                                || (empty($contact->get_id()) && in_array($preference['stub'],$required_preferences))) ? ' checked="checked"' : '' ?> />
                                        <label for="contact_preference_<?= $preference['stub'] ?>"><?= $preference['label'] ?></label>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="contact-external-section">
                <h3 class="border-title">External reference</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_student_id">Reference number</label>

                        <?php
                        $attributes = ['id' => 'contact_student_id'];
                        $args = ['right_icon' => '<span title="Other number for identifying the contact e.g. student ID"><span class="icon-question"></span></span>'];
                        echo Form::ib_input(null, 'student_id', $contact->get_student_id(), $attributes, $args);
                        ?>
                    </div>
                </div>
            </div>

            <?php if (Auth::instance()->has_access('contacts3_notes')): ?>
                <div id="contact_note_section" class="toggleable-block-a">
                    <h3 class="border-title">Add a new note</h3>

                    <div class="form-group">
                        <label class="sr-only" for="contact_notes">Add a new note</label>

                        <div class="col-sm-12">
                            <?= Form::ib_textarea(null, 'notes', null, ['id' => 'contact_notes', 'rows' => 4]); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($contact_type == 'Organisation'):?>
            <div id="contact_note_section" class="toggleable-block-a">
                <h3 class="border-title">Domain name</h3>

                <div class="form-group">
                    <label class="sr-only" for="contact_notes">Domain name</label>
                    <div class="col-sm-12">
                    <?= Form::ib_input(null, 'domain_name',  $contact->get_domain_name(),
                            ['id' => 'domain_name']); ?>
                    </div>
                </div>
            </div>
            <?php if (Settings::instance()->get('organisation_integration_api')):?>

            <div id="contact_note_section" class="toggleable-block-a">
                <h3 class="border-title">External API (CDS) Account</h3>

                <div class="form-group">
                    <label class="sr-only" for="contact_notes">External API (CDS) Account</label>
                    <div class="col-sm-12">
                    <?= Form::ib_select(null, 'external_api_account', array(),null,
                            ['id' => 'external_api_account']); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <button id="find_external_accounts" type="button" class="btn btn-primary btn-lg btn--full d-block add">Find Accounts</button>
                    </div>
                </div>
            </div>
            <?php endif?>
            <?php endif?>
            <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'timeoff')): ?>
                <div id="timeoff_preference_section">
                    <h3 class="border-title">Staff preferences</h3>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label" for="timeoff_annual_leave">Annual Leave</label>

                            <input class="form-input" id="timeoff_annual_leave" name="timeoff_annual_leave" type="text" value="<?=@$timeoff_config['timeoff.annual_leave']['value']?>" placeholder="days / year" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label" for="timeoff_log_hours_per_week">Time to log</label>

                            <input class="form-input" id="timeoff_log_hours_per_week" name="timeoff_log_hours_per_week" type="text" value="<?=@$timeoff_config['timeoff.log_hours_per_week']['value']?>" placeholder="hours / week" />
                        </div>
                    </div>
                </div>

                <div id="timeoff_hours" class="toggleable-block timeoff_hours">
                    <h3 class="border-title">Time preferences</h3>
                    <div class="form-group">
                        <table class="table">
                            <thead>
                                <tr><th>Active</th><th>Day</th><th>Start Time</th><th>End Time</th><th>Hours</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $days = array(0 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                                foreach ($days as $day => $day_name) {
                                    $timeoff_time_preferences = $timeoff_config['timeoff.time_preferences_' . $day_name];
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="timeoff_times[<?=$day?>][active]" value="1" <?=@$timeoff_time_preferences['is_active'] == 1 ? 'checked="checked"' : ''?> /> </td>
                                    <td><input type="hidden" name="timeoff_times[<?=$day?>][day]" value="<?=$day?>" /><?= $day_name;?></td>
                                    <td><input class="form-control timepicker" type="text" name="timeoff_times[<?=$day?>][start]" value="<?=str_replace('00:00', '00', @$timeoff_time_preferences['start_time'])?>" /></td>
                                    <td><input class="form-control timepicker" type="text" name="timeoff_times[<?=$day?>][end]" value="<?=str_replace('00:00', '00', @$timeoff_time_preferences['end_time'])?>" /></td>
                                    <td><input class="form-control" type="text" name="timeoff_times[<?=$day?>][hours]" value="<?=@$timeoff_time_preferences['value']?>" /></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div id="educational_details_section" class="toggleable-block">
                <h3 class="border-title">Current education</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="academic_year_id">Academic year</label>

                        <?php
                        $options = html::optionsFromRows('id', 'title', $academic_years, $contact->get_academic_year_id() , ['value' => '', 'label' => 'Please select']);
                        echo Form::ib_select(null, 'academic_year_id', $options, null, ['id' => 'academic_year_id']);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_school_id">School</label>

                        <?php ob_start(); ?>
                            <option value="">-- Please select-- </option>
                            <?php foreach($schools as $school): ?>
                                <option value="<?= $school['id'] ?>"<?= ($contact->get_school_id() == $school['id']) ? ' selected="selected"' : '' ?>>
                                    <?= $school['name'].(( ! empty($school['address1'])) ? ' - '.$school['address1'] : '') ?>
                                </option>
                            <?php endforeach; ?>
                            <?php $selected = ($contact->get_school_id() == 0 OR is_null($contact->get_school_id()) ) ? ' selected="selected"' : ''; ?>
                            <option <?= $selected ?> value="">-- Not on list --</option>
                        <?php $options = ob_get_clean(); ?>

                        <?= Form::ib_select(null, 'school_id', $options, null, ['id' => 'contact_school_id']); ?>
                    </div>
                </div>
                <div class="form-group hide" id="new_school">
                    <div class="col-sm-12">
                        <div class="control-label">School name</div>
                        <p>
                            Please go to courses providers to add a new school.
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_year_id">School year</label>

                        <?php
                        $options = html::optionsFromRows('id', 'year', $years, $contact->get_year_id(), ['value' => '', 'label' => '-- Please select--']);
                        // Don't require this for school year
                        $class_attr = (Settings::instance()->get('cms_platform') !== 'training_company') ? 'validate[funcCall[validate_school_year]]' : '';
                        $attributes = ['class' => $class_attr, 'id' => 'contact_year_id'];
                        echo Form::ib_select(null, 'year_id', $options, null, $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="flexi_student">Flexi student</label>
                        <div class="btn-group" data-toggle="buttons" id="flexi_student">
                            <label class="btn btn-default<?= ($is_flexi_student) ? ' active' : '' ?>">
                                <input type="radio"<?= ($is_flexi_student) ? ' checked="checked"' : '' ?> value="1" name="is_flexi_student" id="contact_is_flexi_student_yes">Yes
                            </label>
                            <label class="btn btn-default<?= ( ! $is_flexi_student) ? ' active' : '' ?>">
                                <input type="radio"<?= ( ! $is_flexi_student) ? ' checked="checked"' : '' ?> value="0" name="is_flexi_student" id="contact_is_flexi_student_no">No
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="contact_school_id">Course types</label>

                        <?php
                        $options = html::optionsFromRows('id', 'category', $course_types, $contact_course_type_ids);
                        $attributes = ['id' => 'student_course_type_preferences', 'multiple' => 'multiple'];
                        echo Form::ib_select(null, 'course_type_preferences[]', $options, null, $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="cycle">Cycle</label>

                        <?php
                        $options = ['' => '-- Please select --', 'Junior' => 'Junior', 'Senior' => 'Senior', 'Transition' => 'Transition'];
                        echo Form::ib_select(null, 'cycle', $options, $contact->get_cycle(), ['id' => 'cycle']);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12"><?=__('Subjects studied')?></div>

                    <div class="col-sm-12">
                        <div style="max-height: 300px; overflow-y: auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?=__('Subject')?></th>
                                    <th>&nbsp;</th>
                                    <th><?=__('Level')?></th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($subjects as $si => $subject) { ?>
                                <?php
                                $checked = false;
                                foreach ($subject_preferences as $subject_preference) {
                                    if ($subject['id'] == $subject_preference['subject_id']) {
                                        $checked = true;
                                        break;
                                    }
                                }
                                if (!$checked) {
                                    $subject_preference = null;
                                }
                                ?>
                                <tr>
                                    <td><?=$subject['name']?></td>
                                    <td>
                                        <div class="btn-group" data-toggle="buttons" id="subject_preferences[<?=$si?>][subject_id]" style="display: inline-flex;">
                                            <label class="btn btn-default<?= ($checked) ? ' active' : '' ?>">
                                                <input type="radio"<?= ($checked) ? ' checked="checked"' : '' ?> value="<?=$subject['id']?>" name="subject_preferences[<?=$si?>][subject_id]" id="subject_preferences[<?=$si?>][subject_id]yes">Yes
                                            </label>
                                            <label class="btn btn-default<?= ( ! $checked) ? ' active' : '' ?>">
                                                <input type="radio"<?= ( ! $checked) ? ' checked="checked"' : '' ?> value="0" name="subject_preferences[<?=$si?>][subject_id]" id="subject_preferences[<?=$si?>][subject_id]no">No
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" data-toggle="buttons" id="subject_preferences[<?=$si?>][level]" style="display: inline-flex;">
                                        <?php foreach ($levels as $level) { ?>
                                            <?php $checked = @$subject_preference['level_id'] == $level['id']; ?>
                                            <label class="btn btn-default<?= ($checked) ? ' active' : '' ?>">
                                                <input type="radio"<?= ($checked) ? ' checked="checked"' : '' ?> value="<?=$level['id']?>" name="subject_preferences[<?=$si?>][level_id]" id="subject_preferences[<?=$si?>][level_id]"><?=$level['level'][0]?>
                                            </label>
                                        <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="edit-contact-courses_i_would_like">Courses I would like</label>

                        <input type="text" class="form-input" id="edit-contact-courses_i_would_like"
                               name="courses_i_would_like" value="<?= $contact->get_courses_i_would_like() ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="edit-contact-student_points_required">Points Required</label>

                        <input type="text" class="form-control" id="edit-contact-student_points_required"
                               name="points_required" value="<?= $contact->get_points_required() ?>" />
                    </div>
                </div>
            </div>

            <div id="course_type_teaching_preferences_section" class="toggleable-block clearfix">
                <h3 class="border-title">Course type categories teaching preferences</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="teacher_course_type_teaching_preferences" class="control-label">Course types</label>

                        <?php
                        $options = html::optionsFromRows('id', 'category', $course_types, $contact_course_type_ids);
                        $attributes = ['multiple' => 'multiple', 'class' => 'multiple_select2 validate[required]', 'id' => 'teacher_course_type_teaching_preferences'];
                        echo Form::ib_select(null, 'course_type_preferences[]', $options, null, $attributes);
                        ?>
                    </div>
                </div>
            </div>

            <div id="subject_teaching_preferences_section" class="toggleable-block clearfix">
                <h3 class="border-title">Subject teaching preferences</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="teacher_subject_teaching_preferences" class="control-label">Subjects</label>

                        <?php
                        $options = html::optionsFromRows('id', 'name', $subjects, $contact_subject_ids);
                        $attributes = ['multiple' => 'multiple', 'class' => 'multiple_select2 validate[required]', 'id' => 'teacher_subject_teaching_preferences'];
                        echo Form::ib_select(null, 'subject_preferences[]', $options, null, $attributes);
                        ?>
                    </div>
                </div>
            </div>

            <div id="courses_subject_preferences_section" class="toggleable-block">
               <h3 class="border-title">Teaching courses preferences</h3>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="teacher_course_subject_teaching_preferences" class="control-label">Courses</label>

                        <?php
                        $options = html::optionsFromRows('id', 'title', $courses_subjects, $contact_course_subject_ids);
                        $attributes = ['multiple' => 'multiple', 'class' => 'multiple_select2 validate[required]', 'id' => 'teacher_course_subject_teaching_preferences'];
                        echo Form::ib_select(null, 'course_subject_preference[]', $options, null, $attributes);
                        ?>
                    </div>
                </div>
            </div>

            <div class="toggleable-block host-family-section" id="host-family-section">
                <h3 class="border-title">Host profile</h3>

                <div class="form-group host_application-pet_details_button">
                    <div class="col-sm-12">
                        <label class="control-label" for="host_application-pet_details_button">Pets</label>
                    </div>

                    <div class="col-sm-12">
                        <div class="btn-group btn-group-slide" data-toggle="buttons">
                            <label class="btn btn-plain <?=@$host['pets'] != '' ? 'active' : ''?>">
                                <input type="radio" name="host_application-pet_details_button" value="1"
                                       id="host_application-pet_details_button_yes"<?= @$host['pets'] != '' ? ' checked="checked"' : ''?>>Yes
                            </label>
                            <label class="btn btn-plain <?=@$host['pets'] == '' ? 'active' : ''?>">
                                <input type="radio" name="host_application-pet_details_button" value="0"
                                       id="host_application-pet_details_button_no"<?= @$host['pets'] == '' ? ' checked="checked"' : ''?>>No
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group host_application-pet_details"<?= @$host['pets'] == '' ? ' style="display: none;"' : '' ?>>
                    <div class="col-sm-12">
                        <label class="control-label" for="host_application-pet_details_button">Pet details</label>

                        <?= Form::ib_textarea(null, 'host[pets]', @$host['pets'], ['id' => 'host_application-pet_details', 'rows' => 3]); ?>
                    </div>
                </div>

                <div class="form-group host_application-bedroom_details">
                    <div class="col-sm-12 pull-right">
                        <label class="control-label" for="host_application-bedroom_details">Description of student's bedroom and facilities</label>

                        <?= Form::ib_textarea(null, 'host[facilities_description]', @$host['facilities_description'], ['id' => 'host_application-bedroom_details', 'rows' => 3]); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="allowed_profile_types">Students host</label>

                        <?php
                        $options = [
                            'All Ages' => __('All Ages'),
                            'Under 18' => __('Under 18'),
                            'Group Leaders' => __('Group Leaders'),
                            'Smokers' => __('Smokers'),
                            'Male' => __('Male'),
                            'Female' => __('Female'),
                            'Vegetarian' => __('Vegetarian')
                        ];
                        $attributes = ['multiple' => 'multiple', 'class' => 'multiple_select', 'id' => 'allowed_profile_types', 'style' => 'display: none;'];
                        echo Form::ib_select(null, 'host[student_profile][]', $options, @$host['student_profile'], $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="availability">Availability</label>

                        <?php
                        $options = array(
                            'All Year' => __('All Year'),
                            'Summer' => __('Summer'),
                            'Winter' => __('Winter')
                        );
                        echo Form::ib_select(null, 'host[availability]', $options, @$host['availability'], ['id' => 'host_availability']);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="facilities">Facilities</label>

                        <?php
                        $options = array(
                            'WI-FI' => __('Wi-Fi'),
                            'Computer' => __('Computer'),
                            'Breakfast Lunch and Dinner' => __('Breakfast lunch and dinner')
                        );
                        $attributes = ['multiple' => 'multiple', 'class' => 'multiple_select', 'id' => 'facilities', 'style' => 'display: none;'];
                        echo Form::ib_select(null, 'host[facilities][]', $options, @$host['facilities'], $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="host_application-other_rules">House rules</label>

                        <textarea id="host_application-other_rules" name="host[rules]" rows="3"
                                  class="form-input form-input--textarea"><?=htmlentities(@$host['rules'])?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="host_application-other_rules">Other information</label>

                        <textarea id="host_application-other_rules" name="host[other]" rows="3"
                                  class="form-input form-input--textarea"><?=htmlentities(@$host['other'])?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="host_status"><?= __('Host application status') ?></label>

                        <?php
                        $options = array(
                            'Pending' => __('Pending'),
                            'Approved' => __('Approved'),
                            'Declined' => __('Declined')
                        );
                        echo Form::ib_select(null, 'host[status]', $options, @$host['status'], ['id' => 'host_status']);
                        ?>
                    </div>
                </div>
            </div>

            <?php
            if (! is_null($contact->address)) {
                $address_id = ( ! is_null($contact->address)) ? $contact->address->get_address_id() : '';
            }
            else if ( ! is_null($family->address)) {
                $address_id = $family->address->get_address_id() ;
            }
            else {
                $address_id = '' ;
            }
            $use_family_address = ( ! is_null($family->address) AND ($address_id == $family->address->get_address_id()) AND $address_id != '');
            ?>

            <?php // display:none; rather than type="hidden", so .defaultValue can be used ?>
            <input class="form-input" type="text" id="contact_address_id" name="address_id" value="<?= $address_id ?>" style="display:none;" />
        </div>
    </div>

    <? // Action buttons ?>
    <?php if (@$isDialog) { ?>
        <div class="action-buttons form-action-group">
            <button type="button" class="btn btn-primary save-and-select" id="save-and-select-btn">Save</button>
            <button type="button" class="btn-link close-dialog" id="close-dialog-btn">Close</button>
        </div>
    <?php } else { ?>
        <div class="action-buttons form-action-group">
            <button type="button" class="btn btn-primary save_button" data-action="save" data-original-title="Save" data-content="Save and stay on this page">Save</button>
            <?php if ($contact->is_new_contact()): ?>
                <button type="button" class="btn btn-success save_button" data-action="save_and_add" data-original-title="Save &amp; Add" data-content="Save and reload this form to create a new family member">Save &amp; Add</button>
            <?php endif; ?>
            <button type="button" class="btn btn-success save_button" data-action="save_and_exit" data-original-title="Save &amp; Exit" data-content="Save and return to the list screen">Save &amp; Exit</button>
            <?php if ( ! $contact->is_new_contact()): ?>
                <button type="button" class="btn btn-danger delete_button" data-action="delete" data-original-title="Delete" data-content="Delete this contact.">Delete</button>
            <?php else: ?>
                <input type="reset" class="btn btn-default" data-original-title="Reset" data-content="Undo all the change made."/>
            <?php endif; ?>
            <a href="/admin/contacts3" class="btn btn-cancel" data-original-title="Cancel" data-content="Exit the form without saving changes" >Cancel</a>
        </div>
    <?php } ?><div class="floating-nav-marker"></div>

    <?php // Modal boxes ?>
    <div id="contact_confirm_delete" class="modal fade confirm_delete_modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3>Confirm Deletion</h3>
				</div>
				<div class="modal-body">
					<p>Are you sure you wish to delete this contact?</p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-cancel" data-dismiss="modal" data-content="Exit the form without saving changes">Cancel</a>
					<a href="/admin/contacts3/delete_contact/<?= $contact->get_id() ?>" class="btn btn-danger" data-action="delete" data-content="Delete the current contact.">Delete</a>
				</div>
			</div>
		</div>
    </div>

    <div id="cannot_delete_primary_contact_modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3>Cannot Delete</h3>
				</div>
				<div class="modal-body">
					<p>You cannot delete a primary contact.</p>
				</div>
				<div class="modal-footer" style="text-align:center;">
					<a href="#" class="btn btn-cancel" data-dismiss="modal" style="width:100px;" data-content="Exit the form without deleting Contact">OK</a>
				</div>
			</div>
		</div>
    </div>

    <div id="contact_cannot_delete_contact" class="modal fade confirm_delete_modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3>Cannot delete</h3>
				</div>
				<div class="modal-body">
					<p>You cannot delete a contact with open bookings or transactions or is a primary contact.</p>
					<p id="cannot_delete_message"></p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-cancel" data-dismiss="modal" style="width:100px;" data-content="Exit the form without deleting Contact">OK</a>
				</div>
			</div>
		</div>
    </div>

    <div id="contact_no_accounts_modal" class="modal fade no_accounts_modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3>No Accounts</h3>
				</div>
				<div class="modal-body">
					<p>The <span class="modal-family-name"><?= $family->get_family_name() ?></span> family will not have any contacts with the "accounts" preference saved.</p>
					<p>Are you sure you want to continue?</p>
				</div>
				<div class="modal-footer">
					<a href="#" id="save-without-accounts" class="btn btn-primary" data-content="Save the contact without Account Preferences">Save</a>
					<a href="#" class="btn" data-dismiss="modal" data-content="Return to the form to Edit the contact">Return to Form</a>
				</div>
			</div>
		</div>
    </div>
    <?= View::factory('admin/usermanagement/invite_user')->set('modal_popup', true)->set('roles', $all_roles); ?>

    <div id="contact_create_new_family_modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3>Create New Family?</h3>
				</div>
				<div class="modal-body">
					<p>You have not specified an existing family.</p>
					<p>This will create a new primary contact and a new family.</p>
                    <p>using the surname <strong class="new-family-surname"></strong> and address details that you entered?</p>
				</div>
				<div class="modal-footer">
					<a href="#" id="save_contact_with_new_family" class="btn btn-primary" data-choice="1" data-content="Create a new contact and Family, this contact will be set as the primary contact for the family">Create Contact and Family</a>
					<? /*
					<a href="#" class="btn" data-choice="0">Save Contact Only</a>
					*/ ?>
<!--					<a href="#" class="btn" data-dismiss="modal">Cancel</a>-->
				</div>
			</div>
		</div>
    </div>

    <div id="stuff_role_not_selected_modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Missing Contact Information</h3>
                </div>

                <div class="modal-body">
                    <p>You must select at least one role for staff members</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" data-content="Return to the form to Edit the contact">Go back</button>
                </div>
            </div>
        </div>
    </div>

	<div id="contact_mobile_reminder_modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title">Missing Contact Information</h3>
				</div>

				<div class="modal-body">
					<p id="no_mobile_number">You have not specified a mobile number for this contact.</p>
                    <p id="no_email_address">You have not specified an email address for this contact.</p>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-primary save_button" id="contact_mobile_reminder_proceed_no_contact" data-content="Save the contact without an email or mobile number set">Proceed anyway</button>
					<button type="button" class="btn btn-default" data-dismiss="modal" data-content="Return to the form to Edit the contact">Go back</button>
				</div>
			</div>
		</div>
	</div>

    <div id="contact_mobile_delete" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title">Delete Contact Number</h3>
				</div>

				<div class="modal-body">
					<p id="delete_message"></p>
				</div>

				<input type="hidden" id="contact_mobile_delete_number_id" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-danger " id="contact_mobile_delete_proceed" data-content="Delete the contact number">Delete</button>
					<button type="button" class="btn btn-cancel" data-dismiss="modal" data-content="Return to the form to Edit the contact">Cancel</button>
				</div>
			</div>
		</div>
    </div>

    <div id="contact_mobile_delete_primary" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title">Primary Contact Number</h3>
				</div>
				<div class="modal-body">
					<p>You cannot delete a Primary contact mobile Number.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" data-content="Return to the form to Edit the contact">Go back</button>
				</div>
			</div>
		</div>
    </div>

</form>
<div class="modal fade" id="contact_data_overwrite_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Use contact details</h4>
			</div>
			<div class="modal-body">
				<p>Overwrite the current form data with information from <strong id="contact_data_overwrite_name"></strong>?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="contact_data_overwrite_confirm" data-content="Overwrite the current form to display the selected contact">Yes</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" data-content="Return to the form to Edit the current contact">No</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="contact_role_changed_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Role changed</h4>
            </div>
            <div class="modal-body">
                <p>You have changed the Role of this contact. Are you sure you wish to continue?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="contact_role_changed_confirm">Continue</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="contact_role_mistake_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Role Verify</h4>
            </div>
            <div class="modal-body">
                <p>You are changing the contact role for this contact. Please check to ensure this is correct.</p>
                <p>You have assigned these roles <b><span></span></b></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="contact_role_mistake_continue">Continue</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="contact_role_mistake_cancel">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    <?php $coordinates = ($residence->get_coordinates() != '') ? $residence->get_coordinates() : '52.664836,-8.619731,17';
    $billing_coordinates = ($billing_residence->get_coordinates() != '') ? $billing_residence->get_coordinates() : '52.664836,-8.619731,17'; ?>
    $(document).ready(function(){
        <?php if (Settings::instance()->get('google_map_key')) { ?>
        initialize_map('contact_map_summary', 'contact_map_search', 'contact_coordinates', <?= $coordinates ?>);
        if($('#contact2_map_summary').length > 0) {
            initialize_map('contact2_map_summary', 'contact2_map_search', 'contact2_coordinates', <?= $billing_coordinates ?>);
        }
        <?php } ?>
    });
    <?php
    $countries = Model_Country::get_countries(2, 'code');
    $options = '<option value=""></option>';
    foreach ($countries as $country) {
        $options .= '<option value="'.$country['dial_code'].'">+'.$country['dial_code'] . '</option>';
    }
    $country_attributes = array(
        'class'    => 'landline-international_code',
        'readonly' => false,
        'disabled' => false,
        'id'       => 'landline-international_code');
    $country_code_selected = !empty($notification['country_dial_code']) ? $notification['country_dial_code'] : '353';
    $country_code = Model_Country::get_country_code_by_country_dial_code($country_code_selected);
    $codes_array = Model_Country::get_phone_codes_country_code($country_code, 2, 'landline');
    $landline_codes = '<option value=""></option>';
    foreach($codes_array as $landline_code) {
        $landline_codes .= '<option value="'.$landline_code['dial_code'].'">' . $landline_code['dial_code'] . '</option>';
    }
    $mobile_codes_array = Model_Country::get_phone_codes_country_code($country_code);
    $mobile_codes = '<option value=""></option>';
    foreach($mobile_codes_array as $mobile_code) {
        $mobile_codes.= '<option value="' . $mobile_code['dial_code']. '">' . $mobile_code['dial_code'] . '</option>';
    }
    $code_attributes = array(
        'class'    => 'landline-code validate[required]' ,
        'readonly' => false,
        'disabled' => false,
        'id'       => 'dial_code_landline',
    );
    ?>
    var countries_options = '<?=$options?>';
    var landline_codes = '<?=$landline_codes?>';
    var mobile_codes  = '<?=$mobile_codes?>';
</script>
