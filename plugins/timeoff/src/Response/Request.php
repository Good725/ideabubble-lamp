<?php


namespace Ideabubble\Timeoff\Response;


use Ideabubble\Timeoff\Entity\Note;
use Ideabubble\Timeoff\Entity\Request as RequestObject;
use Ideabubble\Timeoff\Entity\Staff;

class Request
{
    public $id; // 42
    public $staff; // {id: 1, name: "Alexandr Makarov", position: "engineer"}
    public $department; // {id: 1, name: "Sales"}
    public $status; // pending|approved|declined|cancelled
    public $type; // annual|bereavement|sick|force majeure|other
    public $period; // [2018-09-01 11:00, 2018-09-02, 1.5]
    public $created_at; // 2018-09-01 11:00
    public $staff_updated_at; // 2018-09-01 11:00
    public $manager_updated_at; // 2018-09-01 11:00
    public $notes = []; // some text goes here
}