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
if (class_exists('Model_Currency') AND count(Model_Currency::getRates()) > 0) {
    $currencies = Model_Currency::getCurrencies(true);
    $pcurrency = Model_Currency::getPreferredCurrency(true);
}
?>
<!-- products-breadcrumbs -->
<!--<div id="breadcrumb-nav">
    <?=IbHelpers::breadcrumb_navigation();?>
</div>-->
<div class="category_list">
    <?=IbHelpers::get_messages()?>
    <div class="clear">

        <div class="left" id="products_list_headings">
            <div class="left" id="heading_buttons">
            </div>
        </div>
        <div id="product" class="left row prod_cat">
			<div class="bck-area"><a href="javascript:history.back(1)"><span class="back-icon"></span> BACK</a></div>

			<?php if (isset($current_category['category'])): ?>
                <div class="category_name"><?= $current_category['category'] ?></div>
            <?php endif; ?>
            <?php if (isset($current_category['description'])): ?>
                <div class="category_information"><?= $current_category['description'] ?></div>
            <?php endif; ?>
            <?php	 if (Settings::instance()->get('product_display_mode') == 'TRUE'): ?>
			 <div class="mode-icon-area">
				<?php
                 $gridactive='active';
				 $listactive='';
			     $thumbactive='';
				  $current_mode=Session::instance()->get('display_mode'); 
                  if($current_mode=='list'){ $listactive='active'; $gridactive='';$thumbactive='';}else{ $listactive='';}
				  if($current_mode=='thumb'){ $thumbactive='active';$gridactive='';$listactive='';}else{ $thumbactive='';}?>
				 <a class="view_mode <?= $gridactive ?> glyphicon glyphicon-th"        data-mode="grid"  title="<?= htmlentities(__('Click to change display mode to "Grid"')) ?>">Grid</a>
				 <a class="view_mode <?= $listactive ?> glyphicon glyphicon-th-list"   data-mode="list"  title="<?= htmlentities(__('Click to change display mode to "List"')) ?>">List</a>
				 <a class="view_mode <?= $thumbactive ?> glyphicon glyphicon-th-large" data-mode="thumb" title="<?= htmlentities(__('Click to change display mode to "Thumb"')) ?>">Thumb</a>
				 <?php if (isset($current_category['category'])): ?>
				<div class="filter-area">
					<div class="sort-area"><label for="srt_filter">Sort by</label>
						<select class="sort_page_filter" id="srt_filter">
							<?php
							$selected = Session::instance()->get('products_feed_order');
							$choices  = array('Order' => 'Order', 'title' => 'Name', 'price' => 'Price');
							?>
							<?php foreach ($choices as $key => $choice): ?>
								<option value="<?= $key ?>"<?= ($key == $selected) ? ' selected="selected"' : '' ?>><?= $choice ?></option>
							<?php endforeach; ?>
						</select>
						<i class="glyphicon glyphicon-chevron-down"></i>
					</div>
					 <div class="show-area"><label for="page_filter">Show</label>
						<div class="show-block">
							<select class="sort_page_filter" id="page_filter">
								<?php
								$selected = Session::instance()->get('products_feed_items_per_page');
								$choices  = array('12' => '12', '24' => '24', '500' => 'All');
								?>
								<?php foreach ($choices as $key => $choice): ?>
									<option value="<?= $key ?>"<?= ($key == $selected) ? ' selected="selected"' : '' ?>><?= $choice ?></option>
						 		<?php endforeach; ?>
							</select>
							<i class="glyphicon glyphicon-chevron-down"></i>
						</div>
					</div>
				 </div>
				 <?php endif; ?>
 			</div>
			 <?php endif; ?>
            <?= $product_categories?>
        </div>
    </div>
</div>
<script>
	$(".sort_page_filter").on('change', function(){
		var page_item   = $('#page_filter').val();
		var srt_item    = $('#srt_filter').val();
		var form_action = $('.paging-bl').attr('action');
		$.ajax({
			url: "<?= URL::Site('frontend/products/get_products_filter') ?>",
			type: "POST",
			data: {page_item : page_item ,sort_order : srt_item ,url :window.location.pathname },
			success: function(result){
				$('#product').html(result);
				$('.paging-bl').each(function()
				{
					$(this).attr('action', form_action);
				});
				$('#page_filter').val(page_item);
				$('#srt_filter').val(srt_item);
			}
		});
    });
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
		// Add infinite scrolling to the products feed
		$('#product').find('.pagedemo').infinite_scroll({
			footer: '#footer',
			feed_item: '#product .pagedemo > a, .thumb_product',
			ajax_url: '/frontend/products/ajax_get_products',
			custom_params: { url: window.location.pathname }
		});
	</script>
<?php endif; ?>
