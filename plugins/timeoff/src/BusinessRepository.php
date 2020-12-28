<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\BusinessSearchDto;

interface BusinessRepository
{
    public function findAll(BusinessSearchDto $dto);
}