<?php defined('SYSPATH') or die('No direct script access.');
class Model_Reports_Sparkline extends ORM
{
	protected $_table_name = 'plugin_reports_sparklines';
	protected $_belongs_to = array(
		'chart_type' => array('model' => 'Reports_Charttype', 'foreign_key' => 'chart_type_id'),
		'total_type' => array('model' => 'Reports_totaltype', 'foreign_key' => 'total_type_id')
	);

	public function render()
	{
		try {
			$report      = new Model_Reports($this->report_id);
			$report->get(TRUE);
			$sql_result  = $report->execute_sql(TRUE);
			$comparisons = FALSE;

			// For the "comparison totals", we want to get the results for the currently-selected range
			// ... and the results for the previous and next day/week/month/year
			// ... and compare the totals
			if ($this->chart_type->stub == 'comparison_total')
			{
				$session       = Session::instance();
				$current_from  = $session->get('dashboard-from');
				$current_to    = $session->get('dashboard-to');
				$range_type    = $session->get('dashboard-range_type');
				$date_diff     = strtotime($current_to) - strtotime($current_from);
				$days_in_range = floor($date_diff/(60*60*24));

				if ($range_type != '')
				{
					$report->get(TRUE);
					// Change the session variables to the previous day/week/month/year and rerun the SQL
					if ($range_type == 'Custom')
					{
						$session->set('dashboard-from', date('Y-m-d', strtotime("$current_from -$days_in_range days")));
						$session->set('dashboard-to',   date('Y-m-d', strtotime("$current_to   -$days_in_range days")));
					}
					else
					{
						$session->set('dashboard-from', date('Y-m-d', strtotime("$current_from -1 $range_type")));
						$session->set('dashboard-to',   date('Y-m-d', strtotime("$current_to   -1 $range_type")));
					}

					$prev_sql_result = $report->execute_sql(TRUE);

					// Get the totals
					$prev_total = $next_total = 0;
					foreach ($prev_sql_result as $row) $prev_total += $row[$this->total_field];
					$comparisons = array('prev_total' => $prev_total, 'range' => $range_type, 'days_in_range' => $days_in_range);

					// Reset the session variables to the current range
					$session->set('dashboard-from', $current_from);
					$session->set('dashboard-to',   $current_to);
				}

			}

			$graph_points      = '';
			$total             = 0;
			$currency          = isset($sql_result[0]['currency']) ? $sql_result[0]['currency'] : '';
			foreach ($sql_result as $row)
			{
				if (isset ($row[$this->x_axis]))
				{
					$graph_points .= $row[$this->x_axis].',';
				}
				else
				{
					$graph_points .= $row[$this->total_field];
				}
				$total        += $row[$this->total_field];
			}
			$graph_points = rtrim($graph_points, ',');

			return View::factory('sparkline')
				->set('sparkline', $this)
				->set('graph_points', $graph_points)
				->set('comparisons', $comparisons)
				->set('currency', $currency)
				->set('total', $total);
		}
		catch (Exception $e)
		{
            Log::instance()->add(Log::ERROR, "Error rendering sparkline.\nReport ID: " . $this->report_id . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
			return '';
		}
	}

}
