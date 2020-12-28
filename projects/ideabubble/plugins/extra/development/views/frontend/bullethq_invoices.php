<h2>Your Invoices</h2>

    <table class="zebra invoices-table">
        <thead>
            <tr>
                <th scope="col">Invoice#</th>
                <th scope="col">Amount</th>
                <th scope="col">Outstanding</th>
                <th scope="col">Due Date</th>
                <th scope="col">Status</th>
                <th scope="col">Items</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($bullethq_invoices as $invoice) {
                $invoice_total = 0;
                if ($invoice['bullethq']) {
                    foreach ($invoice['bullethq']['invoiceLines'] as $invoice_line) {
                        $invoice_total += $invoice_line['rate'];
                    }
                }
            ?>
                <tr data-bullethq-invoice-id="<?= $invoice['id']; ?>">
                    <td><?= str_pad($invoice['id'], 8, '0', STR_PAD_LEFT) ?></td>
                    <td><?= $invoice_total ?></td>
                    <td><?= @$invoice['bullethq']['outstandingAmount'] ?></td>
                    <td><?= $invoice['bullethq']['dueDate'] ?></td>
                    <td><?= $invoice['bullethq']['outstandingAmount'] > 0 ? 'Outstanding'  : 'Paid' ?></td>
                    <td>
                    <?php
                    if ($invoice['bullethq']) {
                        foreach ($invoice['bullethq']['invoiceLines'] as $invoice_line) {
                            echo $invoice_line['description'] . '<br />';
                        }
                    }
                    ?>
                    </td>
                    <td>
                    <?php
                    if ($invoice['bullethq_token']) {
                    ?>
                        <a href="https://accounts-app.bullethq.com/clientViews/invoice.page?token=<?= $invoice['bullethq_token'] ?>"
                           target="_blank">view</a>
                        <a href="https://accounts-app.bullethq.com/clientViews/invoice.pdf?token=<?= $invoice['bullethq_token'] ?>&download=true"
                           target="_blank">download</a>
                    <?php
                    }
                    ?>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
