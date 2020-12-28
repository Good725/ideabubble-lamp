<div class="user-notifications-wrapper">
	<div class="user-notifications-list-wrapper">
		<ul class="user-notifications-list" id="user-notifications-list">
		
			<?php foreach ($notifications as $key => $notification): ?>
				<?php
				switch ($notification['driver'])
				{
					case 'sms'   : $item = 'SMS';     $icon = 'mobile';     break;
					case 'email' : $item = 'Email';   $icon = 'envelope-o'; break;
					default      : $item = 'Message'; $icon = 'bell-o';  break;
				}
				?>
				<li data-message-id="<?= $notification['message_id'] ?>" data-message-final-target-id="<?= $notification['id'] ?>">
				<span class="user-notifications-icon">
					<span class="icon-<?= $icon ?>"></span>
				</span>

					<span class="user-notifications-message">
						<?= ($notification['delivery_status'] != 'READ') ? '<b>' : '' ?>
						<?= $item ?> from <?= trim($notification['sender']) ?>
						<?= ($notification['delivery_status'] != 'READ') ? '</b>' : '' ?>
					</span>
					<?php if ($notification['sent_started']): ?>
						<time class="user-notification-time" datetime="<?= $notification['sent_started'] ?>" title="<?= $notification['sent_started'] ?>"><?= IbHelpers::relative_time_with_tooltip($notification['sent_started']) ?></time>
					<?php endif; ?>
					<a class="user-notifications-read" href="#<? // /admin/messaging/system_read?id=<?= $notification['id'] ?>"><span class="icon-chevron-right"></span></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="user-notifications-footer">
		<a href="/contacts3/frontend/messages">More</a>
	</div>
</div>