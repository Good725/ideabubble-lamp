<?php


namespace Ideabubble\Timeoff\Kohana;


use Ideabubble\Timeoff\ConfigRepository;
use Ideabubble\Timeoff\Entity\Config;
use DB;
use Ideabubble\Timeoff\Entity\Ref;
use Ideabubble\Timeoff\EventDispatcher;
use Ideabubble\Timeoff\Hydrator;

class KohanaConfigRepository implements ConfigRepository
{

    private $hydrator;
    /**
     * @var EventDispatcher
     */
    private $dispatcher;
    
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->hydrator = new Hydrator();
        $this->dispatcher = $dispatcher;
    }
    
    public function get($name, $level, $itemId)
    {
        $select = DB::select()->from('plugin_timeoff_config');
        $select->and_where('name','=', $name);
        $select->and_where('level','=', $level);
        $select->and_where('item_id','=', $itemId);
        $rows = $select->as_object()->execute();
        $result = null;
        if (count($rows) > 0) {
            $result = $this->hydrator->hydrate(Config::class, [
                'name' => $rows[0]->name,
                'value' => $rows[0]->value,
                'ref' => new Ref($rows[0]->level, $rows[0]->item_id)
            ]);
        }
        return $result;
    }
    
    public function set(Config $config)
    {
        // TODO: Implement set() method.
    }
    
}