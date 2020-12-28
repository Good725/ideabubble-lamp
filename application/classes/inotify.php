<?php
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