<? $items = Model_Categories::get_all_categories(); ?>

<div class="panels" id="course-categories">
    <ul>
        <?php foreach ($items as $elem => $val): ?>
            <li class="panel_0 first_panel ">
                <a href="/courses/<?php echo IbHelpers::generate_friendly_url($val['category']) ?>/?id=<?= $val['id'] ?>">
                    <div>
                        <div class="panel_image">
                            <img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $val['file_id'], 'courses') ?>" title="<?= $val['category'] ?>" alt="<?= $val['category'] ?>"/>
                        </div>
                        <div class="overlay">
                            <p>
                                <?= $val['category'] ?>
                            </p>
                        </div>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>




