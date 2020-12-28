<?php


namespace Ideabubble\Timeoff\Entity;


use Ideabubble\Timeoff\Exception;

class Status
{
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const DECLINED = 'declined';
    const CANCELLED = 'cancelled';

    private $value;
    
    public function __construct($value)
    {
        if (!in_array($value, [self::PENDING,self::APPROVED,self::DECLINED,self::CANCELLED])) {
            throw new Exception('Incorrect leave status');
        }
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
}