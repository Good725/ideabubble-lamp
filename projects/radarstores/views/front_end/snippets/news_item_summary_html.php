<div class="summary_item_tile">
    <div class="item_image">
        <?php
        if(!empty($item_data['image'])){
            echo '<img src="'. Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news/_thumbs').'" class="item_image" />';
        }
        ?>
    </div>
    <div class="item_news_contet">
        <span class="summary_item_title light"><h1><?php echo $item_data['title'];?></h1></span>
        <span class="summary_item_summary">
            <p>
                <?php
                    //Just take the first 200 characters
                    echo substr($item_data['summary'], 0, 200).'... ';
                ?> <a class="read-more strong" href="/news/<?php echo $item_data['category'].DIRECTORY_SEPARATOR.$item_data['news_url'];?>">Read More »</a>
            </p>
        </span>
    </div>
</div>