<?php use shop1\WebGuy;

$I = new WebGuy($scenario);
//contact us
$I->wantTo('ensure contact us is working');
$I->amOnPage('/contact-us.html');
$I->fillField('contact_form_name','Test');
$I->fillField('contact_form_address','ideabubble');
$I->fillField('contact_form_tel','0851234567');
$I->fillField('contact_form_email_address','test@ideabubble.ie');
$I->fillField('contact_form_message','Test');
$I->click('submit1');
$I->See('Thank');


//newsletter
$I->wantTo('ensure Newsletter is working');
$I->amOnPage('/');
$I->fillField('newsletter_signup_form_name','ideabubble test');
$I->fillField('newsletter_signup_form_email_address','test@ideabubble.ie');
$I->click('submit-newsletter');
$I->See('Thank');


