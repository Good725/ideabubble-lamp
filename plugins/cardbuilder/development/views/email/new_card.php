<p>Hello, a new card has been add to the order listing.</p>

<p>
	Card details:<br />
	Id: <?= $card['id']  ?><br />
	Name on card: <?= $card['employee_name'] ?><br />
	Created by: <?= $user['email']  ?><br />
	Created on: <?= date('d M Y', strtotime($card['date_created']))  ?><br />
	Department <?= $card['department']  ?><br /><br />
	<a href="<?= URL::base() ?>card-builder.html/?id=<?= $card['id'] ?>" title="<?= URL::base() ?>card-builder.html/?id=<?= $card['id'] ?>">View card</a>
</p>

<p>Go to <a href="<?= URL::base() ?>card-builder-orders.html" title="<?= URL::base() ?>card-builder-orders.html">the order listing</a> to approve the order for printing.</p>

<p>Warm regards,<br />The Regeneron card portal bot.</p>

<p>This email was sent from the following web address:<br /><?= URL::base() ?></p>