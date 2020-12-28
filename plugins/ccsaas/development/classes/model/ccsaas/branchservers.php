<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Ccsaas_Branchservers extends Orm
{
    const HOSTS_TABLE = 'plugin_ccsaas_branch_servers';

    protected $_table_name = 'plugin_ccsaas_branch_servers';

    protected $_belongs_to = [

    ];

    public static function get_all_options()
    {
        $websites_ = DB::select('*')
            ->from(self::HOSTS_TABLE)
            ->order_by('host')
            ->execute()
            ->as_array();
        $websites = array();
        foreach ($websites_ as $website) {
            $websites[$website['id']] = $website['host'];
        }
        return $websites;
    }
}