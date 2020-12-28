<p>Hello, a new order has been placed on the Regeneron Card Portal.</p>

<p>Please see details of the order below and print ready card attached as a pdf document.</p>

<p>
	Order ID: <?= $order['id'] ?><br />
	Date Created: <?= date('d M Y', strtotime($order['date_created'])) ?><br />
	User Approved: <?= $user['email'] ?>
</p>

<p>Warm regards,<br />The Regeneron card portal bot.</p>

<p>This email was sent from the following web address:<br /><?= URL::base() ?></p>