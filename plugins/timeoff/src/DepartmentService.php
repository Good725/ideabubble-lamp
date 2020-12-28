<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\DeptSearchDto;

class DepartmentService
{
    /**
     * @var DepartmentRepository
     */
    private $departmentRepository;
    
    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }
    
    public function findAll(DeptSearchDto $dto)
    {
        return $this->departmentRepository->findAll($dto);
    }
    
    public function findById($id)
    {
        $dto = new DeptSearchDto();
        $dto->id = $id;
        $items = $this->departmentRepository->findAll($dto);
        return isset($items[0]) ? $items[0] : null;
    }
}