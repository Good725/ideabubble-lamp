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
<div id="messaging_notification_template" style="padding-top: 10px;">
	<form class="form-horizontal" name="messaging_notification_template_form" method="post">
		<div id="message-template-alerts">
			<?php if($save_result === false): ?>
				<div class="alert alert-danger"><strong>Error</strong> Message not sent <a href="#" class="close"></a></div>
			<?php elseif ($save_result === true): ?>
				<div class="alert alert-success"><strong>Success</strong> Message sent <a href="#" class="close"></a></div>
			<?php endif; ?>
		</div>

		<div class="form-group">
			<label class="sr-only" for="message-template-name">Name (this will be used as a reference in the code)</label>
			<div class="col-sm-10">
				<input class="form-control" id="message-template-name" type="text" name="template_name" placeholder="Name (this will be used as a reference in the code)" />
			</div>
		</div>


        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#messaging-template-message-tab" aria-controls="messaging-template-message-tab" role="tab" data-toggle="tab">Message</a>
            </li>
            <li role="presentation">
                <a href="#messaging-template-details-tab" aria-controls="messaging-template-details-tab" role="tab" data-toggle="tab">Details</a>
            </li>
            <li role="presentation">
                <a href="#messaging-template-schedule-tab" aria-controls="messaging-template-schedule-tab" role="tab" data-toggle="tab">Schedule</a>
            </li>
            <?php if (!empty($notification_template['id'])): ?>
                <li role="presentation">
                    <a href="#messaging-template-preview-tab" aria-controls="messaging-template-preview-tab" role="tab" data-toggle="tab">Preview</a>
                </li>
            <?php endif; ?>
        </ul>

		<div class="tab-content" id="messaging_notification_template_table">
			<!-- Message tab -->
			<div role="tabpanel" class="tab-pane active" id="messaging-template-message-tab">
				<div class="form-horizontal">

					<div class="form-group">
						<label class="col-sm-2 control-label">Type</label>
						<div class="col-sm-4">
							<select class="form-control" name="type_id">
								<option value="">-- Please Select</option>
								<?php foreach($notification_types as $notification_type_id => $notification_type): ?>
									<option value="<?= $notification_type_id ?>"><?= $notification_type ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="message-template-driver">Driver</label>
						<div class="col-sm-4">
							<select class="form-control" id="message-template-driver" name="driver">
								<option value="dashboard" data-max-message-size="1000">Dashboard</option>
								<option value="email" data-has-message-subject="yes" data-max-message-size="10000000">EMAIL</option>
								<option value="sms" data-max-message-size="160">SMS</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="message-template-name">Name</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="message-template-name" name="name" placeholder="Type your name" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="message-template-from">From</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="message-template-from" name="sender" placeholder="Type to set sender or leave blank to use default messaging driver setting" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="message-template-replyto">Reply To</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="message-template-replyto" name="replyto" placeholder="" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="message-template-to">To</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="message-template-to" name="message-template[to]" placeholder="Type to add contact or contact list" />
							<div class="contact-list-labels" id="message-template-to-contact-list"></div>
						</div>
						<div class="col-sm-2">
							<a href="#" class="show-toggle" data-target="#message-template-cc-wrapper">CC</a>
							<a href="#" class="show-toggle" data-target="#message-template-bcc-wrapper">BCC</a>
						</div>
					</div>

					<div class="form-group" id="message-template-cc-wrapper" style="display: none;">
						<label class="col-sm-2 control-label" for="message-template-cc">CC</label>
						<div class="col-sm-6">
							<input type="text" class="form-control cc-autocomplete" id="message-template-cc" name="message-template[cc]" placeholder="Type to add contact or contact list" />
							<div class="contact-list-labels" id="message-template-cc-contact-list"></div>
						</div>
					</div>

					<div class="form-group" id="message-template-bcc-wrapper" style="display: none;">
						<label class="col-sm-2 control-label" for="message-template-bcc">BCC</label>
						<div class="col-sm-6">
							<input type="text" class="form-control bcc-autocomplete" id="message-template-bcc" name="message-template[bcc]" placeholder="Type to add contact or contact list" />
							<div class="contact-list-labels" id="message-template-bcc-contact-list"></div>
						</div>
					</div>

					<div class="form-group" id="attachments-wrapper">
						<label class="col-sm-2 control-label" for="attachments">Attachments<br />
							<button type="button" id="attachments-add-button">Add file</button>
						</label>
						<div class="col-sm-8">
							<div id="attachments-list"></div>
						</div>
					</div>

					<?php
					if (class_exists('Model_Docarrayhelper')) {
					?>
					<div class="form-group" id="docgeneration-wrapper">
						<label class="col-sm-2 control-label" for="attachments">Document Generation<br />

						</label>
						<div class="col-sm-8">
							<div class="form-group">
								<label class="col-sm-2">Document Generation</label>
								<input type="checkbox" name="doc_generate" value="1" <?=@$template['doc_generate'] == 1 ? 'checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('On') ?>" data-off="<?= __('Off') ?>" />
							</div>
							<div class="form-group">
								<label class="col-sm-2">Doc Helper</label>
								<select name="doc_helper" class="form-group">
									<option value=""></option>
									<?php
									$options = '';
									foreach (get_class_methods('Model_Docarrayhelper') as $docHelper) {
                                        $rm = new ReflectionMethod('Model_Docarrayhelper', $docHelper);
                                        $params = array();
                                        foreach ($rm->getParameters() as $param) {
                                            $params[] = $param->getName();
                                        }
										$options .= '<option value="' . $docHelper . '">' . $docHelper . '(' . implode(', ', $params) . ')</option>';
									}
									echo $options;
									?>
								</select>
							</div>
							<div class="form-group">
								<label class="col-sm-2">Template</label>
								<select name="doc_template_path" class="form-group">
									<option value=""></option>
									<?php
                                    echo HTML::optionsFromRows('path', 'path', Model_Files::getDirectoryTree('/templates', false));
									?>
								</select>
							</div>
                            <div class="form-group">
                                <label class="col-sm-2">File Format</label>
                                <select name="doc_type">
                                    <option value="PDF">PDF</option>
                                    <option value="DOCX">DOCX</option>
                                </select>
                            </div>
						</div>
					</div>


						<?php
					}
					?>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="message-template-subject">Subject</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="subject" id="message-template-subject" />
						</div>
						<div class="col-sm-4">
							<a href="#" class="show-toggle" data-target="#message-template-header-wrapper">header</a>
							<a href="#" class="show-toggle" data-target="#message-template-footer-wrapper">footer</a>
						</div>
					</div>

					<div class="form-group" id="message-template-header-wrapper" style="display: none;">
						<label class="col-sm-2 control-label" for="message-template-header">Header</label>
						<div class="col-sm-8">
							<textarea class="form-control" name="header" id="message-template-header"></textarea>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="message-template-message">Message</label>
						<div class="col-sm-8">
							<label class="message-template-popover-element"
								   data-container="body"
								   data-toggle="popover"
								   data-trigger="focus hover"
								   data-placement="right"
								   data-content="If a message has been defined in the code, tick this box to ensure the message below overwrites it."
								>
								<?php // If the checkbox is unchecked, the hidden field value will be posted ?>
								<input type="hidden" name="overwrite_cms_message" value="0" />
								<?php $checked = (isset($notification_template) AND $notification_template['overwrite_cms_message'] == 1) ?>
								<input type="checkbox" name="overwrite_cms_message" value="1"<?= $checked ? ' checked="checked"' : '' ?> />
								Overwrite automatic message
							</label>
							<textarea class="form-control ckeditor" rows="8" id="message-template-message" name="message"></textarea>
							<br />
							<p id="usable_parameters_in_template"></p>
						</div>
					</div>

					<div class="form-group" id="message-template-signature-wrapper">
						<label class="col-sm-2 control-label" for="message-template-signature_id">Add Signature</label>
						<div class="col-sm-6">
							<select class="form-control" id="message-template-signature_id" name="message-template[signature_id]">
								<option value=""></option>
								<?=html::optionsFromRows('id', 'title', $signatures, @$notification_template['signature_id'])?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="message-template-page_id">Newsletter Page</label>
						<div class="col-sm-8">
						<select class="form-control" id="message-template-page_id" name="page_id">
							<option value=""></option>
							<?php foreach($newsletter_pages as $newsletter_page): ?>
								<option value="<?=$newsletter_page['id']?>"><?=$newsletter_page['title']?></option>
							<?php endforeach; ?>
						</select>
						<span>* will override message if selected</span>
						</div>
					</div>

					<div class="form-group" id="message-template-footer-wrapper" style="display: none;">
						<label class="col-sm-2 control-label">Footer</label>
						<div class="col-sm-8">
							<textarea class="form-control" name="footer"></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- Details tab -->
			<div role="tabpanel" class="tab-pane" id="messaging-template-details-tab">
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="messaging-template-category">Category</label>
						<div class="col-sm-5">
                            <?php
                            $options = html::optionsFromArray(Model_Messaging::getNotificationCategories(), null, ['value' => '', 'label' => '']);
                            $attributes = ['class' => 'ib-combobox','id' => 'messaging-template-category'];
                            echo Form::ib_select(null, 'category_id', $options, null, $attributes);
                            ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label">Description</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="8" name="description"></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- Schedule tab -->
			<div role="tabpanel" class="tab-pane" id="messaging-template-schedule-tab">
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label">Schedule</label>
						<div class="col-sm-10">
							<?php $schedule = @$notification_template['send_interval'] ? true : false; ?>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default<?= ($schedule) ? ' active' : '' ?>">
									<input name="has_interval" type="radio"<?= ($schedule) ? ' checked="checked"' : '' ?> value="1" />Yes
								</label>
								<label class="btn btn-default<?= ( ! $schedule) ? ' active' : '' ?>">
									<input name="has_interval" type="radio"<?= ( ! $schedule) ? ' checked="checked"' : '' ?> value="0" />No
								</label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-2 control-label">Interval</div>
						<div class="col-sm-2 message-interval-group">
							<label for="messaging-template-interval-minutes">Minutes</label>
							<select multiple="multiple" class="form-control" id="messaging-template-interval-minutes" name="interval[minute][]">
								<option value=""></option>
								<option value="*"<?= in_array('*', $interval[0]) ? ' selected="selected"' : ''?>>All</option>
								<?php for($i = 0 ; $i < 60 ; ++$i): ?>
									<option value="<?= $i ?>"<?=in_array((string)$i, $interval[0]) ? ' selected="selected"' : ''?>><?= $i ?></option>
								<?php endfor; ?>
							</select>
						</div>
						<div class="col-sm-2 message-interval-group">
							<label for="messaging-template-interval-hours">Hours</label>
							<select multiple="multiple" class="form-control" id="messaging-template-interval-hours" name="interval[hour][]">
								<option value=""></option>
								<option value="*" <?=in_array('*', $interval[1]) ? 'selected="selected"' : ''?>>All</option>
								<?php for ($i = 0 ; $i < 60 ; ++$i): ?>
									<option value="<?= $i ?>" <?=in_array((string)$i, $interval[1]) ? 'selected="selected"' : ''?>><?= $i ?></option>
								<?php endfor; ?>
							</select>
						</div>
						<div class="col-sm-2 message-interval-group">
							<label for="messaging-template-interval-dates">Dates</label>
							<select multiple="multiple" class="form-control" id="messaging-template-interval-dates" name="interval[day_of_month][]">
								<option value=""></option>
								<option value="*"<?= in_array('*', $interval[2]) ? ' selected="selected"' : '' ?>>All</option>
								<option value="L"<?= in_array('L', $interval[2]) ? ' selected="selected"' : '' ?>>Last</option>
								<?php for ($i = 0 ; $i < 32 ; ++$i){ ?>
									<option value="<?= $i ?>"<?= in_array((string)$i, $interval[2]) ? ' selected="selected"' : '' ?>><?= $i ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-sm-2 message-interval-group">
							<label for="messaging-template-interval-months">Months</label>
							<select multiple="multiple" class="form-control" id="messaging-template-interval-months" name="interval[month][]">
								<option value=""></option>
								<option value="*"<?= in_array('*', $interval[3]) ? ' selected="selected"' : '' ?>>All</option>
								<?php foreach (array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December') as $i => $month): ?>
									<option value="<?= $i ?>"<?= in_array((string)$i, $interval[3]) ? ' selected="selected"' : ''?>><?= $month ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-sm-2 message-interval-group">
							<label for="messaging-template-interval-weekdays">Weekdays</label>
							<select multiple="multiple" class="form-control" id="messaging-template-interval-weekdays" name="interval[day_of_week][]">
								<option value=""></option>
								<option value="*"<?= in_array('*', $interval[4]) ? ' selected="selected"' : '' ?>>All</option>
								<?php foreach(array(0 => 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') as $i => $day_of_week): ?>
									<option value="<?= $i ?>"<?= in_array((string)$i, $interval[4]) ? ' selected="selected"' : '' ?>><?= $day_of_week ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
			</div>

            <?php if (!empty($notification_template['id'])): ?>
                <!-- Preview tab -->
                <div role="tabpanel" class="tab-pane" id="messaging-template-preview-tab">
                    <iframe src="/admin/messaging/template_preview/<?= $notification_template['id'] ?>" height="600" width="100%" style="border: 0;"></iframe>
                </div>
            <?php endif; ?>
		</div>
        <div class="form-action-group">
            <button class="btn btn-primary" type="submit" name="save">Save</button>
            <button class="btn btn-primary" type="submit" name="save" value="save_and_exit">Save &amp; Exit</button>
            <button type="reset" name="reset" class="btn btn-default">Reset</button>
            <a href="/admin/messaging/notification_templates" class="btn btn-cancel">Cancel</a>

        </div>
	</form>
	<style>
		.message-interval-group {
			padding-top: 7px;
		}
		/* Autocomplete */
		.messaging-ui-autocomplete {
			z-index: 9999;
		}
		.messaging-ui-autocomplete .ui-menu-item {
			padding: .3em;
		}
		.messaging-ui-autocomplete .ui-state-focus {
			background: #337ab7;
		}
		/* Labels for list of target contacts for to, cc, bcc */
		.contact-list-labels .label {
			display: inline-block;
			margin-right: 1em;
		}
		.contact-list-labels a.remove-to {
			color: #fff;
			text-decoration: none;
		}
	</style>

	<script>
	$(document).ready(function()
	{
		$('.message-template-popover-element').popover();
	});

	var recipient_provider_ids = <?=json_encode($recipient_provider_ids);?>;
	function add_recipient(existing_recipient)
	{
		var tbody = $("#messaging_recipient_list tbody")[0];
		var tr = document.createElement("tr");
		var td = null;
		var select = null;
		var input = null;
		var id_input = null;
		var status_input = null;
		var button = null;

		td = document.createElement("td");
		id_input = document.createElement("input");
		id_input.type = "hidden";
		id_input.name = "target_id[]";
		if(existing_recipient){
			id_input.value = existing_recipient.id;
		} else {
			id_input.value = "";
		}
		td.appendChild(id_input);
		status_input = document.createElement("input");
		status_input.type = "hidden";
		status_input.name = "target_id_status[]";
		if(existing_recipient){
			status_input.value = "noop";
		} else {
			status_input.value = "insert";
		}
		td.appendChild(status_input);
		select = document.createElement("select");
		select.name = "target_type[]";
		select.setAttribute('class', 'form-control');
		var options_html = '<option value=""></option>';
		for(var i in recipient_provider_ids){
			options_html += '<option value="' + recipient_provider_ids[i] + '">' + recipient_provider_ids[i] + '</option>';
		}
		options_html += '<option value="EMAIL">Email</option><option value="PHONE">Phone</option>';
		select.innerHTML = options_html;
		if(existing_recipient){
			select.value = existing_recipient.target_type;
		}
		if(existing_recipient){
			select.onchange = function(){
				status_input.value = "update";
			};
		}
		td.appendChild(select);
		tr.appendChild(td);

		td = document.createElement("td");
		input = document.createElement("input");
		input.setAttribute('class', 'form-control');
		input.type = "text";
		input.name = "target[]";
		if(existing_recipient){
			input.value = existing_recipient.target;
			input.onchange = function(){
				status_input.value = "update";
			};
		}
		td.appendChild(input);
		tr.appendChild(td);

		td = document.createElement("td");
		button = document.createElement("button");
		button.type = "button";
		button.innerHTML = "delete";
		button.setAttribute('class', 'btn btn-danger');
		button.onclick = function(){
			if(id_input.value != "new"){
				var delete_input = document.createElement("input");
				delete_input.type = "hidden";
				delete_input.name = "target_id_deleted[]";
				delete_input.value = id_input.value;
				button.form.appendChild(delete_input);
			}
			this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
		};
		td.appendChild(button);
		tr.appendChild(td);

		tbody.appendChild(tr);

		$(input).autocomplete({
			source: function(data, callback){
				if(select.value == "EMAIL" || select.value == "PHONE"){
					callback([]);
				} else {
					data.driver = $("[name=driver]").val();
					data.type = select.value;
					$.get("/admin/messaging/to_autocomplete",
							data,
							function(response){
								callback(response);
								$(".ui-helper-hidden-accessible").css("display", "none");
								$(".ui-autocomplete").css("max-height", "300px").css("overflow","auto");
							});
				}
			}
		});
	}

	function fill_template(template_data)
	{
		console.log(template_data);
		var form                    = document.forms.messaging_notification_template_form;
		form.type_id.value          = template_data.type_id;
		form.template_name.value    = template_data.name.trim();
		form.template_name.readOnly = Boolean(template_data.name.trim());
		form.description.value      = template_data.description;
		form.driver.value           = template_data.driver.toLowerCase();
		form.sender.value           = template_data.sender;
		form.replyto.value          = template_data.replyto;
		form.subject.value          = template_data.subject;
		form.message.value          = template_data.message;
		form.page_id.value          = template_data.page_id;
		form.header.value           = template_data.header;
		form.footer.value           = template_data.footer;
		form.page_id.value          = template_data.page_id;
        $(form.category_id).val(template_data.category_id).change();


        try {
			form.doc_generate.checked    = template_data.doc_generate ? true : false;
			form.doc_template_path.value = template_data.doc_template_path;
			form.doc_helper.value        = template_data.doc_helper;
			form.doc_type.value          = template_data.doc_type;
		}catch(exc){

		}

		for(var i in template_data.targets)
		{
			//add_recipient(template_data.targets[i]);
			if(template_data.targets[i].x_details == "cc"){
				$("#message-template-cc-wrapper").show();
				messaging_target_add("#message-template-cc-contact-list", "recipient", template_data.targets[i], {"x_details": "cc"});
			} else if(template_data.targets[i].x_details == "bcc"){
				$("#message-template-bcc-wrapper").show();
				messaging_target_add("#message-template-bcc-contact-list", "recipient", template_data.targets[i], {"x_details": "bcc"});
			} else {
				messaging_target_add("#message-template-to-contact-list", "recipient", template_data.targets[i], {"x_details": ""});
			}
		}
		if (template_data.usable_parameters_in_template) {
			form.template_name.readOnly = true;
			$("#usable_parameters_in_template").html('This template is created by the cms and will be used for <b>' + template_data.create_via_code + '</b><br />' +
				'You can use following parameters in message: <pre>' + template_data.usable_parameters_in_template + '<\/pre>');
		}
		if (template_data.attachments && template_data.attachments.length > 0) {
			for (var i in template_data.attachments) {
				$("#attachments-list").append(
						'<div>' +
						'<label>File Id:</label>' +
						'<input type="text" name="attachment[' + i + '][file_id]" value="' + template_data.attachments[i].file_id + '" />' +
						'</div>'
				);
			}
		}
	}

	<?php if ($notification_template != null){ ?>
	$(document).ready(function()
	{
		fill_template(<?=json_encode($notification_template)?>);
	});
	<?php } ?>

	$("#messaging_recipient_list #add_to").on("click", function(){
		add_recipient();
	});
	$("#messaging_notification_template_table [name=provider]").on("change", function(){
		//alert(this.selectedIndex);
		var has_subject = ($(this.options[this.selectedIndex]).attr("data-has-message-subject"));
		$("#messaging_notification_template_table").find("[name=subject]").prop("disabled", ! has_subject);
	});
	$("[name=messaging_notification_template_form]").on("submit", function(){
		var max_size = parseInt($(this.driver.options[this.driver.selectedIndex]).attr("data-max-message-size"));
		if(this.message.value.length > max_size){
			alert("Maximum message size is " + max_size + "\nPlease shorten your message");
			return false;
		}
		return true;
	});


	</script>
</div>
