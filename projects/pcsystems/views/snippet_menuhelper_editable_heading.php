<? /* Main menu */ ?>
<?php if ($class_name == 'main' OR $class_name == 'main_menu'): ?>
	<?php /* Level 0 */ ?>
	<?php foreach ($menu as $menulv0): ?>
		<li>
			<a href="<?=$menulv0['link']?>" class="main-nav-i"><?=$menulv0['title']?></a>

			<?php /* Level 1 */ ?>
			<?php if ($menulv0['has_sub'] == '1'):?>
				<?php $lv1 = menuhelper::submenu($menulv0); ?>
				<a href="#" class="submenu-expand"></a>
				<ul class="main-nav-drop-down">
					<?php foreach ($lv1 as $menulv1): ?>
						<li>
							<a href="<?=$menulv1['link']?>">
								<span class="orange-points">»</span> <?=$menulv1['title']?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</li>
	<?php endforeach; ?>

<? /* Side menu */ ?>
<?php elseif ($class_name == 'side' OR $class_name == 'side_menu'): ?>
	<?php foreach ($menu as $menulv0): ?>
		<ul class="body-menu <?= $class_name ?>">
			<li class="body-menu-li">
				<div class="body-menu-li-name">
					<a href="<?=$menulv0['link']?>"><?=$menulv0['title']?></a>
					<?php if ($menulv0['has_sub'] == '1'):?>
						<a href="#" class="body-sub-menu-expand"></a>
					<?php endif; ?>
				</div>

				<?php if ($menulv0['has_sub'] == '1'):?>
					<?php $lv1 = menuhelper::submenu($menulv0); ?>
					<ul class="body-sub-menu">
						<?php foreach ($lv1 as $menulv1): ?>
							<li class="body-sub-menu-li">
								<a href="<?=$menulv1['link']?>"><?= $menulv1['title'] ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</li>
		</ul>
	<?php endforeach; ?>

<? /* Default */ ?>
<?php else: ?>
	<ul class="<?=$class_name;?>" >
		<?php foreach($menu as $menulv0): ?>
			<li id="item_<?=$menulv0['id']?>" class="level_<?=$menulv0['level']?> item_<?=$menulv0['item_number']?> <?=$menulv0['first']?> <?=$menulv0['last']?> <?=$menulv0['current']?>">
				<a target="<?=$menulv0['menus_target']?>" href="<?=$menulv0['link']?>">
					<?php if ($menulv0['filename'] != ''): ?>
						<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$menulv0['filename'], $menulv0['location'].'/') ?>" alt="<?= $menulv0['title'] ?>" title="<?= $menulv0['title'] ?>" />
					<?php else: ?>
						<?= $menulv0['title'] ?>
					<?php endif; ?>
				</a>
				<?php if($menulv0['has_sub'] == '1'):?>
					<?php $lv1 = menuhelper::submenu($menulv0); ?>
					<span class="submenu-expand"></span>
					<ul class="level2">
						<?php foreach($lv1 as $menulv1): ?>
							<li id="item_<?=$menulv1['id']?>" class="level_<?=$menulv1['level']?> item_<?=$menulv1['item_number']?> <?=$menulv1['first']?> <?=$menulv1['last']?> <?=$menulv1['current']?>">
								<a target="<?=$menulv1['menus_target']?>" href="<?=$menulv1['link']?>"><?=$menulv1['title']?></a>
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
		<?php echo $last ?>
	</ul>
<?php endif; ?>