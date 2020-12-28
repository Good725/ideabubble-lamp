<li class="panel_li panel_<?=$key?> <?=$first?> <?=$last?>">
    <?php if (!empty($url)): ?>
        <a href="<?=$url?>" class="">
    <?php endif ?>
    <div class="panel_body">
        <?php if($panel["image"] != "0"): ?>
            <div class="panel_image"><img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$panel["image"], 'panels/') ?>" /></div>
        <?php endif; ?>

        <?php if (trim($panel['text']) != ''): ?>
            <div class="overlay">
                <?=$panel["text"]?>
            </div>
        <?php endif; ?>
    </div>
    <?php if (!empty($url)): ?>
        </a>
    <?php endif ?>
</li>