<?php


namespace Ideabubble\Timeoff\Kohana;


use Ideabubble\Timeoff\Entity\Request;
use Ideabubble\Timeoff\Entity\Staff;

class DetailsReportRow
{
    public $staffId;
    public $staffName;
    public $total = 0;
    private $periodCount;
    private $data;
    private $periodRequests = [];
    private $dayLength;
    private $type;
    
    public function __construct(Staff $staff, $periodCount, $dayLength, $type = '')
    {
        $this->periodCount = $periodCount;
        $this->staffId = $staff->getId();
        $this->staffName = $staff->getName();
        $this->data = array_pad([], $periodCount, 0);
        $this->dayLength = $dayLength;
        $this->type = $type;
    }
    
    public function add(Request $request, $periodIndex)
    {
        $this->data[$periodIndex] += floatval($request->getPeriod()->getDuration());
        $this->total += $request->getPeriod()->getDuration();
        if (!isset($this->periodRequests[$periodIndex])) {
            $this->periodRequests[$periodIndex] = [];
        }
        if (!in_array($request->getId(), $this->periodRequests[$periodIndex])) {
            $this->periodRequests[$periodIndex][] = $request->getId();
        }
    }
    
    public function get($periodIndex)
    {
        return $this->data[$periodIndex];
    }
    
    public function render($startDate, $endDate)
    {
        $contact = new \Model_Contacts3($this->staffId);
        $start_year = date('Y', strtotime($startDate));
        $end_year = date('Y', strtotime($endDate));

        $html = '<tr data-user_id="' . $this->staffId . '">';
        $html .= '<td>' . $this->staffName . '</td>'; // User
        $html .= '<td>' . $this->type . '</td>'; // Request type
        $html .= '<td>' . $this->f($this->total / 60,'h') . '</td>'; // Hours
        $html .= '<td>' . $this->f($this->total / (60 * $this->dayLength), 'd') . '</td>'; // Days

        if(strtolower($this->type) === 'annual') {
            for ($year = $start_year; $year <= $end_year; $year++) {
                $days_remaining = $contact->count_timeoff_days_remaining($year, $this->dayLength);
                $html .= '<td>'.$this->f($days_remaining, 'd').'</td>'; // days remaining (each  year)
            }
        } else {
            $html .= '<td></td>';
        }

        
        foreach ($this->data as $index=>$cell) {
            $idList = isset($this->periodRequests[$index]) ? implode(',', $this->periodRequests[$index]) : '';
            $html .= '<td class="timeoff-details-table-request" data-request_ids="['.$idList.']">' . $this->f($cell / 60, 'h') . '</td>';
        }
        $html .= '</tr>';
        
        return $html;
    }

    public function renderCSV($h)
    {
        $cols = [];
        $cols[] = $this->staffName;
        $cols[] = $this->f($this->total / 60,'h');
        $cols[] = $this->f($this->total / (60 * $this->dayLength), 'd');

        foreach ($this->data as $index=>$cell) {
            $cols[] = $this->f($cell / 60, 'h');
        }
        fputcsv($h, $cols, ';');
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