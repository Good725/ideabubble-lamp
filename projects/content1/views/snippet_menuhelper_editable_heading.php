<?php $responsive = (strpos($class_name, 'navbar-nav') !== FALSE); ?>
<?php if ($class_name == 'main' AND Kohana::$config->load('config')->get('template_folder_path') == 'home_wide'): ?>
	<a href="#" id="pull">Menu</a>
<?php endif; ?>
<ul class="<?=$class_name;?>" >
    <?php foreach($menu as $menulv0): ?>
        <li id="item_<?=$menulv0['id']?>" class="level_<?=$menulv0['level']?> item_<?=$menulv0['item_number']?> <?=$menulv0['first']?> <?=$menulv0['last']?> <?=$menulv0['current']?><?= $menulv0['has_sub'] ? ' has_submenu' : '' ?>">
            <a target="<?=$menulv0['menus_target']?>" href="<?=$menulv0['link']?>"><h3><?=$menulv0['title']?></h3></a>
            <?php if($menulv0['has_sub'] == '1'):?>
                <?php $lv1 = menuhelper::submenu($menulv0); ?>
                <ul class="level2<?php if ($responsive) echo ' dropdown-menu'; ?>">
                    <?php foreach($lv1 as $menulv1): ?>
                        <li id="item_<?=$menulv1['id']?>" class="level_<?=$menulv1['level']?> item_<?=$menulv1['item_number']?> <?=$menulv1['first']?> <?=$menulv1['last']?> <?=$menulv1['current']?><?= $menulv1['has_sub'] ? ' has_submenu' : '' ?>">
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
                <?php if ($responsive AND count($lv1) > 0): ?>
                    <a class="dropdown-toggle" data-toggle="dropdown"><b class="caret"></b></a>
                <?php endif; ?>
            <?php endif;?>
        </li>
    <?php endforeach;?>
    <?php echo $last ?>
</ul>