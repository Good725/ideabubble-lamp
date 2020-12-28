<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 27/09/2013
 * Time: 08:50
 * To change this template use File | Settings | File Templates.
 */

//display pages sitemap list
echo Model_Pages::get_pages_html_sitemap();

//display news sitemap list if enabled only
if (Model_Plugin::get_isplugin_enabled_foruser('2','news')){
    echo Model_News::get_news_html_sitemap();
}

//display products (if enabled) sitemap list
if (Model_Plugin::get_isplugin_enabled_foruser('2','products')){
    echo Model_Product::get_products_html_sitemap();
}

//display articles only if enabled
if (Model_Plugin::get_isplugin_enabled_foruser('2','articles')){
    echo Model_Article::get_articles_html_sitemap();
}



