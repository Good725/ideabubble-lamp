<?php use content1\WebGuy;

$I = new WebGuy($scenario);
$I->wantTo('I want to see home page');
$I->amOnPage('/home.html');
$I->canSee('IDEA BUBBLE');
$I->canSee('POWERED');
$I->canSee('Home');
$I->canSee('Contact us');
$I->canSee('Metal');
$I->canSee('Company');
$I->canSee('Quick links');
$I->cantSee('Kohana');


$I->wantTo('See does about page displays');
$I->amOnPage('/about-us.html');
$I->canSee('ABOUT US');
$I->canSee('Latest News');
$I->canSee('recycling');

$I->wantTo('See does contact page displays');
$I->amOnPage('/contact-us.html');
$I->canSee('Online Enquiry');
$I->canSee('Name');
$I->canSee('Email');