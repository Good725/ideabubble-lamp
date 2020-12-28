<div class="category_list">
    <?= IbHelpers::get_messages() ?>
    <div>
        <div id="product" class="product_category">
			 <?php	 if (Settings::instance()->get('product_display_mode') == 'TRUE'): ?>
			<div class="mode-icon-area">
				<?php //$products_plugin_page = '/'.Model_Product::get_products_plugin_page();
				  $gridactive='active';
				  $listactive='';
				  $thumbactive='';
				  $current_mode=Session::instance()->get('display_mode'); 
				  if($current_mode=='list'){ $listactive='active'; $gridactive='';$thumbactive='';}else{ $listactive='';}
				  if($current_mode=='thumb'){ $thumbactive='active';$gridactive='';$listactive='';}else{ $thumbactive='';}?>
				 <a class="view_mode <?php echo $gridactive; ?>"  title="grid">Grid</a>
				 <a class="view_mode <?php echo $listactive; ?>"  title="list">List</a>
				 <a class="view_mode <?php echo $thumbactive; ?>"  title="thumb">Thumb</a>
			</div>
			<?php endif; ?>
			<?php $image = (isset($current_category['image']) AND $current_category['image'] != '') ? '<div class="product-category-image"><img src="'.$base_file_path.'/'.$current_category['image'].'" /></div>' : ''; ?>
            <?php if (isset($current_category['information'])): ?>
                <div class="category_information"><?= $image.$current_category['information'] ?></div>
            <?php endif; ?>
            <?= $product_categories ?>
        </div>
    </div>
</div>
<form id="ProductDetailsForm"></form>
<script type="text/javascript" src="/engine/plugins/products/js/front_end/product_details.js"></script>
<?php
if (isset($view_js_files))
{
	foreach ($view_js_files as $js_item_html) echo $js_item_html;
}
?>
<script>
	$(".view_mode").on("click", function(){
			var title=$(this).attr('title');
			$.ajax({
				 url: "<?php echo URL::Site('frontend/products/change_mode');?>",
				 type: "POST",
				 data: {mode: title},
				 beforeSend: function() {
                    // $("#loading_image").css("display", "block");
                 },
				 success: function(){
				   //$("#loading_image").hide(); 
                   location.reload();
                   
                 }
            });

   });
</script>
<?php if (Settings::instance()->get('products_infinite_scroller') == 1): ?>
	<script src="<?= URL::get_engine_assets_base() ?>js/frontend/infinitescroll.js"></script>
	<script>
		// Add infinite scrolling to the products feed
		$('#product').find('.pagedemo').infinite_scroll({
			footer: '#footer',
			feed_item: '#product .pagedemo > a, .thumb_product',
			ajax_url: '/frontend/products/ajax_get_products',
			custom_params: { url: window.location.pathname }
		});
	</script>
<?php endif; ?>
