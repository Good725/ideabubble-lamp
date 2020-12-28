<?php
$sidebar_location = (Settings::instance()->get('content_location') == 'right') ? 'left' : 'right';
$editable_menu = menuhelper::add_menu_editable_heading('left', '', '', FALSE);
$side_menu = menuhelper::add_menu_editable_heading('side_menu', '', '', FALSE);
?>
<div id="column-242" class="sidebar-column <?= $sidebar_location ?>">

    <? //decide what content to show in sidebar based on page

    // IF (CONTENT layout) courses
    if ($page_data['name_tag'] == 'courses.html')
	{
        //display course categories
        echo Model_Categories::get_front_categories();
    }
	else if (($editable_menu != '') AND ($page_data['layout'] == 'content')) // IF (CONTENT layout) and left menu is full the display
    {
        //show left content menu
        echo $editable_menu;
    }
	else
	{
    ?>
		<?php if (isset($side_menu->menu) AND count($side_menu->menu) > 0): ?>
			<div class="side_menu_wrapper"><?= $side_menu ?></div>
		<?php endif; ?>
        <div id="panels_wrapper">
			<?= Model_Panels::get_panels_feed_view((($page_data['layout'] == 'content2') ? 'content2_' : 'content_').$sidebar_location); ?>
        </div>
    <? } ?>

</div>