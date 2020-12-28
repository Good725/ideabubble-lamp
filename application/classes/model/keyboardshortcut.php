<?php defined('SYSPATH') or die('No direct script access.');

class Model_Keyboardshortcut extends Model
{
	public static function save_all($post)
	{

		try{
			Database::instance()->begin();
			if(isset($post['shortcut_deleted'])){
				foreach($post['shortcut_deleted'] as $id){
					DB::delete('engine_keyboardshortcut_list')->where('id', '=', $id)->execute();
				}
			}
			if(isset($post['shortcut_id'])){
				foreach($post['shortcut_id'] as $i => $id){
					$shortcut = array();
					$shortcut['name'] = trim($post['shortcut_name'][$i]);
					$shortcut['url'] = trim($post['shortcut_url'][$i]);
					$shortcut['keysequence'] = preg_replace('/\s/', '', strtoupper($post['shortcut_keysequence'][$i]));
					if($id == 'new'){
						DB::insert('engine_keyboardshortcut_list', array_keys($shortcut))->values($shortcut)->execute();
					} else {
						DB::update('engine_keyboardshortcut_list')->set($shortcut)->where('id', '=', $id)->execute();
					}
				}
			}
			Database::instance()->commit();
			return true;
        } catch(Exception $e) {
			Database::instance()->rollback();
			return false;
        }
	}

	public static function get_all()
	{
		$shortcuts = DB::select('*')->from('engine_keyboardshortcut_list')->execute()->as_array();
		return $shortcuts;
	}
}
?>