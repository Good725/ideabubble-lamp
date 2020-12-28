<?php $media_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'menus'.'/') ?>
<?php if ($class_name == 'main'): ?>
    <a href="#" id="pull">Menu</a>
<?php endif; ?>
<ul class="<?=$class_name;?>" >
    <?php foreach($menu as $menulv0): ?>
        <li
			id="item_<?=$menulv0['id']?>"
			class="level_<?=$menulv0['level']?> item_<?=$menulv0['item_number']?> <?=$menulv0['first']?> <?=$menulv0['last']?> <?=$menulv0['current']?><?= $menulv0['has_sub'] ? ' has_submenu' : '' ?>"
			data-target="<?= substr($menulv0['link'], strrpos($menulv0['link'], '/') + 1); ?>"
			>
            <a target="<?=$menulv0['menus_target']?>" href="<?=$menulv0['link']?>">
                <?php if ($menulv0['filename'] != ''): ?>
                    <img src="<?= $media_path.$menulv0['filename']  ?>" alt="" />
                <?php endif; ?>
                <?=$menulv0['title']?>
            </a>
            <?php if($menulv0['has_sub'] == '1'):?>
                <?php $lv1 = menuhelper::submenu($menulv0); ?>
				<span class="submenu-expand"></span>
                <ul class="level2">
                    <?php foreach($lv1 as $menulv1): ?>
                        <li
							id="item_<?=$menulv1['id']?>"
							class="level_<?=$menulv1['level']?> item_<?=$menulv1['item_number']?> <?=$menulv1['first']?> <?=$menulv1['last']?> <?=$menulv1['current']?><?= $menulv1['has_sub'] ? ' has_submenu' : '' ?>"
							data-target="<?= substr($menulv0['link'], strrpos($menulv0['link'], '/') + 1); ?>"
							>
                            <a target="<?=$menulv1['menus_target']?>" href="<?=$menulv1['link']?>">
                                <?php if ($menulv1['filename'] != ''): ?>
                                    <img src="<?= $media_path.$menulv1['filename'] ?>" alt="" />
                                <?php endif; ?>
                                <?=$menulv1['title']?></a>
                            <?php if($menulv1['has_sub'] == '1'):?>
                                <?php $lv2 = menuhelper::submenu($menulv1); ?>
								<span class="submenu-expand"></span>
                                <ul class="level3">
                                    <?php foreach($lv2 as $menulv2): ?>
                                        <li id="item_<?=$menulv2['id']?>" class="level_<?=$menulv2['level']?> item_<?=$menulv2['item_number']?> <?=$menulv2['first']?> <?=$menulv2['last']?> <?=$menulv2['current']?>"><a target="<?=$menulv2['menus_target']?>" href="<?=$menulv2['link']?>"><?=$menulv2['title']?></a></li>
                                    <?php endforeach;?>
                                </ul>
                            <?php endif;?>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
        </li>
    <?php endforeach;?>
	<?php if (Settings::instance()->get('main_menu_products') == 1 AND $class_name == 'main'): ?>
		<?php $plain_array = array() ?>
		<?php $categories = Model_Category::get(NULL, NULL, $plain_array, 0, FALSE, TRUE); ?>

		<?php foreach ($categories as $category): ?>
			<li class="level_1"><a href="/products.html/<?= trim(str_replace(' ', '-', $category['category'])) ?>"><?= __($category['category']) ?></a>
				<?php $plain_array = array() ?>
				<?php $subcategories = Model_Category::get(NULL, $category['id'], $plain_array, 0, FALSE, TRUE); ?>
				<?php if (count($subcategories) > 0): ?>
					<span class="submenu-expand"></span>
					<ul class="level2">
						<?php foreach ($subcategories as $subcategory): ?>
							<li class="level_2"><a href="/products.html/<?= trim(str_replace(' ', '-', $subcategory['category'])) ?>""><?= __($subcategory['category']) ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>

    <?php echo $last ?>
</ul>
