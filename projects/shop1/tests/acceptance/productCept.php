<?php use shop1\WebGuy;

$I = new WebGuy($scenario);
//See product
$I->wantTo('Product pages are loading');
$I->amOnPage('/');
$I->see('Home');
$I->amOnPage('/products.html');
$I->see('Popular');
$I->amOnPage('/products.html/Popular');
$I->see('Top 10');
