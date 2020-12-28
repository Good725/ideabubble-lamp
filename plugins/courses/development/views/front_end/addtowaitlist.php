<?php include Kohana::find_file('template_views', 'header');?>

<!-- body start here -->
    <section class="row">
        <div class="page-content">
            <div class="theme-heading large">
                <h2>Add Course to Waitlist.</h2>
                <h2><?=$page_data['schedule']['name']?></h2>
            </div>

            <div class="row">
                <h4> We'll contact you when...</h4>
                <?php $form_identifier = 'addwaitlist_'; ?>
                <form action="/add-to-waitlist.html" method="post" class="form-contact_us add-to-waitlist-form validate-on-submit" id="form-contact-us">
                    <input name="redirect" value="/thank-you.html" type="hidden" />
                    <input name="failpage" value="" type="hidden" />
                    <input name="subject" value="Contact form" type="hidden"/>
                    <input name="schedule_id" value="<?=$page_data['schedule']['id']?>" type="hidden"/>
                    <input name="redirect" value="thank-you.html" type="hidden" />
                    <input name="event" value="add-to-waitlist" type="hidden" />
                    <input name="trigger" value="custom_form" type="hidden" />
                    <input name="form_type" value="Contact Form" type="hidden" />
                    <input name="form_identifier" value="addwaitlist_" type="hidden" />
                    <input name="email_template" value="contactformmail" type="hidden" />

                    <div class="form-row">
                        <?= Form::ib_input(
                            __('Name'),
                            $form_identifier.'form_name',
                            null,
                            ['class' => 'validate[required]', 'id' => $form_identifier.'form_name']
                        ) ?>
                    </div>

                    <div class="form-row">
                        <?= Form::ib_input(
                            __('Email'),
                            $form_identifier.'form_email_address',
                            null,
                            ['class' => 'validate[required,custom[email]]', 'id' => $form_identifier.'form_email_address']
                        ) ?>
                    </div>

                    <div class="form-row mb-0">
                        <input type="submit" class="button btn btn-primary" id="submit-add-to-waitlist" value="Send"/>
                    </div>
                </form>
            </div>
        </div>
    </section>
<!-- footer start here -->
<?php include Kohana::find_file('views', 'footer');?>