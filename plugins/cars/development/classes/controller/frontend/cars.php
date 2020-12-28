<?php

final class Controller_Frontend_Cars extends Controller_Template{
    public function action_cron()
    {
        $this->auto_render = false;
        $id = Settings::instance()->get('car_csv_template');
        $csv_handler = Model_CSV::create($id);
        $csv = Model_Cars::download_csv();
        $csv_handler->execute_csv_import($csv);
    }

	/* Filter the "model" dropdown to only show records of the specified make */
	public function action_ajax_get_models_by_make()
	{
		$this->auto_render = FALSE;
		$query    = Kohana::sanitize($this->request->query());
		$make     = isset($query['make'])    ? $query['make']    : '';
		$selected = isset($query['current']) ? $query['current'] : '';
		$models = Model_Cars::get_models_by_make($make);
		$return = '';
		foreach ($models as $model)
		{
			$return .= '<option value="'.$model['model'].'"'.(($model['model'] == $selected) ? ' selected="selected"' : '').'>'.$model['model'].'</option>';
		}

		echo $return;
	}
}