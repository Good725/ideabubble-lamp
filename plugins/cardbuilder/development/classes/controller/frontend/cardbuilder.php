<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Cardbuilder extends Controller_Template
{

    public function action_save_card()
    {
        $this->auto_render = FALSE;
        $post              = $this->request->post();
        $user              = Auth::instance()->get_user();
        $model             = new Model_cardbuilder($post['id']);
        $role_model        = new Model_Roles;
        $role              = $role_model->get_name($user['role_id']);

        // Backend insurance to stop someone editing someone else's card
        if ($role == 'Card Holder' AND $user['id'] != $model->get_user_id() AND $model->get_user_id() != NULL)
        {
            IbHelpers::set_message('You do not have permission to edit someone else\'s card.', 'error');
            $location = '/card-builder-orders.html';
        }
        else
        {
            $save_data['user_id']              = $user['id'];
            $save_data['id']                   = $post['id'];
            $save_data['employee_name']        = $post['employee_name'];
            $save_data['title']                = $post['title'];
			$save_data['post_nominal_letters'] = $post['post_nominal_letters'];
            $save_data['department']           = $post['department'];
            $save_data['telephone']            = $post['telephone'];
            $save_data['fax']                  = $post['fax'];
            $save_data['mobile']               = $post['mobile'];
            $save_data['email']                = $post['email'];
            $save_data['office_id']            = $post['office_id'];

            $model->set($save_data);

            if ($save_data['id'] == '')
            {
				if ($model->add())
				{
					IBHelpers::set_message('Card #'.$model->get_id().' added', 'success');
					$event_id     = Model_Notifications::get_event_id('new_card_created');
					$notification = new Model_Notifications($event_id);
					$body         = View::factory('email/new_card')->set('card', $model->get_instance())->set('user', Auth::instance()->get_user());
					$notification->send($body);
				}
				else
				{
					IBHelpers::set_message('Error adding card', 'error');
				}

            }
            else
            {
            	if ($model->save())
				{
					IBHelpers::set_message('Card #'.$model->get_id().' saved', 'success');
				}
				else
				{
					IBHelpers::set_message('Error saving card', 'error');
				}
            }

            $location = '/card-builder-orders.html'; // save and exit
            if (isset($post['location']) AND ( ! isset($post['redirect']) OR $post['redirect'] != 'save_and_exit'))
            {
                $location = $post['location'].'/'.$model->get_id(); // save
            }
        }
        $this->request->redirect($location);
    }

    public function action_delete_card()
    {
        $this->auto_render = FALSE;
        $user              = Auth::instance()->get_user();
        $role_model        = new Model_Roles;
        $role              = $role_model->get_name($user['role_id']);

        if ($role != 'Administrator')
        {
            IbHelpers::set_message('You do not have permission to delete cards.');
        }
        else
        {
            $id   = $this->request->param('id');
            $card = new Model_Cardbuilder($id);
            ($card->delete())
                ? IbHelpers::set_message('Card #'.$id.' successfully deleted.', 'success')
                : IbHelpers::set_message('Failed to delete card #'.$id.'.', 'error');
        }

        $this->request->redirect('/card-builder-orders.html');
    }


    // Display the card builder view. For use with short tags
    public static function render_card_builder()
    {
        $user    = Auth::instance()->get_user();
        $uri     = ltrim($_SERVER['REQUEST_URI'], '/');
        $id      = substr($uri, strpos($uri, '/') + 1);
        $id      = is_numeric($id) ? $id : NULL;
        $message = '';

        if ( ! isset($user['id']))
        {
            return '<div class="alert"><div class="close">&times;</div>You must be logged in to use this feature.</div>';
        }
        else
        {
            $role_model = new Model_Roles;
            $card       = new Model_Cardbuilder($id);
            $role       = $role_model->get_name($user['role_id']);
            if ($role == 'Card Holder' AND $user['id'] != $card->get_user_id() AND $card->get_user_id() != NULL)
            {
                return '<div class="alert"><div class="close">&times;</div>You do not have permission to edit someone else\'s card.</div>';
            }
            else
            {
                $card    = new Model_cardbuilder($id);
                $card    = $card->get_instance();

                return View::factory('front_end/card_builder')->set('message', $message)->set('card', $card);
            }
        }
    }

    // Display the order listing view. for use with short tags
    public static function render_order_listing()
    {
        $user = Auth::instance()->get_user();
        if ( ! isset($user['id']))
        {
            return '<div class="alert"><div class="close">&times;</div>You must be logged in to use this feature.</div>';
        }
        else
        {
            $model      = new Model_Cardbuilder;
			$blank_card = $model->get_instance();
            $role_model = new Model_Roles;
            $role       = $role_model->get_name($user['role_id']);
			$is_admin   = ($role == 'Administrator');
            if ($role != 'Manager' AND $role != 'Administrator')
            {
                $cards      = $model->get_users_cards($user['id']);
                $is_manager = FALSE;
            }
            else
            {
                $cards      = $model->get_all_cards();
                $is_manager = TRUE;
            }
            return View::factory('front_end/order_listing')->set('cards', $cards)->set('blank_card', $blank_card)->set('is_manager', $is_manager)->set('is_admin', $is_admin);
        }
    }

    public function action_generate()
    {
        $this->auto_render   = FALSE;
        $data                = $this->request->post();
        $card_ids            = $data['cards'];
		$cards               = Model_Cardbuilder::get_list_of_cards($card_ids);
		$width               = 210;
		$height              = 297;
		$multiplier          = 1;
		$sheets              = array();
		$time                = time();
		$attachments         = array();
		$render              = '';
		$pdf                 = new Model_ProductPDF($width, $height, 14, array(219,297));
		$filename            = $time.'.pdf';

		// Up to 4 different cards per PDF. Anything extra goes on the next PDF
		for ($i = 0; $i < count($card_ids); $i += 4)
		{
			for ($j = 0; $j < 4 AND $i + $j < count($card_ids); $j++)
			{
				$sheets[$i]['cards'][] = $cards[$i + $j];
			}
		}

		foreach ($sheets as $sheet)
		{
			$render .= View::factory('business_card')->set('cards', $sheet['cards'])->render();
		}
		$pdf
			->set_compression(FALSE)
			->set_title('Generated Cards - '.$time)
			->set_filename($filename)
			->set_display_mode('fullpage')
			->set_multiplier($multiplier)
			->set_html($render)
			->set_dpi(300)
			->generate_pdf();

		$attachments[] = '/var/tmp/'.$filename;

		$order = new Model_Cardorder;
		$order->add();
		Model_Cardbuilder::set_cards_order($card_ids,$order->get_id());

		$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('print_notification'));
        $notification = new Model_Notifications($event_id);
		$body = View::factory('email/card_pdf')->set('order', $order->get_instance())->set('user', Auth::instance()->get_user());
        $notification->send($body, $attachments);

        echo 'OK';
    }

	public function action_ajax_get_card_details()
	{
		$this->auto_render = FALSE;
		$id = $this->request->param('id');
		$card = new Model_Cardbuilder($id);
		$card_details = $card->get_instance();
		$this->response->body(json_encode($card_details));
	}

	/* For debugging. Display PDF in HTML format. */
	public function action_html_cards()
	{
		$this->auto_render = FALSE;
		$cards = Model_Cardbuilder::get_list_of_cards(array('26','27','30'));
		echo View::factory('business_card')->bind('cards',$cards)->render();
	}

}