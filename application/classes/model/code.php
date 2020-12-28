<?php 

class Model_Code extends Model {
    //This is the most inventive class name I have ever seen.
    /*
     * Get codes
     */

    public function get_code_range($data)
    {
        $cols = array(0 => 'code',2 => 'group_id',3 => 'role_id',4 => 'date_added',5 => 'published',6 => 'Edit',7 => 'delete');
        $offset     = $data['iDisplayStart'];
        $limit      = $data['iDisplayLength'];
        $search     = (is_string($data['sSearch']) AND strlen($data['sSearch']) > 0) ? $data['sSearch'] : NULL;

        $codes      = array();

        $q = DB::select('id','code','group_id','role_id','date_added','published')->from('user_act_codes');

        if(!is_null($search))
        {
            $q->where('id','LIKE','%'.$search.'%')->or_where('code','LIKE','%'.$search.'%');
        }

        if(isset($data['iSortCol_0']) AND is_numeric($data['iSortCol_0']) AND strlen($data['iSortCol_0']) > 0 AND $data['iSortCol_0'] <= 5)
        {
            if (in_array(strtolower($data['sSortDir_0']), array('asc', 'desc'))) {
                $q->order_by($cols[$data['iSortCol_0']], $data['sSortDir_0']);
            }
        }
        else
        {
            $q->order_by('date_added', 'DESC');
        }

        $q->offset($offset);

		if ($limit > -1)
		{
			$q->limit($limit);
		}

        $codes['data'] = $q->execute()->as_array();

        $codes['count'] = DB::select('id')->from('user_act_codes')->execute()->as_array();

        $codes['data'] = $this->format_code_data($codes['data']);

        return $codes;
    }

    public function format_code_data($data)
    {
        $rows = array();
        foreach ($data AS $code)
        {
            $code['published']    = '<a href="'.URL::site().'admin/settings/status_update_activation_code/'.$code['id'].'" title="Change"><i class="icon-'.($code['published'] == 0 ? 'remove' : 'ok' ).'"></i></a>';
            $code['edit']       = '<a href="'.URL::site().'admin/settings/edit_activation_code/'.$code['id'].'"><i class="icon-pencil"></i></a>';
            $code['delete']     = '<a href="'.URL::site().'admin/settings/delete_activation_code/'.$code['id'].'" class="del-confirm"><i class="icon-trash"></i></a>';

            unset($code['id']);
            $code    = array_values($code);
            $rows[]  = array_merge($code, array('',''));
        }

        return $rows;
    }

    public function get_codes() {
        
        $codes = DB::select()
                    ->from('user_act_codes')
                    ->order_by('id', 'DESC')
                    ->execute();

        return $codes;
    }
    
    public function get_single_code($code_id) {
        
        $code = DB::select()
                    ->from('user_act_codes')
                    ->and_where('id', '=', $code_id)
                    ->execute();
        
        return $code[0];
        
    } 
    
    public function update_single_code($code_id, $post_data) {
        
        $query = DB::update('user_act_codes')
                    ->set(array('code'     =>  $post_data['code'],
                                'group_id' =>  $post_data['group_id'],
                                'role_id'  =>  $post_data['role_id']))
                    ->where('id', '=', $code_id)
                    ->execute();
        
    }
    
    public function get_groups() {
        
        $groups = DB::select()
                    ->from('engine_user_group')
                    ->order_by('user_group', 'ASC')
                    ->execute();

        return $groups;
        
    }
    
    public function get_roles() {
        
        $roles = DB::select()
                    ->from('engine_project_role')
                    ->where('id', '=', '2')
                    ->or_where('id', '=', '3')
                    ->or_where('id', '=', '4')
                    ->execute();

        return $roles;
        
    }
    
    
    
    
    

  
  
  
  

}

?>