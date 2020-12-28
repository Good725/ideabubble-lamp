<?php
$background_settings = Settings::instance()->get();
$background_image      = isset($background_settings['background_image'])               ? trim($background_settings['background_image'])         : '';
$background_color      = isset($background_settings['background_color'])               ? trim($background_settings['background_color'])         : '';
$background_pos_x      = isset($background_settings['background_vertical_position'])   ? $background_settings['background_vertical_position']   : '';
$background_pos_y      = isset($background_settings['background_horizontal_position']) ? $background_settings['background_horizontal_position'] : '';
$background_repeat     = isset($background_settings['background_repeat'])              ? $background_settings['background_repeat']              : '';
$background_attachment = isset($background_settings['background_attachment'])          ? $background_settings['background_attachment']          : '';
$background_css        = isset($background_settings['background_css'])                 ? trim($background_settings['background_css'])           : '';
?>
<?php if (trim($background_image.$background_color.$background_css) != ''): ?>
	<style type="text/css">
		html body {<?php
            if (trim($background_css) != '')
            {
                echo $background_css;
            }
            else
            {
                switch($background_attachment)
                {
                    case 0: $background_attachment = 'scroll'; break;
                    case 1: $background_attachment = 'fixed';  break;
                }
                echo ($background_color) ? 'background-color:'.$background_color.';' : '';
                echo ($background_image) ? 'background-image:url('.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'','bg_images').$background_image.');' : '';
                echo 'background-position:'.$background_pos_y.' '.$background_pos_x.';';
                echo 'background-repeat:'.$background_repeat.';';
                echo 'background-attachment:'.$background_attachment.';';
            }
            ?>}
	</style>
<?php endif; ?>
