<?php


namespace Ideabubble\Timesheets\Kohana;
use DB;
use Ideabubble\Timesheets\Dto\RequestSearchDto;
use Ideabubble\Timesheets\RequestService;
use DateTimeImmutable;
use DateInterval;
use Ideabubble\Timesheets\StaffService;
use Model_Schedules;

class DetailsReport
{
    /**
     * @var RequestService
     */
    private $requestService;
    /**
     * @var StaffService
     */
    private $staffService;
    
    public function __construct(RequestService $requestService, StaffService $staffService)
    {
        $this->requestService = $requestService;
        $this->staffService = $staffService;
    }
    
    public function run(RequestSearchDto $dto, $periodType, $dayLength)
    {
        $dt1 = new DateTimeImmutable($dto->startDate);
        $dt2 = new DateTimeImmutable($dto->endDate);
        $dt2 = $dt2->add(new DateInterval('PT'.(60*60*24 - 1) . 'S'));
        if ($periodType == 'days') {
            $periodLength = 1;
        } else if ($periodType == 'weeks') {
            $periodLength = 7;
        }
        $c = $dt1;
        $periods = [];
        while ($c < $dt2) {
            $endPeriod = $c->add(new DateInterval('PT'.(60*60*24*$periodLength - 1) . 'S'));
            $periods[] = [$c, $endPeriod];
            $c = $endPeriod->add(new DateInterval('PT1S'));
        }
        // setup contacts
        $data = [];
        foreach ($periods as $period) {
            $dto1 = clone $dto;
            $dto1->datesMode = RequestSearchDto::DATES_OVERLAP;
            $dto1->startDate = $period[0]->format('Y-m-d H:i:s');
            $dto1->endDate = $period[1]->format('Y-m-d H:i:s');
            $data[] = $this->requestService->findAll($dto1);
        }
        $contacts = [];

        foreach (Model_Schedules::get_trainers_in_active_schedules($dto->startDate, $dto->endDate) as $row_index => $trainer) {
            $staff = $this->staffService->findById($trainer['id']);
            $contacts[$trainer['id']] = new DetailsReportRow($staff, count($periods), $dayLength, $dto);
        }
        foreach ($data as $periodIndex => $row) {
            foreach ($row as $request) {
                if (!isset($contacts[$request->getStaffId()])) {
                    $staff = $this->staffService->findById($request->getStaffId());
                    if (!$staff) {
                        continue;
                    }
                    $contacts[$request->getStaffId()] = new DetailsReportRow($staff, count($periods), $dayLength, $dto);
                }
                $contacts[$request->getStaffId()]->add($request, $periodIndex);
            }
        }

        $thead = '';
        $thead .= '<th scope="col">User</th>';
        $thead .= '<th scope="col">Booked h</th>';
        $thead .= '<th scope="col">Logged h</th>';
        $thead .= '<th scope="col">Due €</th>';
        
        foreach ($periods as $period) {
            if ($periodType == 'days') {
                $thead .= '<th scope="col" data-period_start="' . $period[0]->format('Y-m-d') . '" data-period_end="' . $period[0]->format('Y-m-d') . '">' . $period[0]->format('D d') . '</th>';
            }
            if ($periodType == 'weeks') {
                if ($period[0]->format('M') != $period[1]->format('M')) {
                    $thead .= '<th scope="col" data-period_start="' . $period[0]->format('Y-m-d') . '" data-period_end="' . $period[1]->format('Y-m-d') . '">' . $period[0]->format('M d') . '-' . $period[1]->format('M d, Y') . '</th>';
                } else {
                    $thead .= '<th scope="col" data-period_start="' . $period[0]->format('Y-m-d') . '" data-period_end="' . $period[1]->format('Y-m-d') . '">' . $period[0]->format('M d') . '-' . $period[1]->format('d, Y') . '</th>';
                }
            }
        }
        
        $tbody = '';
        foreach ($contacts as $contact) {
            $tbody .= $contact->render();
        }
    
        $totals = array_pad([], count($periods), 0);
        $grandTotal = 0;
        $tfoot = '';
        foreach ($contacts as $contact) {
            foreach ($periods as $index=>$period) {
                $totals[$index] += $contact->get($index);
                $grandTotal += $contact->get($index);
            }
        }
        
        $tfoot .= '<th>Totals</th>';
        $tfoot .= '<th></th>';
        $tfoot .= '<th>'.$this->f($grandTotal, 'h').'</th>';
        $tfoot .= '<th></th>';
        //$tfoot .= '<th>'.$this->f($grandTotal / $dayLength, 'd').'</th>';
        foreach ($totals as $val) {
            $tfoot .= '<th>' . $this->f($val, 'h') . '</th>';
        }
        return '<thead>'.$thead.'</thead>'.'<tbody>'.$tbody.'</tbody>'.'<tfoot>'.$tfoot.'</tfoot>';
    }

    public function csv(RequestSearchDto $dto, $periodType, $dayLength)
    {
        $dt1 = new DateTimeImmutable($dto->startDate);
        $dt2 = new DateTimeImmutable($dto->endDate);
        $dt2 = $dt2->add(new DateInterval('PT'.(60*60*24 - 1) . 'S'));
        if ($periodType == 'days') {
            $periodLength = 1;
        } else if ($periodType == 'weeks') {
            $periodLength = 7;
        }
        $c = $dt1;
        $periods = [];
        while ($c < $dt2) {
            $endPeriod = $c->add(new DateInterval('PT'.(60*60*24*$periodLength - 1) . 'S'));
            $periods[] = [$c, $endPeriod];
            $c = $endPeriod->add(new DateInterval('PT1S'));
        }
        // setup contacts
        $data = [];
        foreach ($periods as $period) {
            $dto1 = clone $dto;
            $dto1->startDate = $period[0]->format('Y-m-d H:i:s');
            $dto1->endDate = $period[1]->format('Y-m-d H:i:s');
            $data[] = $this->requestService->findAll($dto1);
        }
        $contacts = [];
        foreach ($data as $periodIndex => $row) {
            foreach ($row as $request) {
                if (!isset($contacts[$request->getStaffId()])) {
                    $staff = $this->staffService->findById($request->getStaffId());
                    if (!$staff) {
                        continue;
                    }
                    $contacts[$request->getStaffId()] = new DetailsReportRow($staff, count($periods), $dayLength, $dto);
                }
                $contacts[$request->getStaffId()]->add($request, $periodIndex);
            }
        }

        $size = 1024*1024*5;
        $csv = fopen('php://temp/maxmemory:' . $size, 'r+');
        $row = array();
        $row[] = 'User';
        $row[] = 'Total hours worked';

        foreach ($periods as $period) {
            if ($periodType == 'days') {
                $row[] = '' . $period[0]->format('D d') . '';
            }
            if ($periodType == 'weeks') {
                if ($period[0]->format('M') != $period[1]->format('M')) {
                    $row[] =  '' . $period[0]->format('M d') . '-' . $period[1]->format('M d, Y') . '';
                } else {
                    $row[] = '' . $period[0]->format('M d') . '-' . $period[1]->format('d, Y') . '';
                }
            }
        }
        fputcsv($csv, $row);

        foreach ($contacts as $contact) {
            $row = array();
            $row[] = $contact->staffName;
            $row[] = $contact->f($contact->total, 'h');

            foreach ($contact->data as $index => $cell) {
                $row[] = '' . $contact->f($cell, 'h') . '';
            }
            fputcsv($csv, $row);
        }

        $totals = array_pad([], count($periods), 0);
        $grandTotal = 0;
        foreach ($contacts as $contact) {
            foreach ($periods as $index=>$period) {
                $totals[$index] += $contact->get($index);
                $grandTotal += $contact->get($index);
            }
        }

        $row = array();
        $row[] = 'Totals';
        $row[] = '' . $this->f($grandTotal, 'h') . '';

        foreach ($totals as $val) {
            $row[] = '' . $this->f($val, 'h') . '';
        }
        fputcsv($csv, $row);

        rewind($csv);
        $response = fread($csv, $size);
        fclose($csv);

        return $response;
    }
    
    
    private function f($value, $suffix)
    {
        if (!empty($value)) {
            $hours = floor($value / 60);
            $minutes = $value % 60;
            return $hours . "h " . $minutes . "m";
        } else {
            return '';
        }
        
    }
    
    
        
        
        
        
        
        
    
}