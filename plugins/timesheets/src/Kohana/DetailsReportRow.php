<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\Entity\Request;
use Ideabubble\Timesheets\Entity\Staff;
use Model_Contacts3;

class DetailsReportRow
{
    public $staffId;
    public $staffName;
    public $total = 0;
    public $periodCount;
    public $data;
    public $periodRequests = [];
    public $dayLength;
    public $booked = 0;
    public $due = 0;
    public $hourly_rate = 0;
    
    public function __construct(Staff $staff, $periodCount, $dayLength, $dto = null)
    {
        $this->periodCount = $periodCount;
        $this->staffId = $staff->getId();
        $this->staffName = $staff->getName();
        $this->data = array_pad([], $periodCount, 0);
        $this->dayLength = $dayLength;
        $contact = new Model_Contacts3($this->staffId);
        $this->booked   = $contact->get_total_minutes_assigned_schedule($dto->startDate, $dto->endDate);
        $this->hourly_rate = $contact->get_hourly_rate() ?? 0;
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
        $this->due = round($this->hourly_rate * ($this->total / 60), 2);
        $this->due = ($this->due == 0) ? '0' : $this->due;
    }
    
    public function get($periodIndex)
    {
        return $this->data[$periodIndex];
    }
    
    public function render()
    {
        $html = '<tr data-user_id="' . $this->staffId . '">';
        $html .= '<td>' . $this->staffName . '</td>';
        $html .= '<td>' . $this->f($this->booked, 'h') .  '</td>';
        $html .= '<td>' . $this->f($this->total, 'h') . '</td>';
        $html .= '<td>' . $this->due . '</td>';
        foreach ($this->data as $index=>$cell) {
            $idList = isset($this->periodRequests[$index]) ? implode(',', $this->periodRequests[$index]) : '';
            $html .= '<td class="timeoff-details-table-request" data-request_ids="['.$idList.']">' . $this->f($cell, 'h') . '</td>';
        }
        $html .= '</tr>';
        
        return $html;
    }

    public function f($value, $suffix)
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