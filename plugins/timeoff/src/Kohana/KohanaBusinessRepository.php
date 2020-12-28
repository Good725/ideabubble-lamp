<?php


namespace Ideabubble\Timeoff\Kohana;


use Ideabubble\Timeoff\BusinessRepository;
use Ideabubble\Timeoff\Dto\BusinessSearchDto;
use Ideabubble\Timeoff\Entity\Business;
use DB;
use Ideabubble\Timeoff\Exception;
use Ideabubble\Timeoff\Hydrator;

class KohanaBusinessRepository implements BusinessRepository
{
    
    private $hydrator;
    
    public function __construct()
    {
        $this->hydrator = new Hydrator();
        
    }

    public function findAll(BusinessSearchDto $dto)
    {
        $select = DB::select()->from('plugin_contacts3_contacts');
        if ($dto->id) {
            $select->and_where('id','=', $dto->id);
        }
        $select->and_where('type', '=', $this->typeByName('Business'));
        $select->order_by('id', 'asc');
        $rows = $select->as_object()->execute();
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->hydrator->hydrate(Business::class, [
                'id' => $item->id,
                'name' => $item->first_name,
            ]);
        }
        return $result;
    }
    
    private function typeByName($name)
    {
        $select = DB::select()->from('plugin_contacts3_contact_type');
        $select->where('label', '=', $name);
        $row = $select->as_object()->execute();
        if (empty($row[0])) {
            throw new Exception('Could not find type "' . $name . '" in table: plugin_contacts3_contact_type');
        }
        return $row[0]->contact_type_id;
        
    }
    
}