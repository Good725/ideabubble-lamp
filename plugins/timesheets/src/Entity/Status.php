<?php


namespace Ideabubble\Timesheets\Entity;


use Ideabubble\Timesheets\Exception;

class Status
{
    const OPEN = 'open';
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const DECLINED = 'declined';
    const CANCELLED = 'cancelled';
    const READY = 'ready';

    private $value;
    
    public function __construct($value)
    {
        if (!in_array($value, [self::OPEN,self::PENDING,self::APPROVED,self::DECLINED,self::CANCELLED,self::READY])) {
            throw new Exception('Incorrect timesheet status: ' . $value);
        }
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
}