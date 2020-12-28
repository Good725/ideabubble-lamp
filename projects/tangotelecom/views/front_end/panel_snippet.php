<?php
$tall_panel   = ($panel['position'] == 'home_content' OR $panel['position'] == 'content_left');
$link_in_text = (strpos($panel['text'], '<a '));
?>

<li class="<?= $panel['position'] ?>_panel panel_<?=$key?> <?=$first?> <?=$last?>">
	<a href="<?= empty($url) ? '#' : $url ?>" class="panel_main_link">
		<?php if ($panel["image"] != "0"): ?>
			<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $panel["image"], 'panels/') ?>" />
		<?php endif; ?>

		<?php if ($panel['position'] == 'home_content' OR $panel['position'] == 'content_left'): ?>
			<h3><?= $panel['title'] ?></h3>
		<?php endif; ?>

		<?php if ( ! $tall_panel AND ! $link_in_text): ?>
			<div class="panel_overlay"><?= $panel["text"] ?></div>
		<?php endif; ?>
	</a>
	<?php if ($tall_panel OR $link_in_text): ?>
		<div class="panel_overlay"><?= $panel["text"] ?></div>
	<?php endif; ?>
</li>