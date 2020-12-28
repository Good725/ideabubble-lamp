<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Dto\DeptSearchDto;
use Ideabubble\Timesheets\Entity\Department;

interface DepartmentRepository
{
    
    /**
     * @param DeptSearchDto $dto
     * @return Department[]
     */
    public function findAll(DeptSearchDto $dto);
}