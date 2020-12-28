<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\DepartmentRepository;
use Ideabubble\Timesheets\Dto\DeptSearchDto;
use Ideabubble\Timesheets\Entity\Department;

use DB;
use Ideabubble\Timesheets\Exception;
use Ideabubble\Timesheets\Hydrator;

class KohanaDepartmentRepository implements DepartmentRepository
{
    private $hydrator;
    
    public function __construct()
    {
        $this->hydrator = new Hydrator();
        
    }
    
    /**
     * @param DeptSearchDto $dto
     * @return Department[]
     * @throws Exception
     * @throws \ReflectionException
     */
    public function findAll(DeptSearchDto $dto)
    {
        $select = DB::select()->from('plugin_contacts3_contacts');
        if ($dto->id) {
            $select->and_where('id','=', $dto->id);
        } else {
            $select->and_where('type', '=', $this->typeByName('Department'));
        }
        $select->order_by('id', 'asc');
        $rows = $select->as_object()->execute();
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->hydrator->hydrate(Department::class, [
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