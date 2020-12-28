<?php
$product_name = str_replace(' ', '-', trim($title));
$add_products_page = rtrim($_SERVER['REQUEST_URI'], '/');
if(strpos($_SERVER['REQUEST_URI'], Model_Product::get_products_plugin_page()) == false){
    $add_products_page = '/'.Model_Product::get_products_plugin_page();
}
?>

