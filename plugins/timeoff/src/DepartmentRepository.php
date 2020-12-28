<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\DeptSearchDto;
use Ideabubble\Timeoff\Entity\Department;

interface DepartmentRepository
{
    public function findAll(DeptSearchDto $dto);
}