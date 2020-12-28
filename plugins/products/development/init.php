<?php
$mysql_version = explode('.', DB::select(DB::expr('@@version'))->execute()->get('@@version'));
if ($mysql_version[0] > 5 || $mysql_version[1] >= 6) {
    try {
        if (count(DB::query(1, "SHOW INDEX IN plugin_products_product WHERE Index_type='FULLTEXT'")->execute()->as_array()) == 0) {
            DB::query(null, 'ALTER IGNORE TABLE `plugin_products_product` ADD FULLTEXT INDEX (`title`, `product_code`)')->execute();
        }
    } catch (Exception $exc) {
    }
}
unset($mysql_version);
?>