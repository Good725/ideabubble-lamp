/*
ts:2016-05-19 22:17:00
*/

UPDATE `plugin_reports_reports`
  SET
  `sql` = 'select \n		c.category,\n		p.title,\n		p.url_title,\n		concat(\'/products.html/\',replace(c.category, \' \', \'-\'), \'/\', p.url_title) as `relative_url`,\n		p.brief_description,\n		replace(replace(p.description, \'<\', \'<\'), \'>\', \'>\') as `description`,\n		p.seo_title,\n		p.seo_keywords,\n		p.seo_description,\n		p.seo_footer ,\n		group_concat(concat(\'<a href=\"/shared_media/ambersos/media/photos/products/\', i.file_name, \'\">\', i.file_name,\'</a><br />\'), \"\\n\") as images,\np.price\n	from plugin_products_product p\n		left join plugin_products_product_categories pc on p.id = pc.product_id\n		left join plugin_products_category c on pc.category_id = c.id\n		left join plugin_products_product_images i on p.id = i.product_id\n	group by p.id\n	order by c.category,p.title',
  `php_modifier` = '$baseCurrency = Settings::instance()->get(\'currency_base\');\nif ($baseCurrency && class_exists(\'Model_Currency\')) {\n    $sql = $this->_sql;\n\n    $rates = Model_Currency::getRates();\n    if (isset($rates[\'GBP\']) && $baseCurrency != \'GBP\') {\n        $sql = str_replace(\"p.price\", \"p.price as `Price(\" . $baseCurrency . \")`, round(p.price * \" . sprintf(\"%.5f\", $rates[\'GBP\']) . \", 2) as `Price(GBP)`\", $sql);\n    } else {\n        $sql = str_replace(\"p.price\", \"p.price as Price(\" . $baseCurrency . \")\", $sql);\n    }\n\n    $this->_sql = $sql;\n}\n',
  `date_modified` = NOW()
  WHERE `name` = 'Products SEO Report';

