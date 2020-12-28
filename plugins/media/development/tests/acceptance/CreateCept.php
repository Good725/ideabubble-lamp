<?php use media\WebGuy;

$I = new WebGuy($scenario);

$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');


//media
$I->wantTo('Want to check panel');
$I->amOnPage('/admin/media');
$I->canSee('Upload Image');
$I->canSee('Presets');
$I->dontSee('Kohona');

//upload a media
$I->wantTo('Want toupload');
$I->amOnPage('/admin/media/multiple_upload');
//$I->attachFile('input[@type="file"]', 'test_upload.png');
//
//$I->attachFile('//p[@id="file_upload_button"]', 'test_upload.png');
//$I->canSee('Title');
$I->dontSee('Kohona');


