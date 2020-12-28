<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dale
 * Date: 19/02/2014
 * Time: 11:31
 * To change this template use File | Settings | File Templates.
 */
?>
<ul id="gallery">
    <? foreach($images AS $image):?>
        <li><a href="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$image['photo_name'], 'gallery')?>" class="lytebox" data-title="<?=$image['title']?>" data-lyte-options="slide:true group:slideshow slideInterval:4500 showNavigation:true autoEnd:false loopSlideshow:true">
                <img src="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$image['photo_name'], 'gallery/_thumbs')?>"/>
        </a></li>

    <? endforeach;?>
</ul>