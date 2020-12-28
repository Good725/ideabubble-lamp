<?php
if (isset($_GET['id']) AND (int)$_GET['id'] > 0) :?>
    <?php

    if (isset($_GET['eid']) AND $_GET['eid'] > 0) {
        $event_id = $_GET['eid'];
    }
    else {
        $event_id = NULL;
    }

    $schedule = Model_Schedules::get_course_and_schedule_short($_GET['id'], $event_id);

    if ($schedule) {
    // If a specific date has not been specified, use the start date
    if (is_null($event_id)) {
        $date = $schedule['start_date'];
    }
    else {
        $date = $schedule['datetime_start'];
    }

    // check if book now button is to be shown on settings toggle value
    $product_enquiry = FALSE;
    if (Settings::instance()->get('product_enquiry') == 1) {
        $product_enquiry = TRUE;
    }
    ?>

    <form action="#" method="post" id="booking_form">
    <input type="hidden" id="subject"  name="subject"       value="Booking form" />
    <input type="hidden"               name="business_name" value="Kilmartin Education Services" />
    <input type="hidden" id="redirect" name="redirect"      value="payment.html" />
    <input type="hidden"               name="event"         value="post_contactForm" />
    <input type="hidden" id="trigger"  name="trigger"       value="booking" />
    <input type="hidden"               name="schedule_id"   value="<?= $schedule['id'] ?>"/>
    <input type="hidden"               name="event_id"      value="<?= $schedule['event_id'] ?>"/>
    <input type="hidden"               name="training"      value="<?= $schedule['title'] ?>">
    <input type="hidden"               name="price"         value="<?= $schedule['fee_amount'] ?>">
    <input type="hidden"
           value="<?= $schedule['name'] ?>, <?= date("Y-m-d H:i", strtotime($date)) ?>, <?= $schedule['location'] ?>"
           name="schedule">
    <section class="content-section inner-content">
    <div class="title"><?= $schedule['title'] ?> (<?= $schedule['name'] ?>
        , <?= date("Y-m-d H:i", strtotime($date)) ?>, <?= $schedule['location'] ?>)
    </div>
    <section class="revision-block">
        <h1><strong>Guardian Details 1</strong></h1>

        <div class="formBlock">
            <section class="col1">
                <label><span>TITLE</span></label>

                <div class="selectbox">
                    <select name="guardian_title" class="styled">
                        <option value="">SELECT</option>
                        <option value="Mr">Mr</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>
                <br class="spacer">
                <label><span>FIRST NAME</span></label>
                <input id="guardian_first_name" name="guardian_first_name" class="validate[required]"
                       type="text"> <span class="require"> *</span>
                <br class="spacer">
                <label><span>Last NAME</span></label>
                <input id="guardian_last_name" name="guardian_last_name" class="validate[required]" type="text">
                <span class="require"> *</span>
                <br class="spacer">
                <label><span>Relationship TO STUDENT</span></label>

                <div class="selectbox">
                    <select name="guardian_relationship_to_student" class="styled">
                        <option value="">SELECT</option>
                        <option value="parent">parent</option>
                        <option value="uncle">uncle</option>
                        <option value="other">Other - please specify below</option>
                    </select>
                </div>
                <br class="spacer">
                <label><span>Please Specify</span></label>
                <input name="guardian_relationship_to_student_other" type="text">
                <br class="spacer">

                <div class="required">( * ) Denotes required field.</div>
            </section>
            <section class="col2">
                <label>ADDRESS</label>

                <div class="fields-col">
                    <input id="guardian_address1" name="guardian_address1" type="text"/>
                    <br class="spacer">
                    <input id="guardian_address2" name="guardian_address2" type="text"/>
                    <br class="spacer">
                    <input id="guardian_address3" name="guardian_address3" type="text"/>
                    <br class="spacer">
                </div>
                <br class="spacer">
                <label><span>City</span></label>
                <input id="guardian_city" name="guardian_city" type="text"/>
                <br class="spacer">
                <label><span>County</span></label>

                <div class="selectbox">
                    <select id="guardian_county" name="guardian_county" class="styled">
                        <option value="">SELECT</option>
                        <?= Model_Cities::get_all_counties_html_options() ?>
                    </select>
                </div>
                <br class="spacer">

            </section>

            <section class="col3">
                <label><span>Email</span></label>
                <input id="guardian_email" name="guardian_email" class="validate[required,custom[email]]"
                       type="text"/><span class="require"> *</span>
                <br class="spacer">
                <label><span>MOBILE</span></label>
                <input id="guardian_mobile" name="guardian_mobile" class="validate[required]" type="text"/><span
                    class="require" id="guardian_mobile_required"> *</span>
                <br class="spacer">
                <label><span>PHONE</span></label>
                <input id="guardian_phone" name="guardian_phone" type="text"/>
                <br class="spacer">

                <p>PREFERRED CONTACT METHOD</p>

                <div class="chkbox1"><label>SMS</label><input type="checkbox" data-id='guardian_preffered_s'
                                                              id="guardian_preffered_sms"
                                                              name="guardian_preffered_sms" value="Yes"
                                                              class="styled"/></div>
                <div class="chkbox1"><label>EMAIL</label><input type="checkbox" data-id='guardian_preffered_e'
                                                                id="guardian_preffered_email"
                                                                name="guardian_preffered_email" value="Yes"
                                                                class="styled"/></div>
                <div class="chkbox1"><label>POST</label><input type="checkbox" data-id='guardian_preffered_p'
                                                               id="guardian_preffered_post"
                                                               name="guardian_preffered_post" value="Yes"
                                                               class="styled"/></div>
            </section>
        </div>
    </section>

    <section class="revision-block">
        <h1><strong>STUDENT DETAILS</strong></h1>

        <div class="formBlock">
            <section class="col1">
                <label><span>TITLE</span></label>

                <div class="selectbox">
                    <select name="student_title" class="styled">
                        <option value="">SELECT</option>
                        <option value="Mr">Mr</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>
                <br class="spacer">
                <label><span>FIRST NAME</span></label>
                <input id="student_first_name" name="student_first_name" class="validate[required]"
                       type="text"/> <span class="require"> *</span>
                <br class="spacer">
                <label><span>Last NAME</span></label>
                <input id="student_last_name" name="student_last_name" class="validate[required]" type="text"/>
                <span class="require"> *</span>
                <br class="spacer">
                <label><span>Date of Birth</span></label>

                <div class="selectbox">
                    <input id="student_date_of_birth" name="student_date_of_birth" type="text"/>
                </div>
<!--                <br class="spacer">-->
<!--                <label><span>Location</span></label>-->
<!---->
<!--                <div class="selectbox">-->
<!--                    <select name="student_location" class="styled">-->
<!--                        <option value="4">SELECT</option>-->
<!--                        <option value="5">Item 1</option>-->
<!--                        <option value="6">Item 2</option>-->
<!--                    </select>-->
<!--                </div>-->
                <br class="spacer">
            </section>

            <section class="col2">
                <div class="chkbox2"><label>Use guardian address details</label><input type="checkbox"
                                                                                       data-id='use_guardian_addr'
                                                                                       id="use_guardian_address"
                                                                                       name="use_guardian_address"
                                                                                       class="styled"/></div>
                <br class="spacer">
                <label>ADDRESS</label>

                <div class="fields-col">
                    <input id="student_address1" name="student_address1" type="text"/>
                    <br class="spacer">
                    <input id="student_address2" name="student_address2" type="text"/>
                    <br class="spacer">
                    <input id="student_address3" name="student_address3" type="text"/>
                    <br class="spacer">
                </div>
                <br class="spacer">
                <label><span>City</span></label>
                <input id="student_city" name="student_city" type="text"/>
                <br class="spacer">
                <label><span>County</span></label>

                <div class="selectbox">
                    <select id="student_county" name="student_county" class="styled">
                        <option value="">SELECT</option>
                        <?= Model_Cities::get_all_counties_html_options() ?>
                    </select>
                </div>
                <br class="spacer">
            </section>

            <section class="col3">
                <div class="chkbox2"><label>Use guardian address details</label><input type="checkbox"
                                                                                       data-id='use_guardian_addr2'
                                                                                       id="use_guardian_address2"
                                                                                       name="use_guardian_address2"
                                                                                       class="styled"/></div>
                <br class="spacer">
                <label><span>Email</span></label>
                <input id="student_email" name="student_email" class="validate[required,custom[email]]"
                       type="text"/><span class="require"> *</span>
                <br class="spacer">
                <label><span>MOBILE</span></label>
                <input id="student_mobile" name="student_mobile" type="text"/>
                <br class="spacer">
                <label><span>PHONE</span></label>
                <input id="student_phone" name="student_phone" type="text"/>
                <br class="spacer">

                <p>PREFERRED CONTACT METHOD</p>

                <div class="chkbox1"><label>SMS</label><input type="checkbox" data-id='student_preffered_s'
                                                              id="student_preffered_sms"
                                                              name="student_preffered_sms" value="Yes"
                                                              class="styled"/></div>
                <div class="chkbox1"><label>EMAIL</label><input type="checkbox" data-id='student_preffered_e'
                                                                id="student_preffered_email"
                                                                name="student_preffered_email" value="Yes"
                                                                class="styled"/></div>
                <div class="chkbox1"><label>POST</label><input type="checkbox" data-id='student_preffered_p'
                                                               id="student_preffered_post"
                                                               name="student_preffered_post" value="Yes"
                                                               class="styled"/></div>
            </section>
        </div>
    </section>

    <div class="right">
        <button id="reset-booking" class="button red btngap" onclick="window.location = 'home.html';">
            <span><span>CANCEL</span></span></button>
        <button id="enquiring-course" data-id="<?= $_GET['id'] ?>" data-event_id="" class="button sky btngap">
            <span><span>ENQUIRE NOW »</span></span>
        </button>
        <?php if ((isset($schedule['fee_amount'])) AND (!$product_enquiry)) {
            echo '<button id="booking-course" data-id="' . $_GET['id'] . '" class="button blue btngap"><span><span>BOOK NOW »</span></span></button>';
        } ?>
    </div>
    </section>
    </form>
    <?php
    } else {
    ?>
    <p>The schedule is not available</p>
    <?php
    }
    ?>
<?php else: ?>
    There is no schedule selected! Please visit <a href='/course-list.html'>Course
        list</a> and select course and schedule!
<?php endif; ?>