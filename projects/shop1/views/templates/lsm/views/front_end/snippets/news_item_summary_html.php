<div class="news-result">
	<div class="news-result-inner">
		<h2 class="news-result-title news-result-title--list"><?= $item_data['title'] ?></h2>

		<div class="news-result-image">
			<figure>
				<img src="/shared_media/<?= Kohana::$config->load('config')->project_media_folder ?>/media/photos/news/<?= empty($item_data['image']) ? 'news-placeholder.png' : $item_data['image'] ?>" alt="<?= $item_data['title_text'] ?>" title="<?= $item_data['alt_text'] ?>" />

				<?php if ( ! empty($item_data['event_date'])): ?>
					<figcaption class="news-result-date"><?= date('<\s\p\a\n>M</\s\p\a\n><\b\r />d<\s\u\p>S</\s\u\p>, Y', strtotime($item_data['event_date'])) ?></figcaption>
				<?php endif; ?>
			</figure>
		</div>

		<div class="news-result-text">
			<h2 class="news-result-title news-result-title--grid"><?= $item_data['title'] ?></h2>

			<div class="news-result-summary"><?= $item_data['summary'] ?></div>

			<a class="news-result-read_more button button--send" href="/news/<?= $item_data['category'].'/'.$item_data['news_url'] ?>"><?= __('Read More') ?></a>
		</div>
	</div>
</div>