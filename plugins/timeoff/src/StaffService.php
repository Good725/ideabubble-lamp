<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\StaffSearchDto;
use Ideabubble\Timeoff\Entity\Staff;

class StaffService
{
    
    /**
     * @var StaffRepository
     */
    private $staffRepository;
    
    public function __construct(StaffRepository $staffRepository)
    {
        $this->staffRepository = $staffRepository;
    }
    
    /**
     * @param $id
     * @return Staff|null
     */
    public function findById($id)
    {
        $dto = new StaffSearchDto();
        $dto->id = $id;
        $items = $this->staffRepository->findAll($dto);
        if (count($items) > 0) {
            return $items[0];
        }
        return null;
    }
    
    public function findByUserId($userId)
    {
        $dto = new StaffSearchDto();
        $dto->userId = $userId;
        $items = $this->staffRepository->findAll($dto);
        if (count($items) > 0) {
            return $items[0];
        }
        return null;
    }
    
    
    public function count(StaffSearchDto $dto)
    {
        return $this->staffRepository->count($dto);
    }
    
    public function update(Staff $staff)
    {
        return $this->staffRepository->update($staff);
    }
    
    
    
    
}