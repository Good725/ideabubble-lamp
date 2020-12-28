<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Dto\StaffSearchDto;
use Ideabubble\Timesheets\Entity\Staff;

interface StaffRepository
{
    /**
     * @param StaffSearchDto $dto
     * @return Staff[]
     */
    public function findAll(StaffSearchDto $dto);
    public function count(StaffSearchDto $dto);
    public function update(Staff $staff);
}