<?php


namespace Ideabubble\Timeoff\Kohana;


use Ideabubble\Timeoff\Dto\StaffSearchDto;
use Ideabubble\Timeoff\Entity\DeptAssignment;
use Ideabubble\Timeoff\Entity\Staff;

use DB;
use Ideabubble\Timeoff\EventDispatcher;
use Ideabubble\Timeoff\Hydrator;
use Ideabubble\Timeoff\StaffRepository;

class KohanaStaffRepository implements StaffRepository
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
    
    public function findAll(StaffSearchDto $dto)
    {
        $select = $this->searchQuery($dto);
        $rows = $select->as_object()->execute();
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->hydrator->hydrate(Staff::class, [
                'id' => $item->id,
                'firstName' => $item->first_name,
                'lastName' => $item->last_name,
                'userId' => $item->linked_user_id,
                'assignments' => $this->getDeptAssignments($item->id)
            ]);
        }
        return $result;
    }
    
    public function count(StaffSearchDto $dto)
    {
        $select = $this->searchQuery($dto, [DB::expr('COUNT(*) as cnt')]);
        $select->offset(null);
        $select->limit(null);
        $row = current($select->execute()->as_array());
        return $row['cnt'];
    }
    
    
    private function searchQuery(StaffSearchDto $dto, $columns = null)
    {
        $select = DB::select_array($columns)
            ->from('plugin_contacts3_contacts')
            ->where('plugin_contacts3_contacts.delete', '=', 0);
        //$select->where('linked_user_id', 'is not', null);

        if (!empty($dto->id))  {
            $select->and_where('id','=', $dto->id);
        }
        if (!empty($dto->userId))  {
            $select->and_where('linked_user_id','=', $dto->userId);
        }
        if (!empty($dto->departmentId))  {
            $select->join(['plugin_contacts3_relations', 'ds'], 'left')->on('ds.child_id','=','plugin_contacts3_contacts.id');
            $select->and_where('ds.parent_id','=', $dto->departmentId);
            if (!empty($dto->role)) {
                $select->and_where('ds.role','=', $dto->role);
            }
        }
        if (!empty($dto->businessId))  {
            $select->join(['plugin_contacts3_relations', 'ds1'], 'inner')->on('ds1.child_id','=','plugin_contacts3_contacts.id');
            $select->join(['plugin_contacts3_relations', 'ds2'], 'inner')->on('ds2.child_id','=','ds1.parent_id');
            $select->and_where('ds2.parent_id','=', $dto->businessId);
            if (!empty($dto->role)) {
                $select->and_where('ds.role','=', $dto->role);
            }
        } else {
            if ($dto->onlyStaff) {
                $select->join(['plugin_contacts3_relations', 'ds1'], 'inner')->on('ds1.child_id','=','plugin_contacts3_contacts.id');
                $select->join(['plugin_contacts3_relations', 'ds2'], 'inner')->on('ds2.child_id','=','ds1.parent_id');
            }
        }
    
        if (!empty($dto->offset)) {
            $select->offset($dto->offset);
        }
        if (!empty($dto->limit)) {
            $select->limit($dto->limit);
        }
    
        if (!empty($dto->orderBy)) {
            $select->order_by($dto->orderBy, $dto->orderDir);
        }

        return $select;
        
    }
    
    /**
     * @param $staffId
     * @return DeptAssignment[]
     * @throws \ReflectionException
     */
    private function getDeptAssignments($staffId)
    {
        $select = DB::select()->from('plugin_contacts3_relations');
        $select->where('child_id', '=', $staffId);
        $rows = $select->as_object()->execute();
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->hydrator->hydrate(DeptAssignment::class, [
                'departmentId' => $item->parent_id,
                'staffId' => $item->child_id,
                'role' => $item->role,
                'position' => $item->position,
            ]);
        }
        return $result;
        
    }
    
    public function update(Staff $staff)
    {
        $delete = DB::delete('plugin_contacts3_relations');
        $delete->where('child_id', '=', $staff->getId());
        $delete->execute();
        foreach ($staff->getAssignments() as $assignment) {
            $data = $this->hydrator->extract($assignment, ['staffId', 'departmentId', 'role', 'position']);
            list($insertId, $affectedRows) = DB::insert('plugin_contacts3_relations', [
                'child_id',
                'parent_id',
                'role',
                'position'
            ])
                ->values([
                    $data['staffId'],
                    $data['departmentId'],
                    $data['role'],
                    $data['position'],
                ])->execute();
        }
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