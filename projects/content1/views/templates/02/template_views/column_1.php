<?php
$sidebar_location = (Settings::instance()->get('content_location') == 'right') ? 'left' : 'right';
$editable_menu = menuhelper::add_menu_editable_heading('left', '', '', FALSE);
?>
<div id="column-242" class="<?= $sidebar_location ?>">

    <? //decide what content to show in sidebar based on page

    // IF (CONTENT layout) courses
    if ($page_data['name_tag'] == 'courses.html') {
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
        <div id="panels_wrapper" class="panels_wrapper">
            <?= Model_Panels::get_panels_feed_view('content_' . $sidebar_location); ?>
        </div>
        <div id="latest_testimonials_wrapper" class="<?= $sidebar_location ?>">
            <?= Model_Testimonials::get_plugin_items_front_end_feed(); ?>
        </div>

    <? } ?>

</div>