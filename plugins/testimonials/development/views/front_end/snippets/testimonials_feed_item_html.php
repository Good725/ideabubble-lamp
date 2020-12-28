<li>
	<div id="testimonials">
		<?php
			if(!empty($feed_item_data['image'])){
				echo '<img src="'. Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'testimonials').'" class="item_image_testimonial_feed" />';
			}
		?>
		<span class="light"><?=$feed_item_data['item_signature']?></span>
		<span class="dark"><?=$feed_item_data['item_company']?></span>
		<p><?= $feed_item_data['summary'] ?>...</p>
        <?php if (Settings::instance()->get('testimonials_read_more') != 'FALSE'): ?>
		    <a class="read-more" href="/testimonials.html/<?php echo $feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['id'];?>"><?=__('Read More')?> »</a>
        <?php endif; ?>
		<?php
			if(!empty($feed_item_data['item_website'])){
				echo '<span class="lunch">Launch Website: <a href="',$feed_item_data['item_website'],'" target="_blank">',$feed_item_data['item_website'],'</a></span>';
			}
		?>
	</div>
</li>