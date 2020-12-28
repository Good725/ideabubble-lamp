<?php


namespace Ideabubble\Timesheets\Entity;


use Ideabubble\Timesheets\Exception;

class Ref
{
    const LEVEL_GLOBAL = 'global';
    const LEVEL_ORGANIZATION = 'organization';
    const LEVEL_DEPARTMENT = 'department';
    const LEVEL_STAFF = 'contact';
    const LEVEL_TIMESHEET = 'timesheet';

    private $level;
    private $itemId;
    
    
    public function __construct($level, $itemId)
    {
        if (!in_array($level, [self::LEVEL_GLOBAL, self::LEVEL_ORGANIZATION, self::LEVEL_DEPARTMENT, self::LEVEL_STAFF, self::LEVEL_TIMESHEET])) {
            throw new Exception('Incorrect level');
        }
        $this->level = $level;
        $this->itemId = $itemId;
    }
    
    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * @return array
     */
    public function getItemId()
    {
        return $this->itemId;
    }
    
    
}