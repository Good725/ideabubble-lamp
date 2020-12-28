<ul class="<?= $class_name ?>" >
	<?php foreach($menu as $menulv0): ?>
		<li id="menu-item-<?=$menulv0['id']?>" class="level_<?=$menulv0['level']?> item_<?=$menulv0['item_number']?> <?=$menulv0['first']?> <?=$menulv0['last']?> <?=$menulv0['current']?>">
			<a target="<?=$menulv0['menus_target']?>" href="<?=$menulv0['link']?>"><?=$menulv0['title']?></a>
			<?php if($menulv0['has_sub'] == '1'):?>
				<?php $lv1 = menuhelper::submenu($menulv0); ?>
				<ul class="level2">
					<?php foreach($lv1 as $menulv1): ?>
						<li id="item_<?=$menulv1['id']?>" class="level_<?=$menulv1['level']?> item_<?=$menulv1['item_number']?> <?=$menulv1['first']?> <?=$menulv1['last']?> <?=$menulv1['current']?>">
							<a target="<?=$menulv1['menus_target']?>" href="<?=$menulv1['link']?>"><?=$menulv1['title']?></a>
							<?php if($menulv1['has_sub'] == '1'):?>
								<?php $lv2 = menuhelper::submenu($menulv1); ?>
								<ul>
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