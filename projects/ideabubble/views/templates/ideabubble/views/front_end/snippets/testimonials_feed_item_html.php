<?php $page = Model_Pages::get_page(trim($_SERVER['SCRIPT_URL'],'/')); ?>

<?php if ( ! empty($page[0]) AND in_array($page[0]['layout'], array('home', 'content'))): ?>
    <li>
        <?php if ( ! empty($feed_item_data['image'])): ?>
            <figure>
                <img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'testimonials') ?>" alt="" />
            </figure>
        <?php endif; ?>

        <blockquote>
            <p><?= $feed_item_data['summary'] ?></p>
            <?php if ( ! empty($feed_item_data['item_company']) OR  ! empty($feed_item_data['item_website'])): ?>
                <cite><?= $feed_item_data['item_company'] ?><?= ( ! empty($slide['item_website'])) ? ' ,'.$feed_item_data['item_website'] : '' ?></cite>
            <?php endif; ?>
        </blockquote>
    </li>
<?php else: ?>
    <li class="swiper-slide">
        <div class="panel-body">
            <p><?= $feed_item_data['summary'];?> </p>
        </div>
        <div class="author-name">
            <?php
            echo $feed_item_data['item_signature'];

            if(!empty($feed_item_data['item_company']))
            {
                echo ' ,'.$feed_item_data['item_company'];
            }
            if(!empty($slide['item_website']))
            {
                echo ' ,'.$feed_item_data['item_website'];
            }
            ?>
        </div>
    </li>
<?php endif; ?>