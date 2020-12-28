<?php use courses\WebGuy;

$I = new WebGuy($scenario);

$I->wantTo('ensure that login page works');
$I->amOnPage('/admin/login');
$I->see('Log');
$I->wantTo('sign in');
$I->fillField('login-email', 'admin@ideabubble.com');
$I->fillField('login-password', 'password2015');
$I->click('login');
$I->see('reports');

//course
$I->wantTo('Want to check course');
$I->amOnPage('/admin/courses');
$I->canSee('Add');
$I->canSee('course');
$I->canSee('Title');

//Add a category
$I->wantTo('Want to Create a category');
$I->amOnPage('/admin/courses/add_category');
$I->fillField('category','TestCategory');
//$I->selectOption('parent_id', 'Grinds/Tutorials');
$I->fillField('summary','TestCategory');
//$I->click("//form[@id='form_add_edit_category']/div[6]/div/div/button[2]");
//$I->click("//form[@id='form_add_edit_category']/div[8]/button[2]");
//$I->see('Edit');

//Add a subject
$I->wantTo('Want to Create a subject');
$I->amOnPage('/admin/courses/add_subject');
$I->fillField('subject_form_name','Testsubject');
//$I->selectOption('parent_id', 'Grinds/Tutorials');
$I->fillField('subject_form_summary','TestSummary');
$I->fillField('subject_form_color','rgb(0, 0, 0)');
//$I->click("//button[@id='publish_no']");
//$I->click("//form[@id='form_add_edit_subject']/div[5]/button[2]");
//$I->see('Edit');


//Add a Location
$I->wantTo('Want to Create a location');
$I->amOnPage('/admin/courses/add_location');
$I->fillField('name','Testsubject');
$I->selectOption('parent_id', 'Limerick');
$I->selectOption('location_type_id', 'Room');
$I->fillField('capacity','20');
$I->fillField('online_capacity','16');
$I->fillField('address1','address1');
$I->fillField('address2','Address2');
$I->fillField('address3','Address3');
$I->selectOption('county_id', 'Limerick');
$I->fillField('email','test@ideabubble.ie');
$I->fillField('phone','0851234567');
$I->click("//form[@id='form_add_edit_location']/div[13]/button[2]");
$I->see('Edit');



//Add a Study Mode
$I->wantTo('Want to Create a Study Mode');
$I->amOnPage('admin/courses/add_study_mode');
$I->see('publish');
//$I->fillField('study_mode','TestStudyMode');
//$I->fillField('summary','SummaryTest');
//$I->click("//button[@id='publish_no']");


//Add a Provider
$I->wantTo('Want to Create a Provider');
$I->amOnPage('/admin/courses/add_provider');
$I->see('publish');
////$I->fillField('name','TestProvider');
//$I->fillField('address1','address1');
//$I->fillField('address2','Address2');
//$I->fillField('address3','Address3');
//$I->selectOption('county_id', 'Limerick');
//$I->selectOption('web_address', 'www.ideabubble.ie');
//$I->fillField('email','test@ideabubble.ie');
//$I->fillField('phone','0851234567');
//$I->click("//form[@id='form_add_edit_location']/div[10]/div/div/button[2]");
//$I->click("//form[@id='form_add_edit_location']/div[11]/button[2]");
//$I->see('Edit');



//add a Course
$I->wantTo('Want to Create a course');
$I->amOnPage('admin/courses/add_course');
$I->fillField('title','TestCourse');
$I->fillField('code','Test course');
$I->selectOption('year_id', 'ALL LEVELS');
$I->selectOption('level_id', 'Higher');
$I->selectOption('category_id', 'Grinds/Tutorials');
//$I->selectOption('subject_id', 'Maths');
$I->selectOption('provider_id', 'Kilmartin Education');
//$I->click("//form[@id='form_add_edit_course']/div[11]/div/div/button[2]");
//$I->click("//form[@id='form_add_edit_course']/div[14]/button[2]");
//$I->canSee('course');



//add a Course
//$I->wantTo('Want to Create Time table');
//$I->amOnPage('admin/courses/add_autotimetable');
//$I->fillField('att_name','TestCourse');
//$I->selectOption('att_category_id', 'Grinds/Tutorials');
//$I->selectOption('att_location_id', 'Limerick');
//$I->fillField('att_date_start','08-06-2015');
//$I->fillField('att_date_end','08-06-2016');
//$I->click("//button[@id='publish_no']");
//$I->click("btn_save");


// selenium webdriver
//add a Schedules
//$I->wantTo('Want to Create a schedules');
//$I->amOnPage('/admin/courses/add_schedule');
//$I->fillField('name','Testschedules');
//$I->selectOption('course_id', 'Accounting TY H - Grinds/Tutorials');
//$I->fillField('max_capacity','100');
//$I->selectOption('location_id', 'Limerick');
//$I->selectOption('trainer_id', 'Itsie');
//$I->selectOption('repeat', 'Daily - No Weekends');
//$I->selectOption('study_mode_id', 'Study model 1');
//$I->fillField('fee_amount','500');
//$I->fillField('start_date','11-06-2015');
//$I->fillField('end_date','11-06-2016');
//$I->click('generate_dates');
//$I->click("//form[@id='form_add_edit_schedule']/div[17]/button[2]");

