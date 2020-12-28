<?php


namespace Ideabubble\Timeoff\Dto;


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
    public $type; // ['annual', 'bereavement']
    public $startDate;
    public $endDate;
    public $orderBy = 'id';
    public $orderDir = 'DESC';
    public $offset;
    public $limit;
    public $keyword = '';

    public $datesMode = self::DATES_OVERLAP;
    
}