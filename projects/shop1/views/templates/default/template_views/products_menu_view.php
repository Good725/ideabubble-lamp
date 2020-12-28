<!-- Products Menu -->
<h1>
	<a href="<?= URL::base()?>products.html"><span class="red">OUR</span> PRODUCTS</a>
</h1>
<div id="products_menu" class="left  nav">
    <?php if (Settings::instance()->get('products_menu') !== FALSE AND Settings::instance()->get('products_menu') == 0): ?>
        <div>
            <?= menuhelper::add_menu_editable_heading('left','ul_level_1'); ?>
        </div>
    <?php else: ?>
        <?= Model_Product::render_products_menu(); ?>
    <?php endif; ?>
</div>
<!-- /Products Menu -->
