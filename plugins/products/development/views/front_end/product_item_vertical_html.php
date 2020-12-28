<?php
$product_url = ($url_title != '') ? $url_title : str_replace(' ', '-', trim($title));
$add_products_page = rtrim($_SERVER['REQUEST_URI'], '/');
if(strpos($_SERVER['REQUEST_URI'], Model_Product::get_products_plugin_page()) == false){
    $add_products_page = '/'.Model_Product::get_products_plugin_page();
}
?>

<div class="left product_item_list detailed">
    <div class="left product_item_list_image">
        <?php $images[0] = ( ! isset($images[0]) OR empty($images[0])) ? 'not_image_available.jpg' : $images[0]; ?>
        <a class="product-image-link" href="<?=$add_products_page.'/'.$product_url?>">
            <img alt="<?= __($title) ?>" src="<?= HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[0], Model_Product::MEDIA_IMAGES_FOLDER)) ?>" />
        </a>
    </div>
    <div class="left product_item_list_info">
        <h2 class="product_item_list_title"><a title="View: '<?=__($title)?>' details" href="<?=$add_products_page.'/'.$product_url ?>"><?= $truncated_title ?></a></h2>
        <div class="product_item_description">
            <p><?=($brief_description)?></p>
        </div>
        <div class="product_item_bottom">
            <?php if (Settings::instance()->get('product_enquiry') != 1): ?>
                <div class="product_item_list_price">
                    <?php
                    if($offer_price < $price AND $display_offer == '1'){
                        echo '<span class="grey">Was €<span class="line-through">'.$price.'</span></span> <span class="yellow strong">NOW €'.$offer_price.'</span>';
                    }
                    else{
                        echo '<span class="yellow strong">€'.$price.'</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
            <div class="product_item_view_link">
                <a title="View product: '<?= __($title) ?>' details" href="<?=$add_products_page.'/'.$product_url?>">View full details</a>
            </div>

        </div>

    </div>
</div>
