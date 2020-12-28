<?php

Class Model_Media_Upload extends ORM
{
    protected $_table_name = 'plugin_media_shared_media';

    public function get_url()
    {
        return Model_Media::get_path_to_id($this->id);
    }

    /**
     * Display the file size and an appropriate unit
     *
     * @param $format string - 'SI' or 'binary', whether to use 1024 or 1000 as the base
     * @return string
     */
    public function format_size($format = 'SI')
    {
        if ($this->size === null || $this->size == '') {
            return '';
        }
        else if (strtolower($format == 'binary')) {
            if ($this->size < 1024) {
                return $this->size.' B';
            } else if ($this->size < 1024 ^ 2) {
                // "KB" (uppercase "K") means 1024 bytes.
                return floor($this->size / 1024). ' KB';
            } else if ($this->size < 1024 ^ 3) {
                return floor($this->size / 1024 / 1024). ' MB';
            } else {
                return floor($this->size / 1024 / 1024 / 1024). ' GB';
            }
        }
        else {
            if ($this->size < 1000) {
                return $this->size.' B';
            } else if ($this->size < 1000 ^ 2) {
                // "kB" (uppercase "K") means 1000 bytes.
                return floor($this->size / 1000). ' kB';
            } else if ($this->size < 1000 ^ 3) {
                return floor($this->size / 1000 / 1000). ' MB';
            } else {
                return floor($this->size / 1000 / 1000 / 1000). ' GB';
            }
        }

    }
}
