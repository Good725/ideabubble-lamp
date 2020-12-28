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
<form id="period-edit" name="payment-edit" class="col-sm-12 form-horizontal" method="post" action="/admin/propman/payment/<?=@$payment['id']?>">
    <input type="hidden" name="id" value="<?=@$payment['id']?>" />

    <fieldset>
        <legend>Details</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Gateway') ?></label>
            <div class="col-sm-1">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <span class="form-control"><?=$payment['gateway']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Gateway TX Id') ?></label>
            <div class="col-sm-1">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <span class="form-control"><?=$payment['gateway_tx']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Amount') ?></label>
            <div class="col-sm-1">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <span class="form-control"><?=$payment['amount']?></span>
                </div>
            </div>
        </div>

        <?php
        if ($payment['booking_id'] == null) {
            ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="edit-payment-booking"><?= __('Booking Id') ?></label>
                <div class="col-sm-5">
                    <select class="form-control" id="edit-payment-booking" name="booking_id">
                        <option value="">Link To Booking</option>
                        <?php
                        foreach ($outstandingBookings as $outstandingBooking) {
                            ?>
                            <option value="<?=$outstandingBooking['id']?>"><?=
                                $outstandingBooking['id'] . ' ' .
                                $outstandingBooking['contact'] . ' ' .
                                $outstandingBooking['property'] . ' ' .
                                $outstandingBooking['checkin'] . ' ' .
                                'Total:&euro;' . $outstandingBooking['price'] . ' ' .
                                'Outstanding:&euro;' . ($outstandingBooking['price'] - $outstandingBooking['paid'])
                                ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="edit-payment-booking"><?= __('Booking') ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <span class="input-group-addon">#<?=$payment['booking_id']?></span>
                        <span class="form-control"><?=$payment['contact'] . ' ' . $payment['property'] . ' ' . $payment['checkin'] . ' - ' . $payment['checkout']?></span>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

    </fieldset>

    <?php
    if ($payment['custom']) {
        $custom = unserialize($payment['custom']);
    ?>
    <fieldset>
        <legend>Balance Payment</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Email') ?></label>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="form-control"><?=$custom['email']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('First Name') ?></label>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="form-control"><?=$custom['firstname']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Last Name') ?></label>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="form-control"><?=$custom['lastname']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Property') ?></label>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="form-control"><?=$custom['property']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Check In') ?></label>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="form-control"><?=$custom['checkin']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Check Out') ?></label>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="form-control"><?=$custom['checkout']?></span>
                </div>
            </div>
        </div>
    </fieldset>
    <?php
    }
    ?>

    <fieldset>
        <legend>Date</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Status') ?></label>
            <div class="col-sm-1">
                <div class="input-group">
                    <span class="form-control"><?=$payment['status']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Created') ?></label>
            <div class="col-sm-1">
                <div class="input-group">
                    <span class="form-control"><?=$payment['created']?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= __('Updated') ?></label>
            <div class="col-sm-1">
                <div class="input-group">
                    <span class="form-control"><?=$payment['updated']?></span>
                </div>
            </div>
        </div>
    </fieldset>

    <?php
    if ($payment['booking_id'] == null) {
    ?>
    <div class="col-sm-12">
        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action"
                    value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <a href="/admin/propman/payments" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>
    <?php
    }
    ?>
</form>
