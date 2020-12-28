<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Dto\ScheduleSearchDto;

interface ScheduleEventRepository
{
    public function findAll(ScheduleSearchDto $dto);
}