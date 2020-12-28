<?php use content1\WebGuy;

$I = new WebGuy($scenario);
$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');
