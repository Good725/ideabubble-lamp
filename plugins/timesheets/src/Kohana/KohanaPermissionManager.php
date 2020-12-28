<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\Entity\Staff;
use Ideabubble\Timesheets\PermissionManager;

class KohanaPermissionManager implements PermissionManager
{
    public function hasPermission(Staff $staff, $permission)
    {
        $auth = \Auth::instance();
        return $auth->role_has_access($staff->getRoleId(), $permission);
    }
    
}