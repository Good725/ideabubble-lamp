<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Notes extends Model
{
    const TABLE_TYPES = 'engine_notes_types';
    const TABLE_NOTES = 'engine_notes_notes';


    public static function get_type_id($type)
    {
        return DB::select('id')
            ->from(self::TABLE_TYPES)
            ->where('type', '=', $type)
            ->execute()
            ->get('id');
    }

    public static function create($type, $reference_id, $note, $user = null)
    {
        $data = array();
        $type_id = $type;
        if (!is_numeric($type)) {
            $type_id = self::get_type_id($type);
        }
        $data['type_id'] = $type_id;
        $data['reference_id'] = $reference_id;
        $data['note'] = $note;
        if ($user == null) {
            $user = Auth::instance()->get_user();
            $data['created_by'] = $user['id'];
        }
        $data['created'] = date::now();

        $inserted = DB::insert(self::TABLE_NOTES)->values($data)->execute();

        return $inserted;
    }

    public static function save($params, $unique_per_type = false, $user = null)
    {
        $data = arr::set($params, 'id', 'type_id', 'reference_id', 'note');
        if (isset($params['type'])) {
            $type_id = $params['type'];
            if (!is_numeric($params['type'])) {
                $type_id = self::get_type_id($params['type']);
            }
            $data['type_id'] = $type_id;
        }
        if ($user == null) {
            $user = Auth::instance()->get_user();
            $data['created_by'] = $user['id'];
        }
        $data['created'] = date::now();
        if ($unique_per_type && !isset($data['id'])) {
            $data['id'] = DB::select('id')
                ->from(self::TABLE_NOTES)
                ->where('type_id', '=', $data['type_id'])
                ->and_where('reference_id', '=', $data['reference_id'])
                ->execute()
                ->get('id');
        }

        if (is_numeric(@$data['id'])) {
            DB::update(self::TABLE_NOTES)->set($data)->where('id', '=', $data['id'])->execute();
        } else {
            $inserted = DB::insert(self::TABLE_NOTES)->values($data)->execute();
            $data['id'] = $inserted[0];
        }

        return $data;
    }

    public static function delete($id)
    {
        DB::update(self::TABLE_NOTES)->set(array('deleted' => 1))->where('id', '=', $id)->execute();
    }

    public static function search($params)
    {
        $selectq = DB::select(
            'notes.*',
            'types.type',
            DB::expr("CONCAT_WS(' ', users.name, users.surname) AS creator")
        )
            ->from(array(self::TABLE_NOTES, 'notes'))
                ->join(array(self::TABLE_TYPES, 'types'), 'inner')->on('notes.type_id', '=', 'types.id')
                ->join(array(Model_Users::MAIN_TABLE, 'users'), 'left')->on('notes.created_by', '=', 'users.id')
            ->where('notes.deleted', '=', 0);

        if (@$params['type']) {
            $selectq->and_where('types.type', '=', $params['type']);
        }

        if (@$params['type_id']) {
            $selectq->and_where('notes.type_id', '=', $params['type_id']);
        }

        if (@$params['reference_id']) {
            $ref_op = '=';
            if (is_array($params['reference_id'])) {
                $ref_op = 'in';
            }
            $selectq->and_where('notes.reference_id', $ref_op, $params['reference_id']);
        }

        if (@$params['id']) {
            $selectq->and_where('notes.id', '=', $params['id']);
        }

        $selectq->order_by('notes.created', 'desc');

        $notes = $selectq->execute()->as_array();
        return $notes;
    }

    public static function reference_join($select, $type, $table, $column)
    {

        $type_id = $type;
        if (!is_numeric($type)) {
            $type_id = self::get_type_id($type);
        }
        $select
            ->join(array(self::TABLE_NOTES, 'notes'), 'left')
                ->on($table . '.' . $column, '=', 'notes.reference_id')
                ->on('notes.type_id', '=', DB::expr($type_id))
            ->join(array(self::TABLE_TYPES, 'types'), 'left')->on('notes.type_id', '=', 'types.id');
    }
}