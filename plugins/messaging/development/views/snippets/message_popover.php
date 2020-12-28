<?php // Remove <html></html> and <body></body> tags from the message, if any  ?>
<?= preg_replace('/<\/?html[^>]*\>/i', '', preg_replace('/<\/?body[^>]*\>/i', '', $message['message'])); ?>
<?php if (!empty($message['attachments'])): ?>
    <h3>Attachments</h3>

    <ul style="margin-left: 1em;">
        <?php foreach ($message['attachments'] as $attachment): ?>
            <li><a href="/admin/messaging/download_attachment/<?= $attachment['id'] ?>"><?= $attachment['name'] ?></a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<pre hidden><?php var_dump($message) ?></pre>