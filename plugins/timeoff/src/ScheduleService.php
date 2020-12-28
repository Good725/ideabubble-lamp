<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\ScheduleSearchDto;

class ScheduleService
{
    /**
     * @var ScheduleRespository
     */
    private $scheduleRespository;
    
    public function __construct(ScheduleRespository $scheduleRespository)
    {
        $this->scheduleRespository = $scheduleRespository;
    }
    
    /**
     * @param ScheduleSearchDto $dto
     * @return Entity\Schedule[]
     */
    public function findAll(ScheduleSearchDto $dto)
    {
        return $this->scheduleRespository->findAll($dto);
    }
    
    public function findById($id)
    {
        $dto = new ScheduleSearchDto();
        $dto->id = $id;
        $items = $this->findAll($dto);
        if ($items) {
            return $items[0];
        } else {
            return null;
        }
    }
    
    public function count(ScheduleSearchDto $dto)
    {
        return $this->scheduleRespository->count($dto);
    }
    
    
}