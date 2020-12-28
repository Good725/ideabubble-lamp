<?php defined('SYSPATH') or die('No direct script access.');

class Model_MessagingRecipientProviderPost implements Model_MessagingRecipientProvider
{
    public function pid()
    {
        return "POST_VAR";
    }

    public function supports($driver)
    {
        return in_array($driver, array('sms', 'email'));
    }

    public function get_by_id($id)
    {
        return array('id' => $id, 'label' => $id);
    }

    public function get_by_label($label)
    {
        return array('id' => $label, 'label' => $label);
    }

    public function search($term)
    {
        return array();
    }

    public function to_autocomplete($term, &$data)
    {
        if (preg_match('/^[a-z_][a-z0-9_]*$/i', $term)) {
            $data[] = array(
                'value' => $term,
                'label' => $term,
                'category' => $this->pid(),
                'ask_input' => true
            );
        }
    }

    public function resolve_final_targets($target, &$target_list, &$warnings)
    {
        $post     = Request::$current->post();
        $driver   = isset($target['driver']) ? $target['driver'] : '';
        $variable = isset($target['target']) ? $target['target'] : '';

        if (isset($post[$variable])) {
            $value_stripped = preg_replace('/\s+/', '', $post[$variable]);

            if ($driver == 'sms') {
                if (!preg_match('/^\+?\d+$/', $value_stripped)) {
                    $warnings[] = 'invalid phone number {' . htmlentities($post[$variable]) . '}';
                } else {
                    $target['target'] = $value_stripped;
                    $target_list[] = $target;
                }
            } else if ($driver == 'email') {
                if (!preg_match('/^[a-z0-9\_\-\.]+\@([a-z0-9\_\-]+\.){1,}[a-z]{2,3}$/i', $value_stripped)) {
                    $warnings[] = 'invalid email {' . htmlentities($post[$variable]) . '}';
                } else {
                    $target['target'] = $value_stripped;;
                    $target_list[] = $target;
                }
            } else {
                $warnings[] = $driver . ' messaging is not supported';
            }
        }
    }

    public function message_details_column()
    {
        return null;
    }

    public function message_details_join($query)
    {

    }
}