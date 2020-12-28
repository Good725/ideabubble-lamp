<?
//todo move to load time in JS at Contact initialisation
// get contact id and balance for display
$contact_id = $contact->get_id();
$transaction = ORM::factory('Kes_Transaction');
$balance_html = $transaction->get_contact_balance_label($contact_id, NULL);


?>

<?= (isset($alert)) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div class="edit_heading-nested" id="list_family_members_wrapper"<?= ($has_family || $family_not_needed) ? '' : ' style="display:none;"' ?>>
    <?php // Not functional yet <button type="button" id="add_family_member_btn" class="btn add_family_member_btn">Add Member</button> ?>
    <?php include 'list_contact_members_table.php' ?>
</div>

<div class="edit_heading-nested" id="edit_family_member_wrapper">
    <input type="hidden" id="family_member-contact_id" value="<?= $contact_id ?>" />

    <div id="edit_family_member_heading" class="edit_heading">
        <div class="edit_heading-left">
            <h2></h2>
            <div class="flags"></div>
        </div>

        <div class="edit_heading-right" id="top_right_info">
            <span id="button_todos_container"></span>
            <?php if (Auth::instance()->has_access('show_accounts_tab_bookings')):?>
                <span id="member_balance_status"><?= $balance_html ?></span>
            <?php endif?>
            <span class="span_client_id">Contact #<?= $contact_id ?></span>

            <?php
            if (Auth::instance()->has_access('contacts3_actions_menu')) {
                $contact_general_dropdown = [];

                if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
                    $href = '/admin/bookings/add_edit_booking/' . ($contact_id == '' ? 'new' : '?contact=' . $contact_id);
                    $contact_general_dropdown[] = [
                        'type' => 'link',
                        'title' => 'Add booking',
                        'link' => $href,
                        'attributes' => [
                            'class' => 'add_contact_booking',
                            'target' => '_blank',
                            'data-contact-id' => $contact_id,
                        ]
                    ];
                }

                if (Auth::instance()->has_access('contacts3_notes')) {
                    $contact_general_dropdown[] = [
                        'type' => 'button',
                        'title' => 'Add note',
                        'attributes' => [
                            'id' => 'add_family_member_note_btn',
                            'data-table' => 'contacts',
                            'class' => 'add_note_btn',
                        ]
                    ];
                }

                if (Auth::instance()->has_access('contacts3_tasks')) {
                    $contact_general_dropdown[] = [
                        'type' => 'button',
                        'title' => 'Add task',
                        'attributes' => ['id' => 'add_family_member_todo_btn', 'data-table' => 'contacts'],
                    ];
                }

                if ((Settings::instance()->get('messaging_popout_menu') == '1') && Auth::instance()->has_access('contacts3_messages')) {
                    $messaging_types = [];
                    $user_contact = Model_Contacts3::get_user_by_contact_id($contact_id);
                    if ($contact->get_email() !== null) {
                        $messaging_types[] = 'email';
                    }

                    if ($contact->get_mobile() !== null) {
                        $messaging_types[] = 'sms';
                    }

                    if ($user_contact !== null) {
                        $messaging_types[] = 'alert';
                    }
                    foreach ($messaging_types as $messaging_type) {
                        $contact_label = "${contact_id} - " . $contact->get_first_name() . " " . $contact->get_last_name() . " - ";
                        $contact_label .= ($messaging_type === 'sms') ? $contact->get_mobile_number() : $contact->get_email();
                        // When sending an alert, it needs the user ID instead of the contact ID
                        $contact_general_dropdown[] = [
                            'type' => 'button',
                            'title' => "Compose {$messaging_type}",
                            'attributes' => [
                                'class' => 'contact_compose_message',
                                'target' => '_blank',
                                'data-id' => ($messaging_type === 'alert') ? $user_contact['id'] : $contact_id,
                                'data-message-type' => $messaging_type,
                                'data-contact-email' => $contact->get_email(),
                                'data-contact-sms' => $contact->get_mobile_number(),
                                'data-contact-label' => $contact_label,
                            ]
                        ];
                    }
                }

                if (!empty($contact_general_dropdown)) {
                    echo View::factory('snippets/btn_dropdown')
                        ->set('title', ['text' => '<span class="icon-ellipsis-h"></span>', 'html' => true])
                        ->set('sr_title', 'Actions')
                        ->set('options_align', 'right')
                        ->set('options', $contact_general_dropdown);
                }
            }
            ?>
        </div>
    </div>
    <div>
        <?php if (Auth::instance()->has_access('contacts3_tab_actions_menu')): ?>
            <div class="heading-buttons" id="header_buttons"></div>
        <?php endif; ?>
        <ul class="edit_heading-tabs nav nav-tabs nav-tabs-family_member">
            <li class="active"><a href="#family-member-details-tab" data-toggle="tab">Details</a></li>

            <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')): ?>
                <li><a data-toggle="tab" href="#family-member-bookings-tab">Bookings</a></li>

                <?php if (Auth::instance()->has_access('contacts3_applications')): ?>
                    <li>
                        <a data-toggle="tab" href="#family-member-applications-tab">
                            <?= Settings::instance()->get('courses_schedule_interviews_enabled') != 1 ? 'Applications' : 'Interviews' ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (Auth::instance()->has_access('contacts3_timetable')): ?>
                    <li><a data-toggle="tab" href="#family-member-timetable-tab">Timetable</a></li>
                <?php endif; ?>

                <?php if (Auth::instance()->has_access('contacts3_attendance')): ?>
                    <li><a data-toggle="tab" href="#family-member-attendance-tab">Attendance</a></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (Auth::instance()->has_access('contacts3_documents')): ?>
                <li><a data-toggle="tab" href="#family-member-documents-tab">Documents</a></li>
            <?php endif; ?>

            <?php if (Auth::instance()->has_access('contacts3_tasks')): ?>
                <li><a data-toggle="tab" href="#family-member-todos-tab">Todos</a></li>
            <?php endif; ?>

            <?php if (Auth::instance()->has_access('contacts3_notes')): ?>
                <li><a data-toggle="tab" href="#family-member-notes-tab">Notes</a></li>
            <?php endif; ?>

            <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')
                && Auth::instance()->has_access('show_accounts_tab_bookings')) { ?>
                <li><a data-toggle="tab" href="#family-member-accounts-tab">Accounts</a></li>
            <?php } ?>

            <?php if (class_exists('Model_Messaging') && Auth::instance()->has_access('contacts3_messages')): ?>
                <li><a data-toggle="tab" href="#family-member-messages-tab">Messages</a></li>
            <?php endif; ?>

            <?php if (class_exists('Model_Messaging') && Auth::instance()->has_access('contacts3_activities')): ?>
                <li><a data-toggle="tab" href="#family-member-activities-tab">Activities</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="family-member-details-tab">
            <div id="contact_menu_wrapper"></div>
        </div>
        <div class="tab-pane" id="family-member-bookings-tab">
            <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
            <div class="content-area"></div>
        </div>

        <?php if (Auth::instance()->has_access('contacts3_applications')): ?>
            <div class="tab-pane" id="family-member-applications-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                <div class="content-area"></div>
            </div>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('contacts3_timetable')): ?>
            <div class="tab-pane" id="family-member-timetable-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                <div class="content-area"></div>
            </div>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('contacts3_attendance')): ?>
            <div class="tab-pane" id="family-member-attendance-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                <div class="content-area"></div>
            </div>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('contacts3_applications')): ?>
            <div class="tab-pane" id="family-member-documents-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>

                <?= View::factory('admin/generate_timetable_doc'); ?>

                <div class="content-area"></div>
            </div>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('contacts3_tasks')): ?>
            <div class="tab-pane" id="family-member-todos-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                <div class="content-area"></div>
            </div>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('contacts3_notes')): ?>
            <div class="tab-pane" id="family-member-notes-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                <div class="content-area"></div>
            </div>
        <?php endif; ?>

        <?php if(Auth::instance()->has_access('show_accounts_tab_bookings')):?>
            <div class="tab-pane" id="family-member-accounts-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                <div class="content-area"></div>
            </div>
        <?php endif?>
        <?php if (class_exists('Model_Messaging') && Auth::instance()->has_access('contacts3_messages')): ?>
            <div class="tab-pane" id="family-member-messages-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                <div class="content-area"></div>
            </div>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('contacts3_activities')): ?>
            <div class="tab-pane" id="family-member-activities-tab">
                <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                <div class="content-area"></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<? // Modal boxes ?>
<div id="add_family_member_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Select a Family Member to Add</h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				<div class="form-group">
					<label class="col-sms3 control-label" for="add_family_member_name">Name</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" id="add_family_member_name" />
					</div>
					<input type="hidden" id="add_family_member_id" />
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-primary">Add Member</a>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>

<div id="guardian_add_booking_warning" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Primary Contact Bookings</h3>
            </div>
            <div class="modal-body">
                <p>This contact is a Guardian.</p>
                <p>You cannot add a booking to a guardian.<br>You Can choose another contact or change this contact to be a <strong>Mature Student</strong></p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="activity-lock-warning-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= __('Do you want to continue') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __('Please note a booking has been commenced by <b class="username"></b> at <b class="time"></b> for this contact do you wish to proceed?<br /> Please note if you continue you may create a double booking for this contact, If unsure please click NO.?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="activity-lock-continue-yes" data-dismiss="modal"><?= __('YES') ?></button>
                <button type="button" class="btn btn-default btn-green" id="activity-lock-continue-no" data-dismiss="modal"><?= __('NO') ?></button>
            </div>
        </div>
    </div>
</div>
