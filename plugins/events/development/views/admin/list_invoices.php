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
<table class="table table-striped dataTable table-condensed " id="list-invoices-table">
    <thead>
        <tr>
            <th scope="col"><?= __('ID#') ?></th>
            <th scope="col">Event</th>

            <?php
            if (Auth::instance()->has_access('events_orders_view')) {
            ?>
            <th scope="col">Amount</th>
            <?php
            }
            ?>
            <th scope="col">Net Amount</th>
            <th scope="col">Due Date</th>
            <th scope="col">Completed</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($invoices as $invoice) {
    ?>
        <tr>
            <td><?=$invoice['id'] ? ($invoiceUpdate ? '<a href="/admin/events/invoice_update/' . $invoice['id'] . '">' . $invoice['id'] . '</a>' : $invoice['id']) : ''?></td>
            <td><?=$invoice['event']?></td>
            <?php
            if (Auth::instance()->has_access('events_orders_view')) {
            ?>
            <td>
                <?=$currencies[$invoice['currency']]['symbol'] ?>
                <?= $invoice['amount'] ?>
            </td>
            <?php
            }
            ?>
            <td>
                <?=$currencies[$invoice['currency']]['symbol'] ?>
                <?=$invoice['net_amount']?>
            </td>
            <td><?=$invoice['due']?></td>
            <td><?=$invoice['completed']?></td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                        <?= __('Actions') ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php if ($invoice['file_id']) { ?>
                            <li>
                                <a class="btn-link list-download-button" href="/admin/events/invoice_download?invoice_id=<?=$invoice['id']?>">
                                    <span class="icon-print"></span> <?= __('Download') ?>
                                </a>
                            </li>
                            <li>
                                <a class="btn-link list-email-button" href="/admin/events/invoice_email" data-id="<?=$invoice['id']?>">
                                    <span class="icon-email"></span> <?= __('Email') ?>
                                </a>
                            </li>
                        <?php } else { ?>

                        <?php } ?>
                    </ul>
                </div>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<script>
    $("#list-invoices-table").on("click", ".list-email-button", function(){
        var url = this.href;
        var el = this;
        $.post(
            url,
            {
                invoice_id: $(el).attr('data-id')
            },
            function (response) {
                if (response.success) {
                    alert('Invoice has been emailed.');
                } else {
                    alert("Unable to send email");
                }
            }
        );
        return false;
    });
</script>
