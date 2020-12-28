<?php
$news_panel = (strpos($panel['text'], '{latestnews}') != FALSE);
$testimonials_panel = (strpos($panel['text'], '{testimonials}') != FALSE);
?>
<li class="panel_li panel_<?= $key ?> <?= $first ?> <?= $last ?><?= $news_panel ? ' news_panel' : '' ?><?= $testimonials_panel ? ' testimonials_panel' : '' ?>">
	<?php if (Kohana::$config->load('config')->get('template_folder_path') == 'wide_banner'): ?>
		<h2 class="panel_heading">
			<?php if ($news_panel): ?>
				<a href="/news.html"><?= $panel['title'] ?></a>
			<?php elseif ($testimonials_panel): ?>
				<a href="/testimonials.html"><?= $panel['title'] ?></a>
			<?php else: ?>
				<?= $panel['title'] ?>
			<?php endif; ?>
		</h2>
	<?php endif; ?>
	<?php if ( ! empty($url)): ?>
		<a href="<?= $url ?>">
	<?php endif ?>
		<div class="panel_body">
			<?php if ($panel['image'] != '0'): ?>
				<div class="panel_image"><img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$panel['image'], 'panels/') ?>" /></div>
			<?php endif; ?>

			<?php if (trim($panel['text']) != ''): ?>
				<div class="overlay">
					<?= str_replace('{testimonials}', Model_Testimonials::get_plugin_items_front_end_feed('Testimonials'), str_replace('{latestnews}', Model_News::get_plugin_items_front_end_feed('News'), $panel["text"])) ?>
				</div>
			<?php endif; ?>
		</div>
	<?php if ( ! empty($url)): ?>
		</a>
	<?php endif ?>
</li>