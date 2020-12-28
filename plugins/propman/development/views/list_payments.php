<div id="dashboard_list_alerts"><?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
</div>

<table class="table table-striped dataTable list-bookings-table" id="list-bookings-table">
    <thead>
        <tr>
            <th scope="col"><?= __('ID') ?></th>
            <th scope="col"><?= __('Amount') ?></th>
            <th scope="col"><?= __('Booking ID') ?></th>
            <th scope="col"><?= __('Customer') ?></th>
            <th scope="col"><?= __('Email') ?></th>
            <th scope="col"><?= __('Property') ?></th>
            <th scope="col"><?= __('Check In') ?></th>
            <th scope="col"><?= __('Check Out') ?></th>
            <th scope="col"><?= __('Gateway') ?></th>
            <th scope="col"><?= __('Status') ?></th>
            <th scope="col"><?= __('Created') ?></th>
            <th scope="col"><?= __('Updated') ?></th>
            <th scope="col"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($payments['records'] as $payment) {
        ?>
        <tr>
            <td><a href="/admin/propman/payment/<?=$payment['id']?>"><?=$payment['id']?></a></td>
            <td><a href="/admin/propman/payment/<?=$payment['id']?>">&euro;<?=$payment['amount']?></a></td>
            <td><a href="/admin/propman/booking/<?=$payment['booking_id']?>"><?=$payment['booking_id']?></a></td>
            <td><a href="/admin/contacts2/edit/<?=$payment['customer_id']?>"><?=$payment['contact']?></a></td>
            <td><?=$payment['email']?></td>
            <td><?=$payment['property']?></td>
            <td><?=$payment['checkin']?></td>
            <td><?=$payment['checkout']?></td>
            <td><?=$payment['gateway']?></td>
            <td><?=$payment['status']?></td>
            <td><?=$payment['created']?></td>
            <td><?=$payment['updated']?></td>
            <td>
                <a class="edit-link" href="/admin/propman/payment/<?=$payment['id']?>" title="<?= __('View') ?>">
                    <span class="icon-pencil"></span> <?= __('View') ?>
                </a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
