<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Assets extends Controller
{
    // Get static assets
    public function action_static()
    {
        $file_path = PROJECTPATH.'www/assets/'.$this->request->param('theme').'/'.$this->request->param('filepath');

        // Return a 404, if the asset does not exist
        if (!file_exists($file_path)) {
            http_response_code(404);
            die();
        }

        // Set the mime type depending on the extension
        $extension = array_pop(explode('.', $this->request->param('filepath')));
        switch (strtolower($extension)) {
            case 'css' : $mime_type = 'text/css'; break;
            case 'js'  : $mime_type = 'application/javascript'; break;
            default    : $mime_type = mime_content_type($file_path); break;
        }
        $this->response->headers('Content-Type', $mime_type);

        // Return the contents of the asset
        $this->auto_render = false;
        echo file_get_contents($file_path);
    }

    // Render a theme's CSS as a CSS page
    public function action_theme_css()
    {
        $this->auto_render = false;
        $theme_name = $this->request->param('id');
        $this->response->headers('Content-Type', 'text/css');

        $theme = ORM::factory('Engine_Theme')->where('stub', '=', $theme_name)->find_published();

        echo $theme->get_parsed_styles();
    }

    public function action_mysqldump()
    {
        $this->auto_render = false;

        $allowed_ips = Settings::instance()->get('engine_db_transfer_allow_ips');
        $allowed_ips = preg_replace('/\s+/', '', $allowed_ips);
        $allowed_ips = explode(',', $allowed_ips);
        $_SERVER['REMOTE_ADDR'];
        if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
            $this->response->status(403);
            return;
        }
        $cfg = Kohana::$config->load('database')->default;
        $cfg['connection']['hostname'];
        $cfg['connection']['username'];
        $cfg['connection']['password'];
        $dir = Kohana::$cache_dir;
        chdir($dir);
        if (stripos($_SERVER['HTTP_HOST'], '.uat.')) {
            DB::query(null, "update engine_settings set value_test=value_stage")->execute();
        } else {
            if (stripos($_SERVER['HTTP_HOST'], '.test.') === false) {
                DB::query(null, "update engine_settings set value_test=value_live")->execute();
                DB::query(null, "update engine_settings set value_stage=value_live")->execute();
                DB::query(null, "update engine_settings set value_dev=value_live")->execute();
            }
        }
        $file = $dir . '/' . $cfg['connection']['database'] . '-' . date('YmdHis') . '.sql';
        //$cmd = 'mysqldump -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" --ignore-table=' . $cfg['connection']['database'] . '.engine_settings ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" > ' . $file;
        $cmd = 'mysqldump -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" > ' . $file;
        set_time_limit(0);
        exec($cmd, $output);
        $cmdgz = "gzip " . $file;
        exec($cmdgz, $outputgz);
        $size = filesize($file . ".gz");

        //echo $cmd;exit;
        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment; filename="' . basename($file . ".gz") . '"');
        header('Content-Length:' . $size);
        readfile($file . ".gz");
        @unlink($file . ".gz");
        @unlink($file);
        exit;
    }

    public function action_cron_mysqltransfer()
    {
        Model_DALM::_acquire_lock();
        $cache_dir = Kohana::$cache_dir . '/' . $_SERVER['HTTP_HOST'] . '/dalm';
        if(file_exists($cache_dir)) { // force dalm on next run
            @unlink($cache_dir . '/engine');
            @unlink($cache_dir . '/project');
        }

        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain');
        $settings = Settings::instance();
        $settings_to_keep = $settings->get('engine_db_transfer_exclude_settings');
        $settings_to_keep = preg_split('/\s*,\s*/', $settings_to_keep);
        $settings_variables = array();
        foreach ($settings_to_keep as $variable) {
            $settings_variables[$variable] = $settings->get($variable);
        }

        $url = $this->request->query('url');
        if ($url == '') {
            $url = trim(Settings::instance()->get('engine_db_transfer_from_url'));
        }
        if ($url == '') {
            echo "no transfer url set";
            return;
        }
        $dir = Kohana::$cache_dir;
        $cfg = Kohana::$config->load('database')->default;
        chdir($dir);
        $file = $dir . '/' . $cfg['connection']['database'] . '-' . date('YmdHis') . '.sql';
        $content = file_get_contents($url);
        file_put_contents($file . '.gz', $content);
        unset($content);
        $cmdgz = "gzip -d " . $file . '.gz';
        exec($cmdgz, $outputgz);
        set_time_limit(0);
        DB::query(null, "set SESSION interactive_timeout=3600")->execute();
        DB::query(null, "set SESSION wait_timeout=3600")->execute();
        $tables = DB::query(Database::SELECT, "select * from information_schema.`TABLES` t where t.TABLE_SCHEMA='" . $cfg['connection']['database'] . "'")->execute()->as_array();
        DB::query(null, "set SESSION foreign_key_checks=0")->execute();
        foreach ($tables as $table) {
            //DB::query(null, "drop database `" . $cfg['connection']['database'] . "`")->execute();
            //DB::query(null, "create database `" . $cfg['connection']['database'] . "`")->execute();
            if ($table['TABLE_NAME'] == 'engine_settings') {
                continue;
            }
            if ($table['TABLE_TYPE'] == 'BASE TABLE') {
                DB::query(null, "drop table if exists " . $table['TABLE_NAME'])->execute();
            }
            if ($table['TABLE_TYPE'] == 'VIEW') {
                DB::query(null, "drop view if exists " . $table['TABLE_NAME'])->execute();
            }
        }
        DB::query(null, "drop table if exists old_settings")->execute();
        DB::query(null, "create table old_settings like engine_settings")->execute();
        DB::query(null, "insert into old_settings (select * from engine_settings)")->execute();
        DB::query(null, "drop table if exists engine_settings")->execute();

        $cmd = 'mysql -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" < ' . $file;
        exec($cmd, $output);
        $cmd = 'mysql -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" -e "update engine_settings inner join old_settings on engine_settings.variable = old_settings.variable set engine_settings.value_test=old_settings.value_test"';
        exec($cmd, $output1);
        $cmd = 'mysql -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" -e "update engine_settings inner join old_settings on engine_settings.variable = old_settings.variable set engine_settings.value_dev=old_settings.value_dev"';
        exec($cmd, $output2);
        $cmd = 'mysql -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" -e "update engine_settings inner join old_settings on engine_settings.variable = old_settings.variable set engine_settings.value_stage=old_settings.value_stage"';
        exec($cmd, $output3);
        $cmd = 'mysql -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" -e "drop table if exists old_settings"';
        exec($cmd, $output4);
        $cmd = 'mysql -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" -e "update plugin_messaging_drivers set is_default=\'NO\'"';
        exec($cmd, $output4);
        $cmd = 'mysql -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" -e "update plugin_messaging_drivers set is_default=\'YES\' WHERE provider=\'null\'"';
        exec($cmd, $output4);

        unlink($file);

        echo "imported to " . $cfg['connection']['database'] . ' from ' . $url;

        Model_DALM::_release_lock();
    }
}