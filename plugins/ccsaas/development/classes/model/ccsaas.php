<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Ccsaas extends Model
{
    const CENTRAL = 'CENTRAL';
    const BRANCH = 'BRANCH';
    const SELF = 'SELF';
    const LEAF = 'LEAF';

    public static function create_on_branch($website)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if (is_numeric($website)) {
            $website = new Model_Ccsaas_Websites($website);
            $website = $website->object();
        }
        $branch = new Model_Ccsaas_Branchservers($website['branch_server_id']);
        $branch = $branch->object();
        if ($branch) {
            $url = $branch['host'] . '/api/ccsaas/create_website';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $website);
            curl_setopt($curl, CURLOPT_URL, $url);
            $response = curl_exec($curl);
            $info = curl_getinfo($curl);
            $err = curl_error($curl);
            curl_close($curl);
        }
    }

    public static function update_on_branch($website)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if (is_numeric($website)) {
            $website = new Model_Ccsaas_Websites($website);
            $website = $website->object();
        }
        $branch = new Model_Ccsaas_Branchservers($website['branch_server_id']);
        $branch = $branch->object();
        if ($branch) {
            $url = $branch['host'] . '/api/ccsaas/update_website';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $website);
            curl_setopt($curl, CURLOPT_URL, $url);
            $response = curl_exec($curl);
            $info = curl_getinfo($curl);
            $err = curl_error($curl);
            curl_close($curl);
        }
    }

    public static function delete_from_branch($website)
    {
        if (is_numeric($website)) {
            $website = new Model_Ccsaas_Websites($website);
            $website = $website->object();
        }
        $branch = new Model_Ccsaas_Branchservers($website['branch_server_id']);
        $branch = $branch->object();
        if ($branch) {
            $url = $branch['host'] . '/api/ccsaas/delete_website';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $website);
            curl_setopt($curl, CURLOPT_URL, $url);
            $response = curl_exec($curl);
            $info = curl_getinfo($curl);
            $err = curl_error($curl);
            curl_close($curl);
        }
    }

    public static function create_vhost($params)
    {
        $params['conf_template_filename'] = Settings::instance()->get('ccsaas_vhost_conf_template');
        $vhost_dir = Settings::instance()->get('ccsaas_vhost_dir');
        $vhost = file_get_contents($params['conf_template_filename']);
        preg_match('#DocumentRoot\s+(.+)#', $vhost, $docroot);
        $docroot = trim($docroot[1], ' "');

        /*foreach ($params as $param => $value) {
            $vhost = str_replace('$' . $param . '$', $value, $vhost);
        }
        file_put_contents($vhost_dir . '/' . $params['HOSTNAME'] . '.conf', $vhost);*/



        $new_db_name = 'wms_';
        if (strpos($params['HOSTNAME'], '.test.ibplatform.ie')) {
            $new_db_name .= 'testing_';
        } else if (strpos($params['HOSTNAME'], '.uat.ibplatform.ie')) {
            $new_db_name .= 'staging_';
        } else if (Kohana::$environment == Kohana::PRODUCTION) {
            $new_db_name .= 'production_';
        } else if (Kohana::$environment == Kohana::STAGING) {
            $new_db_name .= 'staging_';
        } else {
            $new_db_name .= 'testing_';
        }

        $hostname = explode('.', str_replace('www.', '', $params['HOSTNAME']));
        $new_db_name .= $hostname[0];
        $db_exists = DB::query(
            Database::SELECT,
            'select * from information_schema.SCHEMATA where SCHEMA_NAME = "' . $new_db_name . '"'
        )
            ->execute()
            ->current();
        if (!$db_exists) {
            set_time_limit(0);
            ignore_user_abort(true);

            DB::query(null, "create database `" . $new_db_name . "`")->execute();
            $import_sql = $vhost_dir . '/' . $params['PROJECT_FOLDER'] . '.init.sql';
            if (file_exists($import_sql)) {
                $mysql_cfg = Kohana::$config->load('database')->default;
                $import_cmd = 'mysql -u"' . $mysql_cfg['connection']['username'] . '" -p"' . $mysql_cfg['connection']['password'] . '" ' . $new_db_name . ' < ' . $import_sql;
                exec($import_cmd, $import_output, $import_ret);
            }
        }
        $media_tgz = $vhost_dir . '/' . $params['PROJECT_FOLDER'] . '.init.tgz';
        if (file_exists($media_tgz)) {
            chdir($docroot);
            $media_dir = $docroot . '/www/shared_media/' . $hostname[0];
            mkdir($media_dir, 0777, true);
            chdir($media_dir);
            $extract_cmd = 'tar -xf ' . $media_tgz;
            exec($extract_cmd, $extract_output, $extract_ret);
        }

        file_put_contents($vhost_dir . '/json/' . $params['HOSTNAME'] . '.json', json_encode($params, JSON_PRETTY_PRINT));
    }

    public static function delete_vhost($hostname)
    {
        $vhost_dir = Settings::instance()->get('ccsaas_vhost_dir');
        unlink($vhost_dir . '/json/' . $hostname . '.json');
    }

    public static function get_settings_modes($selected_mode = null)
    {
        $options = array(
            '' => '',
            self::CENTRAL => __('Act as Central'),
            self::BRANCH => __('Act as Branch'),
            self::SELF => __('Self Manage'),
            self::LEAF => __('Leaf')
        );
        return html::optionsFromArray($options, $selected_mode);
    }

    public static function verify_hostname($hostname)
    {
        if (preg_match('/^([a-z0-9\-\_]+)(\.[a-z0-9\-\_]+)+$/', $hostname)) {
            return true;
        }
        return false;
    }

    public static function verify_project_folder($project_folder)
    {
        if (preg_match('/^[a-z0-9\-\_]+$/', $project_folder)) {
            return true;
        }
        return false;
    }

    public static function get_project_folders()
    {
        $project_folders = scandir(ENGINEPATH . '/projects');
        unset($project_folders[array_search('.', $project_folders)]);
        unset($project_folders[array_search('..', $project_folders)]);
        $project_folders = array_combine($project_folders, $project_folders);
        return $project_folders;
    }

    public static function get_unused_db_id($project_folder)
    {
        $next_unused_db = DB::select('dbs.*')
            ->from(array('plugin_ccsaas_databases', 'dbs'))
                ->join(array('plugin_ccsaas_websites', 'websites'), 'left')->on('dbs.id', '=', 'websites.database_id')
            ->where('websites.id', 'is', null)
            ->and_where('dbs.db_id', 'like', $project_folder . '_%')
            ->order_by('dbs.id')
            ->limit(1)
            ->execute()
            ->current();
        return $next_unused_db;
    }
}