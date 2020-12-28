<div class="galleries-list-wrapper">
	<?php foreach ($galleries as $gallery_name => $gallery): ?>
		<?php // Show the first image in the gallery, if there is one. ?>
		<?php if (count($gallery) > 0): ?>
			<a href="<?= Request::detect_uri() ?>/<?= $gallery_name ?>" class="galleries-list-item">
				<figure>
					<img src="<?= $filepath ?>/<?= $gallery[0]['photo_name'] ?>" />
					<figcaption><?= $gallery_name ?></figcaption>
				</figure>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>
</div>