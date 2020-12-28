<li>
	<div class="testimonials">
		<?php if( ! empty($feed_item_data['image'])): ?>
			<?php
			$filename = 'shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/testimonials/_thumbs/'.$feed_item_data['image'];
			$filename = '/'.file_exists($filename) ? $filename : str_replace('/_thumbs/', '/', $filename);
			?>
			<img src="<?= $filename ?>" class="item_image_testimonial_feed" />
		<?php endif; ?>
		<p><?=$feed_item_data['item_signature']?></p>
		<p><?=$feed_item_data['item_company']?></p>
		<p><?= $feed_item_data['summary'] ?>...</p>
        <?php if (Settings::instance()->get('testimonials_read_more') != 'FALSE'): ?>
		    <a class="read-more" href="/testimonials.html/<?= $feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['id'];?>"><?=__('Read More')?></a>
        <?php endif; ?>
		<?php if ( ! empty($feed_item_data['item_website'])): ?>
			<p class="launch_website">Launch Website: <a href="<?= $feed_item_data['item_website'] ?>" target="_blank"><?= $feed_item_data['item_website'] ?></a></p>
		<?php endif; ?>
	</div>
</li>