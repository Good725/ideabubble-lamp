<?php
class apachectl
{
    public static $pidfile = '/var/run/httpd/httpd.pid';
    public static $vhost_dir = '/etc/httpd/vhosts.d';
    public static $sample_vhost = '/etc/httpd/vhosts.d/vhost.sample';

    /*
     * does not work due to operation system permissions
     * keep just for reference
     */
    public static function graceful($pidfile = null)
    {
        if ($pidfile == null) {
            $pidfile = self::$pidfile;
        }

        $pid = trim(file_get_contents($pidfile));
        return posix_kill($pid, SIGUSR1);
    }

    /*
     * does not work due to operation system permissions
     * keep just for reference
     */
    public static function gracefulx()
    {

        $cmd = Kohana::$cache_dir . '/graceful';
        $output = array();
        $ret = null;
        //echo $cmd;
        exec($cmd, $output, $ret);

        $output = implode("\n", $output);
        if ($ret == 0) {
            return true;
        }
        return false;
    }

    public static function configtest()
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
}

