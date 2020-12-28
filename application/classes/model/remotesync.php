<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Remotesync extends Model
{
    const SYNC_TABLE = 'engine_remote_sync';

    public static $tables = array(
    );

    public static $table_ids = array(
    );

    public function get_object_synced($type, $id, $id_type = 'cms')
    {
        $selectq = DB::select('sync.*')
            ->from(array(self::SYNC_TABLE, 'sync'))
            ->join(array(self::$tables[$type], 'cms_table'), 'inner')
            ->on('sync.cms_id', '=', 'cms_table' . '.' . self::$table_ids[$type])
            ->and_where('sync.type', '=', $type)
            ->and_where('sync.delete' , '=', 0);
        if ($id_type == 'remote') {
            $selectq->and_where('sync.remote_id', '=', $id);
        } else {
            $selectq->and_where('sync.cms_id', '=', $id);
        }
        $selectq->order_by('sync.id', 'desc');
        $exists = $selectq->execute()->current();
        return $exists;
    }

    public function delete_object_synced($type, $id, $id_type = 'cms') {
        $query = DB::update(self::SYNC_TABLE)->set(array('delete' => 1))
            ->where('type', '=', $type);
        if ($id_type == 'remote') {
            $query->and_where('remote_id', '=', $id);
        } else {
            $query->and_where('cms_id', '=', $id);
        }
        $query->execute();
        return true;
    }

    public function save_object_synced($type, $remote_id, $cms_id)
    {
        $exists = DB::select('*')
            ->from(self::SYNC_TABLE)
            ->where('remote_id', '=', $remote_id)
            ->and_where('cms_id', '=', $cms_id)
            ->and_where('type', '=', $type)
            ->and_where('delete', '=', 0)
            ->execute()
            ->current();
        if ($exists) {
            $update_data = ['cms_id' => $cms_id, 'synced' => date('Y-m-d H:i:s')];

            // If the remote ID is being blanked, soft delete the association.
            if ($remote_id) {
                $update_data['remote_id'] = $remote_id;
            } else {
                $update_data['delete'] = 1;
            }

            DB::update(self::SYNC_TABLE)
                ->set($update_data)
                ->where('id', '=', $exists['id'])
                ->where('delete', '=', 0)
                ->execute();
            $id = $exists['id'];
        } else {
            // Only perform the insert if an ID is specified
            if ($remote_id) {
                $inserted = DB::insert(self::SYNC_TABLE)
                    ->values(array(
                        'synced' => date('Y-m-d H:i:s'),
                        'remote_id' => $remote_id,
                        'cms_id' => $cms_id,
                        'type' => $type
                    ))
                    ->execute();
                $id = $inserted[0];
            } else {
                $id = null;
            }
        }

        return $id;
    }

    public function clear($type)
    {
        DB::delete(self::SYNC_TABLE)->where('type', '=', $type)->execute();
    }
}