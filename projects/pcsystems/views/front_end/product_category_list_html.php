<?php
// Render JS files for this view
if (isset($view_js_files))
{
	foreach ($view_js_files as $js_item_html) echo $js_item_html;
}
?>
<!-- products-breadcrumbs -->
<div class="breadcrumb-nav" id="breadcrumb-nav"><?= trim(''.IbHelpers::breadcrumb_navigation()) ?></div>
<div class="category_list">
    <?= IbHelpers::get_messages() ?>

    <div>
        <div id="products_list_headings">
            <div class="left" id="heading_buttons">
            </div>
        </div>
        <div id="product" class="row prod_cat">
            <?php if (isset($current_category['information'])): ?>
                <div class="category_information"><?= $current_category['information'] ?></div>
            <?php endif; ?>
            <?=$product_categories?>
        </div>
    </div>
</div>