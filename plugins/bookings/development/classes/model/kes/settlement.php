<?php defined('SYSPATH') or die('No direct script access.');

class Model_Kes_Settlement extends Model
{
	const SETTLEMENTS_TABLE = 'plugin_bookings_settlements';
	const SETTLEMENT_PAYMENTS_TABLE = 'plugin_bookings_settlements_payments';

	public static function list_settlements()
	{
		$settlements = DB::select('*')
							->from(self::SETTLEMENTS_TABLE)
							->order_by('date_created', 'desc')
							->execute()
							->as_array();
		return $settlements;
	}
	
	public static function settlement_details($id)
	{
		$result['settlement'] = DB::select('*')
									->from(self::SETTLEMENTS_TABLE)
                                    ->where('id', '=', $id)
									->order_by('date_created', 'desc')
									->execute()
									->as_array();
		if($result['settlement']){
			$result['details'] = DB::select('sp.*', 'p.created', 'b.booking_id', 'bs.schedule_id', array('s.name', 'schedule'), 's.trainer_id', DB::expr("CONCAT_WS(' ', c.title, c.first_name, c.last_name) AS trainer"))
									->from(array(self::SETTLEMENT_PAYMENTS_TABLE, 'sp'))
									->join(array('plugin_bookings_transactions_payments', 'p'), 'inner')->on('sp.payment_id', '=', 'p.id')
									->join(array('plugin_bookings_transactions', 't'), 'inner')->on('p.transaction_id', '=', 't.id')
									->join(array('plugin_bookings_transactions_has_schedule', 'ts'), 'inner')->on('t.id', '=', 'ts.transaction_id')->on('ts.deleted', '=', DB::expr(0))
                                    ->join(array('plugin_courses_schedules', 's'), 'inner')->on('ts.schedule_id', '=', 's.id')
                                    ->join(array('plugin_ib_educate_bookings', 'b'), 'inner')->on('t.booking_id', '=', 'b.booking_id')
                                    ->join(array('plugin_ib_educate_booking_has_schedules','bs'), 'inner')->on('bs.booking_id','=','b.booking_id')->on('ts.schedule_id', '=', 'bs.schedule_id')->on('bs.deleted', '=', DB::expr(0))
									->join(array('plugin_contacts3_contacts', 'c'), 'inner')->on('s.trainer_id', '=', 'c.id')
                                    ->where('sp.settlement_id', '=', $id)
									->order_by('p.created')
									->execute()
									->as_array();
			$result['stats'] = array('trainers' => array(), 'schedules' => array());
			if($result['details']){
				foreach($result['details'] as $payment){
					if(!isset($result['stats']['trainers'][$payment['trainer_id']])){
						$result['stats']['trainers'][$payment['trainer_id']] = array('amount' => 0, 
																						'rental' => 0,
																						'trainer' => $payment['trainer']);
					}
					$result['stats']['trainers'][$payment['trainer_id']]['amount'] += $payment['amount'];
					$result['stats']['trainers'][$payment['trainer_id']]['rental'] += $payment['rental'];
					
					if(!isset($result['stats']['schedules'][$payment['schedule_id']])){
						$result['stats']['schedules'][$payment['schedule_id']] = array('amount' => 0, 
																						'rental' => 0,
																						'schedule' => $payment['schedule']);
					}
					$result['stats']['schedules'][$payment['schedule_id']]['amount'] += $payment['amount'];
					$result['stats']['schedules'][$payment['schedule_id']]['rental'] += $payment['rental'];
				}
				uasort($result['stats']['trainers'], function($t1, $t2){
					return strcasecmp($t1['trainer'], $t2['trainer']);
				});
				uasort($result['stats']['schedules'], function($s1, $s2){
					return strcasecmp($s1['schedule'], $s2['schedule']);
				});
			}
		}
		return $result;
	}

	public static function settle_payg($post)
	{
		$user = Auth::instance()->get_user();
		//var_dump($post);exit();
		Database::instance()->begin();
        try {
            preg_match('/(\d+)y(\d+)/', $post['month'], $paygMonth);
            $paygDate = $paygMonth[2] . '-' . str_pad($paygMonth[1], 2, '0', STR_PAD_LEFT) . '-01';
            $paygTime = strtotime($paygDate . ' 00:00:00');

			$settlement = array('amount' => $post['amount'],
								'settlement' => 'PAYG ' . date('M, Y', $paygTime),
								'date_created' => date('Y-m-d H:i:s'),
								'settled_by' => $user['id'],
                                'for_month' => $paygDate);
			$settlement_id = DB::insert(self::SETTLEMENTS_TABLE, array_keys($settlement))->values($settlement)->execute();
			$settlement_id = $settlement_id[0];

            $payments = json_decode($post['payments'], true);
			foreach($payments as $payment){
                $payment_id = $payment['payment_id'];
                $schedule_id = $payment['schedule_id'];
				$spayment = array('settlement_id' => $settlement_id, 
									'payment_id' => $payment_id);
				//$payment = DB::select('*')->from('plugin_bookings_transactions_payments')->where('id', '=', $payment_id);
				$pschedules = DB::select('p.amount', 's.rental_fee')
								->from(array('plugin_bookings_transactions_payments', 'p'))
									->join(array('plugin_bookings_transactions', 't'), 'inner')
                                        ->on('p.transaction_id', '=', 't.id')
									->join(array('plugin_ib_educate_bookings', 'b'), 'inner')
                                        ->on('t.booking_id', '=', 'b.booking_id')
                                    ->join(array('plugin_ib_educate_booking_has_schedules', 'bs'), 'inner')
                                        ->on('b.booking_id', '=', 'bs.booking_id')
                                        ->on('bs.deleted', '=', DB::expr(0))
									->join(array('plugin_courses_schedules', 's'), 'inner')
                                        ->on('bs.schedule_id', '=', 's.id')
                                        ->on('s.delete', '=', DB::expr(0))
								->where('p.id', '=', $payment_id)
                                ->where('s.id', '=', $schedule_id)
								->execute()
								->as_array();
				if($pschedules){
                    foreach ($pschedules as $pschedule) {
                        $spayment['amount'] = $pschedule['amount'];
                        $spayment['rental'] = round($pschedule['amount'] * ($pschedule['rental_fee'] / 100.0), 2);
                        $spayment['income'] = $spayment['amount'] - $spayment['rental'];
                        DB::insert(self::SETTLEMENT_PAYMENTS_TABLE, array_keys($spayment))
                            ->values($spayment)
                            ->execute();
                    }
				}
			}
			Database::instance()->commit();
			return true;
        } catch(Exception $e) {
			Database::instance()->rollback();
			throw $e;
        }
	}
}
