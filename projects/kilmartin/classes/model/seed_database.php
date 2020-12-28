<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 14/05/15
 * Time: 15:17
 */

class Seed_Database {

    public function __construct()
    {
        $this->insert_contacts();
    }

    private function save_contact($data)
    {
        if ($data['new_family'] == 1)
        {
            $family = new Model_Family();
            $family->load(array('family_name' => $data['last_name']));
            $family->address->load($data);
            $saved = $family->save();
            if ($saved)
            {
                $data['family_id'] = $family->get_id();
            }
        }

        $contact = new Model_Contacts3($data['id']);
        $contact->load($data);
        $contact->address->load($data);
        $saved = $contact->save();
        if($saved)
        {
            $contact_id = $contact->get_id();
            $contact_details = DB::select('id','family_id','residence')
                ->from('plugin_contacts3_contacts')
                ->where('id','=',$contact_id)
                ->execute()->as_array();
            return $contact_details;
        }
        else
        {
            return NULL;
        }
    }

    public static function insert_contacts()
    {

        $q = DB::select()->from('staging_data')->execute()->as_array();


        foreach($q as $line)
        {
            $family_created = FALSE;
            $f = DB::select('family_id','first_name', 'last_name', 'role_id','is_primary','residence')->from('plugin_contacts3_contacts')
                ->where('first_name','=',$line['guardian_first_name'])
                ->and_where('last_name','=',$line['guardian_last_name'])
                ->and_where('role_id','=',1)
                ->and_where('is_primary','=',1)
                ->execute()->as_array();
            if ( ! is_null($f))
            {
                $family_id = $f['family_id'];
                $residence = $f['residence'];
                $family_created = TRUE;
            }
            /*** Create a new family for the contact ***/
            if ( ! $family_created AND $line['guardian_first_name'] != NULL)
            {
                $county_id = Model_Residence::get_county_id($line['county']);

                $data = array(
                    'action' =>'save',
                    'new_family' =>1,
                    'id'=>'',
                    'type'=>1,
                    'family_id'=>'',
                    'role_id'=>1,
                    'is_primary'=>1,
                    'title'=>NULL,
                    'first_name'=>$line['guardian_first_name'],
                    'last_name'=>($line['guardian_last_name']==''?$line['last_name']:$line['guardian_last_name']),
                    'date_of_birth'=>'',
                    'school_id'=>'',
                    'year_id'=>'',
                    'preferences'=>array(0=>1,1=>2,2=>3),
                    'address_id'=>'',
                    'country'=>'IE',
                    'address1'=>$line['address1'],
                    'address2'=>$line['address2'],
                    'address3'=>$line['address3'],
                    'town'=>$line['town'],
                    'county'=>$county_id,
                    'postcode'=>'',
                    'coordinates'=>'',
                    'notifications_group_id'=>'',
                    'notes'=>'',
                    'notifications'=>array(
                        0=>array('id'=>'new','notification_id'=>3,'value'=>$line['phone']),
                        1=>array('id'=>'new','notification_id'=>2,'value'=>$line['mobile'])
                    )
                );
                $family_created = TRUE;
                $contact_details = self::save_contact($data);
                $family_id = $contact_details['family_id'];
                $residence = $contact_details['residence'];
            }

            /*** Set data for child  and create ***/
            $year = DB::select('id','year')->from('plugin_courses_years')->where('year','=',$line['year'])->execute()->as_array();
            $data = array(
                'action' =>'save',
                'new_family' =>1,
                'id'=>'',
                'type'=>1,
                'family_id'=>'',
                'role_id'=>2,
                'is_primary'=>0,
                'title'=>NULL,
                'first_name'=>$line['first_name'],
                'last_name'=>$line['last_name'],
                'date_of_birth'=>'',
                'school_id'=>'',
                'year_id'=>$year['id'],
                'address_id'=>'',
                'country'=>'IE',
                'address1'=>$line['address1'],
                'address2'=>$line['address2'],
                'address3'=>$line['address3'],
                'town'=>$line['town'],
                'county'=>$county_id,
                'postcode'=>'',
                'coordinates'=>'',
                'notifications_group_id'=>'',
                'notes'=>'',
                'notifications'=>''
            );
            if ($family_created)
            {
                $data['family_id']=$family_id;
                $data['address_id']=$residence;
                $data['new_family']=0;
            }
            $contact_details = self::save_contact($data);
            $family_id = $contact_details['family_id'];
            $residence = $contact_details['residence'];


            /*** Save a Father contact if exists ***/
            if ($line['father_first_name'] != NULL)
            {
                $data = array(
                    'action' =>'save',
                    'new_family' =>1,
                    'id'=>'',
                    'type'=>1,
                    'family_id'=>$family_id,
                    'role_id'=>1,
                    'is_primary'=>0,
                    'title'=>NULL,
                    'first_name'=>$line['father_first_name'],
                    'last_name'=>($line['father_last_name']==''?$line['last_name']:$line['father_last_name']),
                    'date_of_birth'=>'',
                    'school_id'=>'',
                    'year_id'=>'',
                    'preferences'=>array(0=>1,1=>2,2=>3),
                    'address_id'=>$residence,
                    'country'=>'IE',
                    'address1'=>$line['address1'],
                    'address2'=>$line['address2'],
                    'address3'=>$line['address3'],
                    'town'=>$line['town'],
                    'county'=>$county_id,
                    'postcode'=>'',
                    'coordinates'=>'',
                    'notifications_group_id'=>'',
                    'notes'=>'',
                    'notifications'=>array(
                        0=>array('id'=>'new','notification_id'=>2,'value'=>$line['father_phone'])
                    )
                );
                self::save_contact($data);
            }
        }

    }

}