<?php

class Model_Content_Progress extends ORM
{
    protected $_table_name = 'plugin_content_progress';

    protected $_belongs_to = [
        'content'  => ['model' => 'Content', 'foreign_key' => 'content_id'],
        'section'  => ['model' => 'Content', 'foreign_key' => 'section_id'],
        'user'     => ['model' => 'User',    'foreign_key' => 'user_id'],
    ];


}