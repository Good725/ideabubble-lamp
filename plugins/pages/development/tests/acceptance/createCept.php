<?php use pages\WebGuy;

$I = new WebGuy($scenario);

//CMS login
$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');

//pages
$I->wantTo('Want to check pages');
$I->amOnPage('/admin/pages');
$I->canSee('Add Page');
$I->canSee('Page Name');

//Create a page
$I->wantTo('Want to Create a pages');
$I->amOnPage('/admin/pages/new_pag');
$I->click("//form[@id='frm_page_edit']/div[2]/ul/li[3]/a");
$I->selectOption('publish', 'No');
$I->fillField('page_name','testpage404');
$I->click('#btn_save_exit');
$I->canSee('pages');