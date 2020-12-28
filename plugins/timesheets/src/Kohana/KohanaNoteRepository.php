<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\Entity\Note;
use DB;
use Ideabubble\Timesheets\EventDispatcher;
use Ideabubble\Timesheets\Hydrator;
use Ideabubble\Timesheets\NoteRepository;

class KohanaNoteRepository implements NoteRepository
{
    private $hydrator;
    /**
     * @var EventDispatcher
     */
    private $dispatcher;
    private $linkId;
    
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->hydrator = new Hydrator();
        $this->dispatcher = $dispatcher;
        $item = DB::select()->from('plugin_contacts3_notes_tables')->where('table','=','plugin_timeoff_requests')->execute()->as_array();
        $this->linkId = $item[0]['id'];
        
    }

    public function findAll($requestId)
    {
        $select = DB::select('n.id','n.link_id','n.date_created','n.note', ['c.id', 'staff_id'])->from(['plugin_contacts3_notes','n']);
        $select->join(['plugin_contacts3_contacts','c'], 'left')->on('c.linked_user_id','=', 'n.created_by');
        $select->and_where('n.table_link_id','=', $this->linkId);
        $select->and_where('n.link_id','=', $requestId);
        $select->order_by('n.id', 'asc');
        $rows = $select->as_object()->execute();
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->hydrator->hydrate(Note::class, [
                'id' => $item->id,
                'requestId' => $item->link_id,
                'userId' => $item->staff_id,
                'createdAt' => $item->date_created,
                'content' => $item->note,
            ]);
        }
        return $result;
    }
    
    public function findOne($id)
    {
        $select = DB::select('n.id','n.link_id','n.date_created','n.note', 'c.id staff_id')->from(['plugin_contacts3_notes','n']);
        $select->join(['plugin_contacts3_contacts','c'], 'left')->on('c.linked_user_id','=', 'n.created_by');
        $select->and_where('n.table_link_id','=', $this->linkId);
        $select->and_where('n.id','=', $id);
        $select->order_by('n.id', 'asc');
        $rows = $select->as_object()->execute();
        $result = null;
        if (isset($rows[0])) {
            $item = $rows[0];
            $result = $this->hydrator->hydrate(Note::class, [
                'id' => $item->id,
                'requestId' => $item->link_id,
                'userId' => $item->staff_id,
                'createdAt' => $item->date_created,
                'content' => $item->note,
            ]);
        }
        return $result;
    }
    
    public function insert(Note $note)
    {
        $rows = DB::select()->from('plugin_contacts3_contacts')->where('id','=',$note->getUserId())->execute()->as_array();
        $userId = $rows[0]['linked_user_id'];
        $data = $this->hydrator->extract($note, ['id','requestId', 'userId', 'createdAt', 'content']);
        DB::insert('plugin_contacts3_notes')->values([
            'table_link_id' => $this->linkId,
            'link_id' => $data['requestId'],
            'note' => $data['content'],
            'date_created' => $data['createdAt'],
            'date_modified' => $data['createdAt'],
            'created_by' => $userId,
            'modified_by' => $userId,
            'publish' => 1,
            'deleted' => 0
        ])->execute();
    }
    
    public function update(Note $note)
    {
        $data = $this->hydrator->extract($note, ['id','requestId', 'userId', 'createdAt', 'content']);
        DB::update('plugin_contacts3_notes')->set([
            'note' => $data['content'],
        ])->where('id','=',$data['id']);
        
    }
    
    
    
}