<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\Dto\ScheduleSearchDto;
use Ideabubble\Timesheets\Entity\ScheduleEvent;
use Ideabubble\Timesheets\Hydrator;
use Ideabubble\Timesheets\ScheduleEventRepository;
use DB;

class KohanaScheduleEventRepository implements ScheduleEventRepository
{
    private $hydrator;
    
    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    public function findAll(ScheduleSearchDto $dto)
    {
        $select = DB::select_array(['s.id', 's.name', 'e.datetime_start', 'e.datetime_end']);
        $rows = $select->from(['plugin_courses_schedules_events', 'e'])
            ->join(['plugin_courses_schedules', 's'], 'left')->on('s.id', '=', 'e.schedule_id')
            ->where('e.datetime_start', '<=', $dto->endDate)
            ->and_where('e.datetime_end', '>=', $dto->startDate)
            ->and_where('e.delete','=',0)
            ->and_where('s.trainer_id', '=', $dto->staffId)
            ->execute()->as_array();
        
        $result = [];
        if ($rows) {
            foreach ($rows as $row) {
                $result[] = $this->hydrator->hydrate(ScheduleEvent::class, [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'datetimeStart' => $row['datetime_start'],
                    'datetimeEnd' => $row['datetime_end'],
                ]);
            }
        }
        return $result;
        
    }
    
}