<?php

use reports1\WebGuy;

$I = new WebGuy($scenario);

$cms_user = 'admin@ideabubble.com';
$cms_pass = 'password2015';
$newreportName = "AC Test Report 1";

//20150618103812 : As an Administrator I want to be able to login to CMS
$I->wantTo('I want to login to CMS');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->fillField('login-email', $cms_user);
$I->fillField('login-password', $cms_pass);
$I->click('login');
$I->see('reports');

//20150618101202 : As an Administrator I want to add a report
$I->wantTo('I want to add a report');
$I->amOnPage('/admin/reports');
$I->canSee('Add report');
$I->canSee('Title');
$I->amOnPage('/admin/reports/add_edit_report');
$I->canSee('View');
$I->canSee('Run Report');
$I->fillField('name', $newreportName);
$I->click("//form[@id='report_edit_form']/ul/li[2]/a"); // click data tab
$I->canSee('SQL');
$I->fillField('name', $newreportName);
$I->fillField('sql', 'SELECT * from plugin_payments_log');
//$I->click('//form[@id="report_edit_form"]/div[2]/div[11]/div/button[2]'); // click save and exit
//$I->canSee('View');
//$I->canSee('Title');
//$I->canSee('ID');
//$I->canSee($newreportName);
//$reportID = $I->grabTextFrom("//table[@id='list_reports_table']/tbody/tr[1]/td[1]/a"); // take from ID col on first row



//As an Administrator I want to add a widget to dashboard
//$I->wantTo('I want to add a widget to dashboard');
//$I->amOnPage('/admin/reports/add_edit_report/'.$reportID);
//$I->click('//*[@id="report_edit_form"]ul/li[7]/a'); // click widget tab
//$I->canSee('X-Axis');


//As an Administrator I want to remove a widget from dashboard

//As an Administrator I want to filter widget data







