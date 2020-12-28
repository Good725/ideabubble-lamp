<?php
$custom_checkout = Settings::instance()->get('checkout_customization');
$student_tab_new_display = true;
// Violates MVC, but these variables are all over the place.
$schedule = new Model_Course_Schedule(isset($added_schedules[0]) ? $added_schedules[0] : '');
$is_student = (isset($contact) && $contact->has_role('student'));

if (isset($event_object)) {
    $sections = ['contact'];
}
// Show both sections if the setting is enabled otherwise or the schedule is a group booking (guardian section is required)
else if (Settings::instance()->get('course_checkout_guardian_fields') == 1 || $schedule->is_group_booking) {
    // Order of "student" and "guardian" sections are reversed for BCFE and BC Language
    // However guardian still needs to be section "0" to maintain the correct the field names.
    $reverse_sections = in_array($custom_checkout, ['bcfe', 'bc_language']);
    $sections = $reverse_sections ? ['1' => 'student', '0' => 'guardian'] : ['guardian', 'student'];
}
else {
    $sections = ['student'];
}

if ($is_student || isset($event_object)) {
    $student_tab_new_display = false;
}

if (empty($application_payment)) {
    $has_fulltime = false;
    $tmp_cart = Session::instance()->get('ibcart');
    if ($tmp_cart) {
        $tmp_cart_data = Controller_FrontEnd_Bookings::get_cart_data($tmp_cart['booking'], $tmp_cart['booking_id'], $tmp_cart['client_id'], $tmp_cart['discounts'], $tmp_cart['courses']);

        foreach ($tmp_cart_data as $tmp_item) {
            if ($tmp_item['type'] == 'course') {
                $has_fulltime = true;
            }
        }
    }
} else {
    $has_fulltime = true;
}

$is_fulltime = @Kohana::$config->load('config')->fulltime_course_booking_enable && empty($application_payment) && @$has_fulltime == true;

/* Determine which email fields are mandatory */
$user_role = new Model_Roles($user['role_id']);

// Default: Both are mandatory.
$guardian_email_required = true;
$student_email_required  = true;

// Custom rule for BCFE: Only student is required.
if (!in_array($custom_checkout, ['bcfe'])) {
    $guardian_email_required = true;
    $student_email_required  = true;
}

// Setting "on": Only the email field relevant to the logged-in user is mandatory.
if (Settings::instance()->get('booking_only_relevant_email_mandatory')) {
    switch (strtolower($user_role->role)) {
        case 'student':
            $guardian_email_required = true;
            $student_email_required  = true;
            break;

        case 'guardian':
        case 'parent':
        case 'teacher':
            $guardian_email_required = true;
            $student_email_required  = false;
            break;

        // If the user is logged out or neither a student nor guardian, fallback to the previous rules.
    }
}

$number_of_delegates = 0;
$delegate_count_session = (!empty($cart_session_info['number_of_delegates']))
    ? $cart_session_info['number_of_delegates']
    : false;
if (!empty($_POST['booking_items'])) {
    foreach ($_POST['booking_items'] as $booking_course) {
        foreach ($booking_course as $booking_item) {
            $number_of_delegates = isset($booking_item['number_of_delegates']) ? $booking_item['number_of_delegates'] : $number_of_delegates;
        }
    }
} else if(is_numeric($delegate_count_session)) {
    $number_of_delegates = $delegate_count_session;
}
if ($number_of_delegates > 1 && !in_array('student', $sections)) {
    $sections[] = 'student';
}
?>

<?php foreach ($sections as $key => $section): ?>
    <?php $prefix = ($section == 'student') ? 'student_' : ''; ?>
    <?php
    if ($section == 'guardian' && $guardian != null) {
        if ($guardian->has_role('mature')) {
            $has_student_tabs = true;
            $fmembers = Model_Contacts3::get_family_members($guardian->get_family_id());

            foreach ($fmembers as $fmember) {
                $contact = new Model_Contacts3($fmember['id']);
                if (!$contact->has_role('student') && !$contact->has_role('mature')) {
                    continue;
                }
                $fcontacts[] = $contact;
            }
            ?>
            <input type="hidden" name="guardian_id" value="<?=$guardian->get_id()?>" />
            <?php
            continue;
        }
    }
    ?>

    <div class="theme-form">
        <h3 class="checkout-heading contact-details-heading">
            <?php
            if ($section == 'student' && $schedule->is_group_booking) {
                echo '<span class="fa fa-address-card"></span> '.__('Delegate details');
            }
            else if ($section == 'student' && count($sections) > 1) {
                echo '<span class="fa fa-address-card"></span> '.__('Student details');
            }
            else if ($section == 'guardian' && count($sections) > 1) {
                echo '<span class="fa fa-address-card-o"></span> '.(in_array($custom_checkout, ['bcfe'])? __('Next of kin') : (($number_of_delegates > 0 && $schedule->is_group_booking) ? 'Lead booker details' :  __('Guardian details')));
            }
            else {
                echo '<span class="fa fa-address-card"></span> '.__('Contact details');
            }
            ?>
        </h3>

        <div class="theme-form-content">
            <div class="theme-form-inner-content">
                <?php // todo: Just put a setting here to control this text ?>
                <?php if ($key == 0 && !in_array($custom_checkout, ['bcfe', 'bc_language', 'sls'])): // Only show this message once ?>
                    <p id="checkout-need_details"><?= __('We need your contact details to improve your booking and checkout progress.') ?></p>
                <?php endif; ?>

                <?php
                $fmembers = array();
                $fcontacts = array();
                $has_student_tabs = false;
                if (isset($contact) && $section == 'student' && !@$application_payment && $schedule->is_group_booking != 1 && !in_array($custom_checkout, ['bcfe', 'bc_language', 'sls'])) {
                    if (isset($logged_contact) && $logged_contact->has_role('guardian')) {
                        $fmembers = Model_Contacts3::get_family_members($contact->get_family_id());

                        foreach ($fmembers as $fmember) {
                            $contact = new Model_Contacts3($fmember['id']);
                            if (!$contact->has_role('student') && !$contact->has_role('mature')) {
                                continue;
                            }
                            $fcontacts[] = $contact;
                        }
                    } else {
                        $fcontacts[] = $logged_contact;
                    }
                    //ob_clean();print_r($fmembers);exit;
                    $has_student_tabs = true;
                }
                ?>

                <?php if ($has_student_tabs): ?>
                    <div>
                        <ul class="nav nav-tabs" id="checkout-student-tabs">
                            <?php $first = true; ?>
                            <?php foreach ($fcontacts as $contact): ?>
                                <li <?=$first ? 'class="active" ' : ''?>data-student_id="<?=$contact->get_id();?>">
                                    <a href="#student_tab_<?=$contact->get_id();?>" data-toggle="tab"><?=$contact->get_first_name() . ' ' . $contact->get_last_name()?></a>
                                </li>
                                <?php $first = false; ?>
                            <?php endforeach; ?>

                            <?php if ($student_tab_new_display): ?>
                                <li data-student_id="new" class="<?=count($fcontacts) == 0 ? 'active' : ''?>"><a href="#student_tab_new" data-toggle="tab" title="<?= __('Add student') ?>"><?=count($fcontacts) == 0 ? __('New Student') : '+'?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($section == 'student' && $schedule->is_group_booking == 1): ?>
                    <div id="checkout-student-tabs-contents">
                        <?php for ($student_counter = 0; $student_counter < $number_of_delegates; $student_counter++): ?>
                            <div class="delegate_box border border-primary rounded mb-3 px-3">
                                <h3 class="my-3">Delegate <?= $student_counter+1 ?></h3>

                                <?php
                                $contact = new Model_Contacts3();
                                $email = '';
                                $disable_fields = false;
                                $email_required = true;
                                $is_already_delegate = true;
                                $subscribe_preference = false;
                                require 'checkout_contact_details_inner.php';
                                ?>
                            </div>
                        <?php endfor; ?>
                        <?php $is_already_delegate = false; ?>
                    </div>
                <?php elseif ($section == 'student' && @$application_payment == false && !in_array($custom_checkout, ['bcfe', 'bc_language', 'sls'])): ?>
                    <div<?= $has_student_tabs ? ' class="tab-content"' : '' ?> id="checkout-student-tabs-contents">
                        <?php $first = true; ?>
                        <?php foreach ($fcontacts as $contact): ?>
                            <div class="tab-pane <?=$first ? '' : 'hidden' ?>" id="student_tab_<?=$contact->get_id()?>">
                                <?php
                                $email_required = $student_email_required;
                                require "checkout_contact_details_inner.php";
                                ?>
                            </div>
                            <?php $first = false; ?>
                        <?php endforeach; ?>

                        <?php if ($student_tab_new_display): ?>
                            <div class="tab-pane <?=count($fcontacts) > 0 ? 'hidden' :'' ?>" id="student_tab_new">
                                <?php
                                $contact = new Model_Contacts3();
                                $email = '';
                                $disable_fields = false;
                                $email_required = false;
                                if (array_search('guardian', $sections) === false) {
                                    $email_required = true;
                                }
                                require "checkout_contact_details_inner.php";
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php
                        $email_required = true;

                        if ($section == 'guardian') {
                            $email_required = $guardian_email_required;

                            if (isset($guardian)) {
                                if (isset($contact)) {
                                    if ($contact->get_id() != $guardian->get_id()) {
                                        //checkout by student. disable parent data edit
                                        $disable_fields = true;
                                    }
                                }
                                $contact = $guardian;
                            } else {
                                $email = '';
                            }
                        }

                        if ($section == 'student') {
                            $email_required = $student_email_required;

                            if (isset($student) && @$application_payment) {
                                $contact = $student;
                                $disable_fields = true;
                            }
                        }
                        require "checkout_contact_details_inner.php";
                        unset($disable_fields);
                        unset($email);
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php if(Settings::instance()->get('cart_special_requirements_enable') == 1):?>
<div class="theme-form">
    <h3 class="checkout-heading contact-details-heading">
     Special Requirements
    </h3>
    <div class="theme-form-content">
        <div class="theme-form-inner-content">
            <div class="form-group" id="checkout-special-requirements-wrapper">
                <div class="col-sm-12">
                    <label for="checkout-special_requirements"><?= __('List any special requirements, include delegate names where necessary') ?></label>
                    <?php
                    $attributes = ['id' => 'checkout-special_requirements', 'rows' => 5, 'style' => 'resize: vertical'];
                    echo Form::ib_textarea(null, 'special_requirements', @$extra_data['special_requirements'], $attributes);
                    ?>
                </div>
             </div>
        </div>
    </div>
</div>
<?php endif?>
<?php
if ($is_fulltime || in_array($custom_checkout, ['bcfe', 'bc_language', 'sls'])) {
    require 'checkout_student_profile.php';
    require Kohana::find_file('views', 'application_student_education');
}

if (in_array($custom_checkout, ['bc_language', 'sls'])) {
    require Kohana::find_file('views', 'application_additional_information');
    require Kohana::find_file('views', 'application_travel_details');
}

if ($is_fulltime) {
    require Kohana::find_file('views', 'application_study_routine');
}
?>

