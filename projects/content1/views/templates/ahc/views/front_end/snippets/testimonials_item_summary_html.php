<section>
	<div class="panel post">
		<h1><?= $item_data['title'] ?></h1>

		<?= $item_data['content'] ?>

		<?php if ( ! empty($item_data['image'])): ?>
			<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'testimonials') ?>" />
		<?php endif; ?>
	</div>
</section>