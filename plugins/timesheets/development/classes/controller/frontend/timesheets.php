<?php
use Ideabubble\Timesheets\Entity\Request;
use Ideabubble\Timesheets\Dto\RequestSearchDto;
use Ideabubble\Timesheets\Entity\Period;
use Ideabubble\Timesheets\Entity\Type;
use Ideabubble\Timesheets\RequestService;
use Ideabubble\Timesheets\Kohana\KohanaRequestRepository;
use Ideabubble\Timesheets\Entity\Note;
use Ideabubble\Timesheets\Dto\DeptSearchDto;

class Controller_Frontend_Timesheets extends Controller_Template
{
    private $requestService;
    private $staffService;
    private $departmentService;
    private $detailsReport;
    private $configService;
    private $scheduleService;
    private $timesheetService;
    /**
     * @var \Ideabubble\Timesheets\Kohana\KohanaGenerator
     */
    private $gen;
    
    public function __construct(\Request $request, \Response $response)
    {
        // would be nice to have dependency injection here, but its Kohana :)
        $dispatcher = timesheets_event_dispatcher();
        $this->gen = new \Ideabubble\Timesheets\Kohana\KohanaGenerator();
        $departmentRepository = new \Ideabubble\Timesheets\Kohana\KohanaDepartmentRepository();
        $this->scheduleService = new \Ideabubble\Timeoff\ScheduleService(new \Ideabubble\Timeoff\Kohana\KohanaScheduleRepository());
        $this->staffService = new \Ideabubble\Timesheets\StaffService(new \Ideabubble\Timesheets\Kohana\KohanaStaffRepository($dispatcher));
        $configService = new \Ideabubble\Timesheets\ConfigService(new \Ideabubble\Timesheets\Kohana\KohanaConfigRepository($dispatcher));
        $this->configService = $configService;
        $this->departmentService = new \Ideabubble\Timesheets\DepartmentService($departmentRepository);
        $timesheetRepository = new \Ideabubble\Timesheets\Kohana\KohanaTimesheetRepository($dispatcher);
        $this->timesheetService = new \Ideabubble\Timesheets\TimesheetService($timesheetRepository);
        $permissionManager = new \Ideabubble\Timesheets\Kohana\KohanaPermissionManager();
        $this->requestService = new RequestService(
            $permissionManager,
            $configService,
            new KohanaRequestRepository($dispatcher),
            new \Ideabubble\Timesheets\Kohana\KohanaNoteRepository($dispatcher),
            $this->staffService,
            $departmentRepository,
            $this->scheduleService,
            $this->timesheetService,
            new \Model_Todos(),
            $this->gen
        );
        $this->detailsReport = new \Ideabubble\Timesheets\Kohana\DetailsReport($this->requestService, $this->staffService);
        
        
        parent::__construct($request, $response);
    }
    
    public function action_cron_create()
    {
        $this->auto_render = false;
        $this->requestService->createTimesheets();
        echo 'Timesheets created' . "\n";
    }
    
    
}