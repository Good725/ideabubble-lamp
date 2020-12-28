<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Entity\Note;

interface NoteRepository
{
    /**
     * @param $requestId
     * @return Note[]
     */
    public function findAll($requestId);
    public function findOne($id);
    public function insert(Note $note);
    public function update(Note $note);
    
}