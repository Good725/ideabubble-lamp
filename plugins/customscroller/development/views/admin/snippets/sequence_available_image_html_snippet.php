<?
// Take the Image NAME except extension
$available_image_name = current(explode('.', $available_image['filename']));
?>

<div id="scroller_items<?=$available_image_name?>" class="span4 available_images<?=@$available_image['sequences']?>" image-name="<?=$available_image['filename']?>">
    <img src="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$available_image['filename'], $available_image['location'].DIRECTORY_SEPARATOR.'_thumbs_cms')?>"
         alt="<?=$available_image['filename']?>"
         id="scroller_available_img_<?=$available_image_name?>"
         title="Add Image: '<?=$available_image_name?>' to Current Scroller Sequence"
         onclick="get_sequence_scroller_item_editor('','<?=$available_image['filename']?>')"/>
</div>
<?
unset($available_image_name);
?>