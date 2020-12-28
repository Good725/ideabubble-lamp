<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\ScheduleSearchDto;
use Ideabubble\Timeoff\Entity\Schedule;

interface ScheduleRespository
{
    /**
     * @param ScheduleSearchDto $dto
     * @return Schedule[]
     */
    public function findAll(ScheduleSearchDto $dto);
    public function count(ScheduleSearchDto $dto);
    
}