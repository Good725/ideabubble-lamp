<?php


namespace Ideabubble\Timesheets\Dto;


class RequestSearchDto
{
    const DATES_OVERLAP = 'overlap';
    const DATES_START = 'start';
    
    public $id;
    public $idList;
    public $staffId;
    public $departmentId;
    public $businessId;
    public $status; // ['pending', 'approved']
    public $type; // ['course', 'internal']
    public $timesheetId;
    public $todoId;
    public $scheduleId;
    public $deleted = 0;
    public $startDate;
    public $endDate;
    public $duration;
    public $description;
    public $text;
    public $orderBy = 'id';
    public $orderDir = 'DESC';
    public $offset;
    public $limit;

    public $datesMode = self::DATES_OVERLAP;
    
}