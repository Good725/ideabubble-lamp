<? $form_identifier = 'contact_'; ?>
<form action="#" method="post" id="form-contact-us">
    <input type="hidden" value="Contact form" name="subject">
    <input type="hidden" value="Kilmartin Education Services" name="business_name">
    <input type="hidden" value="contact-us-thank-you.html" name="redirect">
    <input type="hidden" value="contact_us" name="trigger">
    <section class="revision-block">
        <div class="formBlock">
            <section class="col1">
                <label class="left contact_us_label"><span>Name</span></label>
                <input name="<?= $form_identifier ?>form_name" id="<?= $form_identifier ?>form_name" type="text"
                       class="validate[required]"/>
                <br class="spacer">
                <label class="left contact_us_label"><span>Address</span></label>
                <span class="txtarea contact_us_txtarea">
                    <textarea name="<?= $form_identifier ?>form_address" id="<?= $form_identifier ?>form_address"
                              class="styled"></textarea>
                </span>
                <br class="spacer">
                <label class="left contact_us_label"><span>Email</span></label>
                <input name="<?= $form_identifier ?>form_email_address" id="<?= $form_identifier ?>form_email_address"
                       class="validate[required,custom[email]]" type="text"/>
                <br class="spacer">
                <label class="left contact_us_label"><span>Phone</span></label>
                <input name="<?= $form_identifier ?>form_tel" id="<?= $form_identifier ?>form_tel"
                       class="validate[required]" type="text" value=""/>
                <br class="spacer">
                <label class="left contact_us_label"><span>Comments</span></label>
                <span class="txtarea contact_us_txtarea">
                    <textarea name="<?= $form_identifier ?>form_message" id="<?= $form_identifier ?>form_message"
                              class="styled"></textarea>
                </span>
                <br class="spacer">
            </section>
        </div>
        <div class="fields-col">
            <p>
                <span class="left contact_us_label">&nbsp;</span>
                <input type="checkbox" id="<?= $form_identifier ?>form_add_to_list"
                       name="<?= $form_identifier ?>form_add_to_list" value="1"
                       class="styled" data-id="<?= $form_identifier ?>form_add_to_list_span"/>
                &nbsp; I would like to sign up to the newsletter
            </p>
        </div>
    </section>
    <div class="right">
        <button class="button blue" id="submit-contact-us"><span><span>SEND EMAIL »</span></span></button>
    </div>
</form>