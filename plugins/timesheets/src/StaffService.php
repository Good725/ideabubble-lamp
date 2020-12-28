<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Dto\StaffSearchDto;
use Ideabubble\Timesheets\Entity\Staff;

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
    
    
    /**
     * @param StaffSearchDto $dto
     * @return Staff[]
     */
    public function findAll(StaffSearchDto $dto)
    {
        return $this->staffRepository->findAll($dto);
    }
    
    public function managedStaff($managerId)
    {
        $result = null;
        $dto = new StaffSearchDto();
        $dto->id = $managerId;
        $items = $this->staffRepository->findAll($dto);
        if (count($items) != 1) {
            return null;
        }
        $staff = $items[0];
        $assignments = $staff->getAssignments();
        $departments = [];
        foreach ($assignments as $a) {
            if ($a->getRole() == 'manager') {
                $departments[] = $a->getDepartmentId();
            }
        }
        if (count($departments) > 0) {
            $sdto = new StaffSearchDto();
            $sdto->departmentId = $departments;
            $result = $this->findAll($sdto);
        }
        if (empty($result)) {
            $result = [$staff];
        }
        return $result;
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