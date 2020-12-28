<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Entity\Config;

interface ConfigRepository
{
    /**
     * @param $name
     * @param $level
     * @param $itemId
     * @return Config
     */
    public function get($name, $level, $itemId);
    public function set(Config $config);
    
}