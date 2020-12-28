<!-- Send message modal -->
<?php
$auth = Auth::instance();
$message_types = array('dashboard' => 'Alert');
if ($auth->has_access('messaging_send_system_email')) {
    $message_types['email'] = 'Email';
}
if ($auth->has_access('messaging_send_system_email')) {
    $message_types['sms'] = 'SMS';
}
?>
<?php foreach ($message_types as $message_type => $message_type_name) { ?>
    <div class="modal fade send-message-modal" id="send-message-modal-<?= $message_type ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="send-message-form" id="send-<?= $message_type ?>-form">
                    <input type="hidden" name="driver" value="<?= $message_type ?>" />
                    <input type="hidden" name="message_id" value="" />

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Send an <?= $message_type_name ?></h4>
                    </div>

                    <div class="modal-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#send-<?= $message_type ?>-message-tab" aria-controls="send-<?= $message_type ?>-message-tab" role="tab" data-toggle="tab">Message</a>
                            </li>
                            <li role="presentation">
                                <a href="#send-<?= $message_type ?>-details-tab" aria-controls="send-<?= $message_type ?>-details-tab" role="tab" data-toggle="tab">Template Data</a>
                            </li>
                            <li role="presentation">
                                <a href="#send-<?= $message_type ?>-schedule-tab" aria-controls="send-<?= $message_type ?>-schedule-tab" role="tab" data-toggle="tab">Schedule</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Message tab -->
                            <div role="tabpanel" class="tab-pane active" id="send-<?= $message_type ?>-message-tab">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-from">From</label>
                                        <div class="col-sm-8">
                                            <?php
                                            $user = Auth::instance()->get_user();
                                            switch ($message_type)
                                            {
                                                case 'email': $from = Settings::instance()->get('mandrill_from_email'); break;
                                                case 'sms'  : $from = Settings::instance()->get('twilio_phone_number'); break;
                                                default     : $from = $user['id'];
                                            }
                                            ?>
                                            <?php // the hidden field contains the value sent to the database. The text box contains a more user-readable value ?>
                                            <input
                                                type="text" disabled="disabled" value="<?= ($message_type == 'dashboard') ? $user['email'] : $from ?>"
                                                class="form-control" id="send-<?= $message_type ?>-from"
                                            />
                                            <input type="hidden" name="<?= $message_type ?>[from]" value="<?= $from ?>" />
                                        </div>
                                    </div>

                                    <?php if ($message_type == 'email') { ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-replyto">Reply To</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="send-<?= $message_type ?>-replyto" name="<?= $message_type ?>[replyto]" value="<?= @$replyto ?>" />
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-to">To</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control send-<?= $message_type ?>-to" id="send-<?= $message_type ?>-to" name="<?= $message_type ?>[to]" placeholder="Type to add contact or contact list" />
                                            <div class="contact-list-labels" id="send-<?= $message_type ?>-to-contact-list"></div>
                                        </div>
                                        <?php if ($message_type == 'email'): ?>
                                            <div class="col-sm-2">
                                                <a href="#" class="show-toggle" data-target="#send-<?= $message_type ?>-cc-wrapper">CC</a>
                                                <a href="#" class="show-toggle" data-target="#send-<?= $message_type ?>-bcc-wrapper">BCC</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group" id="send-<?= $message_type ?>-cc-wrapper" style="display: none;">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-cc">CC</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control send-<?= $message_type ?>-cc" id="send-<?= $message_type ?>-cc" name="<?= $message_type ?>[cc]" placeholder="Type to add contact or contact list" />
                                            <div class="contact-list-labels" id="send-<?= $message_type ?>-cc-contact-list"></div>
                                        </div>
                                    </div>

                                    <div class="form-group" id="send-<?= $message_type ?>-bcc-wrapper" style="display: none;">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-bcc">BCC</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control send-<?= $message_type ?>-bcc" id="send-<?= $message_type ?>-bcc" name="<?= $message_type ?>[bcc]" placeholder="Type to add contact or contact list" />
                                            <div class="contact-list-labels" id="send-<?= $message_type ?>-bcc-contact-list"></div>
                                        </div>
                                    </div>

                                    <?php
                                    if ($message_type == 'email'){
                                    ?>
                                    <div class="form-group" id="send-<?= $message_type ?>-attachments-wrapper">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-attachments">Attachments<br />
                                            <button type="button" id="send-<?= $message_type ?>-attachments-add-button">Add file</button>
                                        </label>
                                        <div class="col-sm-8">
                                            <div id="send-<?= $message_type ?>-attachments-list"></div>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>

                                <div class="form-vertical">
                                    <?php if ($message_type == 'email' || $message_type == 'dashboard'): ?>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label" for="send-<?= $message_type ?>-subject">Subject</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="send-<?= $message_type ?>-subject" name="<?= $message_type ?>[subject]" placeholder="Subject" />
                                            </div>

                                            <?php if ($message_type == 'email'): ?>
                                                <div class="col-sm-3 col-sm-offset-9">
                                                    <a href="#" class="show-toggle" data-target="#send-<?= $message_type ?>-header-wrapper">header</a>
                                                    <a href="#" class="show-toggle" data-target="#send-<?= $message_type ?>-footer-wrapper">footer</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($message_type == 'email'): ?>
                                        <div class="form-group" id="send-<?= $message_type ?>-header-wrapper" style="display: none;">
                                            <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-header">Header</label>
                                            <div class="col-sm-12">
                                                <textarea class="form-control" id="send-<?= $message_type ?>-header" name="<?= $message_type ?>[header]"></textarea>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-message">Message</label>
                                        <div class="col-sm-12">
                                            <textarea class="form-control" id="send-<?= $message_type ?>-message" name="<?= $message_type ?>[message]"></textarea>
                                            <input type="hidden" name="<?=$message_type?>[page_id]" />
                                            <span id="send-<?=$message_type?>-page-title"></span>
                                        </div>
                                    </div>

                                    <?php if ($message_type == 'email'): ?>
                                        <div class="form-group" id="send-<?= $message_type ?>-footer-wrapper" style="display: none;">
                                            <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-footer">Footer</label>
                                            <div class="col-sm-12">
                                                <textarea class="form-control" id="send-<?= $message_type ?>-footer" name="<?= $message_type ?>[footer]"></textarea>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group" id="message-template-signature-wrapper">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-signature_id">Add Signature</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="send-<?= $message_type ?>-signature_id" name="<?= $message_type ?>[signature_id]">
                                                <option value=""></option>
                                                <optgroup label="Profile">
                                                    <option value="profile">Use Profile Signature</option>
                                                </optgroup>
                                                <optgroup label="Predefined Signatures">
                                                    <?=html::optionsFromRows('id', 'title', $signatures)?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Details tab -->
                            <div role="tabpanel" class="tab-pane" id="send-<?= $message_type ?>-details-tab">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-name">Template Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="send-<?= $message_type ?>-name" name="<?= $message_type ?>[template_name]" placeholder="Template Name (this will be used as a reference in the code)"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-category">Category</label>
                                        <div class="col-sm-5">
                                            <select class="form-control" id="send-<?= $message_type ?>-category" name="<?= $message_type ?>[category_id]">
                                                <option value=""></option>
                                                <?=html::optionsFromArray(Model_Messaging::getNotificationCategories(), null);?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="send-<?= $message_type ?>-description">Description</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" id="send-<?= $message_type ?>-description" name="<?= $message_type ?>[description]"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule tab -->
                            <div role="tabpanel" class="tab-pane" id="send-<?= $message_type ?>-schedule-tab">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Schedule</label>
                                        <div class="col-sm-10">
                                            <?php $schedule = FALSE; ?>
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default<?= ($schedule) ? ' active' : '' ?>">
                                                    <input type="radio"<?= ($schedule) ? ' checked="checked"' : '' ?> value="1" name="<?= $message_type ?>[schedule]">Yes
                                                </label>
                                                <label class="btn btn-default<?= ( ! $schedule) ? ' active' : '' ?>">
                                                    <input type="radio"<?= ( ! $schedule) ? ' checked="checked"' : '' ?> value="0" name="<?= $message_type ?>[schedule]">No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-2 control-label">Interval</div>
                                        <div class="col-sm-2 message-interval-group">
                                            <label for="send-<?= $message_type ?>-interval-minutes">Minutes</label>
                                            <select multiple="multiple" class="form-control" id="send-<?= $message_type ?>-interval-minutes" name="<?= $message_type ?>[interval][minutes]">
                                                <option value="*">All</option>
                                                <?php for ($i = 0; $i < 60; $i++): ?>
                                                    <option value="<?= $i ?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 message-interval-group">
                                            <label for="send-<?= $message_type ?>-interval-hours">Hours</label>
                                            <select multiple="multiple" class="form-control" id="send-<?= $message_type ?>-interval-hours" name="<?= $message_type ?>[interval][hours]">
                                                <option value="*">All</option>
                                                <?php for ($i = 0; $i < 24; $i++): ?>
                                                    <option value="<?= $i ?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 message-interval-group">
                                            <label for="send-<?= $message_type ?>-interval-dates">Dates</label>
                                            <select multiple="multiple" class="form-control" id="send-<?= $message_type ?>-interval-dates" name="<?= $message_type ?>[interval][dates]">
                                                <option value="*">All</option>
                                                <option value="L">Last</option>
                                                <?php for ($i = 1; $i <= 31; $i++): ?>
                                                    <option value="<?= $i ?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 message-interval-group">
                                            <label for="send-<?= $message_type ?>-interval-months">Months</label>
                                            <select multiple="multiple" class="form-control" id="send-<?= $message_type ?>-interval-months" name="<?= $message_type ?>[interval][months]">
                                                <option value="*">All</option>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 message-interval-group">
                                            <label for="send-<?= $message_type ?>-interval-weekdays">Weekdays</label>
                                            <select multiple="multiple" class="form-control" id="send-<?= $message_type ?>-interval-weekdays" name="<?= $message_type ?>[interval][weekdays]">
                                                <option value="*">All</option>
                                                <option value="1">Monday</option>
                                                <option value="2">Tuesday</option>
                                                <option value="3">Wednesday</option>
                                                <option value="4">Thursday</option>
                                                <option value="5">Friday</option>
                                                <option value="6">Saturday</option>
                                                <option value="7">Sunday</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="btn-group">
                            <button type="submit" name="save" value="send" class="btn btn-primary">Send</button>
                            <button type="submit" name="save" value="save" class="btn btn-default">Save</button>
                            <button type="submit" name="save" value="save_and_exit" class="btn btn-default">Save &amp; Exit</button>
                            <button type="submit" name="save" value="save_as_template" class="btn btn-default" onclick="if($('#send-<?= $message_type ?>-name').val() == ''){alert('Please enter template name');return false;}">Save as Template</button>
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#save-message-draft-modal">Cancel</button>
                            <button type="reset" name="reset" class="btn btn-default">Reset</button>
                        </div>

                        <input type="hidden" name="operation" value="" />
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<div class="modal fade" id="save-message-draft-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?= __('Save as Draft') ?></h4>
			</div>
			<div class="modal-body">
				<p>Would you like to save this message as a draft?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="save-message-draft-yes"><?= __('Yes') ?></button>
				<button type="button" class="btn btn-danger"  id="save-message-draft-no" data-dismiss="modal"><?= __('No') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Review') ?></button>
			</div>
		</div>
	</div>
</div>
