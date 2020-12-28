<?php


namespace Ideabubble\Timesheets\Response;


use Ideabubble\Timesheets\Entity\Note;
use Ideabubble\Timesheets\Entity\Request as RequestObject;
use Ideabubble\Timesheets\Entity\Staff;

class Request
{
    public $id; // 42
    public $staff; // {id: 1, name: "Alexandr Makarov", position: "engineer"}
    public $department; // {id: 1, name: "Sales"}
    public $status; // pending|approved|declined|cancelled
    public $type; // course|internal
    public $schedule_id; // course|internal
    public $todo_id; // course|internal
    public $period; // [2018-09-01 11:00, 2018-09-02, 1.5]
    public $created_at; // 2018-09-01 11:00
    public $description;
    public $item; // ['type', 'id', 'title']
    public $staff_updated_at; // 2018-09-01 11:00
    public $manager_updated_at; // 2018-09-01 11:00
    public $notes = []; // some text goes here
}