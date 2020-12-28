<?=(isset($alert)) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<?php
$currencies = Model_Currency::getCurrencies(true);
?>
<table class="table table-striped dataTable table-condensed dataTable-collapse" id="list-tickets-table">
    <thead>
        <tr>
            <th scope="col"><?= __('ID') ?></th>
            <th scope="col"><?= __('Date') ?></th>
            <th scope="col"><?= __('Type') ?></th>
            <th scope="col"><?= __('Event') ?></th>
            <th scope="col"><?= __('Customer') ?></th>
            <th scope="col"><?= __('Attended') ?></th>
            <th scope="col"><?= __('Time Attended') ?></th>
            <th scope="col"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($tickets as $ticket) {
    ?>
        <tr>
            <td data-label="<?= __('ID') ?>"><?= $ticket['id'] ?></td>
            <td data-label="<?= __('Date') ?>"><?= $ticket['created'] ?></td>
            <td data-label="<?= __('Type') ?>"><?= $ticket['ticket'].' ('.$ticket['type'].')' ?></td>
            <td data-label="<?= __('Event') ?>"><?= $ticket['event'] ?></td>
            <td data-label="<?= __('Customer') ?>"><?= $ticket['buyer'] ?></td>
            <td data-label="<?= __('Attended') ?>"></td>
            <td data-label="<?= __('Time Attended') ?>"></td>
            <td data-label="<?= __('Actions') ?>">
                <a href="/admin/events/ticket?ticket_id=<?= $ticket['id'] ?>&order_id=<?= $ticket['order_id'] ?>&action=download"><?= __('Download Ticket') ?></a>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
