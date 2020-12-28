<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Entity\Config;
use Ideabubble\Timesheets\Entity\Ref;
use Ideabubble\Timesheets\Entity\Staff;

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
            // todo: what if getItemId() returns array?
            $value = $this->configRepository->get($name, $item->getLevel(), $item->getItemId());
            if ($value !== null) {
                return $value->getValue();
            }
        }
        return null;
    }
    
    
    
    
    
}