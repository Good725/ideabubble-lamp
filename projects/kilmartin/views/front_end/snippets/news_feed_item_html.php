<li>
    <div class="feed_item_tile">
        <!--<span class="feed_item_title light"><?php echo $feed_item_data['title'];?></span>-->
        <!--<span class="feed_item_event_date dark"><?php echo (!empty($feed_item_data['event_date']))? date('F dS, Y', strtotime($feed_item_data['event_date'])) : ''?></span>-->
        <div class="feed_item_summary">
            <?php
            echo '<h4 class="newsFeedTitle">' . $feed_item_data['title'] . '</h4>';
            echo '<p class="feed_item_summary_text">';
            //Just take the first 200 characters
            $chars_per_item = 200;
            if (strlen($feed_item_data['summary']) >= $chars_per_item) {
                echo substr(strip_tags($feed_item_data['summary']), 0, $chars_per_item);
            } else {
                echo strip_tags($feed_item_data['summary']);
            }
            echo '</p>';

            ?>
            <div class="item_image">
                <?php
                /*if(!empty($feed_item_data['image'])){
                    echo '<img src="'. Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'news/_thumbs').'" class="item_image" />';
                }*/
                ?>
            </div>
            <p><a class="read-more"
                  href="/news/<?php echo $feed_item_data['category'] . DIRECTORY_SEPARATOR . $feed_item_data['news_url']; ?>">Read
                    More »</a></p>
        </div>
    </div>

</li>