<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\StaffSearchDto;
use Ideabubble\Timeoff\Entity\Staff;

interface StaffRepository
{
    public function findAll(StaffSearchDto $dto);
    public function count(StaffSearchDto $dto);
    public function update(Staff $staff);
}