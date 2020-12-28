<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Lookup extends ORM
{
    // Tables
    const LOOKUP_FIELDS = 'engine_lookup_fields';
    const MAIN_TABLE = 'engine_lookup_values';

    protected $_table_name = self::MAIN_TABLE;
    protected $_publish_column = 'public';
    protected $_date_created_column = 'created';
    protected $_date_modified_column = 'updated';
    protected $_created_by_column = 'autor';
    protected $_modified_by_column = 'autor';


    public static function lookupList($field = null, $args = [])
    {
        $q = DB::select(
            array('fields.name', 'fname'),
            'values.*',
            array('user.name', 'author')
        )
            ->from(array(self::LOOKUP_FIELDS, 'fields'))
                ->join(array(self::MAIN_TABLE, 'values'), 'INNER')
                    ->on('values.field_id', '=', 'fields.id')
                ->join(array(Model_Users::MAIN_TABLE, 'user'), 'LEFT')
                    ->on('user.id', '=', 'values.autor');
        if ($field) {
            if (is_numeric($field)) {
                $q->where('values.field_id', '=', $field);
            } else {
                $q->where('fields.name', '=', $field);
            }
        }

        if (isset($args['public'])) {
            $q->where('public', '=', $args['public']);
        }

        $result = $q
            ->order_by('fields.name')
            // Order alphabetically, with "Other" at the end
            ->order_by(DB::expr("IF(`values`.`label` = 'Other', 1, 0)"), 'asc')
            ->order_by('values.label')
            ->execute()
            ->as_array();
        return $result;
    }

    public static function lookupLoad($lookup_id)
    {
        $result = DB::select(
            array('fields.name', 'fname'),
            'values.*',
            array('user.name', 'author')
        )
        ->from(array(self::LOOKUP_FIELDS, 'fields'))
            ->join(array(self::MAIN_TABLE, 'values'), 'INNER')
                ->on('values.field_id', '=', 'fields.id')
            ->join(array(Model_Users::MAIN_TABLE, 'user'), 'LEFT')
                ->on('user.id', '=', 'values.autor')
        ->where('values.id', '=', $lookup_id)
        ->execute()
        ->current();

        return $result;
    }

    public static function cloneLookup($id)
    {
        $lookup = Model_Lookup::lookupLoad($id);

        if (!empty($lookup)) {
            $values = array(
                'field_id' => $lookup['field_id'],
                'label' => $lookup['label'],
                'value' => $lookup['value'],
                'is_default' => 0,
                'created' => $lookup['created'],
                'updated' => $lookup['updated'],
                'autor' => $lookup['autor'],
                'public' => 0
            );
            self::create_lookup($values);
        }
    }

    public static function makeUniqueValue($id)
    {
        $newValue = DB::select(
            array(DB::expr('MAX(lookups.value) + 1'), 'new_value')
        )
        ->from(array(self::MAIN_TABLE, 'lookups'))
        ->execute()
        ->current();

        $params['value'] = $newValue['new_value'];
        self::update_lookup($params, $id);
    }

    public static function deleteLookup($id)
    {
        DB::delete(self::MAIN_TABLE)->where('id', '=', $id)->execute();
    }

    public static function create_lookup($params)
    {
        // this line is needed to keep only only default lookup for each field_id group
        if (array_key_exists('is_default', $params))
            DB::update(self::MAIN_TABLE)->set(array('is_default' => 0))->where('field_id', '=', $params['field_id'])->execute();

        $query = DB::insert(self::MAIN_TABLE,array_keys($params))->values($params)->execute();
        return $query;
    }

    public static function update_lookup($params, $id)
    {
        // this line is needed to keep only only default lookup for each field_id group
        if (array_key_exists('is_default', $params))
            DB::update(self::MAIN_TABLE)->set(array('is_default' => 0))->where('field_id', '=', $params['field_id'])->execute();

        $query = DB::update(self::MAIN_TABLE)->set($params)->where('id', '=', $id)->execute();
        return $query;
    }

    /*
     * get All lookup Field names
     * from engine_fields
     * @return array
    */
    public static function get_lookup_field_names()
    {
        $result = DB::select('*')
            ->from(self::LOOKUP_FIELDS)
            ->execute()
            ->as_array();
        return $result;
    }

    public static function get_label($field, $value)
    {
        $label = DB::select('values.label')
            ->from(array(self::LOOKUP_FIELDS, 'fields'))
                ->join(array(self::MAIN_TABLE, 'values'), 'INNER')->on('values.field_id', '=', 'fields.id')
            ->where('fields.name', '=', $field)
            ->and_where('values.value', '=', $value)
            ->execute()
            ->get('label');
        return $label;
    }
}
