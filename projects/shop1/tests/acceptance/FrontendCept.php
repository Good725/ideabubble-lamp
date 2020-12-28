<?php use shop1\WebGuy;

$I = new WebGuy($scenario);
$I->wantTo('I want to see home page');
$I->amOnPage('/home.html');
$I->canSee('IDEA BUBBLE');
$I->canSee('POWERED');
$I->canSee('Home');
$I->canSee('Contact us');
$I->cantSee('Kohana');


$I->wantTo('See does home page displays');
$I->amOnPage('/terms-and-conditions.html');
$I->canSee('Terms');
$I->canSee('Deal of the week');
