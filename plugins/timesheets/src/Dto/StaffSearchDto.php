<?php


namespace Ideabubble\Timesheets\Dto;


class StaffSearchDto
{
    public $id;
    public $userId;
    public $departmentId;
    public $managerId;
    public $role;
    public $offset;
    public $limit;
    public $orderBy = 'id';
    public $orderDir = 'DESC';
}