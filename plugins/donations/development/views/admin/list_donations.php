<?=(isset($alert)) ? $alert : ''?>
<?php
$status_labels = array(
    'Processing' => 'Received',
    'Rejected' => 'Rejected',
    'Completed' => 'Approved',
    'Offline' => 'Offline'
);

$warn_color = Settings::instance()->get('donations_warn_color');
$alarm_color = Settings::instance()->get('donations_alarm_color');
$warn_count = Settings::instance()->get('donations_warn_count');
$warn_paid = Settings::instance()->get('donations_warn_paid');
$alarm_count = Settings::instance()->get('donations_alarm_count');
$alarm_paid = Settings::instance()->get('donations_alarm_paid');

$date_format = Settings::instance()->get('date_format') . ' H:i:s';
?>
<style>
    .list-donations-table thead { white-space: nowrap; }
    .list-donations-table tbody tr:hover td { background-color: #f9f9f9; }
    .list-donations-table [class*="icon-"] {font-size: 1.5em;}
</style>
<table class="table dataTable table-striped table-condensed list-donations-table" id="list-donations-table">
	<thead>
		<tr>
			<th scope="col"><?= __('#ID') ?></th>
            <th scope="col"><?= __('Date') ?></th>
            <th scope="col"><?= __('Text') ?></th>
            <th scope="col"><?= __('Mobile') ?></th>
            <th scope="col"><?= __('Number of Requests') ?></th>
            <th scope="col"><?= __('Value of Approved') ?></th>
			<th scope="col"><?= __('Product') ?></th>
            <th scope="col"><?= __('Request') ?></th>
            <th scope="col"><?= __('Status') ?></th>
            <th scope="col"><?= __('Note') ?></th>
			<th scope="col"><?= __('Actions') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($donations as $donation): ?>
			<tr data-id="<?= $donation['id'] ?>">
				<td><?= $donation['id'] ?></td>
                <td><?= date($date_format, strtotime($donation['created'])) ?></td>
                <td><?= $donation['message'] ?></td>
                <td><a class="mobile" data-number="<?= $donation['mobile'] ?>" data-toggle="modal" data-target="#history-donation-modal"><?= $donation['mobile'] ?></a></td>

                <?php // Number of requests ?>
                <?php if ($donation['qty'] >= $alarm_count): ?>
                    <td style="background-color: <?= $alarm_color ?>;color: #fff; font-weight: bold;">
                        <?= $donation['qty'] ?><span class="icon-check-circle right"></span>
                    </td>
                <?php elseif ($donation['qty'] >= $warn_count): ?>
                    <td style="background-color: <?= $warn_color ?>;color: #fff; font-weight: bold;">
                        <?= $donation['qty'] ?><span class="icon-minus-circle right"></span>
                    </td>
                <?php else: ?>
                    <td><?= $donation['qty'] ?><span class="icon-ban right"></span></td>
                <?php endif; ?>

                <?php // Value of approved ?>
                <?php if ($donation['total_paid'] >= $alarm_paid): ?>
                    <td style="background-color: <?= $alarm_color ?>;color: #fff; font-weight: bold;">
                        <?= $donation['total_paid'] ?><span class="icon-check-circle right"></span>
                    </td>
                <?php elseif ($donation['total_paid'] >= $warn_paid): ?>
                    <td style="background-color: <?= $warn_color ?>;color: #fff; font-weight: bold;">
                        <?= $donation['total_paid'] ?><span class="icon-minus-circle right"></span>
                    </td>
                <?php else: ?>
                    <td>
                        <?= $donation['total_paid'] ?><span class="icon-ban right"></span>
                    </td>
                <?php endif; ?>

				<td><?= $donation['product'] ?></td>
                <td><?= $donation['cost'] ?></td>
                <td><?= $status_labels[$donation['status']] ?></td>
                <td><?= html::clean($donation['note']) ?></td>
				<td class="form-actions">
                    <?php if ($adhoc) { ?>
                        <button type="button" class="list-send-button btn btn-default"
                                data-toggle="modal"
                                data-target="#status-donation-modal"
                                data-status=""
                                data-reply="Yes"
                                data-mobile="<?=$donation['mobile']?>"
                                data-id="<?= $donation['id'] ?>"
                                data-contact_id="<?=$donation['contact_id']?>"><?= __('Send Message') ?></button>
                    <?php } else if ($donation['status'] == 'Processing'){ ?>
                        <button type="button" class="list-complete-button-a btn btn-success"
                                data-status="Completed"
                                data-cost="<?= $donation['cost'] ?>"
                                data-id="<?= $donation['id'] ?>"
                                data-contact_id="<?=$donation['contact_id']?>"><?= __('Approve') ?></button>
                        <button type="button" class="list-complete-button btn btn-primary"
                                data-toggle="modal"
                                data-target="#status-donation-modal"
                                data-status="Completed"
                                data-cost="<?= $donation['cost'] ?>"
                                data-id="<?= $donation['id'] ?>"
                                data-contact_id="<?=$donation['contact_id']?>"><?= __('Approve +') ?></button>
                        <button type="button" class="list-reject-button btn btn-danger"
                                data-toggle="modal"
                                data-target="#status-donation-modal"
                                data-status="Rejected"
                                data-id="<?= $donation['id'] ?>"
                                data-contact_id="<?=$donation['contact_id']?>"><?= __('Reject') ?></button>
                    <?php } else if ($donation['status'] == 'Rejected'){?>
                        <button type="button" class="list-reject-button btn btn-default"
                                data-toggle="modal"
                                data-target="#status-donation-modal"
                                data-status="Rejected"
                                data-reply="Yes"
                                data-id="<?= $donation['id'] ?>"
                                data-contact_id="<?=$donation['contact_id']?>"><?= __('Reply') ?></button>
                    <?php } ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<div class="modal modal-primary fade" tabindex="-1" role="dialog" id="status-donation-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/donations/status_set" method="post" id="status-donation-form">
                <input type="hidden" name="id" />
                <input type="hidden" name="status" value="" />
                <input type="hidden" name="contact_id" value="" />
                <input type="hidden" name="mute" value="0" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Update Request') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group mobile">
                        <label class="form-label" for="mobile">Mobile</label>
                        <input class="form-control col-sm-2" type="text" name="mobile" value="" />
                    </div>
                    <div class="form-group paid">
                        <label class="form-label" for="paid">Paid Amount</label>
                        <input class="form-control col-sm-2" type="text" name="paid" value="" readonly/>
                    </div>
                    <div class="form-group note">
                        <label class="form-label"><?=__('Note')?></label>
                        <textarea class="form-control" name="note"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?=__('Reply Message')?></label>
                        <textarea class="form-control" name="reply"></textarea>
                        <span>(enter some text to send as an sms)</span>
                    </div>
                </div>
                <div class="modal-footer form-actions">
                    <button type="submit" class="btn btn-danger" id="update-donation-button"><?= __('Update') ?></button>
                    <button type="submit" class="btn btn-danger hidden" id="mute-donation-button"><?= __('Mute') ?></button>
                    <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal-primary" tabindex="-1" role="dialog" id="history-donation-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= sprintf(__('History of %s'), '<span class="number"></span>') ?></h4>
            </div>
            <div class="modal-body">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th scope="col"><?= __('Message') ?></th>
                            <th scope="col"><?= __('Date') ?></th>
                            <th scope="col"><?= __('Status') ?></th>
                            <th scope="col"><?= __('Note') ?></th>
                            <th scope="col"><?= __('Product') ?></th>
                            <th scope="col"><?= __('Request') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th scope="col" colspan="5" style="text-align: right"><?=__('Total')?></th>
                            <th scope="col" class="cost"></th>
                        </tr>
                        <tr>
                            <th scope="col" colspan="5" style="text-align: right"><?=__('Paid')?></th>
                            <th scope="col" class="paid"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer form-actions">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close') ?></button>
            </div>
        </div>
    </div>
</div>

<?php
$mm = new Model_Messaging();
?>
<script>
var confirm_template = <?=json_encode($mm->get_notification_template('donation-sms-status-approve'));?>;
var complete_template = <?=json_encode($mm->get_notification_template('donation-sms-status-approve'));?>;
var reject_template = <?=json_encode($mm->get_notification_template('donation-sms-status-reject'));?>;
var invalid_template = <?=json_encode($mm->get_notification_template('donation-sms-received-invalid-reply'));?>;
</script>
