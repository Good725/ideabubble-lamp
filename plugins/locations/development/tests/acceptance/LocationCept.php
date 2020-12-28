<?php use locations\WebGuy;

$I = new WebGuy($scenario);
$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');

//locations
$I->wantTo('Want to check locations');
$I->amOnPage('/admin/locations');
$I->canSee('Add location');
$I->canSee('County');

//add a locations
$I->wantTo('Want to add a locations');
$I->amOnPage('/admin/locations/add');
$I->fillField('title', 'test location');
$I->fillField('address_1', 'Address 1');
$I->fillField('address_2', 'Address 2');
$I->fillField('address_3', 'Address 3');
$I->fillField('county', 'Limerick');
$I->fillField('phone', '0851234568');
$I->fillField('email', 'test@ideabubble.ie');
$I->fillField('map_reference', 'test');
$I->click('//form[@id="form_add_edit_location"]/div[11]/button');








