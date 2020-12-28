<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\ScheduleSearchDto;

interface ScheduleEventRepository
{
    public function findAll(ScheduleSearchDto $dto);
}