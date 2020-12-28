<? $form_identifier = '';?>

    Name: <?=@$form['first_name'];?><br/>
    Address: <?=@$form['address'];?><br/>
    Phone: <?=@$form['telephone_no']?><br/>
    Mobile: <?=@$form['mobile_no'];?><br/>
    Email: <?=@$form['email'];?><br/>

<table>
    <thead>
    <tr>
        <td>Date</td><td>Occasion</td><td>Contact Method</td>
    </tr>
    </thead>
    <tbody>
    <tr><td><?=@$form['event_1'];?></td><td><?=@$form['occasion_1'];?></td><td><?=@$form['method_1'];?></td></tr>
    <tr><td><?=@$form['event_2'];?></td><td><?=@$form['occasion_2'];?></td><td><?=@$form['method_2'];?></td></tr>
    <tr><td><?=@$form['event_3'];?></td><td><?=@$form['occasion_3'];?></td><td><?=@$form['method_3'];?></td></tr>
    </tbody>
</table>