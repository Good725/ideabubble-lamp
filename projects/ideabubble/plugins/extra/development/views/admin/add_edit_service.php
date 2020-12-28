<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

<div class="col-sm-12">
	<?=(isset($alert)) ? $alert : ''?>
</div>

<div class="col-sm-12" id="contact_details" style="display: none;">
    <div class="col-sm-6" id="main_contact">
        <dl>
            <dt>Main contact</dt>
            <dd>
                <div class="contact_name"></div>
                <div class="contact_phone"></div>
                <div class="contact_email"></div>
            </dd>
        </dl>
    </div>
    <div class="col-sm-6" id="billing_contact">
        <dl>
            <dt>Billing contact</dt>
            <dd>
                <div class="contact_name"></div>
                <div class="contact_phone"></div>
                <div class="contact_email"></div>
            </dd>
        </dl>
    </div>
</div>

<form class="col-sm-12 form-horizontal" id="form_add_service" name="form_add_service" action="/admin/extra/add_service/" method="post">
    <input type="hidden" id="service_id" name="id" value="<?= @$data['id'] ?>" />
    <input type="hidden" id="service_redirect" name="redirect" />
    <fieldset>
        <legend>Service Details</legend>

        <div class="col-sm-6">
            <!-- Company -->
            <div class="form-group">
                <label class="col-sm-4 control-label required error" for="service_company_id">Company</label>
                <div class="col-sm-8">
                    <select class="form-control" id="service_company_id" name="company_id">
                        <option value="0">Please Select</option>
                        <?php foreach ($dropdowns['customers'] as $customer): ?>
                            <?php
                            if ($customer['id'] == @$data['company_id'])
                                $selected = ' selected="selected"';
                            else
                                $selected = '';
                            ?>
                            <option value="<?= $customer['id'] ?>"<?= $selected ?>><?= $customer['company_title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a onclick="view_details()">view details</a>
                </div>
            </div>

            <!-- Services -->
            <div class="form-group">
                <label class="col-sm-4 control-label required error" for="service_type_id">Services</label>
                <div class="col-sm-8">
                    <select class="form-control" id="service_type_id" name="type_id">
                        <option value="0">Please select</option>
                        <?php foreach ($dropdowns['service_types'] as $service_type): ?>
                            <?php $selected = ((isset($data['type_id']) AND $service_type['id'] == $data['type_id'])) ? ' selected="selected"' : ''; ?>
                            <option value="<?= $service_type['id'] ?>"<?= $selected ?>><?= $service_type['friendly_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Domain -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_url">Domain</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="service_url" name="url" value="<?= @$data['url']; ?>"/>
                    <?php if (isset($data['url'])): ?>
                        <a href="http://<?= $data['url'] ?>" target="_blank">view</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Domain type -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_domain_type_id">Domain Type</label>
                <div class="col-sm-8">
                    <select class="form-control" id="service_domain_type_id" name="domain_type_id">
                        <option value="0">Please Select</option>
                        <?php foreach ($dropdowns['domain_types'] as $domain_type): ?>
                            <?php
                            if ($domain_type['id'] == @$data['domain_type_id'])
                                $selected = ' selected="selected"';
                            else
                                $selected = '';
                            ?>

                            <option value="<?= $domain_type['id'] ?>"<?= $selected ?>><?= $domain_type['friendly_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Host -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_host_id">Host</label>
                <div class="col-sm-8">
                    <select class="form-control" id="service_host_id" name="host_id">
                        <option value="0">Please Select</option>
                        <?php foreach ($dropdowns['hosts'] as $host): ?>
                            <?php
                            if ($host['id'] == @$data['host_id'])
                                $selected = ' selected="selected"';
                            else
                                $selected = '';
                            ?>

                            <option value="<?= $host['id'] ?>"<?= $selected ?>><?= $host['friendly_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="hidden" for="service_new_host">New Host</label>
                    <input class="form-control" type="text" id="service_new_host" placeholder="Enter New Host" />
                </div>
            </div>

            <!-- Control Panel -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_control_panel_id">Control Panel</label>
                <div class="col-sm-8">
                    <select class="form-control" id="service_control_panel_id" name="control_panel_id">
                        <option value="0">Please Select</option>
                        <?php foreach ($dropdowns['control_panels'] as $control_panel): ?>
                            <?php
                            if ($control_panel['id'] == @$data['control_panel_id'])
                                $selected = ' selected="selected"';
                            else
                                $selected = '';
                            ?>

                            <option value="<?= $control_panel['id'] ?>"<?= $selected ?>><?= $control_panel['friendly_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- IP Address -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_ip_address">IP Address</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="service_ip_address" name="ip_address" value="<?= @$data['ip_address'] ?>" />
                </div>
            </div>

            <!-- Start Date -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_date_start">Start Date</label>
                <div class="col-sm-8">
                    <input type="text" id="service_date_start" class="form-control datepicker" name="date_start" value="<?= @$data['date_start'] ?>" />
                </div>
            </div>


            <!-- Expiry Date -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_date_end">Expiry Date</label>
                <div class="col-sm-8">
                    <input type="text" id="service_date_end" class="form-control datepicker" name="date_end" value="<?= @$data['date_end'] ?>" />
                    <a id="refresh_expiration">Get&nbsp;expiry</a>

                    <div id="expiry_date_suggestion_wrapper">
						<label class="sr-only" for="expiry_date_suggestion">Suggestion</label>
						<div class="input-group">
							<input class="form-control" type="text" id="expiry_date_suggestion" disabled="disabled" />
							<div class="input-group-btn">
								<button id="use_expiry_date_suggestion" type="button" class="btn">Use</button>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <!-- Price -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_price">Price</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="service_price" name="price" value="<?= @$data['price'] ?>" />
                </div>
            </div>

            <!-- Discount -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_discount">Discount</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="service_discount" name="discount" value="<?= @$data['discount'] ?>" />
                </div>
            </div>

            <!-- Billing Frequency -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_billing_frequency_id">Billing Frequency</label>
                <div class="col-sm-8">
                    <select class="form-control" id="service_billing_frequency_id" name="billing_frequency_id">
                        <option value="0">Please Select</option>
                        <?php foreach ($dropdowns['billing_frequencies'] as $billing_frequency): ?>
                            <?php
                            if ($billing_frequency['id'] == @$data['billing_frequency_id'])
                                $selected = ' selected="selected"';
                            else
                                $selected = '';
                            ?>

                            <option value="<?= $billing_frequency['id'] ?>"<?= $selected ?>><?= $billing_frequency['friendly_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Payment Type -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_payment_type_id">Payment Type</label>
                <div class="col-sm-8">
                    <select class="form-control" id="service_payment_type_id" name="payment_id">
                        <option value="0">Please Select</option>
                        <?php foreach ($dropdowns['payment_type'] as $payment_type): ?>
                            <?php $selected = (isset($data['payment_id']) AND $payment_type['id'] == $data['payment_id']) ? ' selected="selected"' : ''; ?>
                            <option value="<?= $payment_type['id'];?>"<?= $selected ?>><?= $payment_type['friendly_name'] ;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_status_id">Status</label>
                <div class="col-sm-8">
                    <select class="form-control" id="service_status_id" name="status_id">
                        <option value="0">Please Select</option>
                        <?php foreach ($dropdowns['statuses'] as $status): ?>
                            <?php $selected = (isset($data['status_id']) AND $status['id'] == $data['status_id']) ? ' selected="selected"' : ''; ?>

                            <option value="<?= $status['id'] ?>"<?= $selected ?>><?= $status['friendly_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Referrer -->
            <div class="form-group">
                <label class="col-sm-4 control-label" for="service_referrer">Referrer</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="service_referrer" name="referrer" value="<?= @$data['referrer'] ?>" />
                </div>
            </div>
			
			<div class="form-group">
                <label class="col-sm-4 control-label" for="auto_renew">Auto Renew</label>
                <div class="col-sm-8">
                    <input type="checkbox" id="auto_renew" name="auto_renew" value="1" <?=@$data['auto_renew'] == 1 ? 'checked="checked"' : '' ?>" />
                </div>
            </div>
        </div>

		<div class="col-sm-12">
			<div class="col-sm-6">
				<fieldset>
					<legend>Years Paid For</legend>
					<?php for ($year = 2009; $year <= date('Y') + 10; $year++): ?>

						<?php
						if (@is_numeric(strpos($data['years_paid'], strval($year))))
							$checked = ' checked="checked"';
						else
							$checked = '';
						?>

						<label class="checkbox">
							<input type="checkbox" name="years_paid[<?= $year ?>]"<?= $checked ?> /><?= $year ?>
						</label>

					<?php endfor; ?>
				</fieldset>
			</div>

			<div class="col-sm-6">
				<fieldset>
					<legend>Years Confirmed Renewal For</legend>
					<?php for ($year = 2009; $year <= date('Y') + 10; $year++): ?>

						<?php
						if (@is_numeric(strpos($data['years_confirmed'], strval($year))))
							$checked = ' checked="checked"';
						else
							$checked = '';
						?>

						<label class="checkbox">
							<input type="checkbox" name="years_confirmed[<?= $year ?>]"<?= $checked ?> /><?= $year ?>
						</label>

					<?php endfor; ?>
				</fieldset>
			</div>
		</div>

    </fieldset>

    <?php include 'list_notes.php'; ?>

    <div class="col-sm-12 well">
        <button type="button" id="btn_save" class="btn btn-primary">Save</button>
        <button type="button" id="btn_save_exit" class="btn btn-success">Save &amp; Exit</button>
        <?php if (isset($data['id'])): ?>
            <button type="button" id="btn_invoice" class="btn">Create Invoice</button>
            <button type="button" id="service_btn_delete" class="btn btn-danger">Delete</button>
        <?php endif; ?>
        <a href="/admin/extra/services"><button type="button" class="btn">Cancel</button></a>
    </div>

    <?php if (isset($data['id'])): ?>
        <div id="service_confirm_delete" class="modal fade confirm_delete_modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3>Confirm Deletion</h3>
					</div>
					<div class="modal-body">
						<p>Are you sure you wish to delete this service?</p>
					</div>
					<div class="modal-footer">
						<a href="/admin/extra/delete_service/<?= $data['id'] ?>" class="btn btn-danger" data-action="delete">Delete</a>
						<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
        </div>
    <?php endif; ?>

</form>

<?php if (isset($data['id'])) { ?>
<div class="form-horizontal">
<div id="invoice_create" class="modal fade invoice_create_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/extra/create_invoice" method="post">
                <input type="hidden" name="service_id" value="<?=$data['id']?>" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3>Invoice</h3>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="due_date">Due Date: </label>
                        <div class="col-sm-8"><input type="text" name="due_date" class="datepicker" value="<?=date('d-m-Y')?>" /></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="date_from">For Duration: </label>
                        <div class="col-sm-4"><input type="text" name="date_from" class="datepicker" value="<?=date('d-m-Y')?>" /></div>
                        <div class="col-sm-4"><input type="text" name="date_to" class="datepicker" value="<?=date('d-m-Y', strtotime('+1 year'))?>" /></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="amount">Amount (EUR): </label>
                        <div class="col-sm-8"><input type="text" name="amount" class="" value="<?=@$data['price']?>" /></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="status">Status: </label>
                        <div class="col-sm-8">
                            <select name="status" class="">
                                <option value="Unpaid">Unpaid</option>
                                <option value="Paid">Paid</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="bullethq_save">Save to BulletHQ: </label>
                        <div class="col-sm-8"><input type="checkbox" name="bullethq_save" class="" value="1" checked="checked" /></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-action="invoice">Create</button>
                    <a class="btn" data-dismiss="modal">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?php } ?>

<script type="text/javascript">
    $('#refresh_expiration').click(function()
    {
        $('#expiry_date_suggestion').val('<?= @$data['whois_data']['regrinfo']['domain']['expires'] ?>');
        $('#expiry_date_suggestion_wrapper').show();
    });

    $('#use_expiry_date_suggestion').click(function()
    {
        $('#service_date_end').val($('#expiry_date_suggestion').val());
        $('#expiry_date_suggestion_wrapper').hide();
    });
</script>