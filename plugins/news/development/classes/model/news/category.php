<?php defined('SYSPATH') or die('No direct script access.');

class Model_News_Category extends ORM
{
    protected $_table_name = 'plugin_news_categories';
    protected $_deleted_column = 'delete';

    protected $_has_many = [
        'items' => ['model' => 'News_Item', 'foreign_key' => 'category_id']
    ];
}