<?php
$theme = isset($theme) ? $theme : Model_Engine_Theme::get_current_theme();
require_once Kohana::find_file('template_views', 'header');?>

<div class="container">
    <?= $content ?>
</div>

<?php require_once Kohana::find_file('views', 'footer'); ?>
