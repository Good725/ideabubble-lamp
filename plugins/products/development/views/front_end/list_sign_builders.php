<div id="list_sign_builders" class="list_sign_builders">
    <?php foreach ($sign_builders as $sign_builder): ?>
		<?php $blank_sign = ($sign_builder['file_name'] == '' AND ($sign_builder['layers'] == '' OR $sign_builder['layers'] == '[]')); ?>
		<?php if ( ! $blank_sign): ?>
			<?php $url_name = Model::factory('Pages')->filter_name_tag($sign_builder['title']); ?>
			<a href="/<?= isset($products_plugin_page) ? $products_plugin_page : 'products.html' ?>/<?= $url_name ?>" class="list_sign_builders_item" data-id="<?= $sign_builder['id'] ?>">
				<div class="list_sb_thumb">
					<?php if ((isset($fileinfo['extension']) and $fileinfo['extension'] == 'svg')): ?>
						<img src="<?= $filepath.'_thumbs/'.$sign_builder['file_name'] ?>" alt="" />
					<?php else: ?>
						<img src="<?= $filepath.$sign_builder['file_name'] ?>" alt="" />
					<?php endif; ?>
				</div>
				<div class="list_sb_name"><?= $sign_builder['title'] ?></div>
			</a>
		<?php endif; ?>
    <?php endforeach; ?>
</div>