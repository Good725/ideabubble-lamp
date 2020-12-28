<?php $base_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'projects/_thumbs_cms'); ?>
<tr data-id="<?= $image['id'] ?>">
    <td>
        <img src="<?= $base_path.'/'.$image['filename'] ?>" alt="" />
        <input type="hidden" name="image_ids[]" value="<?= $image['id'] ?>" />
    </td>
    <td><?= $image['filename'] ?></td>
    <td><i class="icon-remove"></i></td>
</tr>