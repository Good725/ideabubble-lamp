<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Safety extends Controller
{
    public static function embed_form()
    {
        $locations  = ORM::factory('Course_Location')->order_by('name')->find_all_published();
        $incident   = new Model_Safety_Incident();
        $severities = $incident->get_enum_options('severity');
        $statuses   = $incident->get_enum_options('status');

        $view = View::factory('admin/incident_form')->set([
            'mode'          => 'frontend',
            'locations'     => $locations,
            'severities'    => $severities,
            'statuses'      => $statuses
        ]);

        $script = URL::get_engine_plugin_asset('safety', 'js/safety.js', ['script_tags' => true, 'cachebust' => true]);

        return $view.$script;
    }

    public function action_save_incident()
    {
        $db = Database::instance();
        $db->begin();

        try {
            $post = $this->request->post();

            // Save the reporter, if new
            $reporter = Auth::instance()->get_contact();

            if (!$reporter->id && isset($post['reporter'])) {
                $reporter->save_data($post['reporter']);
            }

            // Save the incident
            // todo: filter post down to just the fields the end user is allowed to control
            $incident = new Model_Safety_Incident();
            $data = $post + ['reporter_id' => $reporter->id, 'severity' => ''];
            $incident->save_data($data);

            $incident->send_notifications(['admin', 'reporter']);

            $db->commit();

            IbHelpers::set_message(htmlspecialchars('Incident has been reported'), 'success popup_box');

            $this->request->redirect('/report-incident-thank-you');
        }
        catch (Exception $e) {
            $db->rollback();
            Log::instance()->add(Log::ERROR, "Error saving incident.\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Unexpected error saving incident. If this problem continues, please ask an administrator to check the error logs.', 'error popup_box');
            $this->request->redirect('/report-incident');
        }


    }
}
