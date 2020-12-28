<div class="expanded-section" id="contact-editor">
    <?= isset($alert) ? $alert : '' ?>

	<input type="hidden" id="contact-editor-contact_id" value="<?= @$contact_details['id'] ?>" />

    <div id="edit_contact_heading" class="edit_heading">
		<div class="edit_heading-row">
			<h2 class="edit_heading-title">
				Contact
				<strong><?= @$contact_details['first_name'] . ' ' . @$contact_details['last_name'] ?>
					<span class="span_client_id">#<?=@$contact_details['id'] ? @$contact_details['id'] : __('New') ?></span>
				</strong>
			</h2>
			<div class="contact-flags">
				<?php
				foreach (Model_Contacts::getExtentions() as $extention) {
					$ext_data = $extention->getData($contact_details);
					if (isset($ext_data['flags'])) {
						foreach ($ext_data['flags'] as $flag) {
							?>
							<span class="contact-flag contact-flag--<?=@$flag['class']?>" data-value="<?=@$flag['value']?>"><?=$flag['text']?></span>
						<?php
						}
					}
				}
				?>
			</div>

			<?php if (is_numeric(@$contact_details['id'])) { ?>
                <?php if (Auth::instance()->has_access('contacts2_edit') && is_numeric(@$contact_details['id']) && Model_Plugin::is_enabled_for_role('Administrator', 'Families')) { ?>
				<div class="heading-buttons right">
					<div class="btn-group">
						<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
						<ul class="dropdown-menu pull-right">
							<?php if (Auth::instance()->has_access('contacts2_edit') && is_numeric(@$contact_details['id']) && Model_Plugin::is_enabled_for_role('Administrator', 'Families')) { ?>
								<li><a class="add_note contact">Add Note</a></li>
								<li><a class="upload_document" data-toggle="modal" data-target="#edit-event-upload_document_modal-modal">Upload Document</a></li>
                                <?php
                                if(
                                Model_Plugin::is_enabled_for_role('Administrator', 'contacts2')
                                &&
                                Model_Plugin::is_enabled_for_role('Administrator', 'messaging')
                                ) {
                                ?>
                                <li><a class="send_email_contact">Send an email</a></li>
                                <?php
                                }
                                ?>
                                <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'todos')): ?>
                                    <li>
                                        <a
                                            href="/admin/todos/add_todo/?related_plugin_name=contacts2&related_to_id=<?= $contact_details['id'] ?>&return_url=<?= urlencode('/admin/contacts2/edit/'.$contact_details['id']) ?>"
                                            target="_blank">Add task</a>
                                    </li>
                                <?php endif; ?>
							<?php } ?>
						</ul>
					</div>
				</div>
                <?php } ?>
			<?php } ?>
		</div>
		<div class="edit_heading-row">
			<div class="contact-labels">
				<!-- <span class="contact-label">Child</span>
				<span class="contact-label">6 Years</span>
				<span class="contact-label">Phone: 087 0000000</span> -->
			</div>
		</div>
    </div>

        <div id="edit_contact_wrapper">
            <div>
                <div id="header_buttons"></div>
				<div class="expand-section-tabs">
					<ul class="nav nav-tabs nav-tabs-contact">
						<li class="active"><a href="#contact-details-tab" data-toggle="tab">Details</a></li>
						<?php
						foreach (Model_Contacts::getExtentions() as $extention) {
							foreach ($extention->getTabs($contact_details) as $xTab) {
								?>
								<li><a data-toggle="tab" href="#contact-extention-<?= $xTab['name'] ?>-tab"><?= $xTab['title'] ?></a></li>
							<?php
							}
						}
						?>
						<!-- <li><a data-toggle="tab" href="#family-member-accounts-tab">Accounts</a></li> -->
						<?php if(Model_Plugin::is_enabled_for_role('Administrator', 'Courses') && false){ /*not used for now*/?>
						<li><a data-toggle="tab" href="#contact-attendance-tab" aria-expanded="true">Attendance</a></li>
                        <?php } ?>
						<?php if (Auth::instance()->has_access('contacts2_edit') && is_numeric(@$contact_details['id']) && Model_Plugin::is_enabled_for_role('Administrator', 'Families')) { ?>
							<li><a data-toggle="tab" href="#contact-notes-tab">Notes</a></li>

                            <?php if (Auth::instance()->has_access('todos')) { ?>
							<li><a data-toggle="tab" href="#contact-todos-tab">Todos</a></li>
                            <?php } ?>

                            <!-- <li><a data-toggle="tab" href="#contact-permissions-tab">Permissions</a></li> -->
							<li><a data-toggle="tab" href="#contact-documents-tab">Documents</a></li>
						<?php } ?>
					</ul>
				</div>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="contact-details-tab">
                <?php require_once 'contact_details.php' ?>
                <?php
                foreach (Model_Contacts::getExtentions() as $extention) {
                    foreach ($extention->getFieldsets($contact_details, 'last') as $xFieldset) {
                        if ($xFieldset['position'] != 'last')continue;
                ?>
                <?= View::factory($xFieldset['view'], array('contact' => $contact_details, 'data' => $extention->getData($contact_details))); ?>
                <?php
                    }
                }
                ?>
                </div>

                <?php
                foreach (Model_Contacts::getExtentions() as $extention) {
                    foreach ($extention->getTabs($contact_details) as $xTab) {
                ?>
                <div class="tab-pane" id="contact-extention-<?= $xTab['name'] ?>-tab">
                    <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                    <div class="content-area"><?= View::factory($xTab['view'], array('contact' => $contact_details, 'data' => $extention->getData($contact_details))); ?></div>
                </div>
                <?php
                    }
                }
                ?>

				<div class="tab-pane" id="contact-documents-tab">
					<div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
					<div class="content-area"><?=$documents?></div>
				</div>

                <?php if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){ ?>
                <div class="tab-pane" id="contact-messages-tab">
                    <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                    <div class="content-area">
                        <?php if(isset($messages) && count($messages) > 0){ ?>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <table class="table table-striped dataTable contact_messages_table">
                                        <thead>
                                        <tr>
                                            <th scope="col">Type</th>
                                            <th scope="col">From</th>
                                            <th scope="col">Subject</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Last Activity</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($messages as $message){ ?>
                                            <tr data-id="<?= $message['id']; ?>">
                                                <td><?= $message['driver']; ?></td>
                                                <td><?= $message['sender']; ?></td>
                                                <td><a target="_blank" href="/admin/messaging/details?message_id=<?=$message['id']?>"><?= htmlentities($message['subject']); ?></a></td>
                                                <td><?= $message['status']; ?></td>
                                                <td><?= IbHelpers::relative_time(max(strtotime($message['date_created']), strtotime($message['date_updated']))); ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

				<div class="tab-pane" id="contact-attendance-tab">
					<div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
					<div class="content-area"></div>
				</div>

                <?php if (Auth::instance()->has_access('contacts2_edit') && is_numeric(@$contact_details['id'])) { ?>
                <div class="tab-pane" id="contact-permissions-tab">
                    <table class="table" id="contact-permitted-users">
                        <thead>
                            <tr><th colspan="4">These users can access this contact</th></tr>
                            <tr><th>User #</th><th>Email</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permissions as $permission) { ?>
                            <tr>
                                <td><?=$permission['id']?></td>
                                <td><?=$permission['email']?></td>
                                <td><input type="hidden" name="has_permission_user_id[]" value="<?=$permission['id']?>"/><button type="button" class="btn" onclick="$(this).parent().parent().remove();">remove</button> </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>New permission</th>
                                <td><input type="text" id="contact-permission-user" /> </td>
                                <td><button type="button" id="contact-permission-add" class="btn">Add</button> </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php } ?>

                <?php if (Auth::instance()->has_access('contacts2_edit') && is_numeric(@$contact_details['id'])) { ?>
                    <div class="tab-pane" id="contact-notes-tab">
                        <?=View::factory('list_notes2', array('notes' => $notes))?>
                    </div>

					<div class="tab-pane" id="contact-todos-tab">
						<?=View::factory('list_todos')?>
					</div>
                <?php } ?>
            </div>
        </div>
</div>

<?= View::factory('edit_note') ?>
<?= View::factory('admin/snippets/documents_upload_modal_form') ?>

