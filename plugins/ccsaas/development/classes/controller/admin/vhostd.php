#!/usr/bin/php
<?php
/*
 * run this script as root on command line
 * it will run "apachectl graceful" whenever a file is written/deleted/moved in vhost directory
 * */
$GLOBALS['vhosts_d'] = '/etc/httpd/vhosts.d';

class inotify
{
    protected $fd;
    protected $wd_list = array();

    public function __construct()
    {
        $this->fd = inotify_init();
    }

    public function __destruct()
    {
        fclose($this->fd);
    }

    public function add_watch($path, $mask)
    {
        $wd = inotify_add_watch($this->fd, $path, $mask);
        if ($wd) {
            $this->wd_list[] = $wd;
        }
        return $wd;
    }

    public function remove_watch($wd)
    {
        if (inotify_rm_watch($this->fd, $wd)) {
            $index = array_search($wd, $this->wd_list);
            if ($index !== false) {
                unset($this->wd_list[$index]);
            }
            return true;
        }
        return false;
    }

    public function read()
    {
        return inotify_read($this->fd);
    }

    public function set_blocking($mode)
    {
        stream_set_blocking($this->fd, $mode);
    }

    public function queue_len()
    {
        return inotify_queue_len($this->fd);
    }
}

function configtest()
{
    $cmd = 'apachectl configtest 2>&1';
    $output = array();
    $ret = null;

    exec($cmd, $output, $ret);
    $output = implode("\n", $output);
    if ($ret == 0 && $output == 'Syntax OK') {
        return true;
    }
    return false;
}

function verify_hostname($hostname)
{
    if (preg_match('/^([a-z0-9\-\_]+)(\.[a-z0-9\-\_]+)+$/', $hostname)) {
        return true;
    }
    return false;
}

function verify_project_folder($project_folder)
{
    if (preg_match('/^[a-z0-9\-\_]+$/', $project_folder)) {
        return true;
    }
    return false;
}

function delete_vhost($json_file)
{
    $json_file = basename($json_file);
    $conf_file = str_replace('.json', '.conf', $json_file);
    //@unlink($GLOBALS['vhosts_d'] . '/json/' . $json_file);
    unlink($GLOBALS['vhosts_d'] . '/conf/' . $conf_file);
}

function create_vhost($json_file)
{
    $json_file = basename($json_file);
    $parameters = @json_decode(file_get_contents($GLOBALS['vhosts_d'] . '/json/' . $json_file), true);
    if (!$parameters) {
        return false;
    }
    $vhost = file_get_contents($GLOBALS['vhosts_d'] . '/' . basename($parameters['conf_template_filename']));
    if (verify_hostname($parameters['HOSTNAME'])) {
        $vhost = str_replace('$HOSTNAME$', $parameters['HOSTNAME'], $vhost);
    } else {
        return false;
    }
    if (verify_project_folder($parameters['PROJECT_FOLDER'])) {
        $vhost = str_replace('$PROJECT_FOLDER$', $parameters['PROJECT_FOLDER'], $vhost);
    } else {
        return false;
    }
    if (strpos($parameters['HOSTNAME'], '.uat.ibplatform.ie')) {
        $vhost = str_replace("SetEnv KOHANA_ENV TESTING", "SetEnv KOHANA_ENV STAGING", $vhost);
        $vhost = str_replace("testing/engine", "uat/engine", $vhost);
    }
    $vhost = str_replace('$VHOST_DB_ID$', $parameters['VHOST_DB_ID'], $vhost);
    $conf_file = str_replace('.json', '.conf', $json_file);
    file_put_contents($GLOBALS['vhosts_d'] . '/conf/' . $conf_file, $vhost);
    return true;
}

function listen()
{
    set_time_limit(0);
    $listener = new inotify();
    $listener->add_watch($GLOBALS['vhosts_d'] . '/json', IN_CLOSE_WRITE | IN_DELETE | IN_MOVED_TO | IN_MOVED_FROM);
    $ld = fopen('/var/log/vhosts.php.log', 'ac+');

    while (true) {
        $actions = $listener->read();
        if ($actions) {
            foreach ($actions as $action) {
                //echo $action['mask'] . ":";
                //echo $action['name'] . "\n";
                if ($action['mask'] == IN_CLOSE_WRITE || $action['mask'] == IN_MOVED_TO) {
                    create_vhost($action['name']);
                    fwrite($ld, "create vhost " . $action['name'] . " : ");
                } else if ($action['mask'] == IN_DELETE || $action['mask'] == IN_MOVED_FROM) {
                    delete_vhost($action['name']);
                    fwrite($ld, "delete vhost " . $action['name'] . " : ");
                }
            }
            $output = array();
            $ret = null;
            if (configtest()) {
                fwrite($ld, "Syntax Ok\n");
                exec("apachectl graceful", $output, $ret);
                $output = implode("\n", $output);
                //echo $output . "\n";
                //echo $ret . "\n";
            }
        }
    }
}

if (array_key_exists('d', getopt('d'))) { // run like vhosts.d -d ; continue in background
//run in background
    $child_pid = pcntl_fork();
    if ($child_pid == -1) {
        echo "Error\n";
    } else {
        if ($child_pid) {
            //parent exit;
        } else {
            fclose(STDIN);
            fclose(STDOUT);
            fclose(STDERR);
            listen();
        }
    }
} else {
    listen();
}
