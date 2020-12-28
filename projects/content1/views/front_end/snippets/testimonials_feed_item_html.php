<?php if (Kohana::$config->load('config')->get('template_folder_path') != 'wide_banner'): ?>
	<li>
		<div id="testimonials">
			<?php if( ! empty($feed_item_data['image'])): ?>
				<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'testimonials') ?>" class="item_image_testimonial_feed" />
			<?php endif; ?>
			<span class="light"><?=$feed_item_data['item_signature']?></span>
			<span class="dark"><?=$feed_item_data['item_company']?></span>
			<p><?= $feed_item_data['summary'] ?>...</p>
			<?php if (Settings::instance()->get('testimonials_read_more') != 'FALSE'): ?>
				<a class="read-more" href="/testimonials.html/<?php echo $feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['id'];?>">Read More »</a>
			<?php endif; ?>
			<?php if ( ! empty($feed_item_data['item_website'])): ?>
				<span class="lunch">Launch Website: <a href="<?= $feed_item_data['item_website'] ?>" target="_blank"><?= $feed_item_data['item_website'] ?></a></span>
			<?php endif;  ?>
		</div>
	</li>
<?php else: ?>
	<li class="testimonials_feed">
		<div class="testimonial_shortquote">
			<?php if ( ! empty($feed_item_data['image'])): ?>
				<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'testimonials') ?>" class="item_image_testimonial_feed" />
			<?php endif; ?>
			<p>
				<?= nl2br($feed_item_data['summary']) ?>...
				<a class="read-more" href="/testimonials.html/<?= $feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['id'];?>">Read More</a>
			</p>
		</div>
		<div class="testimonial_signature"><?=$feed_item_data['item_signature']?></div>
		<?php if ($feed_item_data['item_company'] != ''): ?>
			<div class="testimonial_company">
				<?php if ($feed_item_data['item_website'] != ''): ?>
					<a href="<?= $feed_item_data['item_website'] ?>"><?= $feed_item_data['item_company'] ?></a>
				<?php else: ?>
					<?= $feed_item_data['item_company'] ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</li>
<?php endif; ?>