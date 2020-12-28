<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\ConfigRepository;
use Ideabubble\Timesheets\Entity\Config;
use DB;
use Ideabubble\Timesheets\Entity\Ref;
use Ideabubble\Timesheets\EventDispatcher;
use Ideabubble\Timesheets\Hydrator;

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
        if (is_array($itemId)) {
            $select->and_where('item_id', 'in', $itemId);
        } else {
            $select->and_where('item_id', '=', $itemId);
        }
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