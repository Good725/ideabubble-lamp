<?php use donalryan\WebGuy;

$I = new WebGuy($scenario);
$I->wantTo('I want to see home page');
$I->amOnPage('/home.html');
$I->canSee('IDEA BUBBLE');
$I->canSee('POWERED');
$I->canSee('Home');
$I->canSee('Contact us');
$I->canSee('Services');
$I->canSee('Commercial');
$I->canSee('Quick links');
$I->cantSee('Kohana');


$I->wantTo('See does home page displays');
$I->amOnPage('/about-us.html');
$I->canSee('ABOUT US');
$I->canSee('2008');

$I->wantTo('See does home page displays');
$I->amOnPage('/car-listings.html');
$I->canSee('Search Nissan');
$I->canSee('More');
$I->canSee('Next');