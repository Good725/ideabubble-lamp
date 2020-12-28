<?php defined('SYSPATH') or die('No Direct Script Access.');

interface Model_Remoteaccountingapi
{
    function get_accounts();
    function get_contacts();
    function sync_contacts($direction = '');
    function save_contact($contact);
}

