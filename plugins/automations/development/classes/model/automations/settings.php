<?php
class Model_Automations_Settings
{
    public static function get_enabled_triggers()
    {
        $triggers = array_keys(
            DB::select('trigger_name')->from(Model_Automations::TABLE_TRIGGER_ENABLE)->execute()->as_array('trigger_name')
        );

        return $triggers;
    }

    public static function save_enabled_triggers($names)
    {
        DB::delete(Model_Automations::TABLE_TRIGGER_ENABLE)->execute();
        foreach ($names as $name) {
            DB::insert(Model_Automations::TABLE_TRIGGER_ENABLE)
                ->values(array('trigger_name' => $name))
                ->execute();
        }
    }
}