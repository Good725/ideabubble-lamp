<?php

class SafetyIncidentTest extends Unittest_TestCase
{
    /**
     * Very basic test. See if the model function for saving an incident works and records a given title.
     */
    public function test_save_incident()
    {
        $db = Database::instance();
        $db->commit();

        // Attempt to save an incident
        $incident = new Model_Safety_Incident();
        $incident->save_data([
            'title' => 'Unittest example',
            'date' => date('Y-m-d'),
            'time' => date('H:i')
        ]);

        // Try to reload the incident that got saved.
        $check_incident = new Model_Safety_Incident($incident->id);

        // See if the name set earlier was saved
        $saved = ($check_incident->title == 'Unittest example');

        // Don't clog the database with testing data
        $db->rollback();

        $this->assertTrue($saved, 'Incident saved');
    }

}