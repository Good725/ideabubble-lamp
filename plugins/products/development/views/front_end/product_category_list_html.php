<?php
// Render CSS Files for THIS View
if (isset($view_css_files))
{
	foreach ($view_css_files as $css_item_html) echo $css_item_html;
}
// Render JS Files for This View
if (isset($view_js_files))
{
	foreach ($view_js_files as $js_item_html) echo $js_item_html;
}
?>
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
        <div id="product" class="left row prod_cat">
		 <?php	 if (Settings::instance()->get('product_display_mode') == 'TRUE'): ?>
			<div class="mode-icon-area">
				<?php //$products_plugin_page = '/'.Model_Product::get_products_plugin_page();
				 //$current_url= strtok($_SERVER["REQUEST_URI"],'?');
				  $gridactive='active';
				  $listactive='';
				  $thumbactive='';
				  $current_mode=Session::instance()->get('display_mode'); 
				  if($current_mode=='list'){ $listactive='active'; $gridactive='';$thumbactive='';}else{ $listactive='';}
				  if($current_mode=='thumb'){ $thumbactive='active';$gridactive='';$listactive='';}else{ $thumbactive='';}?>
				 <a class="view_mode <?= $gridactive; ?>"   data-mode="grid"  title="<?= __('Grid') ?>"><?= __('Grid') ?></a>
				 <a class="view_mode <?= $listactive; ?>"   data-mode="list"  title="<?= __('List') ?>"><?= __('List') ?></a>
				 <a class="view_mode <?= $thumbactive; ?>"  data-mode="thumb" title="<?= __('Thumb') ?>"><?= __('Thumb') ?></a>
			</div>
		 <?php endif; ?>
            <?php if (isset($current_category['information'])): ?>
                <div class="category_information"><?= $current_category['information'] ?></div>
            <?php endif; ?>
            <?=$product_categories?>
        </div>
    </div>
</div>
<!--<div id="loading_image" style="display:none;"><img src="<?= URL::get_engine_plugin_assets_base('products'); ?>images/ajax-loader.gif"></div>-->
<script>
	$(".view_mode").on("click", function(){
			var title=$(this).attr('data-mode');
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
		$(document).ready(function()
		{
			// Add infinite scrolling to the products feed
			var $feed = $('#product').find('.pagedemo');
			$feed.infinite_scroll({
				footer: '#footer',
				feed_item: '#product .pagedemo > a, .thumb_product',
				ajax_url: '/frontend/products/ajax_get_products',
				custom_params: {
					url: window.location.pathname,
					featured: ($feed.find('.product_featured').length) ? 1 : 0
				}
			});
		});
	</script>
<?php endif; ?>
