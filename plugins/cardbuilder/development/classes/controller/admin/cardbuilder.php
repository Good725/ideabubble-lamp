<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 12/12/2014
 * Time: 11:31
 */

class Controller_Admin_Cardbuilder extends Controller_Cms
{
    public function action_index()
    {
        $this->auto_render = false;
        $cards = Model_Cardbuilder::get_all_cards();
        $this->response->body(View::factory('business_card')->bind('cards',$cards));
    }

    public function action_generate()
    {
        $data = $this->request->post();
        $card_list = json_decode($data['boxes'],true);
        $limit = 8;
        $cards = Model_Cardbuilder::get_list_of_cards($card_list,$limit);

        $width = 210;
        $height = 297;
        $multiplier = 1;
        $pdf = new Model_ProductPDF($width, $height,14, 'A4');
        $render = View::factory('business_card')->bind('cards',$cards)->render();
        $time = time();
        $filename = $time.'.pdf';
        $pdf->set_compression(false)->set_title('Generated Cards - '.$time)->set_filename($filename)->set_display_mode('fullpage')->set_multiplier($multiplier)->set_html($render)->generate_pdf();
        $this->response->send_file('/var/tmp/'.$filename,$filename,array('mime_type' => File::mime_by_ext('pdf')));
    }
}