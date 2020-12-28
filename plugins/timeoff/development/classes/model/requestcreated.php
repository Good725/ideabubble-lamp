<?php


class Model_Requestcreated
{
    public function handle(\Ideabubble\Timeoff\Entity\Event\RequestCreated $event)
    {
        
        $message = new Model_Messaging();
        $contacts = [
            [
                'target_type' => 'CMS_CONTACT3',
                'target' => $event->requestId
            ]
        ];
        $params = [
            'staff' => 0,
            'link' => 'http://example.com'
        ];
        $message->send_template('timeoff-manager-request-created', null, date('Y-m-d H:i:s'), $contacts, $params);
    }
}