<li class="<?=$first?> <?=$last?> panels_item">
    <?php if (!empty($url)): ?>
    <a href="<?=$url?>" class="">
        <?php endif ?>
        <div onclick="" id="" class="apanel panel_link">
            <div class="heading"><?=$panel["title"]?></div>
            <div class="panelimage"><img src="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$panel["image"], 'panels/')?>"></div>
            <div class="overlay"><?=$panel["text"]?></div>
        </div>
        <?php if (!empty($url)): ?>
    </a>
<?php endif ?>
</li>