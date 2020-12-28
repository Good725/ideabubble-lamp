<?php


namespace Ideabubble\Timeoff\Entity;


use Ideabubble\Timeoff\Exception;

class Type
{
    const ANNUAL = 'annual';
    const BEREAVEMENT = 'bereavement';
    const SICK = 'sick';
    const FORCE_MAJEURE = 'force majeure';
    const LIEU = 'lieu';
    const OTHER = 'other';
    
    private $value;
    
    public function __construct($value)
    {
        if ($value && !in_array($value, [self::ANNUAL, self::BEREAVEMENT, self::SICK, self::FORCE_MAJEURE, self::OTHER, self::LIEU])) {
            throw new Exception('Incorrect leave type');
        }
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}