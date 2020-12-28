<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Linkchecker extends Model
{

    private static $tablesToCheck = array();

    public static function addTable($name, $idColumn, $dataColumn, $editLink)
    {
        self::$tablesToCheck[] = array(
            'name' => $name,
            'idColumn' => $idColumn,
            'dataColumn' => $dataColumn,
            'editLink' => $editLink
        );
    }

    public static function getLinks()
    {
        $links = array();
        foreach (self::$tablesToCheck as $table) {
            $rows = DB::select($table['idColumn'], $table['dataColumn'])
                ->from($table['name'])
                ->execute()
                ->as_array();
            //print_R($rows);
            foreach ($rows as $row) {
                $urls = array();
                preg_match_all('#(https|http://)([a-z0-9\.\-\_]+)(/[^\s\\"\\\']+)?#i', $row[$table['dataColumn']], $urls);
                if (count($urls[0])) {
                    $links[] = array(
                        'table' => $table['name'],
                        'id' => $row[$table['idColumn']],
                        'column' => $table['dataColumn'],
                        'data' => $row[$table['dataColumn']],
                        'urls' => $urls[0],
                        'edit' => $table['editLink']
                    );
                }
            }
        }
        return $links;
    }

    public static function checkUrl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $result = array();
        $result['data'] = curl_exec($curl);
        $result['error'] = curl_error($curl);
        $result['info'] = curl_getinfo($curl);
        curl_close($curl);
        return $result;
    }

    public static function getInternalUrls($pages = true, $frontendActions = true, $adminActions = true)
    {
        $plugins = DB::select(DB::expr('distinct plugins.*'))
            ->from(array(Model_Plugin::MAIN_TABLE, 'plugins'))
                ->join(array(Model_Plugin::PLUGINS_PER_ROLE_TABLE, 'has'), 'INNER')
                    ->on('plugins.id', '=', 'has.plugin_id')
            ->where('has.enabled', '=', 1)
            ->order_by('name')
            ->execute()
            ->as_array();
        $urls = array();

        if (class_exists('Model_Pages') && $pages) {
            $pages = DB::select('*')
                ->from(Model_Pages::PAGES_TABLE)
                ->where('deleted', '=', 0)
                ->execute()
                ->as_array();
            foreach ($pages as $page) {
                $urls[] = '/' . $page['name_tag'];
            }

            $urls[] = null;
        }

        if ($frontendActions) {
            foreach ($plugins as $plugin) {
                if (class_exists('controller_frontend_' . $plugin['name'])) {
                    $methods = get_class_methods('controller_frontend_' . $plugin['name']);
                    foreach ($methods as $method) {
                        if (strpos($method, 'action_') !== false) {
                            $urls[] = '/frontend/' . $plugin['name'] . '/' . substr($method, strlen('action_'));
                        }
                    }
                }
            }
            $aliases = Controller_Page::getActionAliases();
            foreach ($aliases as $url => $alias) {
                $urls[] = $url;
            }

            $urls[] = null;
        }

        if ($adminActions) {
            foreach ($plugins as $plugin) {
                if (class_exists('controller_admin_' . $plugin['name'])) {
                    $methods = get_class_methods('controller_admin_' . $plugin['name']);
                    foreach ($methods as $method) {
                        if (strpos($method, 'action_') !== false) {
                            $urls[] = '/admin/' . $plugin['name'] . '/' . substr($method, strlen('action_'));
                        }
                    }
                }
            }
        }

        return $urls;
    }

    public static function replace($from, $to)
    {
        foreach (self::$tablesToCheck as $table) {
            DB::query(null, "UPDATE `" . $table['name'] . "` SET `" . $table['dataColumn'] . "` = REPLACE(`" . $table['dataColumn'] . "`, " . Database::instance()->quote($from) . ", " . Database::instance()->quote($to) . ")")
                ->execute();
        }
    }
}