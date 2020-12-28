<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Dto\TimesheetDto;
use Ideabubble\Timesheets\Entity\Timesheet;

interface TimesheetRepository
{
    /**
     * @param TimesheetDto $dto
     * @return Timesheet[]
     */
    public function findAll(TimesheetDto $dto);
    public function count(TimesheetDto $dto);
    public function insert(Timesheet $timesheet);
    public function update(Timesheet $timesheet);
}