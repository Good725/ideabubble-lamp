<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<form id="period-edit" name="booking-edit" class="col-sm-12 form-horizontal" method="post" action="/admin/propman/booking/<?=$booking['id']?>">
    <input type="hidden" name="id" value="<?=$booking['id']?>" />

    <fieldset>
        <legend>Status</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Status') ?></label>
            <div class="col-sm-1">
                <select name="status" class="form-control">
                    <?=html::optionsFromArray(
                        array(
                            'New' => 'New',
                            'Checked In' => 'Checked In',
                            'Checked Out' => 'Checked Out',
                            'Cancelled' => 'Cancelled',
                            'Not Arrived' => 'Not Arrived'
                        ),
                        $booking['status']
                    )?>
                </select>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Details</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Property') ?></label>
            <div class="col-sm-4">
                <a href="/admin/propman/edit_property/<?=$booking['property_id']?>"><?=$booking['property']?></a> </span>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Price') ?></label>
            <div class="input-group col-sm-2">
                <span class="input-group-addon">&euro;</span>
                <span class="form-control"><?=$booking['price'] + $booking['discount']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Discount') ?></label>
            <div class="input-group col-sm-2">
                <span class="input-group-addon">&euro;</span>
                <span class="form-control"><?=$booking['discount']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Total') ?></label>
            <div class="input-group col-sm-2">
                <span class="input-group-addon">&euro;</span>
                <span class="form-control"><?=$booking['price']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Check In') ?></label>
            <div class="col-sm-1">
                <span class="form-control"><?=$booking['checkin']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Check Out') ?></label>
            <div class="col-sm-1">
                <span class="form-control"><?=$booking['checkout']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Guests') ?></label>
            <div class="col-sm-1">
                <span class="form-control"><?=$booking['guests']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Adults') ?></label>
            <div class="col-sm-1">
                <span class="form-control"><?=$booking['adults']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Children') ?></label>
            <div class="col-sm-1">
                <span class="form-control"><?=$booking['children']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Infants') ?></label>
            <div class="col-sm-1">
                <span class="form-control"><?=$booking['infants']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Billing Name') ?></label>
            <div class="col-sm-4">
                <span class="form-control"><?=$booking['billing_name']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Billing Address') ?></label>
            <div class="col-sm-4">
                <span class="form-control"><?=$booking['billing_address']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Billing Town') ?></label>
            <div class="col-sm-4">
                <span class="form-control"><?=$booking['billing_town']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Billing County') ?></label>
            <div class="col-sm-4">
                <span class="form-control"><?=$booking['billing_county']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Billing Country') ?></label>
            <div class="col-sm-4">
                <span class="form-control"><?=$booking['billing_country']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Billing Phone') ?></label>
            <div class="col-sm-4">
                <span class="form-control"><?=$booking['billing_phone']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Billing Email') ?></label>
            <div class="col-sm-4">
                <span class="form-control"><?=$booking['billing_email']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Customer Comments') ?></label>
            <div class="col-sm-4">
                <pre><?=html::chars($booking['comments'])?></pre>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Created') ?></label>
            <div class="col-sm-2">
                <span class="form-control"><?=$booking['created']?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Updated') ?></label>
            <div class="col-sm-2">
                <span class="form-control"><?=$booking['updated']?></span>
            </div>
        </div>

    </fieldset>

    <fieldset>
        <legend>Customer</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Contact') ?></label>
            <div class="col-sm-4">
                <a href="/admin/contacts2/edit/<?=$booking['customer_id']?>"><?=$booking['contact']['first_name'] . ' ' . $booking['contact']['last_name']?></a>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Email') ?></label>
            <div class="col-sm-4">
                <a href="/admin/contacts2/edit/<?=$booking['customer_id']?>"><?=$booking['contact']['email']?></a>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Phone') ?></label>
            <div class="col-sm-4">
                <a href="/admin/contacts2/edit/<?=$booking['customer_id']?>"><?=$booking['contact']['phone'] . ' ' . $booking['contact']['mobile']?></a>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Payments</legend>
        <table class="table table-striped col-sm-4">
            <thead>
            <tr><th>Id</th><th>Amount</th><th>Gateway</th><th>Gateway TX</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php
            foreach($booking['payments'] as $payment) {
                ?>
                <tr>
                    <td><a href="/admin/propman/payment/<?=$payment['id']?>"><?=$payment['id']?></a> </td>
                    <td><?=$payment['amount']?></td>
                    <td><?=$payment['gateway']?></td>
                    <td><?=$payment['gateway_tx']?></td>
                    <td><?=$payment['status']?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </fieldset>

    <div class="col-sm-12">
        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action"
                    value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <a href="/admin/propman/payments" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>
</form>
