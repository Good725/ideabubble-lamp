<li>
	<?php if (trim($feed_item_data['summary'])): ?>
		<p>&quot;<?= $feed_item_data['summary'] ?>&quot;</p>
	<?php endif; ?>
	<?php if (trim($feed_item_data['item_signature'])): ?>
		<p class="testimonial"><?= $feed_item_data['item_signature'] ?></p>
	<?php endif; ?>
</li>
