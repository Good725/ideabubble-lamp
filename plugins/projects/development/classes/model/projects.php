<?php
final class Model_Projects extends Model
{
    const   MAIN_TABLE      = 'plugin_projects_projects';
    const   IMAGE_TABLE     = 'plugin_projects_images';
    const   RELATIONS_TABLE = 'plugin_projects_related';
    private $id             = 'new';
    private $name           = '';
    private $summary        = '';
    private $category       = '';
    private $sub_category   = '';
    private $publish        = '1';
    private $order          = '0';
    private $description    = '';
    private $delete         = 0;

    function __construct($id = 'new')
    {
        $this->set_id($id);
    }

    public function set($data)
    {
        $this->id               = (isset($data['id']))             ? $data['id']           : $this->id;
        $this->name             = (isset($data['name']))           ? $data['name']         : $this->name;
        $this->summary          = (isset($data['summary']))        ? $data['summary']      : $this->summary;
        $this->category         = (isset($data['category']))       ? $data['category']     : $this->category;
        $this->sub_category     = (isset($data['sub_category']))   ? $data['sub_category'] : $this->sub_category;
        $this->publish          = (isset($data['publish']))        ? $data['publish']      : $this->publish;
        $this->order            = (isset($data['order']))          ? $data['order']        : $this->order;
        $this->description      = (isset($data['description']))    ? $data['description']  : $this->description;
        $this->delete           = (isset($data['delete']))         ? $data['delete']       : $this->delete;
    }

    public function load()
    {
        return array(
            'id'            => $this->id,
            'name'          => $this->name,
            'summary'       => $this->summary,
            'category'      => $this->category,
            'sub_category'  => $this->sub_category,
            'publish'       => $this->publish,
            'order'         => $this->order,
            'description'   => $this->description,
            'delete'        => $this->delete
        );
    }

    public function save()
    {
        $ok   = TRUE;
        $data = $this->load();

        try
        {
            Database::instance()->begin();
            if($data['id'] == 'new')
            {
                $q = DB::insert(self::MAIN_TABLE,array_keys($data))->values($data)->execute();
                $this->set_id($q[0]);
            }
            else if(is_numeric($data['id']))
            {
                DB::update(self::MAIN_TABLE)->set($data)->where('id','=',$this->id)->execute();
            }
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            $ok = FALSE;
            Database::instance()->rollback();
            throw $e;
        }
        return $ok;
    }

    public function delete()
    {

        try
        {
            Database::instance()->begin();
            DB::update(self::MAIN_TABLE)->set(array('delete' => 1))->where('id','=',$this->id)->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    public function destination($data)
    {
        if ($data['action'] == 'save_and_exit')
        {
            return URL::site().'admin/projects';
        }
        else
        {
            return URL::site().'admin/projects/add_edit_project/'.$this->id;
        }
    }

    public function get_unrelated_projects()
    {
        $projects = self::get_all_projects();
        $related_projects = $this->get_related_projects();
        return $this->array_recursive_diff($projects,$related_projects);
    }

    public function get_project_images()
    {
        return DB::select('plugin_media_shared_media.id', 'plugin_media_shared_media.filename', array('project_image.id', 'project_image_id'))
            ->from(array(self::IMAGE_TABLE, 'project_image'))
            ->join('plugin_media_shared_media')->on('project_image.image_id', '=', 'plugin_media_shared_media.id')
            ->where('project_image.project_id', '=', $this->get_id())
            ->and_where('project_image.deleted','=', 0)
            ->execute()->as_array();
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_summary()
    {
        return $this->summary;
    }

    public function get_photo()
    {
        $q = DB::select('filename')->from('plugin_media_shared_media')->join(self::IMAGE_TABLE,'LEFT')->on(self::IMAGE_TABLE.'.id','=','plugin_media_shared_media.id')->where('deleted','=','0')->and_where('publish','=','1')->and_where(self::IMAGE_TABLE.'.project_id','=',$this->id)->limit(1)->execute()->as_array();

        return (count($q) > 0) ? $q['filename'] : '';
    }

    public function add_relation($current)
    {
        $ok = true;
        try{
            DB::insert(self::RELATIONS_TABLE,array('project_id','related_to','publish','deleted'))->values(array($current,$this->id,1,0))->execute();
        }
        catch(Exception $e)
        {
            $ok = false;
        }

        return $ok;
    }

    public function add_image($file_name)
    {
        $return['ok'] = TRUE;
        $return['id'] = NULL;
        $q = DB::select('id')->from('plugin_media_shared_media')->where('filename','=',$file_name)->and_where('location','=','projects')->execute()->as_array();
        if (count($q) > 0)
        {
            try
            {
                $insert = DB::insert(self::IMAGE_TABLE,array('project_id', 'image_id'))->values(array($this->id, $q[0]['id']))->execute();
                $return['id'] = $insert[0];
            }
            catch(Exception $e)
            {
                $return['ok'] = FALSE;
            }
        }
        return $return;
    }

    public function save_images($image_ids)
    {
        $current_images    = DB::select('image_id')->from(self::IMAGE_TABLE)->where('deleted', '=', 0)->and_where('project_id', '=', $this->get_id())->execute();
        $current_image_ids = array();
        foreach ($current_images as $current_image)
        {
            $current_image_ids[] = $current_image['image_id'];
        }

        // Add new images
        $images_to_add    = array_diff($image_ids, $current_image_ids);
        if (count($images_to_add) > 0)
        {
            $add_query        = DB::insert(self::IMAGE_TABLE, array('project_id', 'image_id'));
            foreach ($images_to_add as $image_id)
            {
                $add_query = $add_query->values(array($this->get_id(), $image_id));
            }
            $add_query->execute();
        }

        // Delete images that were removed
        $images_to_remove = array_diff($current_image_ids, $image_ids);
        if (count($images_to_remove) > 0)
        {
            DB::update(self::IMAGE_TABLE)->set(array('deleted' => 1))->where('project_id', '=', $this->get_id())->where('image_id', 'in', $images_to_remove)->execute();
        }
    }

    public function remove_relation($current)
    {
        $ok = true;
        try{
           DB::update(self::RELATIONS_TABLE)->set(array('deleted' => 1))->where('project_id','=',$this->id)->and_where('related_to','=',$current)->execute();
        }
        catch(Exception $e)
        {
            $ok = false;
        }
        return $ok;
    }

    private function get()
    {
        $q = DB::select('id','name','summary','category','sub_category','publish','order','description')->from(self::MAIN_TABLE)->where('id','=',$this->id)->limit(1)->execute()->as_array();
        return (count($q) > 0) ? $q[0] : $q;
    }

    public function get_related_projects()
    {
        return DB::select('plugin_projects_projects.id',
            'plugin_projects_projects.name',
            'plugin_projects_projects.category',
            'plugin_projects_projects.date_modified',
            'plugin_projects_projects.publish')
            ->from('plugin_projects_projects')
            ->join('plugin_projects_related','LEFT')->on('plugin_projects_projects.id','=','plugin_projects_related.related_to')
            ->where('project_id','=',$this->id)
            ->and_where('deleted','=',0)
            ->execute()
            ->as_array();
    }

    private function set_id($id)
    {
        if(is_numeric($id) AND $id > 0)
        {
            $this->id = $id;
            $this->set($this->get());
        }
        else
        {
            $this->id = 'new';
        }
    }

    private function array_recursive_diff($array1, $array2)
    {

    $aReturn = array();
    foreach($array1 as $mKey => $mValue)
    {
        if(array_key_exists($mKey, $array2))
        {
            if(is_array($mValue))
            {
                $aRecursiveDiff = $this->array_recursive_diff($mValue, $array2[$mKey]);
                if(count($aRecursiveDiff))
                {
                    $aReturn[$mKey] = $aRecursiveDiff;
                }
            }
            else
            {
                if($mValue != $array2[$mKey])
                {
                    $aReturn[$mKey] = $mValue;
                }
            }
        }
        else
        {
            $aReturn[$mKey] = $mValue;
        }
    }
    return $aReturn;
    }

    public static function get_all_projects()
    {
        return DB::select('id','name','category','date_modified','publish')->from(self::MAIN_TABLE)->where('delete','=',0)->execute()->as_array();
    }

    public static function get_top_menus()
    {
        return array(
            'Projects' => array(
                array(
                    'name' => 'Projects',
                    'link' => '/admin/projects'
                ),
                array(
                    'name' => 'Categories',
                    'link' => '/admin/projects/categories'
                )
            )
        );
    }

    public static function get_breadcrumbs()
    {
        return array(
            array('name' => 'Home',     'link' => '/admin'),
            array('name' => 'Projects', 'link' => '/admin/projects')
        );
    }

    public static function unrelated_as_options($unrelated = NULL)
    {
        $result = '<option value="">Select a project</option>';

        foreach($unrelated AS $project)
        {
            $result.='<option value="'.$project['id'].'">'.$project['name'].'</option>';
        }

        return $result;
    }

    public static function related_as_well_list($related)
    {
        $result = "";
        foreach($related AS $relation)
        {
            $relation = new Model_Projects($relation['id']);
            $result.= View::factory('related_projects',array('project' => $relation));
        }
        return $result;
    }

}
?>