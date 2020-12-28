<li>
	<div class="feed_item_tile">
        <div class="item_image">
            <?php
            if(!empty($feed_item_data['image'])){
                echo '<img src="'. Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'news').'" class="item_image" />';
            }
            ?>
        </div>
		<span class="feed_item_title light"><?php echo $feed_item_data['title'];?></span>
		<span class="feed_item_summary">
				<?php
					echo $feed_item_data['summary'];
				?>
            <a class="read-more strong" href="/news/<?php echo $feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['news_url'];?>">Read More »</a>
		</span>
        <div class="content"><?=$feed_item_data['content']?></div>
	</div>
</li>