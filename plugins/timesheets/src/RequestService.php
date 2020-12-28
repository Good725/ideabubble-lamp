<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timeoff\ScheduleRespository;
use Ideabubble\Timeoff\ScheduleService;
use Ideabubble\Timesheets\Dto\DeptSearchDto;
use Ideabubble\Timesheets\Dto\RequestSearchDto;
use Ideabubble\Timesheets\Dto\ScheduleSearchDto;
use Ideabubble\Timesheets\Dto\StaffSearchDto;
use Ideabubble\Timesheets\Dto\TimesheetDto;
use Ideabubble\Timesheets\Entity\Note;
use Ideabubble\Timesheets\Entity\Period;
use Ideabubble\Timesheets\Entity\Ref;
use Ideabubble\Timesheets\Entity\Request;
use Ideabubble\Timesheets\Entity\Status;
use Ideabubble\Timesheets\Entity\Timesheet;
use Ideabubble\Timesheets\Entity\Type;
use Ideabubble\Timesheets\Response\RequestList;

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
     * @var ScheduleService
     */
    private $scheduleService;
    /**
     * @var TimesheetService
     */
    private $timesheetService;
    
    /**
     * @var PermissionManager
     */
    private $permissionManager;
    
    private $todos;
    
    public function __construct(
        PermissionManager $permissionManager,
        ConfigService $configService,
        RequestRepository $requestRepository,
        NoteRepository $noteRepository,
        StaffService $staffService,
        DepartmentRepository $departmentRepository,
        ScheduleService $scheduleService,
        TimesheetService $timesheetService,
        \Model_Todos $todos,
        Generator $generator)
    {
        $this->configService = $configService;
        $this->requestRepository = $requestRepository;
        $this->noteRepository = $noteRepository;
        $this->staffService = $staffService;
        $this->generator = $generator;
        $this->departmentRepository = $departmentRepository;
        $this->scheduleService = $scheduleService;
        $this->timesheetService = $timesheetService;
        $this->permissionManager = $permissionManager;
        $this->todos = $todos;
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
    
    public function worklogs(RequestSearchDto $dto, $callerId)
    {
        $response = new RequestList();
        try {
            $caller = $this->staffService->findById($callerId);
            $hasAccess = true;
            if (!\Auth::instance()->has_access('timesheets_edit')) {
                if (\Auth::instance()->has_access('timesheets_edit_limited')) {
                    $selectedDepartments = $dto->departmentId;
                    if (empty($selectedDepartments)) { // if no departments provided, use all available departments
                        foreach ($caller->getAssignments() as $assignment) {
                            $selectedDepartments[] = $assignment->getDepartmentId();
                        }
                    }

                    if ($dto->staffId != $caller->getId()) { // looks his own logs
                        if ($selectedDepartments) {
                            $hasAccess = false;
                            foreach ($selectedDepartments as $deptId) {
                                if ($caller->isManagerOf($deptId)) {
                                    $hasAccess = true;
                                    break;
                                }
                            }
                        }
                        if (!$hasAccess) {
                            $timesheet = $this->timesheetService->findById($dto->timesheetId);
                            if ($timesheet->getStaffId() == $callerId) {
                                $hasAccess = true;
                            }
                        }
                    }
                } else {
                    $hasAccess = false;
                }
            }

            if (!$hasAccess) {
                throw new PermissionException('Access denied. You need to view your own timesheets or have manager role for selected departments');
            }

            $requests = $this->findAll($dto);
            $response->total = $this->count($dto);
            foreach ($requests as $request) {
                $response->items[] = $this->respRequest($request);
            }
            $response->status = 'success';
        } catch (\Exception $e) {
            $response->status = 'error';
            $response->error = $e->getMessage() . $e->getTraceAsString();
        }
        return $response;
        
    }
    
    public function stats(Ref $ref, $startDate, $endDate, $callerId)
    {
        try {
            $caller = $this->staffService->findById($callerId);
            if (!\Auth::instance()->has_access('timesheets_edit') && \Auth::instance()->has_access('timesheets_edit_limited')) {
                if ($ref->getLevel() == Ref::LEVEL_DEPARTMENT) {
                    foreach ($ref->getItemId() as $deptId) {
                        if (!$caller->isManagerOf($deptId)) {
                            throw new PermissionException('You have no access to department ' . $deptId);
                        }
                    }
                }
            }

            if (!\Auth::instance()->has_access('timesheets_edit') && \Auth::instance()->has_access('timesheets_edit_limited')) {
                if ($ref->getLevel() == Ref::LEVEL_STAFF) {
                    foreach ($ref->getItemId() as $staffId) {
                        if ($staffId == $callerId) {
                            break;
                        }
                        $staff = $this->staffService->findById($staffId);
                        if (!$caller->canManageStaff($staff)) {
                            throw new PermissionException('You have no access to staff ' . $staffId);
                        }
                    }
                }
            }

            if (!\Auth::instance()->has_access('timesheets_edit') && \Auth::instance()->has_access('timesheets_edit_limited')) {
                if ($ref->getLevel() == Ref::LEVEL_TIMESHEET) {
                    foreach ($ref->getItemId() as $timesheetId) {
                        $timesheet = $this->timesheetService->findById($timesheetId);
                        $staff = $this->staffService->findById($timesheet->getStaffId());
                        if ($caller->getId() != $timesheet->getStaffId() && !$caller->canManageStaff($staff)) {
                            throw new PermissionException('You have no access to timesheet ' . $timesheetId);
                        }
                    }
                }
            }

            if (\Auth::instance()->has_access('timesheets_edit')) {
                // managers can view global stats
                // todo: define who has access to global levels
                if ($ref->getLevel() == Ref::LEVEL_ORGANIZATION || $ref->getLevel() == Ref::LEVEL_GLOBAL) {
                    $canManage = false;
                    foreach ($caller->getAssignments() as $item) {
                        $canManage = $canManage || $item->getRole() == 'manager';
                    }
                    if (!$canManage) {
                        throw new PermissionException('You have no access to global and organization levels');
                    }
                }
            }
            
            $response = $this->getStats($ref, $startDate, $endDate);
            
        } catch (Exception $e) {
            $response = ['error' => $e->getMessage()];
        }
        return $response;
    }
    
    private function getStats(Ref $ref, $startDate, $endDate)
    {
        return [
            'minutes_available' => $this->minutesAvailable($ref),
            'course_minutes_logged' => $this->minutesLogged($ref, new Type(Type::COURSE), $startDate, $endDate),
            'internal_minutes_logged' => $this->minutesLogged($ref, new Type(Type::INTERNAL), $startDate, $endDate),
        ];
        
    }
    
    public function timesheets(TimesheetDto $dto, $callerId)
    {
        try {
            $caller = $this->staffService->findById($callerId);
    
            $hasAccess = true;

            if (!\Auth::instance()->has_access('timesheets_edit')) {
                $selectedDepartments = $dto->departmentId;
                if (empty($selectedDepartments)) { // if no departments provided, use all available departments
                    foreach ($caller->getAssignments() as $assignment) {
                        $selectedDepartments[] = $assignment->getDepartmentId();
                    }
                }


                if ($dto->staffId != $caller->getId()) { // looks his own logs
                    if (!$selectedDepartments) {
                        $hasAccess = false;
                    } else {
                        if ($selectedDepartments) {
                            $hasAccess = false;
                            foreach ($selectedDepartments as $deptId) {
                                if ($caller->isManagerOf($deptId)) {
                                    $hasAccess = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            if (!$hasAccess) {
                throw new PermissionException('Access denied. You need to view your own timesheets or have timesheets_manage pemission');
            }
            
            $items = $this->timesheetService->findAll($dto);
            $response = ['status'=>'success', 'items'=>[]];
            foreach ($items as $item) {
                $staff = $this->staffService->findById($item->getStaffId());
                
                $reviewer = null;
                if ($item->getReviewerId()) {
                    $reviewer = $this->staffService->findById($item->getReviewerId());
                }
                $response['items'][] = [
                    'id'=>$item->getId(),
                    'staff' => ['id'=>$staff->getId(), 'name'=>$staff->getName()],
                    'department_id' => $item->getDepartmentId(),
                    'reviewer' => $reviewer ? ['id'=>$reviewer->getId(), 'name'=>$reviewer->getName()] : null,
                    'lastTransaction' => 0,
                    'stats' => $this->stats(new Ref(Ref::LEVEL_TIMESHEET, [$item->getId()]), $item->getPeriod()->getStartDate(), $item->getPeriod()->getEndDate(), $callerId),
                    'period'=>[$item->getPeriod()->getStartDate(), $item->getPeriod()->getEndDate()],
                    'status'=>$item->getStatus()->getValue(),
                    'note'=>$item->getNote()];
            }
            
        } catch (Exception $e) {
            $response = ['error' => $e->getMessage()];
        }
        return $response;
        
    }
    
    public function createTimesheets()
    {
        $weekStart = new \DateTimeImmutable(date('Y-m-d', strtotime('next Monday', time()) - 604800));
        $weekEnd = $weekStart->modify('+6 days');
        $dto = new DeptSearchDto();
        $departments = $this->departmentRepository->findAll($dto);
        foreach ($departments as $department) {
            $staffDto = new StaffSearchDto();
            $staffDto->departmentId = [$department->getId()];
            $members = $this->staffService->findAll($staffDto);
            foreach ($members as $member) {
                $refs = [
                    new Ref(Ref::LEVEL_GLOBAL, 0),
                    new Ref(Ref::LEVEL_ORGANIZATION, 0),
                    new Ref(Ref::LEVEL_DEPARTMENT, $department->getId()),
                    new Ref(Ref::LEVEL_STAFF, $member->getId())
                ];
                $hoursAvailable = $this->configService->get('timeoff.log_hours_per_week', $refs);
                if ($hoursAvailable > 0) {
                    $tDto = new TimesheetDto();
                    $tDto->staffId = $member->getId();
                    $tDto->startDate = $weekStart->format('Y-m-d');
                    $tDto->endDate = $weekEnd->format('Y-m-d');
                    $found = $this->timesheetService->findOne($tDto);
                    if (!$found) {
                        $timesheet = new Timesheet(
                            $this->generator->nextId(),
                            $member->getId(),
                            $department->getId(), new Period($weekStart->format('Y-m-d') . ' 00:00:00', $weekEnd->format('Y-m-d') . ' 23:59:59', 0));
                        $this->timesheetService->insert($timesheet);
                    }
                }
            }
        }
        
        
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
    
    /**
     * @param Request $request
     * @param $userId
     * @param string|null $note
     */
    public function update(Request $request)
    {
        $this->requestRepository->update($request);
    }
    
    /**
     * @param Request $request
     * @param string $note
     */
    public function submit(Request $request)
    {
        //$this->updateDuration($request);
        $dto = new TimesheetDto();
        $dto->startDate = $request->getPeriod()->getStartDate();
        $dto->endDate = $request->getPeriod()->getEndDate();
        $dto->staffId = $request->getStaffId();
        if (!$request->getDepartmentId()) {
            $staff = $this->staffService->findById($request->getStaffId());
            $assignments = $staff->getAssignments();
            if (count($assignments) > 0) {
                $request->setDepartmentId($assignments[0]->getDepartmentId());
            }
        }
        $timesheet = $this->timesheetService->findOne($dto);
        if (!$timesheet) {
            $weekStart = new \DateTimeImmutable(date('Y-m-d', strtotime('next Monday', strtotime($request->getPeriod()->getStartDate())) - 604800));
            $weekEnd = $weekStart->modify('+6 days');
            $period = new Period($weekStart->format('Y-m-d H:i:s') . ' 00:00:00', $weekEnd->format('Y-m-d')  . ' 23:59:59', 0);
            $timesheet = new Timesheet($this->generator->nextId(), $request->getStaffId(), $request->getDepartmentId(), $period);
            $this->timesheetService->insert($timesheet);
        }
        $request->setTimesheetId($timesheet->getId());
        /*if (!$request->getDepartmentId()) {
            $staff = $this->staffService->findById($request->getStaffId());
            $assignments = $staff->getAssignments();
            $request->setDepartmentId($assignments[0]->getDepartmentId());
        }*/
        $this->requestRepository->insert($request);
        //$this->sendNewRequestAlert($request->getId()); // not wanted for now
        
        // if related timesheet has enough hours logged mark it as ready
        $stats = $this->getStats(
            new Ref(Ref::LEVEL_TIMESHEET, [$timesheet->getId()]),
            $timesheet->getPeriod()->getStartDate(),
            $timesheet->getPeriod()->getEndDate()
        );
        
        if (($stats['course_minutes_logged'] + $stats['internal_minutes_logged']) >= $stats['minutes_available']) {
            $timesheet->setStatus(new Status(Status::READY));
        }
        $this->timesheetService->update($timesheet);
        
        
    }

    public static function minute_to_hour($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return $hours . 'h ' . $minutes . 'm';
    }

    public function sendNewRequestAlert($id)
    {
        $req = $this->findById($id);
        $recipients = array();
        $params = array();
        $params['comment'] = $req->getDescription();
        $params['period'] = date('d/M/Y', strtotime($req->getPeriod()->getStartDate())) . ' - ' . date('d/M/Y', strtotime($req->getPeriod()->getEndDate()));
        $params['duration'] = self::minute_to_hour($req->getPeriod()->getDuration());
        $params['name'] = $this->staffService->findById($req->getStaffId())->getName();
        $dep = new DepartmentService($this->departmentRepository);
        $params['department'] = $dep->findById($req->getDepartmentId())->getName();
        $recipients[] = array(
            'target_type' => 'CMS_CONTACT3',
            'target' => $req->getStaffId()
        );
        if ($req->getDepartmentId()) {
            $managers = \Model_Contacts3::get_child_related_contacts($req->getDepartmentId(), 'manager');
            foreach ($managers as $manager_id) {
                $recipients[] = array(
                    'target_type' => 'CMS_CONTACT3',
                    'target' => $manager_id
                );
            }
        }
        $mm = new \Model_Messaging();
        $mm->send_template(
            'timesheet-request-created',
            null,
            null,
            $recipients,
            $params
        );
    }
    
    
    public function submitTimesheet($timesheetId, $staffId, $reviewerId, $note = null)
    {
        $timesheet = $this->timesheetService->findById($timesheetId);
        $staff = $this->staffService->findById($staffId);
        if (\Auth::instance()->has_access('timesheets_edit') || $timesheet->getStaffId() == $staff->getId() || $staff->isManagerOf($timesheet->getDepartmentId())) {
            $timesheet->setNote($note);
            $timesheet->setReviewerId($reviewerId);
            $this->timesheetService->submit($timesheet);
        } else {
            throw new PermissionException('Only timesheet owner or deprtment manager can submit timesheet');
        }
    }
    
    public function approveTimesheet($timesheetId, $staffId, $note = null)
    {
        $timesheet = $this->timesheetService->findById($timesheetId);
        $staff = $this->staffService->findById($staffId);
        if (\Auth::instance()->has_access('timesheets_edit') || (\Auth::instance()->has_access('timesheets_edit_limited') && $staff->isManagerOf($timesheet->getDepartmentId()))) {
            $timesheet->setNote($note);
            $this->timesheetService->approve($timesheet);
        } else {
            throw new PermissionException('Only department manager can approve timesheet');
        }
    }

    public function rejectTimesheet($timesheetId, $staffId, $note = null)
    {
        $timesheet = $this->timesheetService->findById($timesheetId);
        $staff = $this->staffService->findById($staffId);
        if (\Auth::instance()->has_access('timesheets_edit') || (\Auth::instance()->has_access('timesheets_edit_limited') && $staff->isManagerOf($timesheet->getDepartmentId()))) {
            $timesheet->setNote($note);
            $this->timesheetService->reject($timesheet);
        } else {
            throw new PermissionException('Only department manager can reject timesheet');
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
        $response = new \Ideabubble\Timesheets\Response\Request();
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
        $response->schedule_id = $request->getScheduleId();
        $response->todo_id = $request->getTodoId();
        $response->created_at = $request->getCreatedAt();
        $response->staff_updated_at = $request->getStaffUpdatedAt();
        $response->manager_updated_at = $request->getManagerUpdatedAt() ? $request->getManagerUpdatedAt() : null;
        $response->description = $request->getDescription();
        
        if ($request->getType() == 'course' && $request->getScheduleId() != 0) {
            $item = $this->scheduleService->findById($request->getScheduleId());
            $response->item = ['id'=>$request->getScheduleId(), 'type'=>'course', 'title' => $item->getName()];
        }

        if ($request->getType() == 'internal'  && $request->getTodoId() != 0) {
            $item = $this->todos->get_todo($request->getTodoId());
            $response->item = ['id'=>$request->getTodoId(), 'type'=>'internal', 'title' => $item['title']];
        }

        $notes = $this->getNotes($request->getId());
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
    private function getConfigLevels(Ref $ref)
    {
        $levels = [];
        if ($ref->getLevel() == Ref::LEVEL_STAFF) {
            $levels[] = $ref;
            $staff = $this->staffService->findById($ref->getItemId());
            $assignments = $staff->getAssignments();
            foreach ($assignments as $aitem) {
                if ($aitem->getRole() == 'staff') {
                    $levels[] = new Ref(Ref::LEVEL_DEPARTMENT, $aitem->getDepartmentId());
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
    
    public function minutesAvailable(Ref $ref)
    {
        if ($ref->getLevel() == Ref::LEVEL_TIMESHEET) {
            $timesheet = $this->timesheetService->findById($ref->getItemId());
            $ref = new Ref(Ref::LEVEL_STAFF, $timesheet->getStaffId());
        }
        $hoursAvailable = $this->configService->get('timeoff.log_hours_per_week', $this->getConfigLevels($ref)) ?? 40;
        $count = 1;
        if ($ref->getLevel() == Ref::LEVEL_ORGANIZATION) {
            $dto = new StaffSearchDto();
            $count = $this->staffService->count($dto);
        }
        if ($ref->getLevel() == Ref::LEVEL_DEPARTMENT) {
            $dto = new StaffSearchDto();
            $dto->departmentId = $ref->getItemId();
            $count = $this->staffService->count($dto);
        }
        return $hoursAvailable * $count * 60;
    }
    
    public function sumDuration(RequestSearchDto $dto)
    {
        return intval($this->requestRepository->sumDuration($dto));
    }
    
    public function minutesLogged(Ref $ref, Type $type, $startDate, $endDate)
    {
        $dto = new RequestSearchDto();
        if ($ref->getLevel() == Ref::LEVEL_TIMESHEET) {
            $dto->timesheetId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_STAFF) {
            $dto->staffId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_DEPARTMENT) {
            $dto->departmentId = $ref->getItemId();
        } else if ($ref->getLevel() == Ref::LEVEL_ORGANIZATION) {
            $dto->businessId = $ref->getItemId();
        }
        $dto->type = [$type->getValue()];
        $dto->startDate = $startDate;
        $dto->endDate = $endDate;
        return $this->sumDuration($dto);
    }
    
    public function daysApproved(Ref $ref, $startDate, $endDate)
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
        $dto->status = [Status::APPROVED];
        $dto->type = [Type::COURSE, Type::INTERNAL];
        $dto->startDate = $startDate;
        $dto->endDate = $endDate;
        $items = $this->findAll($dto);
        $dayLength = $this->configService->get('timeoff.day_length', $this->getConfigLevels($ref));
        if (count($items) > 0) {
            foreach ($items as $item) {
                $count = $count + ceil($item->getPeriod()->getDuration() / ($dayLength * 60));
            }
        }
        return $count;
    }
    
    public function daysLeft(Ref $ref, $staftDate, $endDate)
    {
        return $this->daysInLieu($ref, $staftDate, $endDate) - $this->daysApproved($ref, $staftDate, $endDate);
    }
    
    public function daysInLieu(Ref $ref, $startDate, $endDate)
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
        $dto->status = [Status::APPROVED];
        //$dto->type = [Type::LIEU];
        $dto->startDate = $startDate;
        $dto->endDate = $endDate;
        $items = $this->findAll($dto);
        $dayLength = $this->configService->get('timeoff.day_length', $this->getConfigLevels($ref));
        if (count($items) > 0) {
            foreach ($items as $item) {
                $count = $count + ceil($item->getPeriod()->getDuration() / ($dayLength * 60));
            }
        }
        return $count;
    }
    
    public function updateDuration(Request $request)
    {
        $period = $request->getPeriod();
        $request->setPeriod(
            new Period($request->getPeriod()->getStartDate(),
                $request->getPeriod()->getEndDate(),
                $this->getDuration($period->getStartDate(), $period->getEndDate())
            )
        );
    }
    
    public function getDuration($startDate, $endDate)
    {
        $calc = new PeriodCalculator();
        return $calc->calculate($startDate,$endDate);
    }
    
    
    
    
    
    
    
    
    
    
    
}