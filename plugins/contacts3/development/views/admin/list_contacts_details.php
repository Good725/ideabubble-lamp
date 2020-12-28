<?
//todo move to load time in JS at Contact initialisation
// get contact id and balance for display
$family_id = $family->get_id();
$transaction = ORM::factory('Kes_Transaction');
$balance_html = $transaction->get_contact_balance_label(NULL, $family_id);
$contact_type = Model_Contacts3::get_contact_type($contact->get_type());
$family_not_needed = ($contact_type['name'] == 'department' || $contact_type['name'] == 'organisation');
//Check for family is present. Departments and organisations don't have families
$has_family = ( ! is_null($family->get_id()) && !($family_not_needed));
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
<div id="edit_family">
    <div id="edit_family_heading" class="edit_heading"<?= ( ! $has_family) || (Settings::instance()->get('contacts_create_family') != 1) ? ' style="display:none;"' : '' ?>>
		<div class="edit_heading-left">
			<h2><span><?= $family->get_family_name() ?></span> - Family</h2>
			<div class="flags"></div>
		</div>
        <div class="edit_heading-right" id="top_right_info">
            <span id="button_todos_container"></span>
            <?php if (Auth::instance()->has_access('show_accounts_tab_bookings')):?>
                <span id="family_balance_status"><?= $balance_html; ?></span>
            <?php endif?>
            <span class="span_client_id">Family #<?=$family_id ?></span>

            <?php
            echo View::factory('snippets/btn_dropdown')
                ->set('title',         ['text' => '<span class="icon-ellipsis-h"></span>', 'html' => true])
                ->set('sr_title',      'Actions')
                ->set('options_align', 'right')
                ->set('options',       [
                    ['type' => 'button', 'title'  => 'Add note', 'attributes' => ['id' => 'add_family_note_btn', 'class' => 'add_note_btn', 'data-table' => 'family']],
                    ['type' => 'button', 'title'  => 'Add task', 'attributes' => ['id' => 'add_family_todo_btn', 'class' => 'add_todo_btn', 'data-table' => 'family']]
                ]);
            ?>
        </div>
    </div>

    <div<?= ( ! $has_family) || (Settings::instance()->get('contacts_create_family') != 1) ? ' style="display:none;"' : '' ?>>
        <?php if (Auth::instance()->has_access('contacts3_tab_actions_menu')): ?>
            <div class="heading-buttons" id="family_header_buttons"></div>
        <?php endif; ?>

        <ul class="edit_heading-tabs nav nav-tabs nav-tabs-family">
            <?php if(Settings::instance()->get('contacts_create_family') == 1):?>
            <li class="active"><a data-toggle="tab" href="#family-details-tab"  >Details</a></li>
            <?php endif?>
			<?php if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) { ?>
            <li><a data-toggle="tab" href="#family-bookings-tab" >Bookings</a></li>
			<?php } ?>
<!--            <li><a data-toggle="tab" href="#family-documents-tab">Documents</a></li>-->
            <li><a data-toggle="tab" href="#family-todos-tab"    >Todos</a></li>
            <li><a data-toggle="tab" href="#family-notes-tab"    >Notes</a></li>
			<?php if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) { ?>
            <li><a data-toggle="tab" href="#family-accounts-tab" >Accounts</a></li>
			<?php } ?>
            <li><a data-toggle="tab" href="#family-activities-tab" >Activities</a></li>
        </ul>
    </div>
    <div class="tab-content" style="<?= ($family_not_needed) ? 'padding-top:0px; ' : ''; ?>">
        <div class="tab-pane active" id="family-details-tab">
            <div class="alert-area"></div>
            <div class="content-area">
                <?php if ($has_family || $family_not_needed):
                    if ($has_family && Settings::instance()->get('contacts_create_family') == 1):
                        include 'add_edit_family.php';
                        $contact_members = ($family->get_family_name() != '') ? $family->get_family_name() . ' - ' : ' family members';
                    elseif ($family_not_needed):
                        $contact_members = ($contact->get_first_name() != '') ? $contact->get_first_name() . ' - members' : ' members';
                    endif; ?>
                    <div class="edit_heading" id="family_member_header">
                        <div class="edit_heading-left">
                            <h3><?= $contact_members ?></h3>
                        </div>

                        <div class="edit_heading-right">
                            <div class="right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <span class="icon-ellipsis-h"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="#" id="add_new_family_member_link">Add New Member</a></li>
                                        <!--<li><a href="#" data-toggle="modal" data-target="#link_contact_to_family_modal">Link Existing Contact</a></li>-->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
					<?php if (Settings::instance()->get('contacts_create_family') == 1) {?>
                    <p>This contact does not have a saved family.</p>
					<?php } ?>
                <?php endif; ?>
                <?php include 'list_contact_members.php'; ?>
            </div>
        </div>
        <div class="tab-pane" id="family-bookings-tab" ><div class="alert-area"></div><div class="content-area"></div></div>
		<div class="tab-pane" id="family-timetable-tab"><div class="alert-area"></div><div class="content-area"></div></div>
        <div class="tab-pane" id="family-documents-tab"><div class="alert-area"></div><div class="content-area"></div></div>
        <div class="tab-pane" id="family-todos-tab"    ><div class="alert-area"></div><div class="content-area"></div></div>
        <div class="tab-pane" id="family-notes-tab"    ><div class="alert-area"></div><div class="content-area"></div></div>
        <div class="tab-pane" id="family-accounts-tab" ><div class="alert-area"></div><div class="content-area"></div></div>
        <div class="tab-pane" id="family-activities-tab" ><div class="alert-area"></div><div class="content-area"></div></div>
    </div>
</div>
<div id="contact_booking_form_wrapper" class="contact_booking_form_wrapper" >
    <div class="tab-content" style="display: none;">
        <div id="edit_family_member_booking_heading" class="edit_heading">
            <div class="edit_heading-left"><h2>Edit Booking <strong></strong></h2>
                <div class="flags"></div>
            </div>
        </div>
        <div>
            <div class="heading-buttons" id="header_buttons"></div>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#family-member-booking-details-tab" data-toggle="tab">Details</a></li>
				<!-- <li class=""><a href="#family-member-booking-delegates-tab" data-toggle="tab">Delegates</a></li> -->
                <li class=""><a href="#family-member-booking-activity-tab" data-toggle="tab">Activity</a></li>
            </ul>
        </div>
        <div class="tab-pane active" id="family-member-booking-details-tab"><div class="alert-area"></div><div class="content-area"></div></div>
		<div class="tab-pane" id="family-member-booking-delegates-tab"><div class="alert-area"></div><div class="content-area"></div></div>
        <div class="tab-pane" id="family-member-booking-activity-tab"><div class="alert-area"></div><div class="content-area"></div></div>
    </div>
</div>
<? // Modal boxes ?>
<?php if (Auth::instance()->has_access('contacts3_notes')): ?>
    <div id="add_note_modal" class="modal fade add_note_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3>Add a Note</h3>
                </div>
                <div class="modal-body">
                    <p>Add a note to <span class="note_to"><span class="modal-inline-error">error: no target selected</span></span>.</p>
                    <form class="form-horizontal">
                        <input type="hidden" id="add_note_table" class="note_table_name" name="table" value="" />
                        <input type="hidden" id="add_note_link_id" name="link_id" value="" />
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="add_note_note">Note</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="add_note_note" name="note"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary note_save_btn">Save</a>
                    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div id="edit_notes_modal_wrapper"></div>
<?php endif; ?>

<?php if (Auth::instance()->has_access('contacts3_tasks')): ?>
    <div id="add_edit_todos_modal" class="modal fade add_edit_todos_modal">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>
<?php endif; ?>


<div id="add_booking_modal" class="modal hide add_booking_modal" role="dialog" aria-labelledby="add_booking_modal_header" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3 id="add_booking_modal_header">Add New Booking</h3>
				<p>Add a booking for <span class="booking_for"><span class="modal-inline-error">error: no target selected</span></span>.</p>
			</div>
			<div class="modal-body">
				<div class="form_wrapper"></div>
			</div>
			<div class="modal-footer">
				<a href="#" id="add_booking_modal_add_btn" class="btn btn-primary">Add</a>

			</div>
		</div>
	</div>
</div>


<div id="booking_periods_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Modal header</h3>
			</div>
			<div class="modal-body" id="family_modal_body">

			</div>
			<div class="modal-footer">

			</div>
		</div>
	</div>
</div>

<div id="link_contact_to_family_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="link_contact_to_family_modal_heading" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="link_contact_to_family_modal_heading">Link Contact to Family</h3>
			</div>
			<div id="link_contact_to_family_modal_alerts" class="modal-body form-horizontal">
				<div class="alert-area"></div>
				<input type="hidden" id="link_contact_to_family_contact_id" />
				<div class="form-group">
					<label for="link_contact_to_family_autocomplete" class="col-sm-3 control-label">Find</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" id="link_contact_to_family_autocomplete" placeholder="enter mobile / family / last name" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="link_contact_to_family">Add Contact</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
