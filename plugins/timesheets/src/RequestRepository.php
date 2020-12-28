<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Dto\RequestSearchDto;
use Ideabubble\Timesheets\Entity\Request;

interface RequestRepository
{
    public function findAll(RequestSearchDto $dto);
    public function count(RequestSearchDto $dto);
    public function sumDuration(RequestSearchDto $dto);
    public function insert(Request $request);
    public function update(Request $request);
}