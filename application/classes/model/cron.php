<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 25/11/2014
 * Time: 12:31
 */
class Model_Cron extends Model implements Interface_Ideabubble
{
    /*** CLASS CONSTANTS ***/
    CONST CRON_TABLE            = 'engine_cron_tasks';
    CONST CRON_LOG				= 'engine_cron_log';
    CONST CRON_RUN_LEVEL        = Kohana::STAGING;
    CONST CRON_FREQUENCY_TABLE  = 'engine_cron_frequencies';
    CONST PUBLISH_COLUMN        = 'publish';
    CONST CRON_DELETE_COLUMN    = 'delete';
    CONST CRON_DELETE_DEFAULT   = 0;
    CONST VIEW_ANCHOR           = '/admin/settings/crontasks';
    CONST EDIT_ANCHOR           = '/admin/settings/manage_crontask/';
    CONST PLUGIN_FUNCTION_CALL  = 'cron';
    CONST TMP_CRON_TXT          = 'crontask.txt';


    /*** MEMBER DATA ***/
    private $id         = null;
    private $title      = '';
    private $frequency  = '';
    private $plugin_id  = null;
    private $action  = null;
    private $publish    = 1;
    private $delete     = self::CRON_DELETE_DEFAULT;
    private $send_email_on_complete = 0;
    private $extra_parameters = '';
    private $internal_only = false;

    private static $_IDENTIFIER_COLUMN = 'id';

    public static $cron_log_started = null;

    function __construct($id = null)
    {
        if(is_numeric($id))
        {
            $this->set_id($id);
            $this->get(true);
        }
    }

    public function set($data)
    {
        foreach($data as $key=>$value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function save()
    {
        try
        {
            Database::instance()->begin();
            self::_lock_for_write();
            if(is_numeric($this->id))
            {
                $this->_sql_update_cron_task();
            }
            else
            {
                $q = $this->_sql_insert_cron_task();
                $this->set_id($q[0]);
            }

            /*** CREATE CRONJOB ***/

            //$this->_update_crontab(URL::site(), $this->plugin_id, $this->action);

            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
        self::_unlock_tables();

        $this->update_crontab_for_hostname($_SERVER['HTTP_HOST']);
    }

    public function set_id($id = null)
    {
        $this->id = is_numeric($id) ? intval($id) : $this->id;
    }

    public function get($autoload)
    {
        $data = $this->_sql_load_cron_task();

        if($autoload)
        {
            $this->set($data);
        }

        return $data;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_publish()
    {
        return intval($this->publish);
    }

    public function get_send_email_on_complete()
    {
        return intval($this->send_email_on_complete);
    }

    public function get_frequency_id()
    {
        return $this->frequency_id;
    }

    public function get_frequency()
    {
        return $this->frequency;
    }

    public function get_plugin_id()
    {
        return $this->plugin_id;
    }

    public function get_action()
    {
        return $this->action;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_extra_parameters()
    {
        return $this->extra_parameters;
    }

    public function get_instance()
    {
        return array(
            'id' => $this->id,
            'title'         => $this->title,
            'frequency'  => $this->frequency,
            'plugin_id'     => $this->plugin_id,
            'action'     => $this->action,
            'publish'       => $this->publish,
            'delete'        => $this->delete,
            'send_email_on_complete' => $this->send_email_on_complete,
            'extra_parameters' => $this->extra_parameters,
            'internal_only' => $this->internal_only
        );
    }

    public function validate()
    {
        return true;
    }

    private function _sql_update_cron_task()
    {
        DB::update(self::CRON_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
    }

    private function _sql_insert_cron_task()
    {
        return DB::insert(self::CRON_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
    }

    private function _sql_load_cron_task()
    {
        $q = DB::select('id', 'title', 'frequency', 'plugin_id', 'action', 'publish', 'delete', 'send_email_on_complete', 'extra_parameters', 'internal_only')
            ->from(self::CRON_TABLE)
            ->where(self::CRON_DELETE_COLUMN, '=', self::CRON_DELETE_DEFAULT)
            ->and_where(self::$_IDENTIFIER_COLUMN,'=',$this->id)
            ->execute()
            ->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    private function create_crontask()
    {

    }

    private function _update_crontab($site_url,$plugin_id,$runnable_function)
    {
        $output = shell_exec('crontab -l');
        $plugin_name = Model_Plugin::get_plugin_by_id($plugin_id);
        $site_url_reg = str_ireplace('www.', '', $site_url);
        $site_url_reg = preg_replace('#(http|https)://#i','$1://(www.)?',$site_url_reg);
        $cronjob = '#(.+?) wget -O - '.$site_url_reg.preg_quote('frontend'.DIRECTORY_SEPARATOR.$plugin_name.DIRECTORY_SEPARATOR.$runnable_function,'#').' >\/dev\/null 2>&1#i';
        preg_match_all($cronjob, $output, $matches);
        if(count($matches) > 0)
        {
            $data = '';
            if(count($matches[0]) > 0)
            {
                $data = str_replace($matches[0],'',$output);
            }
            else
            {
                $data = $output;
            }
            if($this->publish){
                /** Add the new Cron Task **/
                $data.=$this->get_cron_time().' wget -O - '.$site_url.'frontend'.DIRECTORY_SEPARATOR.$plugin_name.DIRECTORY_SEPARATOR.$runnable_function.' >/dev/null 2>&1';
            }
            file_put_contents(Kohana::$cache_dir . '/' . self::TMP_CRON_TXT, $data.PHP_EOL);
        }

        exec('crontab ' . Kohana::$cache_dir . '/' . self::TMP_CRON_TXT);
        @unlink (Kohana::$cache_dir . '/' . self::TMP_CRON_TXT);
    }

    public function update_crontab_for_hostname($hostname)
    {
        $crontab = shell_exec('crontab -l');
        // cleanup old style cron entries
        //header('content-type: text/plain');echo $hostname . "\n";
        $rex = '#.*wget -O - (http|https)\://(www\.)?' . str_replace('.', '\\.', $hostname) . '.*#i';
        //echo $rex . "\n";
        $crontab = preg_replace($rex, '', $crontab);
        $crontab = preg_replace('#\n+#i', "\n", $crontab);
        $crontab .= "\n";
        //echo $crontab;

        // cleanup unpublished cron entries
        $rex = '#.+HTTP_HOST="' . str_replace('.', '\\.', $hostname) . '"' . '.+#i';
        //echo $rex . "\n";
        $crontab = preg_replace($rex, '', $crontab);
        $crontab = preg_replace('#\n+#i', "\n", $crontab);
        $crontab .= "\n";


        $all_tasks = self::get_all_crontasks();
        //print_r($all_tasks);
        $db_cfg = Kohana::$config->load('database')->default;
        //print_r($db_cfg);
        $environments = array(
            Kohana::PRODUCTION => 'PRODUCTION',
            Kohana::STAGING => 'STAGING',
            Kohana::TESTING => 'TESTING',
            Kohana::DEVELOPMENT => 'DEVELOPMENT',
        );

        $php_path = Settings::instance()->get('php_binary_path');
        $index_php_path = $_SERVER['SCRIPT_FILENAME'];
        $cmd_pre = 'dbhostname="' . $db_cfg['connection']['hostname'] . '" ' .
                'dbusername="' . $db_cfg['connection']['username'] . '" ' .
                'dbpassword="' . $db_cfg['connection']['password'] . '" ' .
                'HTTP_HOST="' . $hostname . '" ' .
                'KOHANA_ENV="' . $environments[Kohana::$environment] . '" ';


        foreach ($all_tasks as $task) {
            if ($task['publish'] == 1) {
                $times = json_decode($task['frequency'],true);

                /** Brute force through each time type and add to string accordingly. **/
                $minutes = count($times['minute']) > 0 ? implode(',',$times['minute']) : 0;
                $hours   = count($times['hour']) > 0 ? implode(',',$times['hour']) : 0;
                $day_of_month = count($times['day_of_month']) > 0 ? implode(',',$times['day_of_month']) : 0;
                $month = count($times['month']) > 0 ? implode(',',$times['month']) : 0;
                $day_of_week = count($times['day_of_week']) > 0 ? implode(',',$times['day_of_week']) : 0;

                $freq = $minutes.' '.$hours.' '.$day_of_month.' '.$month.' '.$day_of_week;


                $cmd = $freq . ' ' . $cmd_pre .
                    'REQUEST_URI="/frontend/' . $task['plugin_name'] . '/' . $task['action'] . '" ' .
                    $php_path . ' ' .
                    $index_php_path .
                    ($task['extra_parameters'] != '' ? ' ' . $task['extra_parameters'] : '');

                $crontab .= $cmd . "\n";
            }
        }

        file_put_contents(Kohana::$cache_dir . '/' . self::TMP_CRON_TXT, $crontab);
        exec('crontab ' . Kohana::$cache_dir . '/' . self::TMP_CRON_TXT, $output, $ret);
        //print_r($output);
        //echo $ret;
        //unlink (Kohana::$cache_dir . '/' . self::TMP_CRON_TXT);

        //echo "\n\n\n------------------\n" . $crontab;
        //exit;
    }

    private function get_cron_time()
    {
        $times = json_decode($this->frequency,true);

        /** Brute force through each time type and add to string accordingly. **/
        $minutes = count($times['minute']) > 0 ? implode(',',$times['minute']) : 0;
        $hours   = count($times['hour']) > 0 ? implode(',',$times['hour']) : 0;
        $day_of_month = count($times['day_of_month']) > 0 ? implode(',',$times['day_of_month']) : 0;
        $month = count($times['month']) > 0 ? implode(',',$times['month']) : 0;
        $day_of_week = count($times['day_of_week']) > 0 ? implode(',',$times['day_of_week']) : 0;

        return $minutes.' '.$hours.' '.$day_of_month.' '.$month.' '.$day_of_week;

    }

    /*** PUBLIC STATIC FUNCTIONS ***/

    public static function create($id = null)
    {
        return new self($id);
    }

    public static function get_all_crontasks()
    {
        return DB::select(
            't1.id',
            't1.title',
            't1.plugin_id',
            't1.publish',
            't1.delete',
            't1.frequency',
            't1.action',
            't1.send_email_on_complete',
            't1.extra_parameters',
            't1.internal_only',
            array('t3.friendly_name','plugin'),
            array('t3.name','plugin_name'),
            DB::expr('MAX(log.started) AS last_started')
        )
            ->from(array(self::CRON_TABLE,'t1'))
            ->join(array('engine_plugins','t3'),'LEFT')->on('t3.id','=','t1.plugin_id')
            ->join(array(self::CRON_LOG, 'log'), 'LEFT')->on('t1.id', '=', 'log.cron_id')
            ->where('t1.'.self::CRON_DELETE_COLUMN,'=',self::CRON_DELETE_DEFAULT)
            ->group_by('t1.id')
            ->order_by('t1.id', 'asc')
            ->execute()->as_array();
    }

    public static function get_all_frequencies()
    {
        return DB::select('id','frequency','publish','delete')->from(self::CRON_FREQUENCY_TABLE)->where('delete','=',0)->execute()->as_array();
    }

    /*** PRIVATE STATIC FUNCTIONS ***/

    private static function _lock_for_write()
    {
        DB::query(null,'SET AUTOCOMMIT = 0')->execute();
        DB::query(null,'LOCK TABLES ' . self::CRON_TABLE . ' WRITE, engine_plugins WRITE')->execute();
    }

    private static function _unlock_tables()
    {
        DB::query(NULL, 'UNLOCK TABLES')->execute();
        DB::query(NULL, 'SET AUTOCOMMIT = 1')->execute();
    }

    public static function record_activity($plugin)
    {
        $plugin_id = DB::select('id')->from('engine_plugins')->where('name', '=', $plugin)->execute()->get('id');
        $activity = new Model_Activity;
        $activity->set_action('run')->set_item_type('engine_cron')->set_item_id($plugin_id)->save();
    }

    public static function start_log($plugin, $action)
    {
        ob_start();
        Model_Cron::record_activity($plugin, $action);
        if(is_numeric($plugin)){
            $plugin_id = $plugin;
        } else {
            $plugin_id = DB::select('id')->from('engine_plugins')->where('name', '=', $plugin)->execute()->get('id');
        }
        $cron_id = DB::select('id')
            ->from(self::CRON_TABLE)
            ->where('plugin_id', '=', $plugin_id)
            ->and_where('action', '=', $action)
            ->execute()
            ->get('id');
        if ($cron_id)
        {
            $id = DB::insert(self::CRON_LOG, array('cron_id', 'started'))
                ->values(array($cron_id, date('Y-m-d H:i:s')))
                ->execute();
            self::$cron_log_started = $id[0];
        }
        else
        {
            self::$cron_log_started = FALSE;
        }
        return self::$cron_log_started;
    }

    public static function complete_log()
    {
        $output = ob_get_clean();
        echo $output;
        $id = self::$cron_log_started;
        $cronlog = DB::select('*')
            ->from(self::CRON_LOG)
            ->where('id', '=', $id)
            ->execute()
            ->current();
        $task = DB::select('*')
            ->from(self::CRON_TABLE)
            ->where('id', '=', $cronlog['cron_id'])
            ->execute()
            ->current();
        DB::update(self::CRON_LOG)
            ->set(array('finished' => date('Y-m-d H:i:s'), 'output' => $output))
            ->where('id', '=', $id)
            ->execute();
        if (strlen($output) > 10000) {
            $message = substr($output, 0, 1000) . "\n...\n" . substr($output, -1000);
        } else {
            $message = $output;
        }
        $cronEmail = Settings::instance()->get('cron_email');
        if ($cronEmail && $task['send_email_on_complete']) {
            $messaging = new Model_Messaging();
            $messaging->get_drivers();
            $messaging->send(
                'email',
                null,
                null,
                array(array('target_type' => 'EMAIL', 'target' => $cronEmail)),
                $message,
                'Cron task: ' . $task['title'] . ' has been executed'
            );
        }
        self::$cron_log_started = null;
    }

    public static function insert_log($plugin, $data)
    {
        if(is_numeric($plugin)){
            $pluginId = $plugin;
        } else {
            $pluginId = DB::select('id')->from('engine_plugins')->where('name', '=', $plugin)->execute()->get('id');
        }

        $cronId = DB::select('id')->from(self::CRON_TABLE)->where('plugin_id', '=', $pluginId)->execute()->get('id');
        if ($cronId) {
            $data['cron_id'] = $cronId;
            $id = DB::insert(self::CRON_LOG, array_keys($data))
                ->values($data)
                ->execute();
            return $id[0];
        }

        return false;
    }

    public static function get_logs($id, $limit = 10)
    {
        return DB::select('*')
            ->from(self::CRON_LOG)
            ->where('cron_id', '=', $id)
            ->order_by('id', 'desc')
            ->limit($limit)
            ->execute()
            ->as_array();
    }

    public static function getAvailableCronActions()
    {
        $plugins = Model_Plugin::get_all();
        $actions = array();
        foreach ($plugins as $plugin) {
            $controllerName = 'Controller_Frontend_' . ucfirst($plugin['name']);
            if (class_exists($controllerName)) {
                foreach (get_class_methods($controllerName) as $action) {
                    if (strpos($action, 'action_cron') !== false) {
                        $action = substr($action, 7);
                        $actions[$plugin['name']][$action] = $action;
                    }
                }
            }
        }
        return $actions;
    }
}