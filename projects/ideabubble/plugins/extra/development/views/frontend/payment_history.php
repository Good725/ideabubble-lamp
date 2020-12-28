<h2>Your Payment History</h2>
<div class="service-form">
    <table class="zebra">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Description</th>
            <th scope="col">Amount</th>
            <th scope="col">Type</th>
            <th scope="col">Date</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= $payment['id'] ?></td>
                <td><?= $payment['url'] ?> - <?= $payment['service_type_friendly_name'] ?></td>
                <td><?= $payment['amount'] ?></td>
                <td><?= $payment['type_id'] ?></td>
                <td><?= date('Y-m-d', strtotime($payment['date'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
