<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Front page Test');
$I->amOnPage('');
$I->canSee('Home');
$I->canSee('Nenagh');


// Commented test script works commented out as i was testing donalryan

/*
 *     php codecept.phar run acceptance --steps
 *
 *
   $I->submitForm('#form-newsletter', array(
    'newsletter_signup_form_name' => 'Miles',
    'newsletter_signup_form_email_address' => 'Davis@email.com',
), 'submit-newsletter');


// Check News letter all project
$I->fillField('newsletter_signup_form_name','davert');
$I->fillField('newsletter_signup_form_email_address','avis@email.com');
$I->click('submit-newsletter');
$I->canSee('thank you');
*/

/*

Only works with horganpharmacy
http://horganpharmacy.websitecms.ie/contact-us.html

$I->fillField('contact_form_name','Sadmir');
$I->fillField('contact_form_address','ideabubble');
$I->fillField('contact_form_tel','0851234567');
$I->fillField('contact_form_email_address','sadmir@ideabubble.ie');
$I->fillField('contact_form_message','Test');
$I->click('submit1');
$I->canSee('Thank You');

*/
$I->canSee('QUICK LINKS');
$I->amOnPage('/contact-us');
$I->canSee('Enquiry');


//CMS log in
$I->amOnPage('/admin/login');
$I->canSee('Log me in');
$I->fillField('email','admin@ideabubble.com');
$I->fillField('password','password');
$I->click('login');
$I->canSee('Welcome ');

//Booking
$I->amOnPage('admin/bookings');
$I->canSee('Add Booking');

//contacts
$I->amOnPage('admin/contacts2');
$I->canSee('Select Action');
$I->canSee('First Name');


//Add a contact
$I->amOnPage('admin/contacts2/add');
$I->fillField('first_name','Sadmir');
$I->fillField('last_name','sadmir');
$I->fillField('email','test1234@ideabubble.ie');
$I->fillField('phone','0861234567');
$I->fillField('notes','Test');
$I->click('#form_add_edit_contact button[type=submit]');
$I->canSee('success');

// Delete contact


//files
$I->amOnPage('admin/files');
$I->canSee('Create Directory');
$I->canSee('Name');

//add file
//delete file


//gallery
$I->amOnPage('admin/gallery');
$I->canSee('Add gallery');
$I->canSee('Category');

//add gallery
//delete gallery

//locations
$I->amOnPage('admin/locations');
$I->canSee('Add location');
$I->canSee('Search');


//media
$I->amOnPage('admin/media');
$I->canSee('Upload Image');
$I->canSee('Presets');


//menus
$I->amOnPage('admin/menus');
$I->canSee('Add Menu Item');
$I->canSee('footer');


//News
$I->amOnPage('admin/news');
$I->canSee('Add News Story');
$I->canSee('Title');



//notifications
$I->amOnPage('admin/notifications');
$I->canSee('Create Form');
$I->canSee('Notifications');


//pages
$I->amOnPage('admin/pages');
$I->canSee('Add Page');
$I->canSee('Page Name');


//panels
$I->amOnPage('admin/panels');
$I->canSee('Add Panel');
$I->canSee('Title');

//products
$I->amOnPage('admin/products');
$I->canSee('Add product');
$I->canSee('Code');

// will need to check tabs


//reports
$I->amOnPage('admin/reports');
$I->canSee('Add report');
$I->canSee('Title');


//testimonials
$I->amOnPage('admin/testimonials');
$I->canSee('Add testimonial');
$I->canSee('Title');
?>