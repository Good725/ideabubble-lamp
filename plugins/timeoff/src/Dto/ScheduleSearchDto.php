<?php


namespace Ideabubble\Timeoff\Dto;


class ScheduleSearchDto
{
    public $id;
    public $staffId;
    public $startDate;
    public $endDate;
    public $name;
    public $orderBy = 'id';
    public $orderDir = 'DESC';
    public $offset;
    public $limit;
}