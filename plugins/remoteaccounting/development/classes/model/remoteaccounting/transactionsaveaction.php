<?php

class Model_Remoteaccounting_TransactionSaveAction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Remote Accounting Save Transaction';
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('transaction_id');
    }

    public function run($params = array())
    {
        try {
            if (!isset($params['transaction_id'])) {
                return;
            }
            if (Settings::instance()->get('remoteaccounting_api') != '') {
                $ac = new Model_Remoteaccounting();
                $transaction = DB::select('transactions.*', 'bookings.payment_method', 'bookings.invoice_details')
                    ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                        ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                            ->on('transactions.booking_id', '=', 'bookings.booking_id')
                    ->where('id', '=', $params['transaction_id'])
                    ->execute()
                    ->current();
                if ($transaction) {
                    if (@$params['payment_method']) {
                        $transaction['payment_method'] = $params['payment_method'];
                    }
                    if (!isset($transaction['items'])) {
                        $schedules = DB::select(
                            DB::expr("GROUP_CONCAT(schedules.name) as description")
                        )
                            ->from(array(Model_Kes_Transaction::TABLE_HAS_SCHEDULES, 'has_schedules'))
                            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                            ->on('has_schedules.schedule_id', '=', 'schedules.id')
                            ->where('transaction_id', '=', $transaction['id'])
                            ->and_where('has_schedules.deleted', '=', 0)
                            ->execute()
                            ->get('description');
                        $courses = DB::select(
                            DB::expr("GROUP_CONCAT(courses.title) as description")
                        )
                            ->from(array(Model_Kes_Transaction::TABLE_HAS_COURSES, 'has_courses'))
                            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                            ->on('has_courses.course_id', '=', 'courses.id')
                            ->where('transaction_id', '=', $transaction['id'])
                            ->and_where('has_courses.deleted', '=', 0)
                            ->execute()
                            ->get('description');
                        $transaction['items'][] = array(
                            'amount' => $transaction['total'],
                            'description' => trim($schedules . ',' . $courses, ',')
                        );

                        $transaction['interest_total'] = DB::select(DB::expr("SUM(ppp.interest) as interest_total"))
                            ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'))
                            ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'ppp'), 'inner')
                            ->on('pp.id', '=', 'ppp.payment_plan_id')
                            ->where('pp.deleted', '=', 0)
                            ->and_where('ppp.deleted', '=', 0)
                            ->and_where('pp.transaction_id', '=', $transaction['id'])
                            ->execute()
                            ->get('interest_total');
                    }
                    if ($transaction['type'] == 1 || $transaction['type'] == 2 || $transaction['type'] == 7) {
                        if (@$params['aiq_customer_code']) {
                            $transaction['aiq_customer_code'] = $params['aiq_customer_code'];
                        }
                        $ac->save_transaction($transaction);
                    }
                }
            }
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }
}