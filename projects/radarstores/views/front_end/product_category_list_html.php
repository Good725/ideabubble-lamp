<!-- products-breadcrumbs -->
<div id="breadcrumb-nav">
    <?=IbHelpers::breadcrumb_navigation();?>
</div>
<div class="category_list">
    <?=IbHelpers::get_messages()?>

    <div class="clear">

        <div class="left" id="products_list_headings">
            <div class="left" id="heading_buttons">
            </div>
        </div>
        <div id="product" class="prod_cat">
            <?=$product_categories?>
        </div>
    </div>
</div>
