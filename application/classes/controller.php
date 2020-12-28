<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller extends Kohana_Controller
{
    protected $autoLogCron = true;
    public static $form_saved = false;
    protected $allow_script_actions = null;
    protected $safe_input = true;

    public function check_script($val)
    {
        if (stripos($val, '<script') !== false && stripos($val, '</script') !== false) {
            $this->safe_input = false;
        }
        if (preg_match('/(onclick|onfocus|onblur|onmouseover|onmousemove|onmouseleave|onkeypress|onkeyup|onkeydown|onerror|onsuccess|onload|onunload|onsubmit)\=/i', $val)) {
            $this->safe_input = false;
        }
    }

    //no input should ever have <script></script>
    public function prevent_script_tag()
    {
        $action = $this->request->action();
        //if some actions need html input. allow it
        if ($this->allow_script_actions !== null)
        if (in_array($action, $this->allow_script_actions)) {
            return;
        }
        array_walk_recursive($_REQUEST, array($this, 'check_script'));
        if ($this->safe_input == false){
            $error_id = Model_Errorlog::save(null, "SECURITY");
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/');
        }

    }

    public function before()
    {
        header('X-Frame-Options: SAMEORIGIN');
        $this->response->headers('X-Frame-Options', 'SAMEORIGIN');
        header('Cache-control: no-store');
        header('Pragma: no-cache');
        $this->response->headers('Cache-control', 'no-store');
        $this->response->headers('Pragma', 'no-cache');
        
        $this->prevent_script_tag();
        Kohana::$config->load('auth')->idle_time = 86400;
        parent::before();
        $end = 'frontend';
        if (preg_match('#^/admin/#i', @$_SERVER['REQUEST_URI'])) {
            $end = 'backend';
        }
        if (preg_match('#^/admin/#i', @$_SERVER['REQUEST_URI'])) {
            $end = 'api';
        }
        if ($end == 'frontend') {
            if (Request::$current->method() == 'POST' && self::$form_saved == false){
                Model_Errorlog::save(null, 'FORM');
                self::$form_saved = true;
            }
        }
        if (class_exists('Model_Ipwatcher')) {
            if(!Model_Ipwatcher::is_ignored($_SERVER['REQUEST_URI']))
            if (Settings::instance()->get('ipwatcher_' . $end. '_active') == 1) {
                if (!Model_Ipwatcher::save_check()) {
                    header('HTTP/1.0 403 Forbidden');
                    exit();
                }
            }
        }

        if ($this->autoLogCron && preg_match('#frontend/(.*?)/(.*?cron[^/]*)/?#i', @$_SERVER['REQUEST_URI'], $cron_match)) {
            try {
                if (!Model_Cron::$cron_log_started) {
                    Model_Cron::start_log($cron_match[1], $cron_match[2]);
                }
            } catch (Exception $exc) {
            }
        }

        if (in_array(Request::$current->controller(), array('settings', 'usermanagement', 'calendars', 'lookup', 'eprinter'))){
            $ma = MenuArea::factory();
            $ma->unregister_link('all');
            if (Request::$current->controller() == 'settings' && Request::$current->action() == 'index') {
                $settings = new Model_Settings();
                $setting_forms = $settings->build_form();
                foreach ($setting_forms as $group => $setting_form) {
                    $ma->register_link('settings?' . http_build_query(array('group' => $group)), $group);
                }
            } else {
                //$ma->register_link('settings', 'Settings');
                $ma->register_link('settings/activities', 'Activities');

                // Temporary to not show these links on staging server
                if (Kohana::$environment !== Kohana::STAGING AND Auth::instance()->check_for_super_level() == 'TRUE') {
                    $ma->register_link('settings/debug_kohana', 'Kohana');
                    $ma->register_link('settings/debug_system', 'Debug');
                }
                $ma->register_link('settings/show_logs', 'App Logs');
                $ma->register_link('settings/dbsync', 'DB Sync');
                $ma->register_link('calendars/index', 'Calendar');
                $ma->register_link('settings/crontasks', 'Cron');
                $ma->register_link('settings/csv', 'CSV');
                $ma->register_link('settings/list_logs', 'DALM');
                $ma->register_link('settings/manage_feeds', 'Feeds');
                if (Model_Plugin::get_isplugin_enabled_foruser('current', 'formbuilder')) {
                    $ma->register_link('formbuilder', 'Formbuilder');
                }
                $ma->register_link('settings/ipwatcher_log', 'IP Watcher');
                $ma->register_link('settings/keyboardshortcuts', 'Keyboard Shortcuts');
                $ma->register_link('settings/logs', 'Logs');
                $ma->register_link('settings/errorlogs', 'Errors');
                $ma->register_link('settings/labels', 'Labels');
                $ma->register_link('settings/layouts', 'Layouts');
                $ma->register_link('settings/localisation_config', 'Localisation');
                $ma->register_link('settings/redirects', 'Redirects');
                $ma->register_link('lookup', 'Lookups');
                $ma->register_link('eprinter', 'Printers');
                $ma->register_link('styleguide', 'Styleguide');
                $ma->register_link('settings/templates', 'Templates');
                $ma->register_link('settings/themes', 'Themes');
                $ma->register_link('settings/website', 'Website');
            }
        }
    }

    public function after()
    {
        if (isset(Model_Cron::$cron_log_started)) {
            if ($this->autoLogCron && Model_Cron::$cron_log_started) {
                Model_Cron::complete_log();
            }
        }
        parent::after();
    }

    public function is_external_referer()
    {
        if (@$_SERVER['HTTP_REFERER'] != '') {
            if (strpos($_SERVER['HTTP_REFERER'], 'http://' . $_SERVER['HTTP_HOST']) !== 0 &&
                strpos($_SERVER['HTTP_REFERER'], 'https://' . $_SERVER['HTTP_HOST']) !== 0)
            {
                return true;
            }
        }
        return false;
    }
}
