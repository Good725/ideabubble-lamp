<?php


namespace Ideabubble\Timeoff\Dto;


class StaffSearchDto
{
    public $id;
    public $userId;
    public $departmentId;
    public $businessId;
    public $role;
    public $offset;
    public $limit;
    public $orderBy = 'id';
    public $orderDir = 'DESC';

    public $onlyStaff = false;
}