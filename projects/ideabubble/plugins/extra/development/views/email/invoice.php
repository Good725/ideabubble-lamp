<b>Invoice ID:</b><?=$form['id'];?><br/>
<b>Due Date:</b><?=$form['dueDate'];?><br/>
<b>Outstanding Amount:</b><?=$form['outstandingAmount'];?><br/>
<b>Client Name:</b><?=$form['clientName'];?><br/>
<table>
    <thead>
        <th>Quantity</th>
        <th>Description</th>
        <th>Item</th>
        <th>Price (ex VAT)</th>
        <th>VAT</th>
        <th>Total</th>
    </thead>
    <tbody>
    <?php
	if($form['invoiceLines'])
    foreach($form['invoiceLines'] AS $key=>$item):
    ?>
    <tr>
        <td><?=$item['quantity'];?></td>
        <td><?=$item['description'];?></td>
        <td><?=$item['item'];?></td>
        <td><?=$item['rate'];?></td>
        <td><?=$item['vatRate'];?></td>
        <td><?=$item['vatRate'] + $item['rate'];?></td>
    </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>