<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Entity\Note;

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