<?php


namespace Ideabubble\Timesheets\Entity;


use Ideabubble\Timesheets\Exception;

class Type
{
    const COURSE = 'course';
    const INTERNAL = 'internal';
    
    private $value;
    
    /**
     * Type constructor.
     * @param $value
     * @throws Exception
     */
    public function __construct($value)
    {
        if (!in_array($value, [self::COURSE, self::INTERNAL])) {
            throw new Exception('Incorrect timesheet type, should be course or internal');
        }
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}