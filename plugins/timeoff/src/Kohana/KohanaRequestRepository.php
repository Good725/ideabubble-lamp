<?php


namespace Ideabubble\Timeoff\Kohana;


use Ideabubble\Timeoff\Dto\RequestSearchDto;
use Ideabubble\Timeoff\Entity\Period;
use Ideabubble\Timeoff\Entity\Request;
use Ideabubble\Timeoff\Entity\Status;
use Ideabubble\Timeoff\Entity\Type;

use DB;
use Ideabubble\Timeoff\EventDispatcher;
use Ideabubble\Timeoff\Hydrator;
use Ideabubble\Timeoff\RequestRepository;

class KohanaRequestRepository implements RequestRepository
{
    private $hydrator;
    private $dispatcher;
    
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->hydrator = new Hydrator();
        $this->dispatcher = $dispatcher;
    }
    
    public function findAll(RequestSearchDto $dto)
    {
        $select = $this->searchQuery($dto);
        $rows = $select->as_object()->execute();
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->hydrator->hydrate(Request::class, [
                'id' => $item->id,
                'staffId' => $item->staff_id,
                'departmentId' => $item->department_id,
                'businessId' => $item->business_id,
                'period' => new Period($item->period_start_date, $item->period_end_date, $item->duration),
                'type' => new Type($item->type),
                'status' => new Status($item->status),
                'createdAt' => $item->created_at,
                'staffUpdatedAt' => $item->staff_updated_at,
                'managerUpdatedAt' => $item->manager_updated_at,
            ]);
        }
        return $result;
        
    }
    
    public function count(RequestSearchDto $dto)
    {
        $select = $this->searchQuery($dto, [DB::expr('COUNT(*) as cnt')]);
        $select->offset(null);
        $select->limit(null);
        $row = $select->execute()->as_array();
        return $row[0]['cnt'];
    }
    
    private function searchQuery(RequestSearchDto $dto, $columns = array('plugin_timeoff_requests.*'))
    {
        $select = DB::select_array($columns)->from('plugin_timeoff_requests')
            ->join(array(\Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                ->on('plugin_timeoff_requests.staff_id', '=', 'contacts.id');
        
        if (!empty($dto->id))  {
            $select->and_where('plugin_timeoff_requests.id','=', $dto->id);
        }
        if (!empty($dto->idList))  {
            $select->and_where('plugin_timeoff_requests.id','in', $dto->idList);
        }
        
        if (!empty($dto->status))  {
            $select->and_where('plugin_timeoff_requests.status','in', $dto->status);
        }
        if (!empty($dto->type))  {
            $select->and_where('plugin_timeoff_requests.type','in', $dto->type);
        }
        if (!empty($dto->departmentId))  {
            $departmentId = is_array($dto->departmentId) ? $dto->departmentId : [$dto->departmentId];
            $select->and_where('plugin_timeoff_requests.department_id','in', $departmentId);
        }
        if (!empty($dto->businessId))  {
            $select->and_where('plugin_timeoff_requests.business_id','=', $dto->businessId);
        }
        if (!empty($dto->staffId)) {
            $staffId = is_array($dto->staffId) ? $dto->staffId : [$dto->staffId];
            $select->and_where('plugin_timeoff_requests.staff_id', 'in', $staffId);
        }
        if (!empty($dto->keyword)) {
            $select->and_where(DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name)"), 'like', '%' . $dto->keyword . '%');
        }
        if (!empty($dto->exclude_id)) {
            $select->and_where('plugin_timeoff_requests.id', '<>', $dto->exclude_id);
        }
    
        if (!empty($dto->startDate) && !empty($dto->endDate)) {
            
            if ($dto->datesMode == RequestSearchDto::DATES_OVERLAP) {
                $select->and_where('plugin_timeoff_requests.period_start_date', '<', DB::expr('DATE_ADD("' . date('Y-m-d', strtotime($dto->endDate)) . '", INTERVAL 1 DAY)'));
                $select->and_where('plugin_timeoff_requests.period_end_date', '>=', $dto->startDate);
            }
            if ($dto->datesMode == RequestSearchDto::DATES_START) {
                $select->and_where('plugin_timeoff_requests.period_start_date', '<', DB::expr('DATE_ADD("' . date('Y-m-d', strtotime($dto->endDate)) . '", INTERVAL 1 DAY)'));
                $select->and_where('plugin_timeoff_requests.period_start_date', '>=', $dto->startDate);
            }
            
        }
        
        
        if (!empty($dto->offset)) {
            $select->offset($dto->offset);
        }
        if (!empty($dto->limit)) {
            $select->limit($dto->limit);
        }
    
        if (!empty($dto->orderBy)) {
            $select->order_by("plugin_timeoff_requests." . $dto->orderBy, $dto->orderDir);
        }
        return $select;

    }
    
    
    public function insert(Request $request)
    {
        $data = $this->hydrator->extract($request, ['id','staffId', 'departmentId','businessId','period', 'type', 'status', 'createdAt', 'staffUpdatedAt', 'managerUpdatedAt']);
        list($insertId, $affectedRows) = DB::insert('plugin_timeoff_requests', [
            'id',
            'staff_id',
            'department_id',
            'business_id',
            'period_start_date',
            'period_end_date',
            'duration',
            'type',
            'status',
            'created_at',
            'staff_updated_at',
            'manager_updated_at'
        ])
            ->values([
                $data['id'],
                $data['staffId'],
                $data['departmentId'],
                $data['businessId'],
                $data['period']->getStartDate(),
                $data['period']->getEndDate(),
                $data['period']->getDuration(),
                $data['type']->getValue(),
                $data['status']->getValue(),
                $data['createdAt'],
                $data['staffUpdatedAt'],
                $data['managerUpdatedAt'],
            ])->execute();
    }
    
    public function update(Request $request)
    {
        $data = $this->hydrator->extract($request, ['id','staffId', 'departmentId', 'businessId', 'period', 'type', 'status', 'createdAt', 'staffUpdatedAt', 'managerUpdatedAt']);
        DB::update('plugin_timeoff_requests')
            ->set([
                'staff_id' => $data['staffId'],
                'department_id' => $data['departmentId'],
                'business_id' => $data['businessId'],
                'period_start_date' => $data['period']->getStartDate(),
                'period_end_date' => $data['period']->getEndDate(),
                'duration' => $data['period']->getDuration(),
                'type' => $data['type']->getValue(),
                'status' => $data['status']->getValue(),
                'created_at' => $data['createdAt'],
                'staff_updated_at' => $data['staffUpdatedAt'],
                'manager_updated_at' => $data['managerUpdatedAt'],
            ])->where('id','=', $data['id'])->execute();
    }
    
    
}