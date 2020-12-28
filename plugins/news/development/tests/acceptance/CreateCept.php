<?php use news\WebGuy;

$I = new WebGuy($scenario);
$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');


//News
$I->wantTo('Want to check News');
$I->amOnPage('/admin/pages');
$I->canSee('Add');
$I->canSee('Title');


//Create a News
$I->wantTo('Want to Create a News');
$I->amOnPage('admin/news/add_edit_item');
$I->selectOption('item_category_id', 'News');
$I->click("//form[@id='form_news_story_add_edit']/div[2]/ul/li[3]/a");
$I->fillField('item_title','TestNews');
$I->click('#btn_save_exit');
$I->canSee('page');