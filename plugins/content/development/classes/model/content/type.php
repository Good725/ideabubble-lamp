<?php

class Model_Content_Type extends ORM
{
    protected $_table_name = 'plugin_content_types';

    protected $_has_many = array(
        'content' => ['model' => 'Content', 'foreign_key' => 'type_id'],
    );
}