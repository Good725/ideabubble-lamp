<?php use contacts2\WebGuy;

$I = new WebGuy($scenario);
$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');

// Codes are written with selenium2 driver
//Student
$I->wantTo('Create a contact (Student)');
$I->amOnPage('/admin/contacts3/add_edit_contact/general');
//$I->fillField('contact_family', 'Noonan - Copay, Blackabbey Road');
$I->selectOption('contact_role_id', 'Student');
//$I->click('//div[@id="contact_is_primary"]/label/input');
$I->selectOption('title', 'Mr');
$I->fillField('contact_first_name', 'First');
$I->fillField('contact_last_name', 'Last');
$I->fillField('contact_date_of_birth', '21-05-2015');
$I->selectOption('contact_year_id', 'ALL LEVELS');
$I->checkOption('#contact_preference_emergency');
$I->checkOption('#contact_preference_accounts');
$I->checkOption('#contact_preference_absentee');
$I->fillField('contact_address1', 'Address1');
$I->fillField('contact_address2', 'Address2');
$I->fillField('contact_address3', 'Address3');
$I->fillField('contact_town', 'Limerick');
$I->selectOption('contact_county', 'Limerick');
$I->fillField('contact_postcode', '0000');
$I->fillField("//fieldset[@id='contact_contact_information_section']/div/input", '0859999999');
$I->click("//fieldset[@id='contact_contact_information_section']/div/div[2]/span/button");
$I->click('//button[@id="save_contact"]');
$I->click('//button[@id="save_contact_with_new_family"]');
$I->click("//form[@id='add_edit_contact']/div[5]/button");
$I->see('Create New');

//
////Teacher
//$I->wantTo('Create a contact (Teacher)');
//$I->amOnPage('/admin/contacts3/add_edit_contact/teacher');
//$I->selectOption('title', 'Mr');
//$I->fillField('contact_first_name', 'TeacherFirstName');
//$I->fillField('contact_last_name', 'TeacherLastName');
//$I->fillField('contact_date_of_birth', '21-05-1900');
//$I->checkOption('#contact_preference_emergency');
//$I->checkOption('#contact_preference_accounts');
//$I->checkOption('#contact_preference_absentee');
//$I->fillField('contact_address1', 'Address2');
//$I->fillField('contact_address3', 'Address3');
//$I->fillField('contact_town', 'Limerick');
//$I->selectOption('contact_county', 'Limerick');
//$I->fillField('contact_postcode', '0000');
//$I->fillField("//fieldset[@id='contact_contact_information_section']/div/input", '0859999999');
//$I->click("//fieldset[@id='contact_contact_information_section']/div/button");
//$I->click("//fieldset[@id='course_type_teaching_preferences_section']/div/div/button");
//$I->click("//fieldset[@id='course_type_teaching_preferences_section']/div/div/ul/li[7]/a/label/input");
//$I->click("//form[@id='add_edit_contact']/div[5]/button");
//$I->click("//form[@id='add_edit_contact']/div[5]/button");
//$I->see('ID');
//
//
////Staff
//$I->wantTo('Create a contact (Staff)');
//$I->amOnPage('/admin/contacts3/add_edit_contact/teacher');
//$I->selectOption('title', 'Mr');
//$I->fillField('contact_first_name', 'StaffFirstName');
//$I->fillField('contact_last_name', 'StaffLastName');
//$I->fillField('contact_date_of_birth', '21-05-1900');
//$I->checkOption('#contact_preference_emergency');
//$I->checkOption('#contact_preference_accounts');
//$I->checkOption('#contact_preference_absentee');
//$I->fillField('contact_address1', 'Address1');
//$I->fillField('contact_address3', 'Address3');
//$I->fillField('contact_town', 'Limerick');
//$I->selectOption('contact_county', 'Limerick');
//$I->fillField('contact_postcode', '0000');
//$I->fillField("//fieldset[@id='contact_contact_information_section']/div/input", '0859999999');
//$I->click("//fieldset[@id='contact_contact_information_section']/div/button");
//$I->click('//button[@id="save_contact_exit"]');
//$I->click('//button[@id="save_contact_exit"]');
//$I->see('Name');
//
//
//
//
////contacts
//$I->amOnPage('/admin/contacts2');
//$I->canSee('Select Action');
//$I->canSee('First Name');
//
//
////Add a contact
//$I->amOnPage('admin//contacts2/add');
//$I->fillField('first_name','Sadmir');
//$I->fillField('last_name','sadmir');
//$I->fillField('email','test1234@ideabubble.ie');
//$I->fillField('phone','0861234567');
//$I->fillField('notes','Test');
//$I->click('#form_add_edit_contact button[type=submit]');
//$I->canSee('success');
