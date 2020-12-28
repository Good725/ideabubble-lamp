<div class="user-notifications-wrapper">
	<div class="user-notifications-message-header border-bottom p-2">
		<a href="#" id="user-notifications-return"><span class="icon-chevron-left"></span> Return to Notifications</a>
	</div>
	<div class="user-notifications-message-body p-2">
		<h2><?= htmlentities($message['subject']) ?></h2>
		<p>From: <?= htmlentities($message['sender_d']) ?></p>
		<p>Sent: <time datetime="<?= $message['sent_started'] ?>"><?= date('H:i l j F Y', strtotime($message['sent_started'])) ?></time></p>
		<p>Method: <?= $message['driver']['driver'] ?></p>
		<hr />
		<p><?= $message['message'] ? nl2br($message['message']) : '' ?></p>
		<?php if (!@$nolink) { ?>
		<hr />
		<a href="/admin/messaging/details?message_id=<?= $message['id'] ?>">Read...</a>
		<?php } ?>
	</div>
</div>