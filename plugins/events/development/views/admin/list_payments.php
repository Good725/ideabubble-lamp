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

<style>
	.list-payments-table {
        table-layout: fixed;
        word-break: break-all;
        word-break: break-word;
	}
</style>
<table class="table table-striped dataTable table-condensed list-payments-table" id="list-payments-table">
    <thead>
        <tr>
            <th scope="col"><?= __('ID') ?></th>
			<th scope="col"><?= __('Statement Descriptor') ?></th>
            <th scope="col"><?= __('Customer') ?></th>
			<th scope="col"><?= __('Type') ?></th>
			<th scope="col"><?= __('Amount') ?></th>
			<th scope="col"><?= __('Status') ?></th>
			<th scope="col"><?= __('Note') ?></th>
			<th scope="col"><?= __('Gateway Details') ?></th>
        </tr>
    </thead>
    <tbody>
		<?php foreach ($payments as $payment): ?>
			<tr>
				<td><?= $payment['id'] ?></td>
				<td><?= $payment['statement_descriptor'] ?></td>
				<td><a class="edit-link" href="/admin/events/order_details/<?= $payment['id'] ?>"><?= $payment['buyer'] ?></a></td>
				<td><?= $payment['paymentgw'] ?></td>
				<td><?= $currencies[$payment['currency']]['symbol'] . $payment['amount'] ?></td>
				<td><?= $payment['status'] ?></td>
				<td><?= $payment['status_reason'] ?></td>
				<td><?= $payment['paymentgw_info'] ?></td>
			</tr>
		<?php endforeach; ?>
    </tbody>
</table>
<script>
	$('#list-payments-table').on('click', 'tbody tr', function(ev)
	{
		if ( ! $(ev.target).is('a, label, button, :input') && ! $(ev.target).parents('a, label, button, :input')[0])
		{
			 <?php // Find the edit link ?>
			 var link = $(this).find('.edit-link').attr('href');

			 <?php // If the user uses the middle mouse button or Ctrl/Cmd key, open the link in a new tab. ?>
			 <?php // Otherwise open it in the same tab ?>
			 if (ev.ctrlKey || ev.metaKey || ev.which == 2) {
			 	window.open(link, '_blank');
			 }
			 else {
			 	window.location.href = link;
			 }
		}
	});
</script>
