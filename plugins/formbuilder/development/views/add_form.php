<?= (isset($alert)) ? $alert : ''; ?>
<h3 class="heading"><?= (isset($form['id'])) ? "Form Designer - ".$form['form_name'] : "Form Creator"; ?></h3>
<ul class="nav nav-tabs">
    <li><a href="#details_tab" data-toggle="tab">Details</a></li>
    <li><a href="#form_builder_tab" data-toggle="tab">Form</a></li>
</ul>
<div class="tab-content clearfix">
    <div class="tab-pane active" id="details_tab">
        <form class="form-horizontal col-sm-9" id="form_event_edit">
            <input type="hidden" id="form_type" value="<?=(isset($current_id) AND $current_id == "new") ? 'new' : $form['id'];?>"/>
            <input type="hidden" id="form_html" name="fields"/>
            <input type="hidden" id="return_action" name="return_action"/>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="form_name">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control validate[required]" id="form_name" name="form_name" value="<?=(isset($form)) ? $form['form_name']: '';?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="form_id">Form ID</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control validate[required]" id="form_id" name="form_id" value="<?=(isset($form)) ? $form['form_id']: '';?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="action">Action</label>
                <div class="col-sm-10">
                    <input class="form-control validate[required]" type="text" id="custom_action" name="action" placeholder="Custom Action" value="<?= (isset($form)) ? $form['action'] : 'frontend/formprocessor/'?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="method">Method</label>
                <div class="col-sm-10">
                    <?php
                    $options = array('' => '', 'POST' => 'POST', 'GET' => 'GET');
                    echo Form::ib_select(null, 'method', $options, $form['method'], array('class' => 'validate[required]', 'id' => 'method'))
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="summary">Summary</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="summary" name="summary" rows="4"><?=(isset($form)) ? $form['summary']: '';?></textarea>
                </div>
            </div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for=""><?= __('Automatically print all form data in emails') ?></label>
				<?php $email_all_fields = ( ! empty($form['email_all_fields'])); ?>
				<div class="col-sm-10">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default<?= $email_all_fields ? ' active' : '' ?>">
							<input type="radio" name="email_all_fields" value="1"<?= $email_all_fields ? ' checked' : '' ?> /> <?= __('Yes') ?>
						</label>
						<label class="btn btn-default<?= ( ! $email_all_fields) ? ' active': ''; ?>">
							<input type="radio" name="email_all_fields" value="0"<?= ( ! $email_all_fields) ? ' checked': ''; ?> /> <?= __('No') ?>
						</label>
					</div>
				</div>
			</div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="publish">Publish</label>
				<div class="col-sm-10">
					<?php $publish = ( ! isset($form['publish']) OR $form['publish'] == 1); ?>
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default<?= ($publish) ? ' active': ''; ?>">
							<input type="radio" name="publish" value="1"<?= ($publish) ? ' checked': ''; ?> /> Yes
						</label>
						<label class="btn btn-default<?= ( ! $publish) ? ' active': ''; ?>">
							<input type="radio" name="publish" value="0"<?= ( ! $publish) ? ' checked': ''; ?> /> No
						</label>
					</div>
				</div>
            </div>

            <input type="hidden" name="captcha_version" value="2" />

            <?php if(Settings::instance()->get('captcha_enabled')): ?>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="captcha">Captcha</label>
					<div class="col-sm-10">
						<?php $captcha_enabled = (isset($form['captcha_enabled']) AND $form['captcha_enabled'] == 1); ?>
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-default<?= ($captcha_enabled) ? ' active': ''; ?>">
								<input type="radio" name="captcha_enabled" value="1"<?= ($captcha_enabled) ? ' checked': ''; ?> /> Yes
							</label>
							<label class="btn btn-default<?= ( ! $captcha_enabled) ? ' active': ''; ?>">
								<input type="radio" name="captcha_enabled" value="0"<?= ( ! $captcha_enabled) ? ' checked': ''; ?> /> No
							</label>
						</div>
					</div>
				</div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="formbuilder-captcha_version">CAPTCHA Version</label>
                    <div class="col-sm-10">
                        <?php
                        $options = array('1' => 'Version 1', '2' => 'Version 2');
                        $selected = empty($form['captcha_version']) ? 1 : $form['captcha_version'];
                        echo Form::ib_select(null, 'captcha_version', $options, $selected);
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="success_page">Captcha Success</label>
                <div class="col-sm-10">
                    <?php
                    $options = Model_Pages::get_pages_as_options(isset($options['redirect']) ? $options['redirect'] : NULL);
                    echo Form::ib_select(null, 'success_page', $options, null, array('id' => 'success_page'));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="failure_page">Captcha Failure</label>
                <div class="col-sm-10">
                    <?php
                    $options = Model_Pages::get_pages_as_options(isset($options['failpage']) ? $options['failpage'] : NULL);
                    echo Form::ib_select(null, 'failure_page', $options, null, array('id' => 'failure_page'));
                    ?>
                </div>
            </div>

			<?php $use_stripe = (isset($form['use_stripe']) AND $form['use_stripe']); ?>
			<?php if (Settings::instance()->get('stripe_enabled')): ?>
				<div class="form-group">
					<label class="col-sm-2 control-label">Stripe</label>
					<div class="col-sm-10">
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-default<?= ($use_stripe) ? ' active': ''; ?>">
								<input type="radio" name="use_stripe" value="1"<?= ($use_stripe) ? ' checked': ''; ?> /> Yes
							</label>
							<label class="btn btn-default<?= ( ! $use_stripe) ? ' active': ''; ?>">
								<input type="radio" name="use_stripe" value="0"<?= ( ! $use_stripe) ? ' checked': ''; ?> /> No
							</label>
						</div>
					</div>
				</div>
			<?php else: ?>
				<input type="hidden" name="use_stripe" value="<?= $use_stripe ? 1 : 0 ?>" />
			<?php endif; ?>
        </form>
    </div>

    <div class="tab-pane" id="form_builder_tab">
        <p>Drag non-hidden fields to reorder them.</p>
        <div id="form_renderer" class="left">
            <form id="form_renderer_complete_form">
                <div id="hidden_fields">
                    <?= (isset($form)) ? substr($form['fields'], 0, strpos($form['fields'], '<li')): ''; ?>
                </div>
                <ul class="sortable">
                    <?= (isset($form)) ? substr($form['fields'], strpos($form['fields'], '<li')): ''; ?>
                </ul>
            </form>
            <script src="<?= URL::get_engine_plugin_assets_base('formbuilder') ?>js/jquery.sortable.min.js"></script>
        </div>

        <div id="form_designer" class="well right">
            <form id="render_form" action="#">
                <label for="new_object_type">Field Type</label>

                <div class="width-100">
                    <select class="form-control" name="new_object_type" id="new_object_type">
                        <option value="">---Select---</option>
                        <option value="text">Text Input</option>
                        <option value="textarea">Textarea</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="select">Dropdown</option>
                        <option value="hidden">Hidden Field</option>
                        <option value="button">Button</option>
                        <option value="submit button">Submit Button</option>
                        <option value="captcha">reCAPTCHA Object</option>
                        <option value="fieldset">Fieldset</option>
                        <option value="datepicker">Datepicker</option>
                        <option value="file">File</option>
                    </select>
                </div>
                <div id="options_area" class="left">

                </div>

                <div id="preview_object">
                    <h6>Preview Object</h6>
                </div>
				<div class="form-designer-actions">
					<button class="btn btn-primary amend_item" id="amend_item">Amend Item</button>
					<input type="submit" class="btn btn-primary" id="add_new_form_object" value="Add Item"/>
				</div>
            </form>
        </div>

        <div id="formbuilder_options">
        <div class="options_text">
            <label for="label">Label</label><input type="text" class="form-control field_label"/>
            <label for="options_name">Name</label><input type="text" class="form-control options_name"/>
            <label for="options_id">ID</label><input type="text" class="form-control options_id" id="options_id"/>
            <label for="options_validation">Validation</label>
            <select class="form-control options_validation">
                <option value="" selected>None</option>
                <option value="validate[required]">Require</option>
                <option>Custom - Does Nothing</option>
            </select>
            <label for="options_width">Width</label><input type="text" class="form-control options_width" id="options_width"/>
        </div>

        <div class="options_textarea">
            <label for="label">Label</label><input type="text" class="form-control field_label"/>
            <label for="options_name">Name</label><input type="text" class="form-control options_name"/>
            <label for="options_id">ID</label><input type="text" class="form-control options_id" id="options_id"/>
            <label for="options_validation">Validation</label>
            <select class="form-control options_validation">
                <option value="" selected>None</option>
                <option value="validate[required]">Require</option>
                <option>Custom - Does Nothing</option>
            </select>
            <label for="options_rows">Rows</label><input type="text" class="form-control options_rows"/>
            <label for="options_cols">Columns</label><input type="text" class="form-control options_cols"/>
        </div>

        <div class="options_dropdown">
            <label for="label">Label</label><input type="text" class="form-control field_label"/>
            <label for="options_name">Name</label><input type="text" class="form-control options_name"/>
            <label for="options_id">ID</label><input type="text" class="form-control options_id"/>
            <label for="default_select">Default Option</label><input type="text" name="default"
                                                                     class="form-control options_select_default"/>
            <label>Value</label>
            <input type="text" style="width:30px;" class="form-control select_value"/>

            <div id="select_add_options">
                <button id="add_extra_select_option">Add Option</button>
            </div>
        </div>

        <div class="options_checkbox">
            <label for="label">Label</label><input type="text" class="form-control field_label"/>
            <label for="options_name">Name</label><input type="text" class="form-control options_name"/>
            <label for="options_id">ID</label><input type="text" class="form-control options_id"/>
            <label for="default_checkbox">Default Checked</label><select class="form-control default_checked" name="default_checkbox">
                <option selected value="no">No</option>
                <option value="yes">Yes</option>
            </select>
        </div>

        <div class="options_button">
            <input type="hidden" class="field_label"/>
            <label for="options_type">Type</label>
            <select class="form-control options_type">
                <option value="button">Button</option>
                <option value="submit">Submit</option>
                <option value="reset">Reset</option>
            </select>
            <label for="options_buttontext">Text</label><input type="text" class="form-control options_buttontext"/>
            <label for="options_name">Name</label><input type="text" class="form-control options_name" />
            <label for="options_id">ID</label><input type="text" class="form-control options_id"/>
            <label for="options_value">Value</label><input type="text" class="form-control options_value"/>
        </div>

        <div class="options_submit_button">
            <input type="hidden" class="field_label"/>
            <label for="options_value">Value</label><input type="text" class="form-control options_value"/>
            <label for="options_id">ID</label><input type="text" class="form-control options_id" />
        </div>

            <div class="options_datepicker">
                <label for="label">Label</label><input type="text" class="form-control field_label"/>
                <label for="options_name">Name</label><input type="text" class="form-control options_name"/>
                <label for="options_value">Value</label><input type="text" class="form-control options_value"/>
                <label for="options_id">ID</label><input type="text" class="form-control options_id" />
            </div>

        <div class="options_hidden">
            <label for="options_value">Name</label><input type="text" class="form-control options_name"/>
            <label for="options_value">Value</label><input type="text" class="form-control options_value"/>
            <label for="options_id">ID</label><input type="text" class="form-control options_id" />
        </div>

        <div class="options_captcha">
            <input type="hidden" class="field_label"/>
        </div>

        <div class="options_fieldset">
            <input type="hidden" class="field_label"/>
            <label for="fieldset_legend">Name</label><input type="text" class="form-control fieldset_legend"/>
            <label for="options_id">ID</label><input type="text" class="form-control options_id" />
        </div>

            <div class="options_files">
                <label for="options_value">Label</label><input type="text" class="form-control options_name field_label"/>
            </div>
        </div>
    </div>

    <div class="well left width-920 bottom-bar">
        <button data-action="save" class="save_form btn btn-success">Save</button>
        <button data-action="save_and_exit" class="save_form btn btn-primary">Save &amp; Exit</button>
        <a href="/admin/formbuilder" id="cancel_button" class="btn">Cancel</a>
    </div>
</div>