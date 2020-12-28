<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Franchisee extends ORM
{
    public static function get_provider($user_id)
    {
        $provider = DB::select('*')
            ->from(Model_Providers::TABLE_PROVIDERS)
            ->where('franchisee_id', '=', $user_id)
            ->execute()
            ->current();
        return $provider;
    }
}
