<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\Dto\TimesheetDto;
use Ideabubble\Timesheets\Entity\Period;
use Ideabubble\Timesheets\Entity\Status;
use Ideabubble\Timesheets\Entity\Timesheet;
use Ideabubble\Timesheets\EventDispatcher;
use Ideabubble\Timesheets\Hydrator;
use Ideabubble\Timesheets\TimesheetRepository;
use DB;

class KohanaTimesheetRepository implements TimesheetRepository
{
    
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->hydrator = new Hydrator();
        $this->dispatcher = $dispatcher;
    }

    public function findAll(TimesheetDto $dto)
    {
        $columns = [
            'r.id', 'r.staff_id', 'r.reviewer_id', 'r.department_id','r.status', 'r.period_start_date', 'r.period_end_date', 'r.note'
        ];

        $select = $this->searchQuery($dto, $columns);
        $rows = $select->as_object()->execute();
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->hydrator->hydrate(Timesheet::class, [
                'id' => $item->id,
                'staffId' => $item->staff_id,
                'reviewerId' => $item->reviewer_id,
                'departmentId' => $item->department_id,
                'status' => new Status($item->status),
                'period' => new Period($item->period_start_date, $item->period_end_date, 0),
                'note' => $item->note
            ]);
        }
        return $result;
    }
    
    private function searchQuery(TimesheetDto $dto, $columns = null)
    {
        $select = DB::select_array($columns)->from(['plugin_timesheets_timesheets','r']);
        
        if (!empty($dto->id) && is_scalar($dto->id))  {
            $select->and_where('id','=', $dto->id);
        }
        if (!empty($dto->id) && is_array($dto->id))  {
            $select->and_where('id','in', $dto->id);
        }
        
        if (!empty($dto->status))  {
            $select->and_where('r.status','in', $dto->status);
        }
        if (!empty($dto->staffId)) {
            $select->and_where('staff_id', '=', $dto->staffId);
        }
        if (!empty($dto->reviewerId)) {
            $select->and_where('reviewer_id', '=', $dto->reviewerId);
        }
        if (!empty($dto->departmentId)) {
            $select->and_where('department_id', 'in', $dto->departmentId);
        }
        
        if (!empty($dto->startDate) && !empty($dto->endDate)) {
    
            $select->and_where('r.period_start_date', '<=', $dto->endDate);
            $select->and_where('r.period_end_date', '>=', $dto->startDate);
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
    
    public function count(TimesheetDto $dto)
    {
        $select = $this->searchQuery($dto, [DB::expr('COUNT(*) as cnt')]);
        $select->offset(null);
        $select->limit(null);
        $row = $select->execute()->as_array();
        return $row[0]['cnt'];
    }
    
    public function insert(Timesheet $timesheet)
    {
        $data = $this->hydrator->extract($timesheet, [
            'id','staffId', 'reviewerId', 'departmentId', 'status','period','note'
        ]);
    
        DB::insert('plugin_timesheets_timesheets')->values([
            'id' => $data['id'],
            'staff_id'=>$data['staffId'],
            'reviewer_id'=>$data['reviewerId'],
            'department_id' => $data['departmentId'],
            'status'=>$data['status']->getValue(),
            'period_start_date'=>$data['period']->getStartDate(),
            'period_end_date'=>$data['period']->getEndDate(),
            'note' => $data['note'],
        ])->execute();
    }
    
    public function update(Timesheet $timesheet)
    {
        $data = $this->hydrator->extract($timesheet, [
            'id','staffId', 'reviewerId', 'departmentId', 'status','period','note'
        ]);
    
        DB::update('plugin_timesheets_timesheets')->set([
            'id' => $data['id'],
            'staff_id'=>$data['staffId'],
            'reviewer_id'=>$data['reviewerId'],
            'department_id' => $data['departmentId'],
            'status'=>$data['status']->getValue(),
            'period_start_date'=>$data['period']->getStartDate(),
            'period_end_date'=>$data['period']->getEndDate(),
            'note' => $data['note'],
        ])->where('id','=', $data['id'])->execute();
    }
    
}