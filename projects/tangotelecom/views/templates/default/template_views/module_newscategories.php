<div class="resources">
    <?php $news_categories = ORM::factory('News_Category')->order_by('order')->find_all_published(); ?>

	<h3>News</h3>
	<ul class="resources">
		<?php foreach ($news_categories as $news_category): ?>
			<li>
				<a href="/news/<?= $news_category->category ?>">
					<?= ($news_category->category == 'News') ? 'Latest' : $news_category->category ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>