<?php defined ('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Allpoints extends Controller_cms
{
    public function action_mark_invoiced()
    {
        $invoiced = $this->request->post('invoiced');
        $now = date::now();
        $db = Database::instance();
        $db->begin();
        foreach ($invoiced as $id => $mark) {
            if ($mark) {
                $updated = DB::update(Model_Allpoints::TRANSACTIONS_TABLE)
                    ->set(array('invoiced' => $now))
                    ->where('id', '=', $id)
                    ->and_where('invoiced', 'is', null)
                    ->execute();
            } else {
                $updated = DB::update(Model_Allpoints::TRANSACTIONS_TABLE)
                    ->set(array('invoiced' => null))
                    ->where('id', '=', $id)
                    ->execute();
            }
            $invoiced[$id] = $updated;
        }
        $db->commit();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($invoiced);
    }
}