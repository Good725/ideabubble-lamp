<?php use testimonials\WebGuy;

$I = new WebGuy($scenario);

$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');

//check testimonials plugin
$I->amOnPage('/admin/testimonials');
$I->see('page');

//add a testimonials
$I->wantTo('Want to add a ');
$I->amOnPage('/admin/testimonials/add_edit_item');
//$I->fillField('item_title', 'test testimonials');
//$I->fillField('item_summary', 'test summery');
//$I->fillField('item_signature', 'Test signature');
//$I->fillField('item_company', 'company');
//$I->fillField('item_website', 'ideabubble.ie');
//$I->click('btn_save');
//$I->see('summery');