<div class="summary_item_tile">
	<div class="item_image">
		<?php if( ! empty($item_data['image'])): ?>
			<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'testimonials') ?>" class="item_image" />
		<?php endif;  ?>
	</div>
	<div class="summary_item_summary">
		<p>
			<?= substr($item_data['summary'], 0, 200).'... '; ?>
            <?php if (Settings::instance()->get('testimonials_read_more') != 'FALSE'): ?>
                <a class="button button-primary read-more" href="/testimonials.html/<?= $item_data['category'].DIRECTORY_SEPARATOR.$item_data['id'];?>">Read More</a>
            <?php endif; ?>
		</p>
	</div>
	<div class="item_content signature"><?=$item_data['item_signature']?></div>
	<div class="item_content signature"><?=$item_data['item_company']?></div>
</div>