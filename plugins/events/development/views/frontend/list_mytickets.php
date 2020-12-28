<?=(isset($alert)) ? $alert : ''?>
<table class="table table-striped dataTable table-condensed " id="list-tickets-table">
    <thead>
        <tr>
            <th scope="col">Event</th>
            <th scope="col">Starts</th>
            <th scope="col">Ticket</th>
            <th scope="col">QRCode</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($tickets as $ticket) {
    ?>
        <tr>
            <td><a href="/ticket/<?=$ticket['order_id'] . '-' . $ticket['order_item_has_date_id'] . '-' . $ticket['code']?>"><?=$ticket['event']?></a></td>
            <td><a href="/ticket/<?=$ticket['order_id'] . '-' . $ticket['order_item_has_date_id'] . '-' . $ticket['code']?>"><?=$ticket['starts']?></a></td>
            <td><a href="/ticket/<?=$ticket['order_id'] . '-' . $ticket['order_item_has_date_id'] . '-' . $ticket['code']?>"><?=$ticket['ticket'] . '(' . $ticket['type'] . ')'?></a></td>
            <td><img src="/qrcode?url=<?=urlencode(URL::site('/ticket/' . $ticket['order_id'] . '-' . $ticket['order_item_has_date_id'] . '-' . $ticket['code']))?>&size=3" alt="QRCode"/> </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>