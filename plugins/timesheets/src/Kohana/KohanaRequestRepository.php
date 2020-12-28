<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\Dto\RequestSearchDto;
use Ideabubble\Timesheets\Entity\Period;
use Ideabubble\Timesheets\Entity\Request;
use Ideabubble\Timesheets\Entity\Status;
use Ideabubble\Timesheets\Entity\Type;

use DB;
use Ideabubble\Timesheets\EventDispatcher;
use Ideabubble\Timesheets\Hydrator;
use Ideabubble\Timesheets\RequestRepository;

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
        $columns = [
            'r.id', 'r.staff_id', 'r.department_id', 'r.business_id', 'r.timesheet_id', 'r.todo_id', 'r.schedule_id',
            'r.duration', 'r.period_start_date', 'r.period_end_date', 'r.type', 'r.deleted', 'r.created_at',
            'r.staff_updated_at', 'r.manager_updated_at', 'r.description'
        ];
        $select = $this->searchQuery($dto, $columns);
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
                'status' => new Status(Status::PENDING),
                'createdAt' => $item->created_at,
                'staffUpdatedAt' => $item->staff_updated_at,
                'managerUpdatedAt' => $item->manager_updated_at,
                'timesheetId' => $item->timesheet_id,
                'todoId' => $item->todo_id,
                'deleted' => $item->deleted,
                'scheduleId' => $item->schedule_id,
                'description' => $item->description
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
    
    public function sumDuration(RequestSearchDto $dto)
    {
        $select = $this->searchQuery($dto, [DB::expr('SUM(`r`.`duration` * (1 + DATEDIFF(`r`.`period_end_date`, `r`.`period_start_date`))) as `cnt`')]);
        $select->offset(null);
        $select->limit(null);
        $row = $select->execute()->as_array();
        return $row[0]['cnt'];
    }
    
    
    private function searchQuery(RequestSearchDto $dto, $columns = null)
    {
        $select = DB::select_array($columns)->from(['plugin_timesheets_requests','r']);
        
        $select->join(['plugin_timesheets_timesheets', 'ts'],         'left')->on('r.timesheet_id',  '=', 'ts.id');
        $select->join(['plugin_timeoff_departments',   'department'], 'left')->on('r.department_id', '=', 'department.id');
        $select->join(['plugin_contacts3_contacts',    'staff'],      'left')->on('r.staff_id',      '=', 'staff.id');
        $select->join(['plugin_courses_schedules',     'schedule'],   'left')->on('r.schedule_id',   '=', 'schedule.id');
        
        if (!empty($dto->id) && is_scalar($dto->id))  {
            $select->and_where('r.id','=', $dto->id);
        }
        if (!empty($dto->id) && is_array($dto->id))  {
            $select->and_where('r.id','in', $dto->id);
        }
        if (!empty($dto->idList))  {
            $select->and_where('r.id','in', $dto->idList);
        }
        
        if (!empty($dto->status))  {
            $select->and_where('ts.status','in', $dto->status);
        }
        if (!empty($dto->type))  {
            $select->and_where('r.type','in', $dto->type);
        }
        if (!empty($dto->deleted))  {
            $select->and_where('r.deleted','=', $dto->deleted);
        }
        if (!empty($dto->departmentId) && is_scalar($dto->departmentId))  {
            $select->and_where('r.department_id','=', $dto->departmentId);
        }
        if (!empty($dto->departmentId) && is_array($dto->departmentId))  {
            $select->and_where('r.department_id','in', $dto->departmentId);
        }
        
        if (!empty($dto->businessId))  {
            $select->and_where('r.business_id','=', $dto->businessId);
        }
        if (!empty($dto->staffId)) {
            $select->and_where('ts.staff_id', '=', $dto->staffId);
        }
        if (!empty($dto->timesheetId)) {
            $select->and_where('r.timesheet_id', '=', $dto->timesheetId);
        }
        if (!empty($dto->todoId)) {
            $select->and_where('r.todo_id', '=', $dto->todoId);
        }
        if (!empty($dto->scheduleId)) {
            $select->and_where('r.schedule_id', '=', $dto->scheduleId);
        }
        
        if (!empty($dto->text)) {
            $select
                ->and_where_open()
                    ->and_where('r.description',  'like', '%'.$dto->text.'%')
                    ->or_where('r.type',          'like', '%'.$dto->text.'%')
                    ->or_where('department.name', 'like', '%'.$dto->text.'%')
                    ->or_where('schedule.name',   'like', '%'.$dto->text.'%')
                    ->or_where(DB::expr("CONCAT(`staff`.`first_name`, ' ', `staff`.`last_name`)"), 'like', '%'.$dto->text.'%')
                    ->or_where(DB::expr("DATE_FORMAT(`r`.`period_start_date`, '%e/%M/%Y')"),       'like', '%'.$dto->text.'%')
                ->and_where_close();
        }
    
        if (!empty($dto->startDate) && !empty($dto->endDate)) {
            
            if ($dto->datesMode == RequestSearchDto::DATES_OVERLAP) {
                $select->and_where('r.period_start_date', '<=', $dto->endDate);
                $select->and_where('r.period_end_date', '>=', $dto->startDate);
            }
            if ($dto->datesMode == RequestSearchDto::DATES_START) {
                $select->and_where('r.period_start_date', '<=', $dto->endDate);
                $select->and_where('r.period_start_date', '>=', $dto->startDate);
            }
        }
        
        
        if (!empty($dto->offset)) {
            $select->offset($dto->offset);
        }
        if (!empty($dto->limit)) {
            $select->limit($dto->limit);
        }

        if (!empty($dto->orderBy)) {
            $this->applyOrder($select, $dto->orderBy, $dto->orderDir);
        } else {
            $this->applyOrder($select, 'period_start_date', 'desc');
        }
        return $select;
        
    }
    
    private function applyOrder(\Database_Query_Builder_Select $select, $orderBy, $orderDir)
    {
        switch ($orderBy) {
            case 'person':
                $select->join(['plugin_contacts3_contacts', 'contacts'], 'left')->on('r.staff_id', '=', 'contacts.id');
                $select->order_by(DB::expr('CONCAT(contacts.first_name, " ", contacts.last_name)'), $orderDir);
                break;
            case 'department':
                $select->join(['plugin_contacts3_contacts', 'departments'], 'left')->on('r.department_id', '=', 'departments.id');
                $select->order_by('departments.first_name', $orderDir);
                break;
            case 'item':
                $select->join(['plugin_todos_todos2', 'todos'], 'left')->on('r.todo_id', '=', 'todos.id');
                $select->join(['plugin_courses_schedules', 'schedules'], 'left')->on('r.schedule_id', '=', 'schedules.id');
                $select->order_by(DB::expr('case r.type when "course" then schedules.name else todos.title end '), $orderDir);
                break;
            default:
                $select->order_by('r.' . $orderBy, $orderDir);
        }
        
        
    }
    
    
    
    public function insert(Request $request)
    {
        $data = $this->hydrator->extract($request, [
            'id','staffId', 'departmentId','businessId','period', 'type', 'status', 'createdAt',
            'staffUpdatedAt', 'managerUpdatedAt','timesheetId','todoId','scheduleId','description','deleted'
        ]);
        
        DB::insert('plugin_timesheets_requests')->values([
            'id' => $data['id'],
            'staff_id'=>$data['staffId'],
            'department_id'=>$data['departmentId'],
            'business_id'=>$data['businessId'],
            'period_start_date'=>$data['period']->getStartDate(),
            'period_end_date'=>$data['period']->getEndDate(),
            'duration'=>$data['period']->getDuration(),
            'type'=>$data['type']->getValue(),
            'created_at' => $data['createdAt'],
            'staff_updated_at' => $data['staffUpdatedAt'],
            'manager_updated_at' => $data['managerUpdatedAt'],
            'timesheet_id' => $data['timesheetId'],
            'todo_id' => $data['todoId'],
            'schedule_id' => $data['scheduleId'],
            'deleted' => $data['deleted'],
            'description' => $data['description'],
            
        ])->execute();
    }
    
    public function update(Request $request)
    {
        $data = $this->hydrator->extract($request, [
            'id','staffId', 'departmentId', 'businessId', 'period', 'type', 'status', 'createdAt', 'staffUpdatedAt',
            'managerUpdatedAt', 'description', 'scheduleId', 'todoId'
        ]);

        DB::update('plugin_timesheets_requests')
            ->set([
                'staff_id' => $data['staffId'],
                'department_id' => $data['departmentId'],
                'business_id' => $data['businessId'],
                'period_start_date' => $data['period']->getStartDate(),
                'period_end_date' => $data['period']->getEndDate(),
                'duration' => $data['period']->getDuration(),
                'type' => $data['type']->getValue(),
                'created_at' => $data['createdAt'],
                'staff_updated_at' => $data['staffUpdatedAt'],
                'manager_updated_at' => $data['managerUpdatedAt'],
                'todo_id' => $data['todoId'],
                'schedule_id' => $data['scheduleId'],
                'deleted' => $data['deleted'],
                'description' => $data['description'],
            ])->where('id','=', $data['id'])->execute();
    }
    
    
}