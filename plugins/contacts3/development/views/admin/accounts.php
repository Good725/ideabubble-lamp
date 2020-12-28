<div>
    <table class="table table-striped table-hover dataTable dataTable-collapse" id="example">
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Transaction</th>
                <th scope="col">Booking</th>
                <th scope="col">Student</th>
                <th scope="col">Schedule</th>
                <th scope="col">Type</th>
                <th scope="col">Price</th>
                <th scope="col">Fees</th>
                <th scope="col">Discount</th>
                <th scope="col">Paid</th>
                <th scope="col">Outstanding</th>
                <th scope="col">Status</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td data-label="Date"><?=$transaction['created']?></td>
                    <td data-label="Transaction"><?=$transaction['id'] . (in_array($transaction['creator_role'], array('Parent/Guardian', 'Student', 'Mature Student')) ? '<br /><span class="online" style="font-size:10px;">Online</span>' : '')?></td>
                    <td data-label="Booking"><?=$transaction['booking_id']?></td>
                    <td data-label="Student"><?=$transaction['first_name'] . ' ' . $transaction['last_name']?></td>
                    <td data-label="Schedule"><?=$transaction['schedule']?></td>
                    <td data-label="Type"><?=$transaction['type']?></td>
                    <td data-label="Price"><?=$transaction['amount']?></td>
                    <td data-label="Fees"><?=$transaction['fee']?></td>
                    <td data-label="Discount"><?=$transaction['discount']?></td>
                    <td data-label="Paid">
                        <?=$transaction['total'] - $transaction['outstanding']?>
                    </td>
                    <td data-label="Outstanding"><?=$transaction['outstanding']?></td>
                    <td data-label="Status">
                        <span class="iconbox">
                            <span class="<?= $transaction['outstanding'] == 0 ? 'text-success icon-check' : 'text-danger icon-exclamation-triangle'?>" aria-hidden="true"></span>
                            <span class="tooltip-box"><?= $transaction['outstanding'] == 0 ? 'Completed' : 'Outstanding' ?></span>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
