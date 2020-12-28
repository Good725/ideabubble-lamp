<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Entity\Staff;

interface PermissionManager
{
    public function hasPermission(Staff $staff, $permission);
}