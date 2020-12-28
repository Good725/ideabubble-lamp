<?php $has_bookings = (!empty($bookedDays) || !empty($filters)); ?>

<div class="form-group vertically_center">
    <div class="col-xs-9 col-sm-6">
        <h3><?= $contact->get_first_name() ?> <?= $contact->get_last_name() ?></h3>
    </div>

    <div class="col-xs-3">
        <?php if ($has_bookings): ?>
            <div class="dropdown search-filter-dropdown right<?= (!empty($filters)) ? ' filter-active' : '' ?>" data-autodismiss="false">
                <button type="button" class="btn-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="search-filter-label"><?= __('Filter') ?></span>
                    <span class="search-filter-amount"><?= (!empty($filters)) ? count($filters) : '' ?></span>
                    <span class="icon-angle-down"></span>
                </button>

                <ul class="dropdown-menu attendance-filter pull-right" data-contact_id="<?= $contact->get_id() ?>" data-date="<?= $date ?>">
                    <?php
                    $filter_options = array(
                        'Not Attending'    => array('label' => __('Not Attending'), 'highlight' => 'not_attending', 'icon' => 'icon_error-circle_alt'),
                        'Absent'           => array('label' => __('Absent'),        'highlight' => 'absent',        'icon' => 'icon_error-circle_alt'),
                        'Late'             => array('label' => __('Late'),          'highlight' => 'late',          'icon' => 'icon_error-triangle_alt'),
                        'Present'          => array('label' => __('Present'),       'highlight' => 'present',       'icon' => 'icon_check'),
                        'Attending'        => array('label' => __('Confirmed'),     'highlight' => 'confirmed',     'icon' => 'icon_check_alt2'),
                        'Early Departures' => array('label' => __('Left Early'),    'highlight' => 'left_early',    'icon' => 'icon_error-oct_alt')
                    );
                    ?>
                    <?php foreach ($filter_options as $status => $option): ?>
                        <li<?= (empty($statistics[$status]) || $statistics[$status] == 0) ? ' class="hidden"' : '' ?>>
                            <a class="dropdown-option dropdown-option-<?= $option['highlight'] ?>">
                                <label style="display: block; margin: 0;">
                                    <?php
                                    $checked = !empty($filters[$status]);
                                    $attributes = array('data-status' => $status, 'class' => 'right search-filter-checkbox');
                                    echo Form::ib_checkbox(null, 'attendance_filter[]', $status, $checked, $attributes);
                                    ?>
                                    <?= $option['label'] ?>
                                </label>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-xs-12 col-sm-3">
        <?php if ($has_bookings && Auth::instance()->has_access('contacts3_limited_family_access')): ?>
            <button type="button" class="btn btn-success btn-lg btn--full bulk-update-button" data-contact_id="<?= $contact->get_id() ?>"><?= __('Bulk update') ?></button>
        <?php endif; ?>
    </div>
</div>

<?php if ($has_bookings): ?>
    <div class="tabs_content">
        <ul class="clearfix list-unstyled">
            <?php foreach ($filter_options as $status => $option): ?>
                <li<?= (empty($filtered_statistics[$status]) || $filtered_statistics[$status] == 0) ? ' class="hidden"' : '' ?>>
                    <?= $option['label'] ?> - <span class="text-<?= $option['highlight'] ?>"><?= !empty($filtered_statistics[$status]) ? $filtered_statistics[$status] : 0 ?></span>
                </li>
            <?php endforeach; ?>
            <li aria-hidden="true">&nbsp;</li>
        </ul>
    </div>

    <div class="form-row contact_attendance_block" id="contact_attendance_block_<?= $contact->get_id() ?>">

        <div class="booking-days swiper-container timeline-swiper fullwidth--mobile">
            <?php include('booked_days.php'); ?>
        </div>

        <div class="booking-classes swiper-container timeline-swiper fullwidth--mobile">
            <?php include('booked_classes.php'); ?>
        </div>

        <div class="selected-classes"></div>
    </div>
<?php else: ?>
    <p><?= __('There are no bookings to display.') ?></p>
<?php endif; ?>

<?php include_once 'add_note_popup_calender.php'; ?>
<?php if ($allow_attendance_edit) { ?>
<div class="attendance-note-form attendance-note-form--class hidden">
    <div class="form-row gutters">
        <div class="col-sm-6">
            <p><?= __('Would you like to mark this 1 class as:') ?></p>

            <div class="form-row gutters">
                <div class="col-sm-7 col-md-6">
                    <?php
                    $options = array(1 => __('Attending'), 0 => 'Not Attending');
                    echo Form::ib_select(null, 'attending', $options, 1);
                    ?>
                </div>

                <div class="hidden-xs col-sm-5 col-sm-4">
                    <button class="attendance-note-form-confirm btn btn-default form-btn btn--full" type="button">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row gutters">
        <div class="col-sm-6">
            <label style="display: block;">
                <?= __('Add a reason for not attending') ?>
                <?= Form::ib_textarea(null, 'note', null) ?>
            </label>
        </div>
    </div>

    <div class="hidden-sm hidden-md hidden-lg">
        <button class="attendance-note-form-confirm btn btn-default form-btn btn--full" type="button"><?= __('Confirm') ?></button>
    </div>
</div>
<?php } ?>
<?php if ($allow_attendance_edit) { ?>
<div class="attendance-note-form attendance-note-form--day hidden">
    <div class="form-row gutters">
        <div class="col-sm-6">
            <p class="singular"><?= __('Would you like to mark this 1 class as:') ?></p>
            <p class="plural hidden"><?= __('Would you like to mark these $1 classes as:', array('$1' => '<span class="attendance-note-class_count">0</span>')) ?></p>

            <div class="form-row gutters">
                <div class="col-sm-7 col-md-6">
                    <?php
                    $options = array(1 => __('Attending'), 0 => 'Not Attending');
                    echo Form::ib_select(null, 'attending', $options, 1);
                    ?>
                </div>

                <div class="hidden-xs col-sm-5 col-sm-4">
                    <button class="attendance-note-form-confirm btn btn-success form-btn btn--full" type="button"><?= __('Confirm') ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row gutters">
        <div class="col-sm-6">
            <label style="display: block;">
                <?= __('Add a reason for not attending') ?>
                <?= Form::ib_textarea(null, 'note', null) ?>
            </label>
        </div>
    </div>

    <div class="hidden-sm hidden-md hidden-lg">
        <button class="attendance-note-form-confirm btn btn-success form-btn btn--full" type="button"><?= __('Confirm') ?></button>
    </div>
</div>
<?php } ?>

<?php if (!$allow_attendance_edit && $ask_attendance_auth) { ?>
    <div class="attendance-ask-auth col-sm-6">
        <div class="form-row gutters">
            <div class="col-sm-12">
                <p><?= __('You are not authorized to edit attendance. Ask your parent for auth code.') ?></p>
            </div>
        </div>

        <div class="form-row">
            <input type="hidden" id="attendance_auth_id" value="" />
            <button id="attendance-auth-send" class="btn btn-success form-btn btn--full" type="button"><?= __('Send Auth Code') ?></button>
            <p id="attendance-auth-send-message" class="hidden"><?= __('Authorization code has been sent.') ?></p>
        </div>
    </div>

    <div class="attendance-confirm-auth col-sm-6">
        <div class="form-row gutters">
            <div class="col-sm-12">
                <p><?= __('If you have an auth code. Please enter') ?></p>
            </div>
            <div class="form-row gutters">
                <?=Form::ib_input('Auth code', 'attendance_edit_auth_code', '', array('id' => 'attendance_edit_auth_code'))?>
            </div>
        </div>

        <div class="form-row">
            <button id="attendance-auth-confirm" class="btn btn-success form-btn btn--full" type="button"><?= __('Authorize') ?></button>
        </div>
    </div>
<?php } ?>

<style>
    .tabs_content {
        font-weight: 300;
        font-size: 14px;
    }

    .tabs_content li {
        float: left;
        padding: 0 10px 5px 0;
    }

    .tabs_content li + li {
        padding-left: 10px;
    }

    .text-absent,        .dropdown-option-absent .icon        {color: #e52058;}
    .text-not_attending, .dropdown-option-not_attending .icon {color: #9d9d9d;}
    .text-late,          .dropdown-option-late .icon          {color: #ef9022;}
    .text-present,       .dropdown-option-present .icon       {color: #68ab4a;}
    .text-confirmed,     .dropdown-option-confirmed .icon     {color: #95c813;}
    .text-left_early,    .dropdown-option-left_early .icon    {color: #a864a8;}

    .dropdown-option:hover .icon {color: #fff;}

    .dropdown-menu>li>.dropdown-option-absent:hover        {background-color: #e52058;color: #fff;}
    .dropdown-menu>li>.dropdown-option-not_attending:hover {background-color: #9d9d9d;color: #fff;}
    .dropdown-menu>li>.dropdown-option-late:hover          {background-color: #ef9022;color: #fff;}
    .dropdown-menu>li>.dropdown-option-present:hover       {background-color: #68ab4a;color: #fff;}
    .dropdown-menu>li>.dropdown-option-confirmed:hover     {background-color: #95c813;color: #fff;}
    .dropdown-menu>li>.dropdown-option-left_early:hover    {background-color: #a864a8;color: #fff;}

    .attendance-filter {font-size: 16px;}
    .attendance-filter .form-checkbox {margin-top: .5em; margin-bottom:.5em;}

    .attendance-note-form {
        font-weight: 200;
    }

    .booking-classes {
        border-bottom: 1px solid #12387f;
    }
</style>

