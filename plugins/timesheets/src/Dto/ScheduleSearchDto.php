<?php


namespace Ideabubble\Timesheets\Dto;


class ScheduleSearchDto
{
    public $staffId;
    public $startDate;
    public $endDate;
    public $orderBy = 'id';
    public $orderDir = 'DESC';
    public $offset;
    public $limit;
}