<!-- products-breadcrumbs -->
<?php
$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;

$ok             = ($disable_purchase != 1 AND Settings::instance()->get('product_enquiry') != 1);
$stock_disabled = (Settings::instance()->get('stock_enabled') != 'TRUE');
$in_stock       = ((Settings::instance()->get('stock_enabled') == 'TRUE' AND ((isset($options) AND count($options) > 0) OR (isset($quantity) AND isset($quantity_enabled) AND $quantity_enabled == '1'))));
$in_stock       = ($in_stock XOR $stock_disabled);
$ok             = ($ok AND $in_stock);
if(Settings::instance()->get('stock_enabled') == 'TRUE')
{
    if(Settings::instance()->get('override_stock') == 'TRUE' AND !((isset($options) AND count($options) > 0) OR (isset($quantity) AND isset($quantity_enabled) AND $quantity_enabled == '1')))
    {
        $ok = false;
    }
}
//$ok = $ok AND ((Settings::instance()->get('override_stock') != 'TRUE' AND Settings::instance()->get('stock_enabled') == 'TRUE') XOR (Settings::instance()->get('stock_enabled') != 'TRUE'));
?>
<div id="breadcrumb-nav">
    <?=IbHelpers::breadcrumb_navigation();?>
</div>
<form id="ProductDetailsForm" name="ProductDetailsForm">
    <div id="product">
        <div id="product_image">
            <?php
            ( ! isset($images[0]) OR empty($images[0])) ? $images[0] = 'no_image_available.jpg' : NULL;
            echo '<img src="',Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[0], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/')),'" class="left" id="prodImage_0" alt="',$images[0],'">';

            ?>
            <div class="proThum-area left">
                <?php
                for($i = 1; $i < count($images); $i++){
                    $third_img = '';
                    if($i % 3 == 0){
                        $third_img = ' third_img';
                    }
                    echo '<img onclick="imageTrick(\'',$i,'\')" src="',
                    Model_Media::get_path_to_media_item_admin(
                        Kohana::$config->load('config')->project_media_folder,$images[$i],
                        rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/').'/_thumbs'
                    ),'" alt="',$images[$i],'" class="productAltImage left',$third_img,'" id="prodImage_',$i,'">';
                }
                ?>
            </div>
        </div>
        <div id="product_details">
			<?php $purchase_enabled = (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1); ?>
			<?php if ($purchase_enabled OR Settings::instance()->get('purchase_disabled_show_prices')): ?>
                <div id="product_price">
                    <?php if ($display_offer != 0): ?>
                        <dl>
                            <dt>Price</dt>
                            <dd><s>&nbsp;&euro;<?= $price ?>&nbsp;</s></dd>
                            <dt>Offer Price</dt>
                            <dd id="final_price" data-product_price="<?= $price ?>">&euro;<?= $offer_price ?></dd>
                        </dl>
                    <?php else: ?>
                        <dl>
                            <dt>Price</dt>
                            <dd id="final_price" data-product_price="<?= $price ?>">&euro;<?= $price ?></dd>
                        </dl>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div id="product_description">
                <h1>Product Details</h1>
				<?php if (isset($brief_description) AND $brief_description != ''): ?>
					<div class="product_description_text product_brief_description_text">
						<p><?= $brief_description ?></p>
					</div>
				<?php endif; ?>

                <div class="product_description_text">
                    <?=$description?>
                </div>
                <?php
                $option_groups = Model_Option::get_all_groups_by_product($id);
                foreach($option_groups AS $index => $option_group)
                {
                    if($option_group['option_group'] == "Text")
                    {
                        echo '<input type="text" name="text_input" class="option_text_input validate[required]"/>';
                        $option_group_shot_name = explode(" ",$option_group['option_group']);
                        $options = array();
                    }
                    else
                    {
                        $option_group_shot_name = explode(" ",$option_group['option_group']);
                        if(Settings::instance()->get('stock_enabled') == "TRUE")
                        {
                            if($option_group['is_stock'] == "1")
                            {
                                $options = Model_Option::get_stock_options($option_group['option_group'],$id);
                            }
                            else
                            {
                                $options = Model_Option::get_options_by_field('group', $option_group['option_group']);
                            }
                        }
                        else
                        {
                            $options = Model_Option::get_options_by_field('group', $option_group['option_group']);
                        }
                    }
                    echo '<label for="',$option_group['option_group'],'" class="text_label">',(($option_group['group_label'] !== NULL AND !empty($option_group['group_label'])) ? $option_group['group_label'] : $option_group_shot_name[0]),': </label>';
                    echo '<div class="prod_option_item left">';
                    echo '<select id="option_',$index,'" name="',$option_group['option_group'],'" class="prod_option';
                    if (!empty($option_group['required'])){
                        echo ' validate[required]';
                    }
                    echo '" onchange="updateProductOptionPrice(this,'.$id.')">';
                    echo '<option value="">Choose...</option>';
                    foreach($options as $option)
                    {
                        $label = ((float)$option['price'] > 0) ? $option['label'] ." + &euro;". $option['price'] : $option['label'];
                        echo '<option value="',$option['id'],'" data-option_price="'.$option['price'].'"'.(($option['default'] == 1) ? ' selected="selected"' : '').'>',$label, '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                    echo '<div class="notice"></div>';
                }
                ?>

                <?php if($ok): ?>
                    <label for="qty" class="text_label left clear_left grey">Quantity:</label>
                    <select id="qty" name="qty" style="width:50px;" class="left">
                        <?php
                        $quantity_dropdown = (isset($quantity_enabled) AND $quantity_enabled == '1' AND count($options) == 0 AND $quantity < 11) ? ($quantity+1): 11;
                        for($i = 1;$i < $quantity_dropdown;++$i):
                        ?>
                        <option><?=$i;?></option>
                        <?php
                        endfor;
                        ?>
                    </select>
                <?php endif; ?>

				<?php if (isset($tags) AND (count($tags) > 0)): ?>
					<div class="product_tags">
						<h4>Tags</h4>
						<ul>
							<?php foreach ($tags as $tag): ?>
								<li><?= $tag['title'] ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
            </div>
            <div class="out_of_stock" style="display:none;">
                This product/option combination is out of stock.
            </div>

            <?php
            $url_parts    = explode('/', trim($_SERVER['SCRIPT_URL'], '/'));
            $product_name = end($url_parts);
            ?>

            <?php if($ok): ?>
                <div id="product_purchase">
                    <button type="button" id="add_to_cart_button" onclick="validateQty(<?=$id?>, $('#qty').val(), getOptions());">
                        Add to Cart &raquo;
                    </button>
                    <button type="button" id="purchase_button" onclick="validateQty_and_checkout(<?=$id?>, $('#qty').val(), getOptions());">
                        Buy Now &raquo;
                    </button>
                    <?php if(in_array($assets_folder_path, array(24, 25))){ ?>
                        <div id="contact_us_button"><a href="<?=URL::site()?>contact-us.html?pid=<?= $id ?>&pname=<?= $product_name ?>">Contact Us</a></div>
                    <?php } ?>
                    <div id="continue_shopping">
						<?php
						$continue_shopping_url = Session::instance()->get('last_product_browsing_url');
						$continue_shopping_url = ($continue_shopping_url == '') ? '/products.html' : $continue_shopping_url;
						?>
                        <a href="<?= $continue_shopping_url ?>">Continue Shopping &raquo;</a>
                    </div>
                    <br />
                    <div class="successful"></div>
                </div>
            <?php else: ?>
                <?php if ( ! $in_stock): ?>
                    <div class="notice notice_bad out_of_stock_notice" style="display:block;">Sorry, this product is out of stock.</div>
                <?php endif; ?>

				<?php if (in_array($assets_folder_path, array(24, 25))): ?>
					<div id="contact_us_button"><a href="<?=URL::site()?>contact-us.html?pid=<?= $id ?>&pname=<?= $product_name ?>">Contact Us</a></div>
				<?php endif; ?>
            <?php endif; ?>

            <div id="product_social_media">
                <div class="fb-like"
                     data-href="<?= substr(URL::site(),0,-1).$_SERVER["REQUEST_URI"] ?>"
                     data-width="280"
                     data-height="22"
                     data-colorscheme="light"
                     data-layout="button"
                     data-action="like"
                     data-show-faces="false"
                     data-send="false"></div>

                <? if (strpos($_SERVER['HTTP_HOST'],'tadghoflynn') !== FALSE): ?>
                    <!-- AddThis Button BEGIN -->
                    <div class="addthis_toolbox addthis_default_style">
                        <a href="https://www.addthis.com/bookmark.php?v=250&amp;username=ra-537f53e21efc17e5" class="addthis_button_compact">Share</a>
                        <span class="addthis_separator">&#124;</span>
                        <a class="addthis_button_preferred_2"></a>
                        <a class="addthis_button_preferred_3"></a>
                        <a class="addthis_button_preferred_4"></a>
                    </div>
                    <script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#username=ra-537f53e21efc17e5"></script>
                    <!-- AddThis Button END -->
                <?php endif; ?>
            </div>

            <?php if(!in_array($assets_folder_path, array(24, 25))){ ?>
                <div id="contact_us_button"><a href="<?=URL::site()?>contact-us.html?pid=<?= $id ?>&pname=<?= $product_name ?>">Contact Us</a></div>
            <?php } ?>
        </div>

        <?php if (isset($related_to) AND count($related_to) > 0): ?>
            <div id="products_related">
                <h2>Related Products</h2>
                <?= Model_Product::render_related_products_html($related_to); ?>
            </div>
        <?php endif; ?>
        <div id="youtube_videos">
            <?php
            $product = new Model_Product($id);
            $videos = $product->get_youtube_videos();
            if(count($videos) > 0)
            {
                echo '<h1>Product Videos</h1>';
            }
            foreach($videos AS $key=>$video):
                ?>
                <div class="youtube_video left">
                    <object width="240" height="158" data="https://www.youtube.com/v/<?=$video['video_id'];?>" type="application/x-shockwave-flash"><param name="allowFullScreen" value="true"/><param name="src" value="https://www.youtube.com/v/<?=$video['video_id'];?>"/></object>
                </div>
            <?php
            endforeach;
            ?>
        </div>
    </div>
    <?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
    <script type="text/javascript" src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/product_details.js"></script>
    <script type="text/javascript" src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/checkout.js"></script>

</form>

<?php if (isset($reviews)): ?>
	<?= View::factory('front_end/list_product_reviews')->set('reviews', $reviews) ?>
	<?= View::factory('front_end/add_product_review')->set('product_id', $id) ?>
<?php endif; ?>