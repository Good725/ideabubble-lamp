<?php
class MediaImapSyncEmailPostProcessor
{
    public function process($message)
    {
        try {
            Database::instance()->begin();
            $settings = Model_Media::get_external_sync('imap');
            $file_types = explode("\n", $settings['file_types']);

            $import = false;
            if ($settings['sync_accounts'])
            foreach ($message['recipients_final'] as $recipient) {
                if (in_array($recipient['target'], $settings['sync_accounts'])) {
                    $import = true;
                }
            }

            if ($import) {
                foreach ($message['attachments'] as $attachment) {
                    $extension = substr($attachment['name'], strpos($attachment['name'], '.'));
                    if (in_array($extension, $file_types) || true) {
                        $is_image = in_array($extension, array('.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tiff'));
                        $media = new Model_Media();
                        $media->add_media_file(
                            array(
                                'content' => base64_decode($attachment['content']),
                                'name' => $attachment['name'],
                                'type' => ''
                            ),
                            'imap'
                        );
                        $media->create_system_thumbnail($attachment['name'], 'photos/imap');
                    }
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
}