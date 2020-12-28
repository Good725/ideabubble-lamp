<?php use products\WebGuy;

$I = new WebGuy($scenario);

$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');


//products
$I->wantTo('Want to check products');
$I->amOnPage('/admin/products');
$I->canSee('Add product');
$I->canSee('Code');


//add a product
$I->wantTo('Want to Create a Product');
$I->amOnPage('/admin/products');
$I->See('Code');
$I->amOnPage('/admin/products/add_product');
$I->fillField('title','testproduct');
$I->fillField('price','100');
$I->click("//form[@id='form_add_edit_product']/ul/li[4]/a");
//$I->click("//div[@id='details_tab']/div[7]/div/div/button[2]");
$I->click("//form[@id='form_add_edit_product']/div[3]/button");
$I->See('Products');