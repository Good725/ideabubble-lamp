<?php


namespace Ideabubble\Timeoff\Kohana;


use Ideabubble\Timeoff\Dto\ScheduleSearchDto;
use Ideabubble\Timeoff\Entity\Schedule;
use Ideabubble\Timeoff\Hydrator;
use Ideabubble\Timeoff\ScheduleRespository;
use DB;

class KohanaScheduleRepository implements ScheduleRespository
{
    private $hydrator;
    const TABLE = 'plugin_courses_schedules';
    
    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    public function findAll(ScheduleSearchDto $dto)
    {
        $select = $this->searchQuery($dto);
        $rows = $select->as_object()->execute();
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->hydrator->hydrate(Schedule::class, [
                'id' => $item->id,
                'name' => $item->name,
                'trainerId' => $item->trainer_id
            ]);
        }
        return $result;
        
    
    }
    
    public function count(ScheduleSearchDto $dto)
    {
        $select = $this->searchQuery($dto, [DB::expr('COUNT(*) as cnt')]);
        $select->offset(null);
        $select->limit(null);
        $row = $select->execute()->as_array();
        return $row[0]['cnt'];
    }
    
    private function searchQuery(ScheduleSearchDto $dto, $columns = null)
    {
        $select = DB::select_array($columns)->from(self::TABLE);
        if (!empty($dto->id))  {
            $select->and_where('id','=', $dto->id);
        }
        if (!empty($dto->idList))  {
            $select->and_where('id','in', $dto->idList);
        }

        if (!empty($dto->name))  {
            $select->and_where('name','like', '%'.$dto->name.'%');
            
        }
        
        if (!empty($dto->staffId)) {
            $select->and_where('trainer_id', '=', $dto->staffId);
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
    
    
}