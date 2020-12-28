<div class="well project_container">
    <span class="project_image">
        <?php if($project->get_photo() != ""):?>
        <img src="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'projects/',$project->get_photo());?>"/>
        <?php endif;?>
    </span>
    <span class="project_info">
        <b><?=$project->get_name();?></b>
        <?=$project->get_summary();?>
    </span>
    <span class="project_options">
        <button type="button" class="btn-danger remove_project_link" data-id="<?=$project->get_id();?>">Remove</button>
    </span>
    <div style="clear:both;"></div>
</div>