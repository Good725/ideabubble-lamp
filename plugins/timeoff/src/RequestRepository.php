<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\RequestSearchDto;
use Ideabubble\Timeoff\Entity\Request;

interface RequestRepository
{
    public function findAll(RequestSearchDto $dto);
    public function count(RequestSearchDto $dto);
    public function insert(Request $request);
    public function update(Request $request);
}