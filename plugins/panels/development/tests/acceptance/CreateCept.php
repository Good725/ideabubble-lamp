<?php use reports\WebGuy;

$I = new WebGuy($scenario);

$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');


//panel
$I->wantTo('Want to check panel');
$I->amOnPage('/admin/panels');
$I->canSee('Add Panel');
$I->canSee('Title');
$I->dontSee('Kohona');

//Create a panel
$I->wantTo('Want to Create a panel');
$I->amOnPage('/admin/panels/add_edit_item');
$I->selectOption('panel_type_id','Static');
$I->selectOption('panel_publish', 'No');
$I->fillField('panel_title','testpage404');
$I->click('#btn_save_exit');
$I->canSee('Add Panel');
$I->canSee('Title');
$I->dontSee('Kohona');

