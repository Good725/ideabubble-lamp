<?php

class Model_Resources extends ORM {

    protected $_table_name = 'engine_resources';
    const TABLE_RESOURCES = 'engine_resources';
    const TABLE_HAS_PERMISSION = 'engine_role_permissions';

    public function resourceValidation($data) {

        $rules = Validation::factory($data)
            ->rule('type_id', 'not_empty')
            //it shoould be chacked only for type "action"
            //->rule('parent_controller', 'not_empty')
            ->rule('alias', 'not_empty');

        if(!Request::current()->param('id'))
            $rules->rule('alias', 'Model_Resources::unique_alias');

        $rules->rule('name', 'not_empty');

        return $rules;
    }

    /*
     * Gets list of actions of current controller
     */
    public function get_actions_4_controller() {

        return ORM::factory('Resources')
            ->where('type_id', '=', 1)
            ->and_where('parent_controller','=',  $this->id)
            ->order_by('name', 'ASC')
            ->find_all();

    }

    public function get_code_pieces_4_controller() {

        return ORM::factory('Resources')
            ->where('type_id', '=', 2)
            ->and_where('parent_controller','=',  $this->id)
            ->find_all();

    }

    public function get_controller_name() {

        //echo Debug::vars($this->parent_controller);

        return DB::select()
            ->from('engine_resources')
            ->where('id','=', $this->parent_controller)
            ->execute()->get('alias');

    }

    /*
     * Unique validation
     */
    public static function unique_alias($alias)
    {
        // Check if the username already exists in the database
        return ! DB::select(array(DB::expr('COUNT(alias)'), 'total'))
            ->from('engine_resources')
            ->where('alias', '=', $alias)
            ->execute()
            ->get('total');
    }

    public static function search($params = array())
    {
        $select = DB::select('resources.*', array('parent.name', 'parent'))
            ->from(array('engine_resources', 'resources'))
                ->join(array('engine_resources', 'parent'), 'left')->on('resources.parent_controller', '=', 'parent.id');

            $select->order_by('name', 'asc');

        $resources = $select->execute()->as_array();
        foreach ($resources as $i => $resource) {
            $resources[$i]['permissions'] = DB::select('r.*', DB::expr('if(p.resource_id, 1, 0) as has_permission'))
            ->from(array('engine_project_role', 'r'))
                ->join(array('engine_role_permissions', 'p'), 'left')->on('r.id', '=', 'p.role_id')->on('p.resource_id', '=', DB::expr($resource['id']))
                ->execute()
                ->as_array();
        }
        return $resources;
    }

    public static function get_by_alias($alias) {
        $select = DB::select('resources.*')
            ->from(array('engine_resources', 'resources'))
            ->join(array('engine_resources', 'parent'), 'left')
            ->on('resources.parent_controller', '=', 'parent.id')
            ->where('resources.alias', '=', $alias)->execute()->current();
        return $select;
    }
}

