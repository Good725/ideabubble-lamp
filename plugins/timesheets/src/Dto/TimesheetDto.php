<?php


namespace Ideabubble\Timesheets\Dto;


class TimesheetDto
{
    public $id;
    public $staffId;
    public $reviewerId;
    public $departmentId;
    public $startDate;
    public $endDate;
    public $status;
    
    public $orderBy = 'id';
    public $orderDir = 'DESC';
    public $offset;
    public $limit;
    
}