<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Lookup_Field extends ORM
{
    protected $_table_name = 'engine_lookup_fields';

    protected $_has_many = [
        'lookups' => ['model' => 'Lookup', 'foreign_key' => 'field_id']
    ];
}