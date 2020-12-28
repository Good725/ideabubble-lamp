<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Prototypes extends Controller_CMS
{
	public function action_index()
	{

	}

	public function action_raw()
	{
		$this->auto_render = FALSE;

		$view_name = $this->request->param('id');
		$styles    = array (URL::get_engine_plugin_assets_base('prototypes').'css/login.css' => 'screen');
		echo View::factory('prototypes/'.$view_name)->set('styles', $styles);
	}

	public function action_cms()
	{
		$view_name = $this->request->param('id');
		$this->template->body = View::factory('prototypes/'.$view_name);
	}

    /* Dummy data for a server-side DataTable */
    public function action_ajax_states_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $data = $this->request->query();
        $states = [[ "abbr"=>"AL", "name"=>"Alabama", "capital"=>"Montgomery"], [ "abbr"=>"AK", "name"=>"Alaska", "capital"=>"Juneau"], [ "abbr"=>"AZ", "name"=>"Arizona", "capital"=>"Phoenix"], [ "abbr"=>"AR", "name"=>"Arkansas", "capital"=>"Little Rock"], [ "abbr"=>"CA", "name"=>"California", "capital"=>"Sacramento"], [ "abbr"=>"CO", "name"=>"Colorado", "capital"=>"Denver"], [ "abbr"=>"CT", "name"=>"Connecticut", "capital"=>"Hartford"], [ "abbr"=>"DE", "name"=>"Delaware", "capital"=>"Dover"], [ "abbr"=>"FL", "name"=>"Florida", "capital"=>"Tallahassee"], [ "abbr"=>"GA", "name"=>"Georgia", "capital"=>"Atlanta"], [ "abbr"=>"HI", "name"=>"Hawaii", "capital"=>"Honolulu"], [ "abbr"=>"ID", "name"=>"Idaho", "capital"=>"Boise"], [ "abbr"=>"IL", "name"=>"Illinois", "capital"=>"Springfield"], [ "abbr"=>"IN", "name"=>"Indiana", "capital"=>"Indianapolis"], [ "abbr"=>"IA", "name"=>"Iowa", "capital"=>"Des Moines"], [ "abbr"=>"KS", "name"=>"Kansas", "capital"=>"Topeka"], [ "abbr"=>"KY", "name"=>"Kentucky", "capital"=>"Frankfort"], [ "abbr"=>"LA", "name"=>"Louisiana", "capital"=>"Baton Rouge"], [ "abbr"=>"ME", "name"=>"Maine", "capital"=>"Augusta"], [ "abbr"=>"MD", "name"=>"Maryland", "capital"=>"Annapolis"], [ "abbr"=>"MA", "name"=>"Massachusetts", "capital"=>"Boston"], [ "abbr"=>"MI", "name"=>"Michigan", "capital"=>"Lansing"], [ "abbr"=>"MN", "name"=>"Minnesota", "capital"=>"Saint Paul"], [ "abbr"=>"MS", "name"=>"Mississippi", "capital"=>"Jackson"], [ "abbr"=>"MO", "name"=>"Missouri", "capital"=>"Jefferson City"], [ "abbr"=>"MT", "name"=>"Montana", "capital"=>"Helana"], [ "abbr"=>"NE", "name"=>"Nebraska", "capital"=>"Lincoln"], [ "abbr"=>"NV", "name"=>"Nevada", "capital"=>"Carson City"], [ "abbr"=>"NH", "name"=>"New Hampshire", "capital"=>"Concord"], [ "abbr"=>"NJ", "name"=>"New Jersey", "capital"=>"Trenton"], [ "abbr"=>"NM", "name"=>"New Mexico", "capital"=>"Santa Fe"], [ "abbr"=>"NY", "name"=>"New York", "capital"=>"Albany"], [ "abbr"=>"NC", "name"=>"North Carolina", "capital"=>"Raleigh"], [ "abbr"=>"ND", "name"=>"North Dakota", "capital"=>"Bismarck"], [ "abbr"=>"OH", "name"=>"Ohio", "capital"=>"Columbus"], [ "abbr"=>"OK", "name"=>"Oklahoma", "capital"=>"Oklahoma City"], [ "abbr"=>"OR", "name"=>"Oregon", "capital"=>"Salem"], [ "abbr"=>"PA", "name"=>"Pennsylvania", "capital"=>"Harrisburg"], [ "abbr"=>"RI", "name"=>"Rhode Island", "capital"=>"Providence"], [ "abbr"=>"SC", "name"=>"South Carolina", "capital"=>"Columbia"], [ "abbr"=>"SD", "name"=>"South Dakota", "capital"=>"Pierre"], [ "abbr"=>"TN", "name"=>"Tennessee", "capital"=>"Nashville"], [ "abbr"=>"TX", "name"=>"Texas", "capital"=>"Austin"], [ "abbr"=>"UT", "name"=>"Utah", "capital"=>"Salt Lake City"], [ "abbr"=>"VT", "name"=>"Vermont", "capital"=>"Montpelier"], [ "abbr"=>"VA", "name"=>"Virginia", "capital"=>"Richmond"], [ "abbr"=>"WA", "name"=>"Washington", "capital"=>"Olympia"], [ "abbr"=>"WV", "name"=>"West Virginia", "capital"=>"Charleston"], [ "abbr"=>"WI", "name"=>"Wisconsin", "capital"=>"Madison"], [ "abbr"=>"WY", "name"=>"Wyoming", "capital"=>"Cheyenne"]];

        $results = array_slice($states, $data['iDisplayStart'], $data['iDisplayLength'], true);

        $rows = [];
        foreach ($results as $key => $result) {
            $rows[] = [
                $key+1,
                $result['abbr'],
                htmlentities($result['name']),
                htmlentities($result['capital']),
                '<label class="checkbox-icon"><input type="checkbox" checked /><span class="checkbox-icon-unchecked icon-ban-circle"></span><span class="checkbox-icon-checked icon-check"></span></label>',
                View::factory('snippets/btn_dropdown')
                    /* Replace all this with ->set('type', 'actions') when the necessary branch has been merged */
                    ->set('title',         ['text' => '<span class="icon-ellipsis-h"></span>', 'html' => true])
                    ->set('sr_title',      'Actions')
                    ->set('btn_type',      'outline-primary')
                    ->set('options_align', 'right')
                    /**/
                    ->set('options',       [
                        ['type' => 'button', 'title'  => ['html' => true, 'text' => '<span class="icon-pencil"></span> Edit']],
                        ['type' => 'button', 'title'  => ['html' => true, 'text' => '<span class="icon-clone"></span> Clone']],
                        ['type' => 'button', 'title'  => ['html' => true, 'text' => '<span class="icon-close"></span> Delete']]
                    ])->render()
            ];
        }

        $return = [
            'iTotalDisplayRecords' => count($results),
            'iTotalRecords' => count($states),
            'aaData' => $rows
        ];

        echo json_encode($return, JSON_PRETTY_PRINT);
    }

}