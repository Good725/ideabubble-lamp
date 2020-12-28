<div class="summary_item_tile">
	<span class="summary_item_title light"><h2><?php echo $item_data['title'];?></h2></span>
	<span class="summary_item_event_date dark"><?php echo (!empty($item_data['event_date']))? date('F dS, Y', strtotime($item_data['event_date'])) : ''?></span>
	<span class="summary_item_summary">
		<p>
			<?php
				//Just take the first 200 characters
				echo substr(strip_tags($item_data['summary']), 0, 200).'...<br/>';
            if(!empty($item_data['image'])){
                echo '<img src="'. Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news/_thumbs').'"
                alt="'  .((isset($item_data['alt_text']))   ? $item_data['alt_text']   : '').'"
                title="'.((isset($item_data['title_text'])) ? $item_data['title_text'] : '').'"
                class="item_image"
                />';
                echo "<br/>";
            }
			?> <a class="read-more strong" href="/news/<?php echo $item_data['category'].DIRECTORY_SEPARATOR.$item_data['news_url'];?>">Read More »</a>
		</p>
	</span>
</div>