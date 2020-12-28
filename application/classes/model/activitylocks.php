<?php defined('SYSPATH') or die('No direct script access.');

class Model_ActivityLocks extends Model
{
    const LOCKS_TABLE = 'engine_activity_locks';

    public static function lock($plugin, $activity)
    {
        //clear orphan locks
        $timeout = (int)Settings::instance()->get('engine_activity_lock_timeout');
        if ($timeout > 0) {
            DB::delete(self::LOCKS_TABLE)
                ->where('locked', '<=', date('Y-m-d H:i:s', time() - $timeout))
                ->execute();
        }

        $user = Auth::instance()->get_user();
        $userId = $user['id'];
        $session = session_id();


        // check if already locked
        $existingLock = DB::select('locks.*', 'users.email')
            ->from(array(self::LOCKS_TABLE, 'locks'))
                ->join(array(Model_Users::MAIN_TABLE, 'users'), 'left')
                    ->on('locks.locked_by', '=', 'users.id')
            ->where('locks.plugin', '=', $plugin)
            ->and_where('locks.activity', '=', $activity)
            ->execute()
            ->current();
        if ($existingLock && $existingLock['locked_by'] != $userId) {
            $result = array(
                'locked' => false,
                'locked_by' => $existingLock['email'],
                'time' => $existingLock['locked']
            );
        } else {
            if ($existingLock['locked_by'] == $userId) {
                DB::update(self::LOCKS_TABLE)
                    ->set(array('locked' => date('Y-m-d H:i:s')))
                    ->where('id', '=', $existingLock['id'])
                    ->execute();
                $result = array(
                    'locked' => $existingLock['id']
                );
            } else {
                try {
                    $lockId = DB::insert(self::LOCKS_TABLE,
                        array('plugin', 'activity', 'locked_by', 'locked', 'session'))
                        ->values(array($plugin, $activity, $userId, date('Y-m-d H:i:s'), $session))
                        ->execute();
                    $result = array(
                        'locked' => $lockId[0]
                    );
                } catch (Exception $exc) {
                    $result = array(
                        'locked' => false,
                    );
                }
            }
        }

        return $result;
    }

    public static function unlock($plugin, $activity)
    {
        //clear orphan locks
        $timeout = (int)Settings::instance()->get('engine_lock_timeout');
        if ($timeout > 0) {
            DB::delete(self::LOCKS_TABLE)
                ->where('locked', '<=', date('Y-m-d H:i:s', time() - $timeout))
                ->execute();
        }

        $user = Auth::instance()->get_user();
        $userId = $user['id'];
        DB::delete(self::LOCKS_TABLE)
            ->where('plugin', '=', $plugin)
            ->and_where('activity', '=', $activity)
            ->and_where('locked_by', '=', $userId)
            ->execute();
    }
}