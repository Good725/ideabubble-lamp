<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Entity\Config;
use Ideabubble\Timeoff\Entity\Ref;
use Ideabubble\Timeoff\Entity\Staff;

class ConfigService
{
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    
    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }
    
    /**
     * @param $name
     * @param $levels Ref[]
     * @return mixed|null
     */
    public function get($name, $levels)
    {
        foreach ($levels as $item) {
            $value = $this->configRepository->get($name, $item->getLevel(), $item->getItemId());
            if ($value !== null) {
                return $value->getValue();
            }
        }
        return null;
    }


    public function getx($name, $levels)
    {
        foreach ($levels as $item) {
            $config = \DB::select('*')
                ->from('plugin_timeoff_config')
                ->and_where('item_id', '=', $item->getItemId())
                ->and_where('name', '=', $name)
                ->execute()
                ->current();
            if ($config !== null) {
                return $config;
            }
        }
        return null;
    }
    
    
}