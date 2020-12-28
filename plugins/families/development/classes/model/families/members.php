<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Families_Members extends ORM
{
    const TABLE = 'plugin_family_members';

    protected $_table_name = self::TABLE;
    protected $_belongs_to = array(
        'family' => array('model' => 'Family')
        
    );


    public static function add_family_member($family_id, $contact_id, $role)
    {
        $exists = DB::select('*')
            ->from(self::TABLE)
            ->where('family_id', '=', $family_id)
            ->and_where('contact_id', '=', $contact_id)
            ->execute()
            ->current();
        $family = DB::select('*')
            ->from(Model_Families::TABLE)
            ->where('id', '=', $family_id)
            ->execute()
            ->current();
        if ($exists) {
            DB::update(self::TABLE)
                ->set(array('role' => $role))
                ->where('family_id', '=', $family_id)
                ->and_where('contact_id', '=', $contact_id)
                ->execute();
        } else {
            DB::insert(self::TABLE)
                ->values(array('family_id' => $family_id, 'contact_id' => $contact_id, 'role' => $role))
                ->execute();
        }
        Model_Contacts::add_relation($family['primary_contact_id'], $contact_id, null);
    }

    public static function get_all($family_id)
    {
        $membersq = DB::select(
            'contacts.*',
            DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) AS fullname"),
            "members.role",
            "members.contact_id",
            "members.family_id"
        )
            ->from(array(self::TABLE, 'members'))
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')
                    ->on('members.contact_id', '=', 'contacts.id')
            ->where('contacts.deleted', '=', 0)
            ->and_where('members.family_id', '=', $family_id)
            ->order_by('contacts.first_name')
            ->order_by('contacts.last_name');

        $members = $membersq->execute()->as_array();
        return $members;
    }
}