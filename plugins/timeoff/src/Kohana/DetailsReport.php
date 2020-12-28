<?php


namespace Ideabubble\Timeoff\Kohana;
use DB;
use Ideabubble\Timeoff\Dto\RequestSearchDto;
use Ideabubble\Timeoff\RequestService;
use DateTimeImmutable;
use DateInterval;
use Ideabubble\Timeoff\StaffService;

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
    
    public function run(RequestSearchDto $dto, $periodType, $dayLength, $mode = 'html')
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
                if (!isset($contacts["{$request->getStaffId()},{$request->getType()}"])) {
                    $staff = $this->staffService->findById($request->getStaffId());
                    if (!$staff) {
                        continue;
                    }
                    $contacts["{$request->getStaffId()},{$request->getType()}"] = new DetailsReportRow($staff,
                        count($periods), $dayLength, ucfirst($request->getType()));
                }
                    $contacts["{$request->getStaffId()},{$request->getType()}"]->add($request, $periodIndex);

            }
        }

        if ($mode == 'html') {
            return $this->renderHTML($periods, $periodType, $contacts, $dayLength, $dto->startDate, $dto->endDate, $this->requestService);
        }
        if ($mode == 'csv') {
            return $this->renderCSV($periods, $periodType, $contacts, $dayLength);
        }
        

    }
    
    private function renderHTML($periods, $periodType, $contacts, $dayLength, $startDate, $endDate, $requestService)
    {
        $thead = '';
        $thead .= '<th scope="col">User</th>';
        $thead .= '<th scope="col">Type</th>';
        $thead .= '<th scope="col">Hours</th>';
        $thead .= '<th scope="col">Days</th>';

        $start_year = date('Y', strtotime($startDate));
        $end_year = date('Y', strtotime($endDate));

        for ($year = $start_year; $year <= $end_year; $year++) {
            $thead .= '<th scope="col">Days left ('.$year.')</th>';
        }
    
        foreach ($periods as $period) {
            if ($periodType == 'days') {
                $thead .= '<th scope="col">' . $period[0]->format('D d') . '</th>';
            }
            if ($periodType == 'weeks') {
                if ($period[0]->format('M') != $period[1]->format('M')) {
                    $thead .= '<th scope="col">' . $period[0]->format('M d') . '-' . $period[1]->format('M d, Y') . '</th>';
                } else {
                    $thead .= '<th scope="col">' . $period[0]->format('M d') . '-' . $period[1]->format('d, Y') . '</th>';
                }
            }
        }
    
        $tbody = '';
        foreach ($contacts as $contact) {
            $tbody .= $contact->render($startDate, $endDate, $requestService);
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
        $tfoot .= '<th>'.$this->f($grandTotal / 60, 'h').'</th>';
        $tfoot .= '<th>'.$this->f($grandTotal / (60 * $dayLength), 'd').'</th>';

        for ($year = $start_year; $year <= $end_year; $year++) {
            $tfoot .= '<th scope="col"></th>';
        }

        foreach ($totals as $val) {
            $tfoot .= '<th>' . $this->f($val / 60, 'h') . '</th>';
        }
        return '<thead>'.$thead.'</thead>'.'<tbody>'.$tbody.'</tbody>'.'<tfoot>'.$tfoot.'</tfoot>';
        
    }
    
    private function renderCSV($periods, $periodType, $contacts, $dayLength)
    {
        $size = 1024*1024*5;
        $csv = fopen('php://temp/maxmemory:' . $size, 'r+');
        $titles = [];
        $titles[] = 'User';
        $titles[] = 'Hours';
        $titles[] = 'Days';
        foreach ($periods as $period) {
            if ($periodType == 'days') {
                $titles[] = $period[0]->format('D d');
            }
            if ($periodType == 'weeks') {
                if ($period[0]->format('M') != $period[1]->format('M')) {
                    $titles[] = $period[0]->format('M d') . '-' . $period[1]->format('M d, Y');
                } else {
                    $titles[] = $period[0]->format('M d') . '-' . $period[1]->format('d, Y');
                }
            }
        }
        fputcsv($csv, $titles, ';');
        
    
        foreach ($contacts as $contact) {
            $contact->renderCSV($csv);
        }
    
        $totals = array_pad([], count($periods), 0);
        $grandTotal = 0;
        $tfoot = [];
        foreach ($contacts as $contact) {
            foreach ($periods as $index=>$period) {
                $totals[$index] += $contact->get($index);
                $grandTotal += $contact->get($index);
            }
        }
    
        $tfoot[] = 'Totals';
        $tfoot[] = $this->f($grandTotal / 60, 'h');
        $tfoot[] = $this->f($grandTotal / (60 * $dayLength), 'd');
        foreach ($totals as $val) {
            $tfoot[] = $this->f($val / 60, 'h');
        }
        fputcsv($csv, $tfoot, ';');
        rewind($csv);
        $response = fread($csv, $size);
        fclose($csv);
        return "sep=;\n" . $response;
        
    }
    
    
    private function f($value, $suffix)
    {
        if (!empty($value)) {
            return round($value, 1) . $suffix;
        } else {
            return '';
        }
        
    }
    
    
        
        
        
        
        
        
    
}