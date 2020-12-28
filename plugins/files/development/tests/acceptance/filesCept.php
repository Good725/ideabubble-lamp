<?php use files\WebGuy;

$I = new WebGuy($scenario);

$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');

$I->wantTo('I want to check Files plugin');
$I->amOnPage('/admin/files');
$I->see('create');


$I->wantTo('I want to add a Files');
$I->amOnPage('/admin/files/add?parent_id=1');
$I->see('New File');
