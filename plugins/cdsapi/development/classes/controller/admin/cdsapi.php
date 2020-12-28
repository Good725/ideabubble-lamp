<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Cdsapi extends Controller_Cms
{
    public function action_sync_accounts()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'text/plain');
        $na = new Model_NAVAPI();
        $na->event_sync();
    }

    public function action_test()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'text/plain');
        $cds = new Model_CDSAPI();
        if (!$cds->auth()) {
            echo "Auth failed\n";
        } else {
            echo "Auth succeeded\n";
        }

        $create_account = array(
            'name' => 'new courseCo test',
            'address1_line1' => 'ireland',
            'address1_city' => 'dublin',
            'sp_homepage' => 'courseco.ie',
            'emailaddress1' => 'me@icourseco.ie'
        );

        if (1) {
            echo 'Initial account data <pre>' . print_r($create_account, 1) . '</pre>';
            $cdsid = $cds->create_account(
                100231,
                $create_account
            );
            echo 'Initial account creation response <pre>'  . print_r($cdsid, 1) . '</pre>';
        }
        $updated_account = array(
            'name' => 'albert einstein',
            'address1_line1' => 'ireland',
            'address1_city' => 'dublin',
            'sp_homepage' => 'ideabubble.ie'
        );
        if (1) {
            echo 'Updated  account data <pre>' . print_r($create_account, 1) . '</pre>';
            $cdsid = $cds->update_account(
                100231,
                $updated_account

            );
            echo 'Updated account response <pre>'  . print_r($cdsid, 1) . '</pre>';

        }
        if (1) {
            $account = $cds->get_account(100231);
            echo 'Retrieve Account By Id from CDS <pre>'  . print_r($account, 1) . '</pre>';
        }
        if (1) {
            $accounts = $cds->search_accounts('sp_homepage', 'ideabubble.ie');
            echo 'Search  Account By Domain Name in  CDS <pre>'  . print_r($accounts, 1) . '</pre>';
        }
        if (1) {
            $accounts = $cds->search_accounts('emailaddress1', 'me@ideabubble.ie');
            echo 'Search  Account By Email in  CDS <pre>'  . print_r($accounts, 1) . '</pre>';
        }
        if (1) {
            $accounts = $cds->search_accounts('name', 'thomas edison');
            echo 'Search  Account By Name in  CDS <pre>'  . print_r($accounts, 1) . '</pre>';
        }
    }
}