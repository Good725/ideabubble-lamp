<?php


namespace Ideabubble\Timeoff\Entity;


use Ideabubble\Timeoff\Exception;

class Config
{
    private $ref;
    private $name;
    private $value;
    
    public function __construct($name, $value, Ref $ref)
    {
        $this->name = $name;
        $this->value = $value;
        $this->ref = $ref;
    }
    
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    
}