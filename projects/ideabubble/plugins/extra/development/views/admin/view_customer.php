<div class="col-sm-12 page-header">
	<?=(isset($alert)) ? $alert : '';?>
	<h2 class="plugin_title"><img class="plugin_icon" src="" />Extrabubble - Add/Edit Customer</h2>
	<div class="pull-left"><a href='<?=URL::site()?>admin/extra/view_customer'>Add Customer</a></div>
</div>
<div class="col-sm-12 tab-content">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#details_tab" data-toggle="tab">Editor</a></li>
    </ul>
    <div class="tab-pane active" id="details_tab">
        <form action="<?=URL::site();?>admin/extra/save_customer" class="form-horizontal" id="customer_edit" method="POST">
            <input type="hidden" id="id" name="id" value="<?=(isset($customer['id'])) ? $customer['id']: 'new';?>"/>
            <input type="hidden" name="user_id" value="<?= isset($customer['user_id']) ? $customer['user_id'] : ''; ?>" />
            <div class="alert alert-error" id="email_error" style="display:none;">
                This email address is already in use.
            </div>
            <div class="col-sm-6">
                <h3>Company Details</h3>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="company_title">Company</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="company_title" name="company_title" value="<?=isset($customer['company_title']) ? $customer['company_title'] :'';?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="address1">Address 1</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="address1" name="address1" value="<?=(isset($customer['address1'])) ? $customer['address1']: '';?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="address2">Address 2</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="address2" name="address2" value="<?=(isset($customer['address2'])) ? $customer['address2']: '';?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="address3">Address 3</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="address3" name="address3" value="<?=(isset($customer['address3'])) ? $customer['address3']: '';?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="county">County</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="county" id="county">
                            <?=$counties;?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="phone">Phone No.</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="phone" name="phone" value="<?=isset($customer['phone']) ? $customer['phone'] :'';?>">
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <h3>Main Contact Details</h3>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="contact">Select a contact:</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="contact" id="contact">
                            <?=$contacts;?>
                        </select>
                    </div>
                </div>
                <div class="contact-details">
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="contact_first_name">First Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="contact_first_name" name="contact_first_name" value="" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="contact_last_name">Last Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="contact_last_name" name="contact_last_name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="contact_phone">Phone</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="contact_email">Email</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="contact_email" name="contact_email" value="" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="contact_password">Change Password</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="contact_password" name="contact_password" value="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <h3>Billing Contact Details</h3>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="billing_contact">Select a contact:</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="billing_contact" id="billing_contact">
                            <?=$billing_contacts;?>
                        </select>
                    </div>
                </div>
                <div class="billing-contact-details">
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="billing_first_name">First Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="billing_first_name" name="billing_first_name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="billing_last_name">Last Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="billing_last_name" name="billing_last_name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="billing_phone">Phone</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="billing_phone" name="billing_phone" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="billing_email">Email</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="billing_email" name="billing_email" value="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <h3>Notes</h3>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="notes"></label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="notes" name="notes" rows="4"><?=isset($customer['notes']) ? $customer['notes'] :'';?></textarea>
                    </div>
                </div>
            </div>
			
			<?php if(count($cards)){ ?>
			<br clear="all" />
			
			<div class="col-sm-6">
                <h3>Payment Cards</h3>

                <div class="form-group">
                    <div class="col-sm-8">
                        <table class="zebra cards-table table">
							<thead>
								<tr>
									<th scope="col">Card Number#</th>
									<th scope="col">Expires</th>
									<th scope="col">Delete</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach($cards as $card){
								?>
									<tr data-card-id="<?= $card['id']; ?>">
										<td><?=$card['card_number']?></td>
										<td><?=date('m/Y', strtotime($card['expdate']))?></td>
										<td><input type="checkbox" name="card_delete[]" value="<?=$card['id']?>" /></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
                    </div>
                </div>
            </div>
			<?php } ?>

			<div class="col-sm-12 well" id="save-area">
				<input type="submit" class="btn btn-primary" value="Save"/>
			</div>
        </form>

        <?php if (isset($customer['id'])): ?>
            <table class="table table-striped dataTable" id="categories_table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Service</th>
                        <th scope="col">Domain</th>
                        <th scope="col">Expiry</th>
                        <th scope="col">Type</th>
                        <th scope="col">Price</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($services as $service): ?>
                        <?php
                            if (strtotime($service['date_end']) > 0) {
                                $expiry_date = date('d/m/Y', strtotime($service['date_end']));
                            }
                        ?>

                        <tr id="service_<?= $service['id'] ?>" onclick="location.href='<?php echo URL::Site('admin/extra/edit_service/' . $service['id']); ?>'">
                            <td><?= $service['id'] ?></td>
                            <td><?= $service['service_type'] ?></td>
                            <td><?= $service['url'] ?></td>
                            <td><?= @$expiry_date ?></td>
                            <td><?= $service['domain_type'] ?></td>
                            <td><?= $service['price'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>