<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Dto\BusinessSearchDto;

interface BusinessRepository
{
    public function findAll(BusinessSearchDto $dto);
}