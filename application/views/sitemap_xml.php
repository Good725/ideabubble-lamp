<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 27/09/2013
 * Time: 08:50
 * To change this template use File | Settings | File Templates.
 */
//check if models exist in engine or project folders (weak but a start to avoid loading errors)
$productpresent = Kohana::find_file('classes', 'model/product');
$articlepresent = Kohana::find_file('classes', 'model/article');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <?//display pages sitemap list
    echo Model_Pages::get_pages_xml_sitemap();

    //display news sitemap list
    echo Model_News::get_news_xml_sitemap();

    //display products (if engine plugin present) sitemap list
    if (!empty($productpresent)){
       echo Model_Product::get_products_xml_sitemap();
    }
    //display articles (if engine plugin present) sitemap list
    if (!empty($articlepresent)){
       echo Model_Article::get_articles_xml_sitemap();
    }
    ?>
</urlset>