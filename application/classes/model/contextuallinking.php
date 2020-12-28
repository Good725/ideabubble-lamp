<?php

/**
 * Created by PhpStorm.
 * User: sargs
 * Date: 2/16/2017
 * Time: 11:15 AM
 */
class Model_ContextualLinking extends Model
{

    private static $model_table_references = "engine_contextual_linking_references";
    private static $model_contextual_linking_data = "engine_contextual_linking_data";
    private static $src_pk_col_name = "src_id";
    private static $dst_pk_col_name = "dst_id";
    private static $src_type_col_name = "src_type";
    private static $dst_type_col_name = "dst_type";
    private static $dst_table_alias = "dst_table_alias";


    public static function getTableId($table_name)
    {

        $rows = DB::select('id')
            ->from(self::$model_table_references)
            ->where('table_name', '=', ':table_name')
            ->param(':table_name', $table_name)
            ->execute()
            ->as_array();
        return $rows[0]['id'];

    }


    public static function addObjectLinking($src_obj_pk, $src_obj_type, $dst_obj_pk, $dst_obj_type)
    {
        $references = DB::select('*')
            ->from(self::$model_table_references)
            ->where('id', 'IN', array($src_obj_type, $dst_obj_type))
            ->execute()
            ->as_array();

        if ($src_obj_type == $dst_obj_type && $src_obj_pk == $dst_obj_pk) {
            throw new Exception('your are trying to link object to itself.');

        }
        if ($src_obj_type != $dst_obj_type && count($references) != 2) {
            throw new Exception('both $src_obj_type and $dst_obj_type need to have corresponding records in engine_contextual_linking_references table');
        }

        // check if link does not exists in table
        $subquery = DB::select('*')->from(self::$model_contextual_linking_data)
            ->where(self::$src_pk_col_name, '=', $src_obj_pk)
            ->and_where(self::$src_type_col_name, '=', $src_obj_type)
            ->and_where(self::$dst_pk_col_name, '=', $dst_obj_pk)
            ->and_where(self::$dst_type_col_name, '=', $dst_obj_type)
            ->execute()
            ->as_array();

        if (count($subquery) == 0 ) {
            DB::insert(self::$model_contextual_linking_data, array( self::$src_pk_col_name, self::$src_type_col_name, self::$dst_pk_col_name, self::$dst_type_col_name ))
                ->values(array( $src_obj_pk, $src_obj_type, $dst_obj_pk, $dst_obj_type ))
                ->execute();
        }
    }

    /**
     * @param $src_obj_pk this is id of the object you want to get childes for
     * @param $src_obj_type this is type oif the object registered in engine_contextual_linking_references
     * @param $dst_obj_type this is type of the destination object. basically you are selecting all childes of src object of this type.
     * @param int $limit regular mysql limit
     * @param int $offset regular mysql offset
     * @return mixed  returns an array
     * @throws Exception
     */
    public static function getAllLinkedObjectsOfType($src_obj_pk, $src_obj_type, $dst_obj_type, $limit = 100, $offset = 0)
    {

        $references = DB::select('*')
            ->from(self::$model_table_references)
            ->where('id', 'IN', array($src_obj_type, $dst_obj_type))
            ->execute()
            ->as_array();

        if ($src_obj_type != $dst_obj_type && count($references) != 2) {
            throw new Exception('unable to find corresponding records of  $src_obj_type and $dst_obj_type  in engine_contextual_linking_references table');
        }

        $src_object_table_name = null;
        $src_object_col_name = null;
        $dst_object_table_name = null;
        $dst_object_col_name = null;
        foreach ($references as $ref) {
            if ($ref['id'] == $src_obj_type) {
                $src_object_table_name = $ref['table_name'];
                $src_object_col_name = $ref['link_column_name'];
            }

            if ($ref['id'] == $dst_obj_type) {
                $dst_object_table_name = $ref['table_name'];
                $dst_object_col_name = $ref['link_column_name'];
            }
        }

        if (empty($src_object_table_name) || empty($dst_object_table_name) || empty($src_object_col_name) || empty($dst_object_col_name)) {
            throw new Exception("empty table name or column name values for one of  $src_obj_type,  $src_obj_type records in engine_contextual_linking_references");
        }


        $query = DB::select("$dst_object_table_name.*")
            ->from(self::$model_contextual_linking_data)
            ->join($dst_object_table_name)
            ->on($dst_object_table_name . '.' . $dst_object_col_name, '=', self::$model_contextual_linking_data . '.' . self::$dst_pk_col_name)
            ->where(self::$model_contextual_linking_data . '.' . self::$src_pk_col_name, '=', DB::expr($src_obj_pk))
            ->where(self::$model_contextual_linking_data . '.' . self::$src_type_col_name, '=', DB::expr($src_obj_type))
            ->where(self::$model_contextual_linking_data . '.' . self::$dst_type_col_name, '=', $dst_obj_type);


        $query->limit($limit)->offset($offset);

        $result = $query->execute()->as_array();

        return $result;
    }

    /**
     * deletes ObjectLinking
     * @param $src_obj_pk
     * @param $src_obj_type
     * @param $dst_obj_pk
     * @param $dst_obj_type
     */
    public static function deleteObjectLinking($src_obj_pk, $src_obj_type, $dst_obj_pk, $dst_obj_type)
    {
        DB::delete(self::$model_contextual_linking_data)
            ->where(self::$src_pk_col_name, '=', $src_obj_pk)
            ->and_where(self::$src_type_col_name, '=', $src_obj_type)
            ->and_where(self::$src_type_col_name, '=', $src_obj_type)
            ->and_where(self::$dst_pk_col_name, '=', $dst_obj_pk)
            ->and_where(self::$dst_type_col_name, '=', $dst_obj_type)
            ->execute();
    }

}