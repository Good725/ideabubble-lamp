<?php


namespace Ideabubble\Timeoff;


use Ideabubble\Timeoff\Dto\DeptSearchDto;
use Ideabubble\Timeoff\Dto\RequestSearchDto;
use Ideabubble\Timeoff\Dto\ScheduleSearchDto;
use Ideabubble\Timeoff\Dto\StaffSearchDto;
use Ideabubble\Timeoff\Entity\Note;
use Ideabubble\Timeoff\Entity\Period;
use Ideabubble\Timeoff\Entity\Ref;
use Ideabubble\Timeoff\Entity\Request;
use Ideabubble\Timeoff\Entity\Staff;
use Ideabubble\Timeoff\Entity\Status;
use Ideabubble\Timeoff\Entity\Type;
use Ideabubble\Timeoff\Kohana\DetailsReport;
use Ideabubble\Timeoff\Response\DataTable\RequestList;
use MongoDB\Driver\Manager;

class RequestService
{
    /**
     * @var RequestRepository
     */
    private $requestRepository;
    /**
     * @var NoteRepository
     */
    private $noteRepository;
    /**
     * @var StaffService
     */
    private $staffService;
    /**
     * @var Generator
     */
    private $generator;
    /**
     * @var DepartmentRepository
     */
    private $departmentRepository;
    /**
     * @var ConfigService
     */
    private $configService;
    /**
     * @var ScheduleEventRepository
     */
    private $scheduleRepository;
    
    public function __construct(
        ConfigService $configService,
        RequestRepository $requestRepository,
        NoteRepository $noteRepository,
        StaffService $staffService,
        DepartmentRepository $departmentRepository,
        ScheduleEventRepository $scheduleRepository,
        Generator $generator)
    {
        $this->configService = $configService;
        $this->requestRepository = $requestRepository;
        $this->noteRepository = $noteRepository;
        $this->staffService = $staffService;
        $this->generator = $generator;
        $this->departmentRepository = $departmentRepository;
        $this->scheduleRepository = $scheduleRepository;
    }
    
    /**
     * @param RequestSearchDto $dto
     * @return Request[]
     */
    public function findAll(RequestSearchDto $dto)
    {
        return $this->requestRepository->findAll($dto);
    }
    
    public function count(RequestSearchDto $dto)
    {
        return $this->requestRepository->count($dto);
    }
    
    
    
    public function timeoffConflicts(RequestSearchDto $dto)
    {
        $request = $this->findById($dto->id);
        $dto->startDate = $request->getPeriod()->getStartDate();
        $dto->endDate = $request->getPeriod()->getEndDate();
        $dto->status = [Status::PENDING, Status::APPROVED];
        return $this->findAll($dto);
    }
    
    public function scheduleConflicts(ScheduleSearchDto $dto)
    {
        return $this->scheduleRepository->findAll($dto);
    }
    
    /**
     * @param RequestSearchDto $dto
     * @return Request
     */
    public function findOne(RequestSearchDto $dto)
    {
        $items = $this->requestRepository->findAll($dto);
        if (count($items) > 0) {
            return $items[0];
        } else {
            return null;
        }
    }
    
    public function findById($id)
    {
        $dto = new RequestSearchDto();
        $dto->id = $id;
        return $this->findOne($dto);
    }
    
    public function save(Request $request, $managerId, $note = null, $skipValidation = false)
    {
        if (!$skipValidation && !$request->can_edit()) {
            throw new Exception('You not have permission to edit this request.');
        }

        $request->setManagerUpdated();
        $this->update($request);

        $this->saveNotes($request, $note);
    }

    public function saveNotes(Request $request, $notes)
    {
        $new_notes = array_filter(explode("\n", $notes));
        $old_notes = $this->getNotes($request->getId());

        for ($i = 0; $i < count($old_notes); $i++) {
            $note = new \Model_Contacts3_Note($old_notes[$i]->getId());
            if (isset($new_notes[$i])) {
                // Replace existing notes, with new notes
                $note->set('note', $new_notes[$i]);
                $note->save_with_moddate();
            } else {
                // Delete old notes, if there are fewer new notes than old notes
                $note->delete_and_save();
            }
        }

        // If there are more new notes than old notes, insert the new ones.
        $table_id = \DB::select()->from('plugin_contacts3_notes_tables')->where('table','=','plugin_timeoff_requests')->execute()->get('id', 0);
        for (; $i < count($new_notes); $i++) {
            $note = new \Model_Contacts3_Note();
            $note->note = $new_notes[$i];
            $note->table_link_id = $table_id;
            $note->link_id = $request->getId();
            $note->save_with_moddate();
        }
    }

    /**
     * @param Request $request
     * @param $userId
     * @param string|null $note
     */
    private function update(Request $request)
    {
        $this->requestRepository->update($request);
    }
    
    /**
     * @param Request $request
     * @param string $note
     */
    public function submit(Request $request, $note = null)
    {
        $this->updateDuration($request);
        $this->requestRepository->insert($request);
        $this->saveNotes($request, $note);
    }
    
    public function approve($requestId, $managerId, $note = null, $skipManagerTest = false)
    {
        $request = $this->findById($requestId);
        if (!$skipManagerTest) {
            $manager = $this->staffService->findById($managerId);
            if (!$manager->isManagerOf($request->getDepartmentId())) {
                throw new Exception('Manager does not belong to department ' . $request->getDepartmentId());
            }
        }
        $request->setStatus(new Status(Status::APPROVED));
        $request->setManagerUpdated();
        $this->update($request);
        if (!empty($note)) {
            $noteObject = new Note($this->generator->nextId(), $request->getId(), $managerId, $note);
            $this->addNote($noteObject);
        }
    }
    
    public function decline($requestId, $managerId, $note = null, $skipManagerTest = false)
    {
        $request = $this->findById($requestId);
        if (!$skipManagerTest) {
            $manager = $this->staffService->findById($managerId);
            if (!$manager->isManagerOf($request->getDepartmentId())) {
                throw new Exception('Manager does not belong to department ' . $request->getDepartmentId());
            }
        }
        $request->setManagerUpdated();
        $request->setStatus(new Status(Status::DECLINED));
        $this->update($request);
        if (!empty($note)) {
            $noteObject = new Note($this->generator->nextId(), $request->getId(), $managerId, $note);
            $this->addNote($noteObject);
        }
    }
    
    public function addNote(Note $note)
    {
        $request = $this->findById($note->getRequestId());
        if ($note->getUserId() == $request->getStaffId()) {
            $request->setStaffUpdated();
        } else {
            $request->setManagerUpdated();
        }
        $this->update($request);
        return $this->noteRepository->insert($note);
    }
    
    public function getNotes($requestId)
    {
        return $this->noteRepository->findAll($requestId);
    }
    
    public function respRequest(Request $request)
    {
        $response = new \Ideabubble\Timeoff\Response\Request();
        $response->id = $request->getId();
        $staff = $this->staffService->findById($request->getStaffId());
        if (!empty($staff)) {
            $response->staff = ['id'=>$staff->getId(), 'name'=>$staff->getName(), 'position'=>$staff->positionIn($request->getDepartmentId())];
        }
        $deptDto = new DeptSearchDto();
        $deptDto->id = $request->getDepartmentId();
        $dept = $this->departmentRepository->findAll($deptDto);
        if (!empty($dept)) {
            $response->department = ['id'=>$dept[0]->getId(), 'name'=>$dept[0]->getName()];
        }
        $response->status = $request->getStatus();
        $response->period = [$request->getPeriod()->getStartDate(), $request->getPeriod()->getEndDate(), $request->getPeriod()->getDuration()];
        $response->type = $request->getType();
        $response->created_at = $request->getCreatedAt();
        $response->staff_updated_at = $request->getStaffUpdatedAt();
        $response->manager_updated_at = $request->getManagerUpdatedAt() ? $request->getManagerUpdatedAt() : null;
        $notes = $this->getNotes($request->getId());

        $response->can_edit = $request->can_edit();
        $response->can_view = $request->can_view();

        foreach ($notes as $note) {
            $user = $this->staffService->findById($note->getUserId());
            $response->notes[] = [
                'id'=>$note->getId(),
                'created_at'=>$note->getCreatedAt(),
                'user_id'=>$note->getUserId(),
                'name' => isset($user) ? $user->getName() : 'undefined',
                'content' => $note->getContent()
            ];
        }
        return $response;
    }
    
    
    // todo: support multiple organizations
    // todo: find out if user can be a member of multiple departments or not?
    public function getConfigLevels(Ref $ref)
    {
        $levels = [];
        if ($ref->getLevel() == Ref::LEVEL_STAFF) {
            $levels[] = $ref;
            $staff = $this->staffService->findById($ref->getItemId());
            $assignments = $staff->getAssignments();
            foreach ($assignments as $aitem) {
                if ($aitem->getRole() == 'staff' || $aitem->getRole() == 'manager') {
                    $levels[] = new Ref(Ref::LEVEL_DEPARTMENT, $aitem->getDepartmentId());
                    $organization_ids = \Model_Contacts3::get_parent_related_contacts($aitem->getDepartmentId());
                    foreach ($organization_ids as $organization_id) {
                        $levels[] = new Ref(Ref::LEVEL_ORGANIZATION, $organization_id);
                    }
                    break;
                }
            }
        } else if ($ref->getLevel() == Ref::LEVEL_DEPARTMENT) {
            $levels[] = $ref;
        }
        $levels[] = new Ref(Ref::LEVEL_ORGANIZATION, 0);
        $levels[] = new Ref(Ref::LEVEL_GLOBAL, 0);
        return $levels;
    }
    
    public function daysAvailable(Ref $ref)
    {
        $daysAvailable = $this->configService->get('timeoff.annual_leave', $this->getConfigLevels($ref));

        $count = 1;
        $dto = new StaffSearchDto();
        $dto->onlyStaff = true;
        if ($ref->getLevel() == Ref::LEVEL_DEPARTMENT) {
            $dto->departmentId = $ref->getItemId();
            $count = $this->staffService->count($dto);
        }
        if ($ref->getLevel() == Ref::LEVEL_ORGANIZATION) {
            $dto->businessId = $ref->getItemId();
            $count = $this->staffService->count($dto);
        }
        if ($ref->getLevel() == Ref::LEVEL_STAFF) {
            $dto->id = $ref->getItemId();
            $count = $this->staffService->count($dto);
        }
        return $daysAvailable * $count * $this->dayLengthMinutes($ref);
    }
    
    public function dayLengthMinutes(Ref $ref)
    {
        return $this->configService->get('timeoff.day_length', $this->getConfigLevels($ref)) * 60;
        
    }
    
    public function daysPendingApproval(Ref $ref, $startDate, $endDate, $staffId = null)
    {
        $count = 0;
        $dto = new RequestSearchDto();
        $dto->onlyStaff = true;
        if ($ref->getLevel() == Ref::LEVEL_STAFF) {
            $dto->staffId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_DEPARTMENT) {
            $dto->departmentId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_ORGANIZATION) {
            $dto->businessId = $ref->getItemId();
        }
        if ($staffId != null) {
            $dto->staffId = $staffId;
        }
        $dto->status = [Status::PENDING];
        $dto->type = [Type::SICK, Type::OTHER, Type::BEREAVEMENT, Type::ANNUAL];
        $dto->startDate = $startDate;
        $dto->endDate = $endDate;
        $items = $this->findAll($dto);
        if (count($items) > 0) {
            foreach ($items as $item) {
                $count = $count + $item->getPeriod()->getDuration();
            }
        }
        return $count;
    }
    
    public function daysApproved(Ref $ref, $startDate, $endDate, $staffId = null)
    {
        $count = 0;
        $dto = new RequestSearchDto();
        if ($ref->getLevel() == Ref::LEVEL_STAFF) {
            $dto->staffId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_DEPARTMENT) {
            $dto->departmentId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_ORGANIZATION) {
            $dto->businessId = $ref->getItemId();
        }
        if ($staffId != null) {
            $dto->staffId = $staffId;
        }
        $dto->status = [Status::APPROVED];
        $dto->type = [Type::SICK, Type::OTHER, Type::BEREAVEMENT, Type::ANNUAL];
        $dto->startDate = $startDate;
        $dto->endDate = $endDate;
        $items = $this->findAll($dto);
        if (count($items) > 0) {
            foreach ($items as $item) {
                $count = $count + $item->getPeriod()->getDuration();
            }
        }
        return $count;
    }
    
    public function daysLeft(Ref $ref, $staftDate, $endDate, $staffId = null)
    {
        $available = $this->daysAvailable($ref);
        $lieu      = $this->daysInLieu($ref, $staftDate, $endDate, $staffId);
        $approved  = $this->daysApproved($ref, $staftDate, $endDate, $staffId);

        return $available + $lieu - $approved;
    }
    
    public function daysInLieu(Ref $ref, $startDate, $endDate, $staffId = null)
    {
        $count = 0;
        $dto = new RequestSearchDto();
        $dto->onlyStaff = true;
        if ($ref->getLevel() == Ref::LEVEL_STAFF) {
            $dto->staffId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_DEPARTMENT) {
            $dto->departmentId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_ORGANIZATION) {
            $dto->businessId = $ref->getItemId();
        }
        if ($staffId != null) {
            $dto->staffId = $staffId;
        }
        $dto->status = [Status::APPROVED];
        $dto->type = [Type::LIEU];
        $dto->startDate = $startDate;
        $dto->endDate = $endDate;
        $items = $this->findAll($dto);
        if (count($items) > 0) {
            foreach ($items as $item) {
                $count = $count + $item->getPeriod()->getDuration();
            }
        }
        return $count;
    }

    public function netDaysInLieu(Ref $ref, $startDate, $endDate, $staffId = null)
    {
        $lieu = $this->daysInLieu($ref, $startDate, $endDate, $staffId);
        $approved = $this->daysApproved($ref, $startDate, $endDate, $staffId);

        return ($lieu > $approved) ? $lieu - $approved : 0;
    }
    
    public function updateDuration(Request $request)
    {
        $period = $request->getPeriod();
        $range = false;
        if (date('Y-m-d', strtotime($period->getStartDate())) != date('Y-m-d', strtotime($period->getEndDate()))) {
            $range = true;
        }
        $request->setPeriod(
            new Period($request->getPeriod()->getStartDate(),
                $request->getPeriod()->getEndDate(),
                $duration = $this->getDuration(
                    $period->getStartDate(),
                    date('Y-m-d H:i:s', strtotime($period->getEndDate()) + ($range ? 86400 : 0)),
                    $request->getDepartmentId(),
                    $request->getType()
                )
            )
        );
    }
    
    public function getDuration($startDate, $endDate, $departmentId, $type = null)
    {
        $calc = new PeriodCalculator();
        return $calc->calculate($startDate, $endDate, $departmentId, $type);
    }
    
    
    
    
    
    
    
    
    
    
    
}