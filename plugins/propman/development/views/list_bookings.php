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
            <th scope="col"><?= __('Status') ?></th>
            <th scope="col"><?= __('Customer') ?></th>
            <th scope="col"><?= __('Email') ?></th>
            <th scope="col"><?= __('Property') ?></th>
            <th scope="col"><?= __('Check In') ?></th>
            <th scope="col"><?= __('Check Out') ?></th>
            <th scope="col"><?= __('Paid') ?></th>
            <th scope="col"><?= __('Outstanding') ?></th>
            <th scope="col"><?= __('Created') ?></th>
            <th scope="col"><?= __('Updated') ?></th>
            <th scope="col"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($bookings['records'] as $booking) {
        ?>
        <tr>
            <td><a href="/admin/propman/booking/<?=$booking['id']?>"><?=$booking['id']?></a></td>
            <td><?=$booking['status']?></td>
            <td><a href="/admin/contacts2/edit/<?=$booking['customer_id']?>"><?=$booking['contact']?></a></td>
            <td><?=$booking['email']?></td>
            <td><?=$booking['property']?></td>
            <td><?=$booking['checkin']?></td>
            <td><?=$booking['checkout']?></td>
            <td></td>
            <td></td>
            <td><?=$booking['created']?></td>
            <td><?=$booking['updated']?></td>
            <td>
                <a class="edit-link" href="/admin/propman/booking/<?=$booking['id']?>" title="<?= __('View') ?>">
                    <span class="icon-pencil"></span> <?= __('View') ?>
                </a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
