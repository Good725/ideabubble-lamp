<?php $settings = Settings::instance()->get(); ?>
<div class="news-story">
	<?php if ( ! empty($item_data['image'])): ?>
		<div class="news-story-image">
            <img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news') ?>"
                 alt="<?=   (isset($item_data['alt_text']))   ? $item_data['alt_text']   : ''; ?>"
                 <?= (isset($item_data['title_text'])) ? ' title="'.$item_data['title_text'].'"' : '' ?>
                  />
		</div>
	<?php endif; ?>

	<h1 class="news-story-title"><?= $item_data['title'] ?></h1>

	<div class="news-story-content"><?= $item_data['content'] ?></div>

	<div class="news-story-navigation">
		<a href="/news/<?= $item_data['category'] ?>" class="news-story-return">Return to &quot;<?=ucfirst($item_data['category'])?>&quot;</a>
	</div>

	<?php if ($settings['addthis_id']): ?>
		<div class="news-story-social">
			<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
				<a class="news-story-social-link news-story-social-link--share addthis_button_compact" href="https://www.addthis.com/bookmark.php?v=250&amp;username=ra-<?= $settings['addthis_id'] ?>">
					<span class="news-story-share_icon fa fa-share-alt"></span>
					<span><?= __('Share') ?></span>
				</a>
				<a class="news-story-social-link addthis_button_preferred_1"></a>
				<a class="news-story-social-link addthis_button_preferred_2"></a>
				<a class="news-story-social-link addthis_button_preferred_3"></a>
				<a class="news-story-social-link addthis_button_preferred_4"></a>
			</div>
			<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#username=ra-<?= $settings['addthis_id'] ?>"></script>
		</div>
	<?php endif; ?>
</div>